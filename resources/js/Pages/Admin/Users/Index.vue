<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
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

const confirmDelete = (user: UserItem) => {
  Dialog({
    title: 'Hapus User?',
    message: `User "${user.name}" tidak akan bisa login lagi ke seluruh aplikasi SSO.`,
    onConfirm: () => {
      router.delete(route('admin.users.destroy', user.id), {
        onSuccess: () => Snackbar.success('User berhasil dihapus'),
      })
    },
  })
}
</script>

<template>
  <Head title="Kelola Users - SSO Admin" />

  <div class="android-layout">
    <header class="top-app-bar">
      <Link :href="route('dashboard')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Manajemen User</h1>
      <Link :href="route('admin.users.create')" class="add-button">
        <var-icon name="account-plus" :size="22" color="#4f46e5" />
      </Link>
    </header>

    <main class="android-content">
      <div class="hero-card">
        <div class="hero-text">
          <h3>Kelola Pengguna 👥</h3>
          <p>Daftar semua pengguna terdaftar dan hak aksesnya.</p>
        </div>
        <var-icon name="account-group" class="hero-icon" />
      </div>

      <div class="action-bar">
        <Link :href="route('admin.users.create')" class="action-btn">
          <var-icon name="account-plus" :size="18" />
          Tambah User
        </Link>
      </div>

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
              <td style="text-align: right" class="action-cell">
                <Link :href="route('admin.users.edit', user.id)" class="action-link edit-link">Edit</Link>
                <var-button size="mini" type="danger" text @click="confirmDelete(user)">Hapus</var-button>
              </td>
            </tr>
            <tr v-if="!users.data.length">
              <td colspan="4" class="empty-row">
                <div class="empty-state">
                  <var-icon name="account-off" :size="36" color="#cbd5e1" />
                  <p>Belum ada pengguna terdaftar.</p>
                  <Link :href="route('admin.users.create')" class="empty-link">Tambah User Baru</Link>
                </div>
              </td>
            </tr>
          </tbody>
        </var-table>
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

.back-button, .add-button {
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

.action-bar {
  display: flex;
  justify-content: flex-end;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #4f46e5;
  color: #ffffff;
  padding: 8px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: background-color 0.2s;
}

.action-btn:hover {
  background: #4338ca;
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

.action-cell {
  white-space: nowrap;
}

.action-link {
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  padding: 4px 8px;
  border-radius: 6px;
  display: inline-block;
}

.edit-link {
  color: #d97706;
}

.edit-link:hover {
  background-color: #fef3c7;
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

.empty-link {
  color: #4f46e5;
  font-weight: 600;
  text-decoration: none;
  font-size: 13px;
}
</style>
