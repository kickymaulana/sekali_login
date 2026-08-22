# PRD — Aplikasi SSO Sekali Login

## 1. Ringkasan

Aplikasi **SSO (Single Sign-On)** bernama "Sekali Login" — satu kredensial untuk mengakses semua aplikasi internal. User login sekali dengan **NIK + password**, lalu dapat mengotorisasi berbagai aplikasi klien melalui protokol **OAuth2 (Authorization Code + Refresh Token)** tanpa login ulang.

Server SSO juga menyediakan **MCP server** agar agen AI dapat mengambil data pengguna secara terstruktur.

- **Stack**: Laravel 13, Inertia 3, Vue 3 + TypeScript, Tailwind 4, Varlet UI, Laravel Passport, spatie/laravel-permission, laravel/mcp.
- **URL akses**: `http://localhost/sekali_login/public` (via Apache, bukan `php artisan serve`).
- **Bahasa UI**: Indonesia.

## 2. Tujuan & Metrik Keberhasilan

| Tujuan | Metrik |
|---|---|
| Satu login untuk banyak aplikasi | User mengotorisasi ≥2 aplikasi klien tanpa login ulang |
| Kelola klien OAuth terpusat | Admin membuat/mereset klien melalui UI admin |
| Kelola user, role, permission terpusat | Admin mengelola user & role melalui UI admin |
| Revoke akses mudah & transparan | User dapat melihat & mencabut akses aplikasi per-token |
| Integrasi AI aman & terstruktur | MCP server mengembalikan data user valid |

## 3. Persona & Role

### 3.1 End User
- Login NIK + password.
- Melihat dashboard ringkasan: jumlah aplikasi aktif, token diterbitkan, jumlah role.
- Melihat daftar aplikasi yang terhubung (`/connected-apps`).
- Mencabut akses aplikasi.
- Mengubah password.

### 3.2 Admin (role `admin`)
- Semua kemampuan End User.
- CRUD klien OAuth2 + kelola/regenerate client secret.
- CRUD user + assign role.
- CRUD role & permission.

### Model role
- `admin` — akses penuh area admin (`/admin/*`).
- `user` — akses area pribadi.
- Permission: `manage users` (diberikan ke `admin`).

## 4. Alur SSO (OAuth2)

### 4.1 Otorisasi aplikasi klien
1. User membuka aplikasi klien (menuju `GET /oauth/authorize`).
2. User diarahkan ke halaman login SSO bila belum login.
3. User login NIK + password → session dibuat.
4. User menyetujui otorisasi klien (halaman authorize).
5. SSO mengembalikan *authorization code* → klien menukarnya dengan *access token* + *refresh token* (`POST /oauth/token`).
6. Klien memanggil `GET /api/user` memakai access token untuk mendapatkan profil (id, nik, name, email).

### 4.2 Pencabutan akses
- `POST /connected-apps/{tokenId}/revoke` → set `oauth_access_tokens.revoked = 1`.

## 5. Kebutuhan Fungsional

Prioritas: **P0** = harus ada, **P1** = penting, **P2** = nanti.

### 5.1 Autentikasi (P0) — sudah diimplementasikan
- **AUTH-01** User login dengan NIK + password. *Status: ada (`LoginController@store`).*
- **AUTH-02** Opsi "Ingat Saya" (remember). *Ada.*
- **AUTH-03** User register akun baru. *Ada (`RegisterController`).*
- **AUTH-04** Logout menghapus session. *Ada.*
- **AUTH-05** Ganti password (wajib password lama, min 8 karakter). *Ada (`/password/change`).*
- **AUTH-06** Validasi error ditampilkan dalam Bahasa Indonesia. *Ada.*

### 5.2 Dashboard (P0) — sudah diimplementasikan
- **DASH-01** Menampilkan ringkasan: aplikasi aktif, session aktif, token diterbitkan, jumlah role.
- **DASH-02** Menampilkan daftar aplikasi terhubung (nama, kategori, tanggal koneksi, status).
- **DASH-03** Halaman Profile menampilkan data user + role + permission.

