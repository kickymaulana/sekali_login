# 4-LEGACY-DECODER.md — Analisis Kodebase SSO Sekali Login

> Dibuat: 2026-08-22 · Metode: legacy-decoder (reverse engineering dari kode existing)

## FASE 1 — Discovery (Stack)

| Aspek | Nilai |
|---|---|
| Framework | Laravel 13 (PHP ^8.3) |
| Frontend | Inertia 3 + Vue 3 + TypeScript (`<script setup lang="ts">`) |
| Styling | Tailwind CSS 4 (via `@tailwindcss/vite`, CSS-first, tanpa config file) + Varlet UI + Lottie |
| UI Library | `@varlet/ui`, `@fontsource/inter`, `lottie-web` |
| Routing frontend | Ziggy (`route()`), plugin `ZiggyVue` di `resources/js/app.ts` |
| Auth/OAuth | Laravel Passport 13 (OAuth2), spatie/laravel-permission 8 |
| MCP | laravel/mcp 0.9 (endpoint `/mcp/sso`) |
| DB | MariaDB (dev, `sekalilogin`), sqlite `:memory:` (test via phpunit.xml) |
| Build | Vite 8 + laravel-vite-plugin 3, `base: '/build/'` |
| Dev tools | Pint, PHPUnit 12, vue-tsc, Pail, Pao, Tinker, Mockery |
| Serving | Apache htdocs subdir → `http://localhost/sekali_login/public` (bukan `artisan serve`) |

### Struktur folder
```
app/Http/Controllers/       Auth/ (login, register), Admin/ (clients, users, roles), ConnectedAppController
app/Mcp/Servers/            SsoServer (server MCP)
app/Mcp/Tools/              GetUserInfoTool
app/Models/User.php         model user + HasRoles + HasApiTokens
app/Providers/              AppServiceProvider (forceRootUrl + Passport authorizeView)
app/Http/Middleware/        HandleInertiaRequests (shared props Inertia)
bootstrap/app.php           routing, middleware Spatie alias
routes/web.php, api.php, ai.php, console.php
resources/js/Pages/         Home, Profile, Security, Auth/*, Auth/OAuth, Profile/ConnectedApps, Admin/*
database/migrations/        users, cache, jobs, permission_tables, oauth_* (Passport), add_nik
database/seeders/           DatabaseSeeder → UserSeeder (admin)
```

## FASE 2 — Codebase Mapping

### Entry point & alur request
1. Apache → `public/index.php` → Laravel.
2. Middleware web: `HandleInertiaRequests` → inject `csrf_token`, `app_url`, `new_client` (flash session), `auth.user` (id, name, email, roles, permissions) ke semua props Inertia.
3. Semua URL di-force ke `config('app.url')` via `URL::forceRootUrl` di `AppServiceProvider::boot` — alasan APP_URL harus `http://localhost/sekali_login/public`.
4. `routes/ai.php` (laravel/mcp) auto-register — **tidak** tercantum di `withRouting` (web/api/commands), tapi route `/mcp/sso` tetap aktif (verified via `route:list`).

### Middleware
- `role` / `permission` / `role_or_permission` = alias Spatie (bootstrap/app.php).
- Area admin: `['auth', 'role:admin']`.
- API: `auth:api` (Passport token).

### Dependency & gap arsitektur
- `Passport::authorizationView` di-register **2x** di AppServiceProvider; yang kedua (blade `mcp/authorize`) menang → halaman Inertia `Auth/OAuth/Authorize.vue` dead code.
- Klien OAuth dikelola controller custom (`OAuthClientController`), bukan API Passport resmi → secret plaintext di DB, tidak memakai hashing Passport.

## FASE 3 — Business Logic Extraction

### Endpoint (web)
| Method | URI | Controller | Auth |
|---|---|---|---|
| GET/POST | `/login` | `Auth\LoginController@create/store` | guest |
| GET/POST | `/register` | `Auth\RegisterController@create/store` | guest |
| POST | `/logout` | `Auth\LoginController@destroy` | auth |
| GET | `/` (dashboard) | closure di web.php | auth |
| GET | `/profile` | closure | auth |
| GET | `/connected-apps` | `ConnectedAppController@index` | auth |
| POST | `/connected-apps/{tokenId}/revoke` | `ConnectedAppController@revoke` | auth |
| GET | `/security` | closure | auth |
| GET/POST | `/password/change` | closure | auth |
| GET | `/api/user` | closure (api.php) | `auth:api` |
| admin/... | `clients`, `users`, `roles` (resource) + `clients.secret`, `clients.regenerate-secret` | `Admin\*Controller` | auth + role:admin |
| GET | `/mcp/sso` (+POST/DELETE) | laravel/mcp `SsoServer` | tanpa auth |
| GET | `/oauth/*`, `/oauth/token` | Passport | — |

