<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useForm, Head, Link, usePage } from '@inertiajs/vue3'
import { Snackbar } from '@varlet/ui'
import lottie from 'lottie-web'

const form = useForm({
  nik: '',
  password: '',
  remember: false,
})

const showPw = ref(false)
const lottieContainer = ref<HTMLElement | null>(null)
let lottieInstance: any = null

onMounted(() => {
  if (lottieContainer.value) {
    const baseUrl = (usePage().props.app_url as string) || window.location.origin
    lottieInstance = lottie.loadAnimation({
      container: lottieContainer.value,
      renderer: 'svg', loop: true, autoplay: true,
      path: `${baseUrl.replace(/\/$/, '')}/assets/lottie/${encodeURIComponent('Loading Lottie animation.json')}`,
    })
  }
})

onBeforeUnmount(() => { if (lottieInstance) lottieInstance.destroy() })

const submit = () => {
  form.post(route('login'), {
    onError: () => {
      if (form.errors.nik) Snackbar.error(form.errors.nik)
      else if (form.errors.password) Snackbar.error(form.errors.password)
    },
  })
}
</script>

<template>
  <Head title="Login - SSO Server" />

  <div class="screen">
    <div class="grain"></div>
    <div class="circle c1"></div>
    <div class="circle c2"></div>

    <div class="card">
      <div class="logo-section">
        <div ref="lottieContainer" class="lottie-box"></div>
        <h1>Masuk SSO</h1>
        <p>Gunakan akun SSO Anda untuk mengakses semua aplikasi</p>
      </div>

      <form @submit.prevent="submit" class="form">
        <div class="field">
          <label>NIK (Nomor Induk Karyawan)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input v-model="form.nik" placeholder="Masukkan NIK Anda" class="input" />
          </div>
          <span v-if="form.errors.nik" class="error">{{ form.errors.nik }}</span>
        </div>

        <div class="field">
          <label>Kata Sandi</label>
          <div class="input-wrap">
            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input :type="showPw ? 'text' : 'password'" v-model="form.password" placeholder="Masukkan kata sandi" class="input" />
            <button type="button" class="eye-btn" @click="showPw = !showPw">
              <svg v-if="!showPw" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
          <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
        </div>

        <label class="checkbox-row">
          <input type="checkbox" v-model="form.remember" />
          <span>Ingat Saya</span>
        </label>

        <button type="submit" class="btn-primary" :disabled="form.processing">
          {{ form.processing ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>

      <p class="footer-text">Belum punya akun? <Link :href="route('register')" class="link">Daftar Sekarang</Link></p>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.screen {
  min-height: 100vh; display: flex; align-items: center; justify-content: center;
  background: #f8fafc; font-family: 'Inter', sans-serif; position: relative; overflow: hidden; padding: 20px;
}
.grain { position: fixed; inset: 0; opacity: 0.03; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E"); pointer-events: none; }
.circle { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; }
.c1 { width: 350px; height: 350px; background: #4f46e5; top: -100px; right: -80px; opacity: 0.12; }
.c2 { width: 250px; height: 250px; background: #7c3aed; bottom: -60px; left: -60px; opacity: 0.08; }

.card {
  position: relative; z-index: 1; width: 100%; max-width: 420px;
  background: #ffffff; border-radius: 28px; padding: 40px 32px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;
}
.logo-section { text-align: center; margin-bottom: 24px; }
.lottie-box { width: 140px; height: 140px; margin: -16px auto -8px; }
.logo-section h1 { margin: 0 0 6px; font-size: 24px; font-weight: 800; color: #0f172a; }
.logo-section p { margin: 0; font-size: 13px; color: #64748b; line-height: 1.5; }
.form { display: flex; flex-direction: column; gap: 20px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
.input-wrap {
  display: flex; align-items: center; gap: 10px;
  background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 14px; padding: 0 14px;
  transition: border-color 0.2s;
}
.input-wrap:focus-within { border-color: #4f46e5; background: #fff; }
.input-icon { flex-shrink: 0; }
.input {
  flex: 1; border: none; background: transparent; padding: 14px 0;
  font-size: 14px; font-family: inherit; color: #0f172a; outline: none;
}
.input::placeholder { color: #94a3b8; }
.eye-btn { background: none; border: none; cursor: pointer; padding: 4px; display: flex; }
.error { font-size: 12px; color: #ef4444; }
.checkbox-row { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; cursor: pointer; }
.checkbox-row input { width: 16px; height: 16px; accent-color: #4f46e5; }
.btn-primary {
  width: 100%; padding: 16px; border: none; border-radius: 14px;
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  color: #fff; font-size: 15px; font-weight: 700; cursor: pointer;
  font-family: inherit; transition: all 0.2s;
  box-shadow: 0 8px 25px rgba(79,70,229,0.25);
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 30px rgba(79,70,229,0.35); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.footer-text { text-align: center; font-size: 13px; color: #64748b; margin: 24px 0 0; }
.link { color: #4f46e5; font-weight: 700; text-decoration: none; }
.link:hover { text-decoration: underline; }
</style>
