# AGENTS.md — SSO Sekali Login

## Stack
Laravel 13 (PHP 8.3) + Inertia 3 + Vue 3 TS (`<script setup lang="ts">`) + Tailwind 4 (CSS-first, tanpa config) + Varlet UI + Lottie + Ziggy.
SSO OAuth2 via Laravel Passport 13 + spatie/laravel-permission 8 + laravel/mcp 0.9. Custom auth (tanpa Breeze/Fortify). UI Bahasa Indonesia.

## Serve (penting)
Jalan via Apache htdocs subdir → http://localhost/sekali_login/public (bukan `php artisan serve`).
`.env`: `APP_URL=.../public` + `URL::forceRootUrl()` di `app/Providers/AppServiceProvider.php` → semua URL ber-prefix `/sekali_login/public`.
Vite `base: '/build/'`. `npm run dev` (HMR) / `npm run build` → `public/build` (gitignored).

## Auth
Login = **NIK + password** (`Auth::attempt(['nik'])`), bukan email. `nik` nullable-unique.
Seeder admin `kickymaulana@gmail.com` / `password123` **TANPA NIK** → tak bisa login via NIK.
Register → auto-login, role default `user`.

## OAuth2 (inti)
Admin buat client via UI `/admin/clients` (controller custom `Admin/OAuthClientController`, bukan Passport API):
- secret `Str::random(40)` → Passport 13 hash otomatis di model setter; tampil sekali (flash `new_client`)
- `grant_types [authorization_code, refresh_token]`, `redirect_uris` array

Flow: `/oauth/authorize` → blade `mcp/authorize` (konsen) → code → `POST /oauth/token` → `GET /api/user` (Bearer).
Revoke: `POST /connected-apps/{clientId}/revoke` → revoke **semua token** client untuk user (`revoked=1`).

**GOTCHA:** `authorizationView` di-register 2x di `AppServiceProvider`; blade `mcp/authorize` menang (**SENGAJA**, utk flow MCP/AI). Inertia `Auth/OAuth/Authorize.vue` = dead code. Blade kirim `state` kosong (bug, jangan ubah tanpa diskusi).

**Blade `mcp/authorize` (teks dinamis, final):**
- Subtitle: `{{ $client->name }} akan menggunakan identitas SSO Anda untuk proses login dan menentukan hak akses aplikasi.`
- Tombol kiri: `Batalkan`
- Tombol kanan: `Lanjut ke {{ $client->name }}`

## MCP (laravel/mcp)
`routes/ai.php` auto-register (tidak perlu `withRouting(ai:)`). Server `app/Mcp/Servers/SsoServer.php`, tools `app/Mcp/Tools/*` (daftar di `$tools` array).
Endpoint `/mcp/sso` **TANPA AUTH** (sengaja utk testing Inspector). Scope `mcp:use` (dari `ensureMcpScope`).
`opencode.json` → MCP remote `http://localhost/sekali_login/public/mcp/sso`.

## Frontend
Pola **"android-layout"**: `.android-layout` > `.top-app-bar` > `.android-content` (max-width 520–900). Hero gradient indigo–purple.
Komponen Varlet: `var-icon`, `var-button`, `var-input`, `var-chip`, `var-table`, `var-bottom-navigation`, `Snackbar`, `Dialog`.
Shared props (`HandleInertiaRequests`): `csrf_token`, `app_url`, `new_client` (flash), `auth.user {id, name, email, roles, permissions}`.

**Connected apps — grup per aplikasi (bukan per token):**
- `ConnectedAppController@index`: `GROUP BY client_id` → tampil 1 baris per app (nama, `token_count`, `last_connected` = `MAX(created_at)`).
- `Profile/ConnectedApps.vue`: card per app — "Terhubung {tgl} · {n} token", tombol Cabut → revoke per client.
- Dashboard `/` juga grup per app (sama).
- Format tanggal: `Carbon::translatedFormat('d M Y')` locale `id` → "22 Agt 2026" (standar Carbon id).

## DB
MariaDB `sekalilogin` (dev), sqlite `:memory:` (test). Tabel: `users`, `oauth_*` (Passport), `roles`/`permissions` (Spatie), `sessions`/`cache`/`jobs`.
Relasi: users 1—N oauth_access_tokens · oauth_clients 1—N oauth_access_tokens · users N—M roles N—M permissions.

## Commands
- `composer test` = `config:clear` + `artisan test` (PHPUnit sqlite memory; **BELUM ada test**)
- `vendor/bin/pint` (format, pint.json default)
- `npx vue-tsc --noEmit` (typecheck; **ERROR pre-existing** — vue-tsc incompatible dgn TS 7, jangan pakai sbg validasi)
- `php artisan passport:hash` (hash secret lama; client baru sudah hash otomatis)
- `composer setup` (fresh env + migrate + build)

## Docs rujukan
- `.agents/4-LEGACY-DECODER.md` — analisis lengkap + ERD + gap
- `.agents/3-TASKS.md` — task list (B1/S1/S2 P0 belum dikerjakan)
- `.agents/2-TECH-SPEC.md` — tech spec
- `PRD.md` — dokumen produk

## JANGAN diubah tanpa diskusi user
- Blade `mcp/authorize` (authorizationView + teks dinamis) — dipakai flow MCP/AI, teks final
- Validasi redirect: tetap `required|url` (admin butuh localhost utk test)
- Mode saat ini: pahami saja, jangan ubah kode tanpa diminta