### Business rules
1. **Login berbasis NIK + password** (`Auth::attempt(['nik' => ...])`), bukan email. `nik` nullable + unique (migration `add_nik_to_users_table`).
2. **Register**: wajib nik (unique), nama, email (unique), password (confirmed, rules default). Assign role default `user` bila role ada. Auto-login. Redirect via `Inertia::location` (full browser redirect, mendukung alur OAuth).
3. **Login sukses** → `Inertia::location` ke `url.intended` (default dashboard), session regenerate.
4. **Dashboard**: hitung connected apps dari `oauth_access_tokens` (revoked=false) join `oauth_clients`; summary aktifApps/sessions/tokensIssued/rolesCount.
5. **Client OAuth** (custom): buat `Client` Passport manual — id UUID, `owner_id` = admin pembuat, secret `Str::random(40)` **plaintext**, `redirect_uris` array, `grant_types = [authorization_code, refresh_token]`. Secret tampil sekali (flash `new_client`); `showSecret` selalu menolak; `regenerateSecret` buat secret baru.
6. **Revoke akses** = update `oauth_access_tokens.revoked = 1` (scoped ke user pemilik token).
7. **Role/permission**: spatie. Roles `admin`, `user`. Permission `manage users` → `admin`. UserController wajib pilih role (exists:roles,name).
8. **MCP tool `get-user-info`**: cari user by email OR id, return id/name/email/roles/created_at. Error bila tak ditemukan.
9. **Ganti password**: validasi password lama via `Hash::check`, baru min 8 + confirmed.

### Gap / temuan (untuk task list)
- G1: Seeder admin tanpa NIK → tak bisa login NIK.
- G2: Client secret plaintext.
- G3: `authorizationView` double-register → Inertia authorize mati.
- G4: MCP `/mcp/sso` tanpa proteksi auth.
- G5: Tidak ada reset/lupa password, MFA, rate-limit.
- G6: `api/user` hanya kembalikan id/nik/name/email (tanpa roles/permissions).
- G7: Tidak ada test sama sekali (hanya `tests/TestCase.php`).

## FASE 4 — Database Reverse (ERD)

### Tabel inti
```
users
  id (PK, bigint) · nik (string nullable unique) · name · email (unique) · email_verified_at · password · remember_token · timestamps

oauth_clients (Passport)
  id (uuid) · user_id(owner) nullable · name · secret (plaintext) · provider · redirect (text) / redirect_uris · personal_access_client · password_client · revoked · created_at/updated_at
  → owner: Admin pilih `owner_id` + `owner_type` (get_class(user))

oauth_access_tokens
  id (uuid) · user_id · client_id · name · scopes · revoked · created_at · updated_at · expires_at
  → dasar hitung "connected apps" & revoke

oauth_auth_codes / oauth_refresh_tokens / oauth_device_codes
  (Passport standar)

roles / permissions / model_has_roles / model_has_permissions / role_has_permissions
  (spatie standar, via migration `create_permission_tables`)

cache / jobs / sessions
  (framework standar; session & cache driver = database)
```

### Relasi
- `users` 1—N `oauth_access_tokens` (via user_id)
- `oauth_clients` 1—N `oauth_access_tokens` (via client_id)
- `users` N—M `roles` N—M `permissions` (spatie)

### Catatan
- Tidak ada tabel `personal_access_clients` seed → cek bahwa `passport:install` sudah dijalankan (tabel oauth_* ada di migration).
- `nik` diindeks unique tapi nullable → beberapa user boleh tanpa NIK, tapi login NIK tidak akan menemukan mereka.

## Referensi lanjutan
- PRD: `PRD.md`
- Stack & perintah: `AGENTS.md` (jika ada) / README
- Next: `.agents/3-TASKS.md`
