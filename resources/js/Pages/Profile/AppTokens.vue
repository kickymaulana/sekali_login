<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'

interface TokenItem {
  token_id: string
  created_at: string
  expires_at: string
}

interface AppItem {
  id: string
  name: string
}

const props = defineProps<{ app: AppItem; tokens: TokenItem[] }>()
</script>

<template>
  <Head :title="`${props.app.name} - Token Aktif`" />
  <div class="layout">
    <var-app-bar :title="props.app.name" title-position="center">
      <template #left><var-button round text @click="router.get(route('aplikasi-terhubung'))"><var-icon name="arrow-left" :size="24" /></var-button></template>
    </var-app-bar>
    <main class="content">
      <div class="desc">Token aktif untuk aplikasi ini saja.</div>

      <div class="actions" v-if="props.tokens.length">
        <var-button block type="danger" @click="router.post(route('aplikasi-terhubung.revoke-all', props.app.id))">Cabut Semua Token</var-button>
      </div>

      <div v-if="!props.tokens.length" class="empty">
        <var-icon name="lock" :size="48" color="#cbd5e1" />
        <p>Tidak ada token aktif</p>
      </div>

      <div v-for="token in props.tokens" :key="token.token_id" class="card">
        <div class="app-info">
          <div class="app-icon">
            <var-icon name="lock" :size="24" color="#4f46e5" />
          </div>
          <div class="app-detail">
            <span class="app-name">Token {{ token.token_id.slice(0, 8) }}</span>
            <span class="app-date">Dibuat {{ token.created_at }} · Exp {{ token.expires_at }}</span>
          </div>
        </div>
        <var-button size="small" type="danger" text @click="router.post(route('token-aktif.revoke', token.token_id))">Cabut</var-button>
      </div>
    </main>
  </div>
</template>

<style scoped>
.layout { display:flex;flex-direction:column;height:100vh;background:#f8fafc;font-family:Roboto,sans-serif; }
.content { flex:1;overflow-y:auto;padding:16px 20px 80px;display:flex;flex-direction:column;gap:12px; }
.actions { display:flex; }
.desc { font-size:13px;color:#64748b; }
.empty { text-align:center;padding:40px;color:#94a3b8;font-size:14px; }
.card { display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:16px;padding:14px 16px;border:1px solid #f1f5f9; }
.app-info { display:flex;align-items:center;gap:12px; }
.app-icon { width:40px;height:40px;border-radius:12px;background:#e0e7ff;display:flex;align-items:center;justify-content:center; }
.app-detail { display:flex;flex-direction:column; }
.app-name { font-size:14px;font-weight:600;color:#0f172a; }
.app-date { font-size:11px;color:#94a3b8; }
</style>
