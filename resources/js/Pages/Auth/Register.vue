<script setup lang="ts">
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import { Snackbar } from '@varlet/ui'

const form = useForm({
  nik: '',
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const showPw = ref(false)
const showPw2 = ref(false)

const submit = () => {
  form.post(route('register'), {
    onError: () => {
      if (form.errors.nik) Snackbar.error(form.errors.nik)
      else if (form.errors.name) Snackbar.error(form.errors.name)
      else if (form.errors.email) Snackbar.error(form.errors.email)
      else if (form.errors.password) Snackbar.error(form.errors.password)
      else if (form.errors.password_confirmation) Snackbar.error(form.errors.password_confirmation)
    },
  })
}
</script>

<template>
  <Head title="Buat Akun - SSO Server" />

  <div class="screen">
    <div class="grain"></div>
    <div class="circle c1"></div>
    <div class="circle c2"></div>

    <div class="card">
      <div class="logo-section">
        <div class="icon-circle">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>
          </svg>
        </div>
        <h1>Buat Akun SSO</h1>
        <p>Daftar untuk mengakses aplikasi terintegrasi</p>
      </div>

      <form @submit.prevent="submit" class="form">
        <div class="field">
          <label>NIK (Nomor Induk) <span class="req">*</span></label>
          <div class="input-wrap">
            <input v-model="form.nik" placeholder="Masukkan NIK karyawan" class="input" />
          </div>
          <span v-if="form.errors.nik" class="error">{{ form.errors.nik }}</span>
        </div>

        <div class="field">
          <label>Nama Lengkap <span class="req">*</span></label>
          <div class="input-wrap">
            <input v-model="form.name" placeholder="Masukkan nama lengkap" class="input" />
          </div>
          <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
        </div>

        <div class="field">
          <label>Alamat Email <span class="opt">(opsional)</span></label>
          <div class="input-wrap">
            <input v-model="form.email" placeholder="contoh@email.com" class="input" />
          </div>
          <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
        </div>

        <div class="field">
          <label>Kata Sandi <span class="req">*</span></label>
          <div class="input-wrap">
            <input :type="showPw ? 'text' : 'password'" v-model="form.password" placeholder="Minimal 8 karakter" class="input" />
            <button type="button" class="eye-btn" @click="showPw = !showPw">
              <svg v-if="!showPw" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
          <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
        </div>

        <div class="field">
          <label>Konfirmasi Kata Sandi <span class="req">*</span></label>
          <div class="input-wrap">
            <input :type="showPw2 ? 'text' : 'password'" v-model="form.password_confirmation" placeholder="Ulangi kata sandi" class="input" />
            <button type="button" class="eye-btn" @click="showPw2 = !showPw2">
              <svg v-if="!showPw2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
          <span v-if="form.errors.password_confirmation" class="error">{{ form.errors.password_confirmation }}</span>
        </div>

        <button type="submit" class="btn-primary" :disabled="form.processing">
          {{ form.processing ? 'Memproses...' : 'Daftar Sekarang' }}
        </button>
      </form>

      <p class="footer-text">Sudah memiliki akun? <Link :href="route('login')" class="link">Masuk di sini</Link></p>
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
  background: #ffffff; border-radius: 28px; padding: 36px 32px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;
}
.logo-section { text-align: center; margin-bottom: 28px; }
.icon-circle {
  width: 56px; height: 56px; border-radius: 16px;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 14px; box-shadow: 0 8px 20px rgba(79,70,229,0.25);
}
.logo-section h1 { margin: 0 0 4px; font-size: 22px; font-weight: 800; color: #0f172a; }
.logo-section p { margin: 0; font-size: 13px; color: #64748b; }
.form { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 4px; }
.field label { font-size: 12px; font-weight: 600; color: #475569; }
.req { color: #ef4444; }
.opt { color: #94a3b8; font-weight: 400; font-size: 11px; }
.input-wrap {
  display: flex; align-items: center; gap: 10px;
  background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 14px; padding: 0 14px;
  transition: border-color 0.2s;
}
.input-wrap:focus-within { border-color: #4f46e5; background: #fff; }
.input {
  flex: 1; border: none; background: transparent; padding: 13px 0;
  font-size: 14px; font-family: inherit; color: #0f172a; outline: none;
}
.input::placeholder { color: #94a3b8; }
.eye-btn { background: none; border: none; cursor: pointer; padding: 4px; display: flex; }
.error { font-size: 11px; color: #ef4444; margin-top: 2px; }
.btn-primary {
  width: 100%; padding: 16px; border: none; border-radius: 14px; margin-top: 4px;
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
