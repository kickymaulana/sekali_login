<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3'
import { Snackbar } from '@varlet/ui'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register'), {
    onError: () => {
      if (form.errors.name) {
        Snackbar.error(form.errors.name)
      } else if (form.errors.email) {
        Snackbar.error(form.errors.email)
      } else if (form.errors.password) {
        Snackbar.error(form.errors.password)
      } else if (form.errors.password_confirmation) {
        Snackbar.error(form.errors.password_confirmation)
      }
    },
  })
}
</script>

<template>
  <Head title="Buat Akun - SSO Server" />

  <div class="android-layout">
    <!-- Header App Bar -->
    <header class="top-app-bar">
      <Link :href="route('login')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Registrasi Akun</h1>
      <div class="header-placeholder"></div>
    </header>

    <main class="android-content">
      <!-- Welcome Hero Banner -->
      <div class="hero-card">
        <div class="hero-text">
          <h3>Satu Akun untuk Semua Aplikasi 👋</h3>
          <p>Daftarkan akun SSO kamu untuk mengakses seluruh portal sistem secara terintegrasi.</p>
        </div>
        <var-icon name="shield-check-outline" class="hero-icon" />
      </div>

      <!-- Form Section -->
      <div class="form-container">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Nama Input -->
          <var-input
            v-model="form.name"
            label="Nama Lengkap"
            placeholder="Masukkan nama lengkap Anda"
            :error-message="form.errors.name"
            :states="form.errors.name ? 'error' : undefined"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="account-outline" color="#6366f1" />
            </template>
          </var-input>

          <!-- Email Input -->
          <var-input
            v-model="form.email"
            label="Alamat Email"
            placeholder="contoh@email.com"
            :error-message="form.errors.email"
            :states="form.errors.email ? 'error' : undefined"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="email-outline" color="#6366f1" />
            </template>
          </var-input>

          <!-- Password Input -->
          <var-input
            type="password"
            v-model="form.password"
            label="Kata Sandi"
            placeholder="Minimal 8 karakter"
            :error-message="form.errors.password"
            :states="form.errors.password ? 'error' : undefined"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="lock-outline" color="#6366f1" />
            </template>
          </var-input>

          <!-- Confirm Password Input -->
          <var-input
            type="password"
            v-model="form.password_confirmation"
            label="Konfirmasi Kata Sandi"
            placeholder="Ulangi kata sandi"
            :error-message="form.errors.password_confirmation"
            :states="form.errors.password_confirmation ? 'error' : undefined"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="key-outline" color="#6366f1" />
            </template>
          </var-input>

          <!-- Submit Button -->
          <div class="pt-4">
            <var-button
              type="primary"
              block
              native-type="submit"
              :loading="form.processing"
              class="register-btn"
            >
              Daftar Sekarang
            </var-button>
          </div>
        </form>

        <!-- Footer Link -->
        <div class="auth-footer">
          <span>Sudah memiliki akun SSO?</span>
          <Link :href="route('login')" class="login-link">
            Masuk di sini
          </Link>
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

.register-btn {
  background: linear-gradient(135deg, #4f46e5, #6366f1) !important;
  font-weight: 700 !important;
  height: 48px !important;
  font-size: 15px !important;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25) !important;
}

.auth-footer {
  margin-top: 20px;
  text-align: center;
  font-size: 13px;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.login-link {
  color: #4f46e5;
  font-weight: 700;
  text-decoration: none;
}

.login-link:hover {
  text-decoration: underline;
}
</style>
