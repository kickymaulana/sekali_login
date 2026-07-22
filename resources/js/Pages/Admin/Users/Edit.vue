<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface UserItem {
  id: number
  name: string
  email: string
  roles: { name: string }[]
}

const props = defineProps<{
  user: UserItem
  roles: string[]
}>()

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  role: props.user.roles[0]?.name || '',
})

const submit = () => {
  form.put(route('admin.users.update', props.user.id), {
    onSuccess: () => Snackbar.success('Data user berhasil diperbarui'),
    onError: () => {
      if (form.errors.name) Snackbar.error(form.errors.name)
      else if (form.errors.email) Snackbar.error(form.errors.email)
      else if (form.errors.password) Snackbar.error(form.errors.password)
    },
  })
}

const confirmDelete = () => {
  Dialog({
    title: 'Hapus User?',
    message: `User "${props.user.name}" tidak akan bisa login lagi ke seluruh aplikasi SSO.`,
    onConfirm: () => {
      router.delete(route('admin.users.destroy', props.user.id), {
        onSuccess: () => {
          Snackbar.success('User berhasil dihapus')
          router.get(route('admin.users.index'))
        },
      })
    },
  })
}
</script>

<template>
  <Head :title="'Edit User - ' + user.name" />

  <div class="android-layout">
    <header class="top-app-bar">
      <Link :href="route('admin.users.index')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Edit User</h1>
      <var-button text round @click="confirmDelete">
        <var-icon name="delete-outline" :size="22" color="#ef4444" />
      </var-button>
    </header>

    <main class="android-content">
      <div class="hero-card">
        <div class="hero-text">
          <h3>Edit Pengguna ✏️</h3>
          <p>Perbarui data dan hak akses untuk <strong>{{ user.name }}</strong>.</p>
        </div>
        <var-icon name="account-edit" class="hero-icon" />
      </div>

      <div class="form-container">
        <form @submit.prevent="submit">
          <var-input v-model="form.name" label="Nama Lengkap" placeholder="Masukkan nama lengkap" :error-message="form.errors.name">
            <template #prepend-icon><var-icon name="account-outline" color="#6366f1" /></template>
          </var-input>

          <var-input v-model="form.email" label="Email" placeholder="contoh@email.com" :error-message="form.errors.email">
            <template #prepend-icon><var-icon name="email-outline" color="#6366f1" /></template>
          </var-input>

          <var-input type="password" v-model="form.password" label="Password Baru (Kosongkan jika tidak diubah)" placeholder="Minimal 8 karakter" :error-message="form.errors.password">
            <template #prepend-icon><var-icon name="lock-outline" color="#6366f1" /></template>
          </var-input>

          <var-input type="password" v-model="form.password_confirmation" label="Konfirmasi Password Baru" placeholder="Ulangi password baru">
            <template #prepend-icon><var-icon name="key-outline" color="#6366f1" /></template>
          </var-input>

          <var-select v-model="form.role" label="Pilih Role" :error-message="form.errors.role">
            <var-option v-for="r in roles" :key="r" :label="r" :value="r" />
          </var-select>

          <div class="form-actions">
            <Link :href="route('admin.users.index')" class="cancel-btn">Batal</Link>
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
