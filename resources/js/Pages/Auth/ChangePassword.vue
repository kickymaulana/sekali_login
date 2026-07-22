<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Snackbar } from '@varlet/ui'

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('password.change'), {
    onSuccess: () => {
      Snackbar.success('Password berhasil diubah!')
      form.reset()
    },
    onError: () => {
      if (form.errors.current_password) Snackbar.error(form.errors.current_password)
      else if (form.errors.password) Snackbar.error(form.errors.password)
    },
  })
}
</script>

<template>
  <Head title="Ubah Password - SSO" />

  <div class="android-layout">
    <header class="top-app-bar">
      <Link :href="route('profile')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Ubah Password</h1>
      <div class="header-placeholder"></div>
    </header>

    <main class="android-content">
      <div class="hero-card">
        <div class="hero-text">
          <h3>Keamanan Akun 🔒</h3>
          <p>Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.</p>
        </div>
        <var-icon name="lock-reset" class="hero-icon" />
      </div>

      <div class="form-container">
        <form @submit.prevent="submit">
          <var-input
            type="password"
            v-model="form.current_password"
            label="Password Saat Ini"
            placeholder="Masukkan password lama Anda"
            :error-message="form.errors.current_password"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="lock-outline" color="#6366f1" />
            </template>
          </var-input>

          <var-input
            type="password"
            v-model="form.password"
            label="Password Baru"
            placeholder="Minimal 8 karakter"
            :error-message="form.errors.password"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="lock-check-outline" color="#6366f1" />
            </template>
          </var-input>

          <var-input
            type="password"
            v-model="form.password_confirmation"
            label="Konfirmasi Password Baru"
            placeholder="Ulangi password baru"
            :error-message="form.errors.password_confirmation"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="key-outline" color="#6366f1" />
            </template>
          </var-input>

          <div class="password-rules">
            <div class="rule">
              <var-icon :name="form.password.length >= 8 ? 'check-circle' : 'circle-outline'" :size="16" :color="form.password.length >= 8 ? '#10b981' : '#cbd5e1'" />
              <span>Minimal 8 karakter</span>
            </div>
            <div class="rule">
              <var-icon :name="form.password === form.password_confirmation && form.password.length > 0 ? 'check-circle' : 'circle-outline'" :size="16" :color="form.password === form.password_confirmation && form.password.length > 0 ? '#10b981' : '#cbd5e1'" />
              <span>Konfirmasi password sesuai</span>
            </div>
          </div>

          <div class="form-actions">
            <Link :href="route('profile')" class="cancel-btn">Batal</Link>
            <var-button type="primary" native-type="submit" :loading="form.processing" class="submit-btn">
              Simpan Password
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
  min-height: 0;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 480px;
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
  max-width: 230px;
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

.password-rules {
  display: flex;
  flex-direction: column;
  gap: 6px;
  background: #f8fafc;
  border-radius: 10px;
  padding: 12px;
}

.rule {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #64748b;
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