### 5.3 OAuth Client Management (P0) — sudah diimplementasikan
- **CLI-01** Admin CRUD klien OAuth (nama + redirect URL). *Ada (`OAuthClientController`).*
- **CLI-02** Client secret dibuat `Str::random(40)`, **ditampilkan sekali** saat pembuatan. *Ada.*
- **CLI-03** Regenerate secret bila bocor/lupa. *Ada (`admin.clients.regenerate-secret`).*
- **CLI-04** Secret tidak bisa dibaca ulang setelah dibuat. *Ada (endpoint `showSecret` menolak).*
- **CLI-05** Grant type default: `authorization_code` + `refresh_token`.

### 5.4 User Management (P0) — sudah diimplementasikan
- **USR-01** Admin CRUD user (nik, name, email, password).
- **USR-02** Assign/sinkronisasi role per user.
- **USR-03** Pencarian user berdasar nama/email/NIK, pagination.

### 5.5 Role & Permission (P1) — sudah diimplementasikan
- **ROL-01** Admin CRUD role.
- **ROL-02** Kelola permission per role.

### 5.6 Connected Apps (P0) — sudah diimplementasikan
- **CON-01** User melihat daftar aplikasi yang memiliki token aktif.
- **CON-02** User mencabut akses per token (revoke).

### 5.7 MCP Server (P1) — sudah diimplementasikan
- **MCP-01** Server MCP `SsoServer` di `/mcp/sso`.
- **MCP-02** Tool `get-user-info` — cari user berdasarkan email/ID, kembalikan id, name, email, roles, created_at.

## 6. Kebutuhan Non-Fungsional

- **Autentikasi**: Laravel Passport (OAuth2); session berbasis DB.
- **Otorisasi**: spatie/laravel-permission; area admin dijaga middleware `role:admin`.
- **Frontend**: Inertia SPA + Vue 3 + TypeScript + Tailwind 4 + Varlet UI; routing via Ziggy (`route()`).
- **Data**: MariaDB (dev), sqlite `:memory:` untuk test (phpunit.xml).
- **UI**: Bahasa Indonesia konsisten.
- **Build**: Vite → `public/build`; dev HMR via `npm run dev`.

## 7. Gap & Risiko (perlu keputusan produk)

| # | Gap | Dampak | Saran |
|---|---|---|---|
| 1 | User seed `kickymaulana@gmail.com` tidak punya NIK → **tidak bisa login** via NIK | Admin buntu saat dev | Set NIK pada seeder/DB dev |
| 2 | Client secret disimpan **plaintext** di `oauth_clients.secret` | Kebocoran DB = semua client bisa disalahgunakan | Hash secret (Passport `Hash::make`), tampilkan sekali |
| 3 | `Passport::authorizationView` di-register 2x di `AppServiceProvider`; yang terakhir (blade `mcp/authorize`) menang → Inertia `Auth/OAuth/Authorize.vue` **mati** | Halaman authorize Inertia tak pernah terpakai | Hapus registrasi pertama, atau perbaiki urutan |
| 4 | Endpoint MCP `/mcp/sso` tanpa auth (komentar di `routes/ai.php`) | Data user bocor bila endpoint publik | Pasang proteksi (token/MCP OAuth) sebelum produksi |
| 5 | Tidak ada reset password / lupa password | User lupa password harus dibantu admin | Tambah alur reset (P2) |
| 6 | Tidak ada MFA / rate-limit login | Risiko brute-force | Tambah rate-limit (P2) |

## 8. Out of Scope (saat ini)

- Login sosial / SAML / OIDC.
- MFA / 2FA.
- Manajemen session multi-perangkat & device revocation.
- Rate limiting & threat protection.
- Reset password mandiri.
- Dashboard statistik lanjutan.
- Multi-tenant / multiple SSO server.

## 9. Definisi Selesai (per fitur)

- Fitur diimplementasikan sesuai requirement.
- Validasi input & pesan error Indonesia.
- Lint/typecheck lulus: `vendor/bin/pint`, `npx vue-tsc --noEmit`.
- Test (jika ada) lulus: `composer test`.
- Tidak ada secret/credential masuk ke git.
