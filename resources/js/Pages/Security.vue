<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

const activeTab = ref(2)
const handleTabChange = (i: number) => {
    if (i === 0) router.get(route('dashboard'))
    else if (i === 1) router.get(route('connected-apps'))
    else if (i === 3) router.get(route('profile'))
}
</script>

<template>
    <Head title="Keamanan - SSO" />
    <div class="layout">
        <var-app-bar title="Keamanan" title-position="center">
            <template #left><var-button round text @click="router.get(route('dashboard'))"><var-icon name="arrow-left" :size="24" /></var-button></template>
        </var-app-bar>
        <main class="content">
            <div class="hero">
                <div class="hero-icon">🔐</div>
                <h1>Keamanan Akun SSO</h1>
                <p>Informasi penting untuk menjaga keamanan akun SSO Anda</p>
            </div>

            <div class="card">
                <h3>Tips Keamanan</h3>
                <div class="tip"><span class="num">1</span> Jangan bagikan password SSO ke siapapun, termasuk IT</div>
                <div class="tip"><span class="num">2</span> Gunakan password yang kuat, minimal 8 karakter</div>
                <div class="tip"><span class="num">3</span> Ganti password secara berkala</div>
                <div class="tip"><span class="num">4</span> Logout setelah selesai menggunakan aplikasi</div>
                <div class="tip"><span class="num">5</span> Cabut akses aplikasi yang tidak lagi digunakan</div>
            </div>

            <div class="card">
                <h3>Tentang SSO Server</h3>
                <p class="about-text">
                    SSO Server (sekalilogin.gotechdynamics.com) adalah pusat autentikasi terpadu PT Mark Dynamics Indonesia Tbk.
                    Dengan satu akun SSO, Anda dapat mengakses seluruh aplikasi perusahaan tanpa perlu membuat akun baru.
                </p>
                <p class="about-text">
                    Teknologi: Laravel Passport (OAuth2) — standar industri yang digunakan oleh Google, Facebook, dan GitHub.
                </p>
            </div>

            <Link :href="route('password.change')" class="change-pwd-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Ganti Password
            </Link>

            <p class="ver">SSO Server v2.0.0 — IT Department</p>
        </main>

        <var-bottom-navigation v-model:active="activeTab" fixed placeholder @change="handleTabChange">
            <var-bottom-navigation-item label="Beranda" icon="home-outline" />
            <var-bottom-navigation-item label="Aplikasi" icon="cellphone" />
            <var-bottom-navigation-item label="Keamanan" icon="lock" />
            <var-bottom-navigation-item label="Profil" icon="account-circle-outline" />
        </var-bottom-navigation>
    </div>
</template>

<style scoped>
.layout { display:flex;flex-direction:column;min-height:100vh;background:#f8fafc;font-family:Roboto,sans-serif; }
.content { flex:1;padding:20px 24px 24px;display:flex;flex-direction:column;gap:16px;max-width:600px;margin:0 auto;width:100%;box-sizing:border-box; }
.hero { text-align:center;padding:16px 0; }
.hero-icon { font-size:48px;margin-bottom:8px; }
.hero h1 { margin:0 0 4px;font-size:22px;font-weight:800;color:#0f172a; }
.hero p { margin:0;font-size:13px;color:#64748b; }
.card { background:#fff;border-radius:16px;padding:20px;border:1px solid #f1f5f9;box-shadow:0 2px 6px rgba(0,0,0,0.02); }
.card h3 { margin:0 0 14px;font-size:15px;font-weight:700;color:#0f172a; }
.tip { display:flex;align-items:flex-start;gap:10px;padding:8px 0;border-bottom:1px solid #f8fafc;font-size:13px;color:#475569; }
.tip:last-child { border-bottom:none; }
.num { width:24px;height:24px;border-radius:50%;background:#4f46e5;color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.about-text { font-size:13px;color:#475569;line-height:1.6;margin:0 0 8px; }
.change-pwd-btn { display:flex;align-items:center;justify-content:center;gap:10px;padding:16px;background:#fff;border:2px solid #e2e8f0;border-radius:16px;color:#4f46e5;font-size:15px;font-weight:700;text-decoration:none;transition:all 0.2s; }
.change-pwd-btn:hover { border-color:#4f46e5;background:#eef2ff; }
.ver { text-align:center;font-size:11px;color:#94a3b8;margin:0; }
</style>
