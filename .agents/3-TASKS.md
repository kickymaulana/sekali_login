# 3-TASKS.md — Task List dari Legacy Decoder

> Sumber: `.agents/4-LEGACY-DECODER.md` · Prioritas: P0 (kritis) / P1 (penting) / P2 (nice-to-have)

## Bug fix

- [ ] **B1 (P0)** Seeder admin tak punya NIK → tak bisa login. Set NIK di `database/seeders/UserSeeder.php` (mis. `'nik' => '1234567890'`) atau perbaiki `LoginController` agar fallback ke email. — *lihat `LoginController.php:27`, `UserSeeder.php:26`*
- [ ] **B2 (P1)** `Passport::authorizationView` double-register. Hapus registrasi pertama (Inertia `Auth/OAuth/Authorize`) di `AppServiceProvider.php:30-38`, atau balik urutan agar Inertia menang. Saat ini blade `mcp/authorize` selalu menang. — *KEPUTUSAN USER: blade `mcp/authorize` DIPERTAHANKAN (dibutuhkan flow MCP/AI). Kalau diubah, cukup hapus registrasi Inertia (dead code), jangan ganti blade.*
- [ ] **B3 (P1)** `api/user` tidak mengembalikan roles/permissions. Tambahkan agar klien SSO tahu hak akses user. — *`routes/api.php:6`*

## Keamanan

- [ ] **S1 (P0)** Client secret plaintext (`Str::random(40)` disimpan langsung). Hash dengan Passport — model setter `Client.php` otomatis hash via `castAttributeAsHashedString`; jalankan `php artisan passport:hash` untuk secret yang sudah ada (plaintext). Tapi `OAuthClientController@store/regenerateSecret` perlu tes kompatibilitas cukup + `$client->plainSecret` masih tersedia.
- [ ] **S2 (P0)** Endpoint MCP `/mcp/sso` tanpa auth. Pasang proteksi (MCP OAuth dari laravel/mcp atau middleware token) sebelum produksi. — *komentar di `routes/ai.php:8`*
- [ ] **S3 (P2)** Rate-limit login & ganti password (brute-force).

## Fungsional (gap PRD)

- [ ] **F1 (P2)** Alur reset/lupa password.
- [ ] **F2 (P2)** MFA/2FA.
- [ ] **F3 (P2)** Manajemen session multi-perangkat + revoke session.

## Kualitas & teknik

- [ ] **Q1 (P1)** Refactor route closure (`/`, `/profile`, `/security`, `/password/change`) di `routes/web.php` menjadi controller (`DashboardController`, `ProfileController`, `SecurityController`, `PasswordController`).
- [ ] **Q2 (P1)** Validasi redirect URI client: sekarang menerima array; pastikan validasi `url` + whitelist `localhost` saat dev. — *DIBATALKAN: admin butuh redirect localhost untuk test di local. Validasi tetap `required|url`.*
- [ ] **Q3 (P2)** Hapus `/up` health + middleware yang tak terpakai bila tidak perlu.
- [ ] **Q4 (P2)** Tambah unit/feature test (tidak ada test sama sekali). Mulai: login NIK, CRUD client, revoke token, guard admin.

## Verifikasi

- [ ] Pint: `vendor/bin/pint`
- [ ] Typecheck: `npx vue-tsc --noEmit`
- [ ] Test: `composer test`
- [ ] Manual: login NIK → authorize client → `/connected-apps` → revoke
