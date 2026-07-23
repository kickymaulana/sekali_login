<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { Snackbar } from '@varlet/ui'
import { computed, ref } from 'vue'

interface NewClient {
  id: string
  name: string
  secret: string
}

const page = usePage()
const newClient = computed<NewClient | null>(() => (page.props as any)?.new_client ?? null)
const showCopied = ref(false)

const form = useForm({
  name: '',
  redirect: '',
})

const submit = () => {
  showCopied.value = false
  form.post(route('admin.clients.store'), {
    onError: () => {
      if (form.errors.name) Snackbar.error(form.errors.name)
      else if (form.errors.redirect) Snackbar.error(form.errors.redirect)
    },
  })
}

const copyToClipboard = async (text: string) => {
  try {
    await navigator.clipboard.writeText(text)
    showCopied.value = true
    Snackbar.success('Disalin ke clipboard!')
    setTimeout(() => { showCopied.value = false }, 2000)
  } catch {
    Snackbar.error('Gagal menyalin')
  }
}
</script>

<template>
  <Head title="Tambah Client - SSO Admin" />

  <div class="android-layout">
    <header class="top-app-bar">
      <Link :href="route('admin.clients.index')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Tambah App</h1>
      <div class="header-placeholder"></div>
    </header>

    <main class="android-content">
      <div class="hero-card">
        <div class="hero-text">
          <h3>Daftarkan Aplikasi Baru 🔗</h3>
          <p>Buat client ID dan secret untuk aplikasi eksternal yang ingin terhubung ke SSO.</p>
        </div>
        <var-icon name="apps-box" class="hero-icon" />
      </div>

      <!-- Success Card: Tampilkan setelah create -->
      <div v-if="newClient" class="success-card">
        <div class="success-header">
          <var-icon name="check-circle" :size="32" color="#22c55e" />
          <h3>Aplikasi Berhasil Didaftarkan!</h3>
        </div>
        <p class="success-desc">Simpan <strong>Client Secret</strong> berikut — tidak akan ditampilkan lagi.</p>

        <div class="secret-box">
          <div class="secret-field">
            <span class="secret-label">Client ID</span>
            <div class="secret-value-row">
              <code class="secret-value">{{ newClient.id }}</code>
              <var-button size="small" round text @click="copyToClipboard(newClient.id)">
                <var-icon name="content-copy" :size="16" color="#6366f1" />
              </var-button>
            </div>
          </div>
          <div class="secret-field">
            <span class="secret-label">Client Secret</span>
            <div class="secret-value-row">
              <code class="secret-value">{{ newClient.secret }}</code>
              <var-button size="small" round text @click="copyToClipboard(newClient.secret)">
                <var-icon name="content-copy" :size="16" color="#6366f1" />
              </var-button>
            </div>
          </div>
        </div>

        <div class="warning-box">
          <var-icon name="alert" :size="18" color="#f59e0b" />
          <span>Jangan bagikan Client Secret ini kepada siapapun. Simpan di tempat aman.</span>
        </div>

        <Link :href="route('admin.clients.index')" class="to-list-btn">
          Ke Daftar Aplikasi
        </Link>
      </div>

      <!-- Form -->
      <div v-else class="form-container">
        <form @submit.prevent="submit">
          <var-input v-model="form.name" label="Nama Aplikasi" placeholder="Contoh: Portal Absensi" :error-message="form.errors.name">
            <template #prepend-icon><var-icon name="apps-box" color="#6366f1" /></template>
          </var-input>

          <var-input v-model="form.redirect" label="Callback / Redirect URL" placeholder="https://app.com/auth/callback" :error-message="form.errors.redirect">
            <template #prepend-icon><var-icon name="link-variant" color="#6366f1" /></template>
          </var-input>

          <div class="form-actions">
            <Link :href="route('admin.clients.index')" class="cancel-btn">Batal</Link>
            <var-button type="primary" native-type="submit" :loading="form.processing" class="submit-btn">
              Daftarkan
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

.header-placeholder {
  width: 24px;
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

/* Success Card */
.success-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 24px 20px;
  border: 2px solid #bbf7d0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.success-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.success-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #166534;
}

.success-desc {
  margin: 0;
  font-size: 13px;
  color: #64748b;
  line-height: 1.4;
}

.secret-box {
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.secret-field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.secret-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #94a3b8;
}

.secret-value-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.secret-value {
  flex: 1;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  padding: 8px 12px;
  border-radius: 8px;
  font-family: monospace;
  font-size: 12px;
  color: #4f46e5;
  word-break: break-all;
  line-height: 1.5;
}

.warning-box {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: #fef3c7;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 12px;
  color: #92400e;
  line-height: 1.4;
}

.to-list-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #4f46e5;
  color: #ffffff;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: background-color 0.2s;
}

.to-list-btn:hover {
  background: #4338ca;
}
</style>
