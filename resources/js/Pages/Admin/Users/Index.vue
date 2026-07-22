<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface UserItem {
  id: number
  name: string
  email: string
  roles: { name: string }[]
}

// 🟢 Perbaikan sintaks defineProps
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

  <div class="admin-container">
    <div class="header-section">
      <div>
        <h1 class="page-title">Manajemen User</h1>
        <p class="page-subtitle">Daftar semua pengguna terdaftar dan hak aksesnya</p>
      </div>
      <var-button type="primary" round @click="openCreateModal">
        <template #icon><var-icon name="account-plus" /></template>
        Tambah User
      </var-button>
    </div>

    <!-- Users Table Card -->
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
        </tbody>
      </var-table>
    </div>

    <!-- Modal Form -->
    <var-dialog v-model:show="showModal" :title="editingUser ? 'Edit User' : 'Tambah User Baru'">
      <form @submit.prevent="submitForm" class="dialog-form">
        <var-input v-model="form.name" label="Nama Lengkap" :error-message="form.errors.name" />
        <var-input v-model="form.email" label="Email" :error-message="form.errors.email" />
        <var-input
          type="password"
          v-model="form.password"
          :label="editingUser ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Password'"
          :error-message="form.errors.password"
        />
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
.admin-container { padding: 24px; max-width: 1200px; margin: 0 auto; }
.header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
.page-subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }
.table-card { background: #fff; border-radius: 16px; border: 1px solid #f1f5f9; overflow: hidden; }
.font-bold { font-weight: 600; color: #0f172a; }
.dialog-form { display: flex; flex-direction: column; gap: 16px; padding: 12px 0; }
</style>
