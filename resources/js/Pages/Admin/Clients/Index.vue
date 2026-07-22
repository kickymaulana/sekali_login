<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
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

// Helper untuk mengambil URL Redirect pertama sebagai String
const getRedirectUrl = (client: OAuthClient): string => {
  if (Array.isArray(client.redirect_uris)) {
    return client.redirect_uris[0] || ''
  }
  return client.redirect_uris || client.redirect || ''
}

// Dialog State
const showCreateModal = ref(false)
const editingClient = ref<OAuthClient | null>(null)

// Forms
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
  form.redirect = getRedirectUrl(client) // 🟢 Pastikan bernilai string tunggal
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

  <div class="admin-container">
    <!-- Header -->
    <div class="header-section">
      <div>
        <h1 class="page-title">OAuth Clients</h1>
        <p class="page-subtitle">Kelola aplikasi eksternal yang terhubung ke SSO Server</p>
      </div>
      <var-button type="primary" round @click="openCreateModal">
        <template #icon><var-icon name="plus" /></template>
        Tambah App
      </var-button>
    </div>

    <!-- Empty State -->
    <div v-if="!clients.length" class="empty-card">
      <var-icon name="apps-box" :size="48" color="#cbd5e1" />
      <p>Belum ada aplikasi klien terdaftar.</p>
    </div>

    <!-- Client Cards Grid -->
    <div v-else class="clients-grid">
      <div v-for="client in clients" :key="client.id" class="client-card">
        <div class="card-header">
          <div class="client-info">
            <h3>{{ client.name }}</h3>
            <var-chip size="small" :type="client.revoked ? 'danger' : 'success'">
              {{ client.revoked ? 'Nonaktif' : 'Aktif' }}
            </var-chip>
          </div>
        </div>

        <div class="card-body">
          <div class="info-row">
            <span class="label">Client ID:</span>
            <code class="code-val">{{ client.id }}</code>
          </div>
          <div class="info-row">
            <span class="label">Redirect URI:</span>
            <span class="text-val">{{ getRedirectUrl(client) }}</span>
          </div>
        </div>

        <div class="card-footer">
          <var-button size="small" type="warning" text @click="openEditModal(client)">
            Edit
          </var-button>
          <var-button size="small" type="danger" text @click="confirmDelete(client.id)">
            Hapus
          </var-button>
        </div>
      </div>
    </div>

    <!-- Modal Form (Tambah / Edit) -->
    <var-dialog v-model:show="showCreateModal" :title="editingClient ? 'Edit Client' : 'Daftarkan App Baru'">
      <form @submit.prevent="submitForm" id="clientForm" class="dialog-form">
        <var-input
          v-model="form.name"
          label="Nama Aplikasi"
          placeholder="Contoh: Portal Absensi"
          :error-message="form.errors.name"
        />
        <var-input
          v-model="form.redirect"
          label="Callback / Redirect URL"
          placeholder="https://app.com/auth/callback"
          :error-message="form.errors.redirect"
        />
      </form>
      <template #actions>
        <var-button text @click="showCreateModal = false">Batal</var-button>
        <var-button type="primary" :loading="form.processing" @click="submitForm">Simpan</var-button>
      </template>
    </var-dialog>
  </div>
</template>

<style scoped>
.admin-container { padding: 24px; max-width: 1200px; margin: 0 auto; }
.header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
.page-subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }
.clients-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; }
.client-card { background: #fff; border-radius: 16px; border: 1px solid #f1f5f9; padding: 16px; display: flex; flex-direction: column; gap: 12px; }
.card-header { display: flex; justify-content: space-between; align-items: flex-start; }
.client-info h3 { margin: 0 0 6px 0; font-size: 16px; font-weight: 700; color: #1e293b; }
.info-row { display: flex; flex-direction: column; gap: 4px; font-size: 12px; }
.info-row .label { color: #94a3b8; font-weight: 500; }
.code-val { background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-family: monospace; color: #4f46e5; word-break: break-all; }
.text-val { color: #334155; word-break: break-all; }
.card-footer { display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #f8fafc; padding-top: 8px; }
.dialog-form { display: flex; flex-direction: column; gap: 16px; padding: 12px 0; }
.empty-card { background: #fff; border-radius: 16px; padding: 48px; text-align: center; color: #94a3b8; border: 1px dashed #cbd5e1; }
</style>
