<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface AppItem { token_id: string; app_name: string; created_at: string; expires_at: string | null }

const props = defineProps<{ apps: AppItem[] }>()
const page = usePage()
const pp = page.props as any
const baseUrl = pp.app_url || ''
const csrf = pp.csrf_token || ''

const confirmRevoke = (tokenId: string, appName: string) => {
    Dialog({
        title: 'Cabut Akses?',
        message: `Aplikasi "${appName}" tidak akan bisa mengakses akun Anda lagi.`,
        onConfirm: async () => {
            const res = await fetch(`${baseUrl}/connected-apps/${tokenId}/revoke`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } })
            if (res.ok || res.redirected) { Snackbar.success('Akses dicabut'); window.location.reload() }
        },
    })
}
</script>

<template>
    <Head title="Aplikasi Terhubung - SSO" />
    <div class="layout">
        <var-app-bar title="Aplikasi Terhubung" title-position="center">
            <template #left><var-button round text @click="router.get(route('profile'))"><var-icon name="arrow-left" :size="24" /></var-button></template>
        </var-app-bar>
        <main class="content">
            <div class="desc">Aplikasi yang memiliki akses ke akun SSO Anda.</div>

            <div v-if="!apps.length" class="empty">
                <var-icon name="cellphone" :size="48" color="#cbd5e1" />
                <p>Belum ada aplikasi terhubung</p>
            </div>

            <div v-for="app in apps" :key="app.token_id" class="card">
                <div class="app-info">
                    <div class="app-icon">
                        <var-icon name="cellphone" :size="24" color="#4f46e5" />
                    </div>
                    <div class="app-detail">
                        <span class="app-name">{{ app.app_name }}</span>
                        <span class="app-date">Terhubung {{ app.created_at }}</span>
                    </div>
                </div>
                <var-button type="danger" size="small" text @click="confirmRevoke(app.token_id, app.app_name)">Cabut</var-button>
            </div>
        </main>
    </div>
</template>

<style scoped>
.layout { display:flex;flex-direction:column;height:100vh;background:#f8fafc;font-family:Roboto,sans-serif; }
.content { flex:1;overflow-y:auto;padding:16px 20px 80px;display:flex;flex-direction:column;gap:12px; }
.desc { font-size:13px;color:#64748b; }
.empty { text-align:center;padding:40px;color:#94a3b8;font-size:14px; }
.card { display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:16px;padding:14px 16px;border:1px solid #f1f5f9; }
.app-info { display:flex;align-items:center;gap:12px; }
.app-icon { width:40px;height:40px;border-radius:12px;background:#e0e7ff;display:flex;align-items:center;justify-content:center; }
.app-detail { display:flex;flex-direction:column; }
.app-name { font-size:14px;font-weight:600;color:#0f172a; }
.app-date { font-size:11px;color:#94a3b8; }
</style>
