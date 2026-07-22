<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface OAuthClient {
  id: string
  name: string
  secret: string
  redirect_uris: string[] | string
  redirect?: string
  revoked: boolean
}

const props = defineProps<{
  clients: OAuthClient[]
}>()

const getRedirectUrl = (client: OAuthClient): string => {
  if (Array.isArray(client.redirect_uris)) {
    return client.redirect_uris[0] || ''
  }
  return client.redirect_uris || client.redirect || ''
}

const confirmDelete = (client: OAuthClient) => {
  Dialog({
    title: 'Hapus OAuth Client?',
    message: `Aplikasi "${client.name}" yang dihapus tidak akan bisa menggunakan SSO lagi.`,
    onConfirm: () => {
      router.delete(route('admin.clients.destroy', client.id), {
        onSuccess: () => Snackbar.success('Client berhasil dihapus'),
      })
    },
  })
}
</script>

<template>
  <Head title="Kelola OAuth Clients - SSO Admin" />

  <div class="android-layout">
    <header class="top-app-bar">
      <Link :href="route('dashboard')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">OAuth Clients</h1>
      <Link :href="route('admin.clients.create')" class="add-button">
        <var-icon name="plus" :size="22" color="#4f46e5" />
      </Link>
    </header>

    <main class="android-content">
      <div class="hero-card">
        <div class="hero-text">
          <h3>Aplikasi Terintegrasi 🔗</h3>
          <p>Kelola aplikasi eksternal yang terhubung ke SSO Server.</p>
        </div>
        <var-icon name="apps-box" class="hero-icon" />
      </div>

      <div class="action-bar">
        <Link :href="route('admin.clients.create')" class="action-btn">
          <var-icon name="plus" :size="18" />
          Tambah App
        </Link>
      </div>

      <div v-if="!clients.length" class="empty-card">
        <var-icon name="apps-box" :size="48" color="#cbd5e1" />
        <p>Belum ada aplikasi klien terdaftar.</p>
        <Link :href="route('admin.clients.create')" class="empty-link">Tambah App Baru</Link>
      </div>

      <div v-else class="clients-grid">
        <div v-for="client in clients" :key="client.id" class="client-card">
          <div class="card-header">
            <div class="client-icon-box">
              <var-icon name="cellphone" :size="22" color="#4f46e5" />
            </div>
            <var-chip size="small" :type="client.revoked ? 'danger' : 'success'" round>
              {{ client.revoked ? 'Nonaktif' : 'Aktif' }}
            </var-chip>
          </div>
          <h3 class="client-name">{{ client.name }}</h3>
          <div class="card-body">
            <div class="info-row">
              <span class="label">Client ID</span>
              <code class="code-val">{{ client.id }}</code>
            </div>
            <div class="info-row">
              <span class="label">Redirect URI</span>
              <span class="text-val">{{ getRedirectUrl(client) }}</span>
            </div>
          </div>
          <div class="card-footer">
            <Link :href="route('admin.clients.edit', client.id)" class="action-link edit-link">Edit</Link>
            <var-button size="small" type="danger" text @click="confirmDelete(client)">Hapus</var-button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.android-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #f8fafc;
  font-family: Roboto, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: #1e293b;
}

.top-app-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background-color: #ffffff;
  border-bottom: 1px solid #f1f5f9;
  position: sticky;
  top: 0;
  z-index: 10;
}

.back-button, .add-button {
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.app-bar-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.android-content {
  flex: 1;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 900px;
  margin: 0 auto;
  width: 100%;
}

.hero-card {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  border-radius: 20px;
  padding: 20px;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.35);
}

.hero-text h3 {
  margin: 0 0 6px 0;
  font-size: 16px;
  font-weight: 700;
}

.hero-text p {
  margin: 0;
  font-size: 12px;
  opacity: 0.9;
  line-height: 1.4;
  max-width: 300px;
}

.hero-icon {
  font-size: 46px !important;
  opacity: 0.25;
}

.action-bar {
  display: flex;
  justify-content: flex-end;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #4f46e5;
  color: #ffffff;
  padding: 8px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: background-color 0.2s;
}

.action-btn:hover {
  background: #4338ca;
}

.empty-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 48px 32px;
  text-align: center;
  color: #94a3b8;
  border: 1px dashed #cbd5e1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  font-size: 14px;
}

.empty-link {
  color: #4f46e5;
  font-weight: 600;
  text-decoration: none;
}

.clients-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 16px;
}

.client-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.client-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.client-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: #e0e7ff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.client-name {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
}

.card-body {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.info-row {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 12px;
}

.info-row .label {
  color: #94a3b8;
  font-weight: 500;
  text-transform: uppercase;
  font-size: 10px;
  letter-spacing: 0.5px;
}

.code-val {
  background: #f1f5f9;
  padding: 4px 8px;
  border-radius: 6px;
  font-family: monospace;
  color: #4f46e5;
  word-break: break-all;
  font-size: 12px;
}

.text-val {
  color: #334155;
  word-break: break-all;
  font-size: 12px;
}

.card-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  border-top: 1px solid #f8fafc;
  padding-top: 12px;
}

.action-link {
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  padding: 4px 8px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
}

.edit-link {
  color: #d97706;
}

.edit-link:hover {
  background-color: #fef3c7;
}
</style>
