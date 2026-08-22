# 2-TECH-SPEC.md — Tech Spec SSO Sekali Login

> Sumber: `.agents/4-LEGACY-DECODER.md` · Status: existing codebase, dokumen rekonstruksi

## 1. Tech Stack

| Layer | Pilihan | Catatan |
|---|---|---|
| Backend | Laravel 13 / PHP ^8.3 | config di `bootstrap/app.php` |
| Frontend | Inertia 3 + Vue 3 + TypeScript | `<script setup lang="ts">`, SPA |
| UI | Tailwind CSS 4 (Vite) + Varlet UI + Lottie | CSS-first, tanpa tailwind.config |
| Routing FE | Ziggy (`route()`) | plugin `ZiggyVue` di `app.ts` |
| Auth SSO | Laravel Passport 13 | OAuth2 authorization_code + refresh_token |
| RBAC | spatie/laravel-permission 8 | alias `role`/`permission` di bootstrap |
| MCP | laravel/mcp 0.9 | `routes/ai.php` auto-register |
| DB | MariaDB (dev), sqlite `:memory:` (test) | session/cache/queue = database |
| Build | Vite 8, `base: '/build/'` | output `public/build` (gitignored) |
| Serve | Apache subdir `http://localhost/sekali_login/public` | `URL::forceRootUrl` |

## 2. DB Design

```
users
  id PK · nik string nullable unique · name · email unique · password · email_verified_at · remember_token · timestamps

oauth_clients (Passport)
  id uuid PK · user_id(owner) nullable · name · secret · provider · redirect/redirect_uris · personal_access_client · password_client · revoked · timestamps

oauth_access_tokens
  id uuid PK · user_id · client_id FK · name · scopes · revoked · expires_at · timestamps

oauth_auth_codes / oauth_refresh_tokens / oauth_device_codes (Passport standar)

roles · permissions · model_has_roles · model_has_permissions · role_has_permissions (spatie)

cache · jobs · sessions (framework)
```

Relasi:
- `users` 1—N `oauth_access_tokens`
- `oauth_clients` 1—N `oauth_access_tokens`
- `users` N—M `roles` N—M `permissions`

Catatan: `nik` unique nullable → user tanpa NIK tak bisa login NIK.

## 3. Interface

### Halaman (Inertia)
- Guest: `Auth/Login`, `Auth/Register`
- User: `Home` (dashboard), `Profile`, `Security`, `Profile/ConnectedApps`, `Auth/ChangePassword`
- Admin (`/admin/*`): `Admin/Clients/{Index,Create,Edit}`, `Admin/Users/*`, `Admin/Roles/*`
- OAuth: `Auth/OAuth/Authorize` — **dead code**, blade `mcp/authorize` menang (B2)

### Shared props (`HandleInertiaRequests`)
`csrf_token`, `app_url`, `new_client` (flash), `auth.user {id, name, email, roles, permissions}`

### API
- `GET /api/user` — Passport token → id, nik, name, email (belum ada roles, B3)

## 4. Alur

### Auth web
```
Guest → /login (NIK+password) → Auth::attempt(['nik']) → session regenerate
     → Inertia::location(url.intended | dashboard)
Register → nik unique + email unique → assign role 'user' → auto-login
Logout → invalidate session → redirect /login
Ganti password → Hash::check(current) → Hash::make(new, min 8)
```

### OAuth2 (SSO inti)
```
Client app → GET /oauth/authorize → (belum login? → /login) → approve
  → authorization code → POST /oauth/token (code) → access + refresh token
  → client panggil GET /api/user (Bearer) → profil user
Revoke: POST /connected-apps/{tokenId}/revoke → oauth_access_tokens.revoked=1
```

### Admin
```
/oauth clients: CRUD custom → Client Passport (uuid, secret Str::random(40) plaintext,
  redirect_uris array, grant_types [authorization_code, refresh_token])
  secret tampil sekali (flash new_client) · regenerate → secret baru · showSecret → tolak
/users: CRUD + assign role (wajib role exists)
/roles: CRUD + syncPermissions
```

### MCP
```
/mcp/sso (SsoServer) → tool get-user-info(search: email|id)
  → User where email|id → id, name, email, roles, created_at
```

## 5. Keamanan

| Area | Kondisi | Rekomendasi |
|---|---|---|
| Login | NIK+password, tanpa rate-limit | rate-limit (S3) |
| Client secret | plaintext di DB | Passport hashed secret, tampil sekali (S1) |
| MCP endpoint | tanpa auth | pasang MCP OAuth/token (S2) |
| RBAC | middleware `role:admin` | sudah OK |
| Password | `hashed` cast + bcrypt 12 | sudah OK |
| CSRF | session token, Ziggy | sudah OK |
| Session | database driver | sudah OK |
| Token | Passport access/refresh | sudah OK |
| Error | JSON hanya untuk `api/*` (`shouldRenderJsonWhen`) | sudah OK |

Prioritas task: lihat `.agents/3-TASKS.md` (B1/S1/S2 = P0).
