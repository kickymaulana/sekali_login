<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'

interface AppItem {
  client_id: string
  app_name: string
  icon_url: string | null
  url: string
}

interface PaginatedApps {
  data: AppItem[]
  from: number | null
  to: number | null
  total: number
  prev_page_url: string | null
  next_page_url: string | null
}

const props = defineProps<{ apps: PaginatedApps }>()
</script>

<template>
  <Head title="Aplikasi Belum Terhubung - SSO" />
  <div class="layout">
    <var-app-bar title="Aplikasi Belum Terhubung" title-position="center">
      <template #left>
        <var-button round text @click="router.get(route('profile'))">
          <var-icon name="arrow-left" :size="24" />
        </var-button>
      </template>
    </var-app-bar>

    <main class="content">
      <div class="desc">Daftar aplikasi yang belum terhubung dengan akun SSO Anda.</div>

      <div v-if="!props.apps.data.length" class="empty">
        <var-icon name="apps-box" :size="48" color="#cbd5e1" />
        <p>Semua aplikasi sudah terhubung</p>
      </div>

      <div v-for="app in props.apps.data" :key="app.client_id" class="card">
        <a :href="app.url" target="_blank" rel="noopener noreferrer" class="app-info">
          <div class="app-icon">
            <img v-if="app.icon_url" :src="app.icon_url" :alt="`Icon ${app.app_name}`" class="app-icon-image" />
            <var-icon v-else name="apps-box" :size="24" color="#4f46e5" />
          </div>
          <div class="app-detail">
            <span class="app-name">{{ app.app_name }}</span>
            <span class="app-status">Belum terhubung</span>
          </div>
        </a>
        <var-icon name="open-in-new" :size="20" color="#94a3b8" />
      </div>

      <div v-if="props.apps.total" class="pagination">
        <span>Menampilkan {{ props.apps.from }}–{{ props.apps.to }} dari {{ props.apps.total }} aplikasi</span>
        <div class="page-buttons">
          <a v-if="props.apps.prev_page_url" :href="props.apps.prev_page_url">Sebelumnya</a>
          <a v-if="props.apps.next_page_url" :href="props.apps.next_page_url">Selanjutnya</a>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.layout { display:flex;flex-direction:column;height:100vh;background:#f8fafc;font-family:Roboto,sans-serif; }
.content { flex:1;overflow-y:auto;padding:16px 20px 80px;display:flex;flex-direction:column;gap:12px; }
.desc { font-size:13px;color:#64748b; }
.empty { text-align:center;padding:40px;color:#94a3b8;font-size:14px; }
.card { display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:16px;padding:14px 16px;border:1px solid #f1f5f9;gap:12px; }
.app-info { display:flex;align-items:center;gap:12px;text-decoration:none;min-width:0; }
.app-icon { width:40px;height:40px;border-radius:12px;background:#e0e7ff;display:flex;align-items:center;justify-content:center;overflow:hidden; }
.app-icon-image { width:100%;height:100%;object-fit:cover; }
.app-detail { display:flex;flex-direction:column; }
.app-name { font-size:14px;font-weight:600;color:#0f172a; }
.app-status { font-size:11px;font-weight:600;color:#d97706; }
.pagination { display:flex;justify-content:space-between;align-items:center;gap:12px;font-size:12px;color:#64748b;margin-top:8px; }
.page-buttons { display:flex;gap:8px; }
.page-buttons a { color:#4f46e5;text-decoration:none;font-weight:600; }
</style>
