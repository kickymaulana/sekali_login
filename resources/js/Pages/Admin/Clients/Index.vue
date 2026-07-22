<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
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

const showCreateModal = ref(false)
const editingClient = ref<OAuthClient | null>(null)

const form = useForm({
  name: '',
  redirect: '',
})

const openCreateModal = () => {
  editingClient.value = null
  form.reset()
  form.clearErrors()
  showCreateModal.value = true
}

const openEditModal = (client: OAuthClient) => {
  editingClient.value = client
  form.clearErrors()
  form.name = client.name
  form.redirect = getRedirectUrl(client)
  showCreateModal.value = true
}

const submitForm = () => {
  if (editingClient.value) {
    form.put(route('admin.clients.update', editingClient.value.id), {
      onSuccess: () => {
        showCreateModal.value = false
        Snackbar.success('Aplikasi berhasil diperbarui')
      },
      onError: (errors) => {
        console.error('Error Validasi Update:', errors)
      }
    })
  } else {
    form.post(route('admin.clients.store'), {
      onSuccess: () => {
        showCreateModal.value = false
        Snackbar.success('Aplikasi klien berhasil didaftarkan')
      },
      onError: (errors) => {
        console.error('Error Validasi Store:', errors)
      }
    })
  }
}

const confirmDelete = (clientId: string) => {
  Dialog({
    title: 'Hapus OAuth Client?',
    message: 'Aplikasi yang dihapus tidak akan bisa menggunakan SSO lagi.',
    onConfirm: () => {
      router.delete(route('admin.clients.destroy', clientId), {
        onSuccess: () => Snackbar.success('Client berhasil dihapus'),
      })
    },
  })
}
</script>

<template>
  <Head title="Kelola OAuth Clients - SSO Admin" />

  <div class="android-layout">
    <!-- Header App Bar -->
    <header class="top-app-bar">
      <Link href="/home" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">OAuth Clients</h1>
      <var-button text round @click="openCreateModal">
        <var-icon name="plus" :size="22" color="#4f46e5" />
      </var-button>
    </header>

    <main class="android-content">
      <!-- Hero -->
      <div class="hero-card">
        <div class="hero-text">
          <h3>Aplikasi Terintegrasi 🔗</h3>
          <p>Kelola aplikasi eksternal yang terhubung ke SSO Server.</p>
        </div>
        <var-icon name="apps-box" class="hero-icon" />
      </div>

      <!-- Empty State -->
      <div v-if="!clients.length" class="empty-card">
        <var-icon name="apps-box" :size="48" color="#cbd5e1" />
        <p>Belum ada aplikasi klien terdaftar.</p>
        <var-button type="primary" round @click="openCreateModal">Tambah App Baru</var-button>
      </div>

      <!-- Client Cards Grid -->
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
            <var-button size="small" type="warning" text @click="openEditModal(client)">Edit</var-button>
            <var-button size="small" type="danger" text @click="confirmDelete(client.id)">Hapus</var-button>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Form -->
    <var-dialog v-model:show="showCreateModal" :title="editingClient ? 'Edit Client' : 'Daftarkan App Baru'">
      <form @submit.prevent="submitForm" id="clientForm" class="dialog-form">
        <var-input v-model="form.name" label="Nama Aplikasi" placeholder="Contoh: Portal Absensi" :error-message="form.errors.name">
          <template #prepend-icon><var-icon name="apps-box" color="#6366f1" /></template>
        </var-input>
        <var-input v-model="form.redirect" label="Callback / Redirect URL" placeholder="https://app.com/auth/callback" :error-message="form.errors.redirect">
          <template #prepend-icon><var-icon name="link-variant" color="#6366f1" /></template>
        </var-input>
      </form>
      <template #actions>
        <var-button text @click="showCreateModal = false">Batal</var-button>
        <var-button type="primary" :loading="form.processing" @click="submitForm">Simpan</var-button>
      </template>
    </var-dialog>
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

.back-button {
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

.dialog-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 12px 0;
}
</style>
