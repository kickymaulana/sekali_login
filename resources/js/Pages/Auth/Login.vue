<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3'
import { Snackbar } from '@varlet/ui'

const form = useForm({
  nik: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onError: () => {
      if (form.errors.nik) {
        Snackbar.error(form.errors.nik)
      } else if (form.errors.password) {
        Snackbar.error(form.errors.password)
      }
    },
  })
}
</script>

<template>
  <Head title="Login - SSO Server" />

  <div class="android-layout">
    <!-- Header App Bar -->
    <header class="top-app-bar">
      <div class="header-placeholder"></div>
      <h1 class="app-bar-title">Masuk SSO</h1>
      <div class="header-placeholder"></div>
    </header>

    <main class="android-content">
      <!-- Welcome Hero Banner -->
      <div class="hero-card">
        <div class="hero-text">
          <h3>Selamat Datang Kembali 👋</h3>
          <p>Masuk dengan akun SSO Anda untuk mengakses semua layanan terintegrasi.</p>
        </div>
        <var-icon name="login" class="hero-icon" />
      </div>

      <!-- Form Section -->
      <div class="form-container">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- NIK Input -->
          <var-input
            v-model="form.nik"
            label="NIK (Nomor Induk)"
            placeholder="Masukkan NIK Anda"
            :error-message="form.errors.nik"
            :states="form.errors.nik ? 'error' : undefined"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="card-account-details" color="#6366f1" />
            </template>
          </var-input>

          <!-- Password Input -->
          <var-input
            type="password"
            v-model="form.password"
            label="Kata Sandi"
            placeholder="Masukkan kata sandi"
            :error-message="form.errors.password"
            :states="form.errors.password ? 'error' : undefined"
            clearable
          >
            <template #prepend-icon>
              <var-icon name="lock-outline" color="#6366f1" />
            </template>
          </var-input>

          <!-- Remember Me -->
          <div class="remember-row">
            <var-checkbox v-model="form.remember">
              Ingat Saya
            </var-checkbox>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <var-button
              type="primary"
              block
              native-type="submit"
              :loading="form.processing"
              class="login-btn"
            >
              Masuk
            </var-button>
          </div>
        </form>

        <!-- Footer Link -->
        <div class="auth-footer">
          <span>Belum punya akun?</span>
          <Link :href="route('register')" class="register-link">
            Daftar Sekarang
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

.remember-row {
  display: flex;
  align-items: center;
  padding-top: 4px;
}

.login-btn {
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

.register-link {
  color: #4f46e5;
  font-weight: 700;
  text-decoration: none;
}

.register-link:hover {
  text-decoration: underline;
}
</style>
