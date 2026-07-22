<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
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
  client: OAuthClient
}>()

const redirectUrl = Array.isArray(props.client.redirect_uris)
  ? props.client.redirect_uris[0] || ''
  : props.client.redirect_uris || props.client.redirect || ''

const form = useForm({
  name: props.client.name,
  redirect: redirectUrl,
})

const submit = () => {
  form.put(route('admin.clients.update', props.client.id), {
    onSuccess: () => Snackbar.success('Aplikasi berhasil diperbarui'),
    onError: () => {
      if (form.errors.name) Snackbar.error(form.errors.name)
      else if (form.errors.redirect) Snackbar.error(form.errors.redirect)
    },
  })
}

const confirmDelete = () => {
  Dialog({
    title: 'Hapus OAuth Client?',
    message: `Aplikasi "${props.client.name}" yang dihapus tidak akan bisa menggunakan SSO lagi.`,
    onConfirm: () => {
      router.delete(route('admin.clients.destroy', props.client.id), {
        onSuccess: () => {
          Snackbar.success('Client berhasil dihapus')
          router.get(route('admin.clients.index'))
        },
      })
    },
  })
}
</script>

<template>
  <Head :title="'Edit Client - ' + client.name" />

  <div class="android-layout">
    <header class="top-app-bar">
      <Link :href="route('admin.clients.index')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Edit Client</h1>
      <var-button text round @click="confirmDelete">
        <var-icon name="delete-outline" :size="22" color="#ef4444" />
      </var-button>
    </header>

    <main class="android-content">
      <div class="hero-card">
        <div class="hero-text">
          <h3>Edit Aplikasi ✏️</h3>
          <p>Perbarui pengaturan untuk <strong>{{ client.name }}</strong>.</p>
        </div>
        <var-icon name="cellphone" class="hero-icon" />
      </div>

      <div class="form-container">
        <form @submit.prevent="submit">
          <var-input v-model="form.name" label="Nama Aplikasi" placeholder="Contoh: Portal Absensi" :error-message="form.errors.name">
            <template #prepend-icon><var-icon name="apps-box" color="#6366f1" /></template>
          </var-input>

          <var-input v-model="form.redirect" label="Callback / Redirect URL" placeholder="https://app.com/auth/callback" :error-message="form.errors.redirect">
            <template #prepend-icon><var-icon name="link-variant" color="#6366f1" /></template>
          </var-input>

          <div class="info-box">
            <var-icon name="information-outline" :size="18" color="#6366f1" />
            <span>Client ID: <code class="client-id">{{ client.id }}</code></span>
          </div>

          <div class="form-actions">
            <Link :href="route('admin.clients.index')" class="cancel-btn">Batal</Link>
            <var-button type="primary" native-type="submit" :loading="form.processing" class="submit-btn">
              Simpan Perubahan
            </var-button>
          </div>
        </form>
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
  max-width: 520px;
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
  max-width: 250px;
}

.hero-text p strong {
  color: #ffffff;
  font-weight: 700;
}

.hero-icon {
  font-size: 46px !important;
  opacity: 0.25;
}

.form-container {
  background: #ffffff;
  border-radius: 20px;
  padding: 24px 20px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.form-container form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #e0e7ff;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 12px;
  color: #4f46e5;
  font-weight: 500;
}

.client-id {
  background: #c7d2fe;
  padding: 2px 8px;
  border-radius: 4px;
  font-family: monospace;
  font-size: 11px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 8px;
}

.cancel-btn {
  display: inline-flex;
  align-items: center;
  padding: 0 20px;
  height: 40px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  text-decoration: none;
  border: 1px solid #e2e8f0;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background-color: #f1f5f9;
}

.submit-btn {
  font-weight: 700 !important;
  height: 40px !important;
}
</style>
