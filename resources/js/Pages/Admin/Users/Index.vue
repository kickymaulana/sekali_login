<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface UserItem {
  id: number
  name: string
  email: string
  roles: { name: string }[]
}

const props = defineProps<{
  users: { data: UserItem[] }
  roles: string[]
}>()

const showModal = ref(false)
const editingUser = ref<UserItem | null>(null)

const form = useForm({
  name: '',
  email: '',
  password: '',
  role: '',
})

const openCreateModal = () => {
  editingUser.value = null
  form.reset()
  form.clearErrors()
  if (props.roles && props.roles.length > 0) {
    form.role = props.roles[0]
  }
  showModal.value = true
}

const openEditModal = (user: UserItem) => {
  editingUser.value = user
  form.clearErrors()
  form.name = user.name
  form.email = user.email
  form.password = ''
  form.role = user.roles[0]?.name || (props.roles[0] ?? '')
  showModal.value = true
}

const submitForm = () => {
  if (editingUser.value) {
    form.put(route('admin.users.update', editingUser.value.id), {
      onSuccess: () => {
        showModal.value = false
        Snackbar.success('Data user berhasil diperbarui')
      },
    })
  } else {
    form.post(route('admin.users.store'), {
      onSuccess: () => {
        showModal.value = false
        Snackbar.success('User baru berhasil ditambahkan')
      },
    })
  }
}

const confirmDelete = (userId: number) => {
  Dialog({
    title: 'Hapus User?',
    message: 'User ini tidak akan bisa login lagi ke seluruh aplikasi SSO.',
    onConfirm: () => {
      router.delete(route('admin.users.destroy', userId), {
        onSuccess: () => Snackbar.success('User berhasil dihapus'),
      })
    },
  })
}
</script>

<template>
  <Head title="Kelola Users - SSO Admin" />

  <div class="android-layout">
    <!-- Header App Bar -->
    <header class="top-app-bar">
      <Link href="/home" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Manajemen User</h1>
      <var-button text round @click="openCreateModal">
        <var-icon name="account-plus" :size="22" color="#4f46e5" />
      </var-button>
    </header>

    <main class="android-content">
      <!-- Hero -->
      <div class="hero-card">
        <div class="hero-text">
          <h3>Kelola Pengguna 👥</h3>
          <p>Daftar semua pengguna terdaftar dan hak aksesnya.</p>
        </div>
        <var-icon name="account-group" class="hero-icon" />
      </div>

      <!-- Users Table -->
      <div class="table-card">
        <var-table>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Role SSO</th>
              <th style="text-align: right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users.data" :key="user.id">
              <td class="font-bold">{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>
                <var-chip size="small" type="info" v-for="role in user.roles" :key="role.name">
                  {{ role.name }}
                </var-chip>
              </td>
              <td style="text-align: right">
                <var-button size="mini" type="warning" text @click="openEditModal(user)">Edit</var-button>
                <var-button size="mini" type="danger" text @click="confirmDelete(user.id)">Hapus</var-button>
              </td>
            </tr>
            <!-- Empty Row -->
            <tr v-if="!users.data.length">
              <td colspan="4" class="empty-row">
                <div class="empty-state">
                  <var-icon name="account-off" :size="36" color="#cbd5e1" />
                  <p>Belum ada pengguna terdaftar.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </var-table>
      </div>
    </main>

    <!-- Modal Form -->
    <var-dialog v-model:show="showModal" :title="editingUser ? 'Edit User' : 'Tambah User Baru'">
      <form @submit.prevent="submitForm" class="dialog-form">
        <var-input v-model="form.name" label="Nama Lengkap" placeholder="Masukkan nama lengkap" :error-message="form.errors.name">
          <template #prepend-icon><var-icon name="account-outline" color="#6366f1" /></template>
        </var-input>
        <var-input v-model="form.email" label="Email" placeholder="contoh@email.com" :error-message="form.errors.email">
          <template #prepend-icon><var-icon name="email-outline" color="#6366f1" /></template>
        </var-input>
        <var-input
          type="password"
          v-model="form.password"
          :label="editingUser ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Password'"
          placeholder="Minimal 8 karakter"
          :error-message="form.errors.password"
        >
          <template #prepend-icon><var-icon name="lock-outline" color="#6366f1" /></template>
        </var-input>
        <var-select v-model="form.role" label="Pilih Role Spatie" :error-message="form.errors.role">
          <var-option v-for="r in roles" :key="r" :label="r" :value="r" />
        </var-select>
      </form>
      <template #actions>
        <var-button text @click="showModal = false">Batal</var-button>
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

.table-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}

.font-bold {
  font-weight: 600;
  color: #0f172a;
}

.dialog-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 12px 0;
}

.empty-row {
  text-align: center;
  padding: 32px !important;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #94a3b8;
  font-size: 13px;
}
</style>
