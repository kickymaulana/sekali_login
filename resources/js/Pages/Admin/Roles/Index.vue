<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface PermissionItem { id: number; name: string }
interface RoleItem { id: number; name: string; permissions: PermissionItem[] }

const props = defineProps<{
  roles: RoleItem[]
  permissions: PermissionItem[]
}>()

const confirmDelete = (role: RoleItem) => {
  Dialog({
    title: 'Hapus Role?',
    message: `Role "${role.name}" akan dihapus. User yang memiliki role ini mungkin akan kehilangan hak akses.`,
    onConfirm: () => {
      router.delete(route('admin.roles.destroy', role.id), {
        onSuccess: () => Snackbar.success('Role berhasil dihapus'),
      })
    },
  })
}
</script>

<template>
  <Head title="Roles & Permissions - SSO Admin" />

  <div class="android-layout">
    <header class="top-app-bar">
      <Link :href="route('dashboard')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Roles & Permissions</h1>
      <Link :href="route('admin.roles.create')" class="add-button">
        <var-icon name="plus" :size="22" color="#4f46e5" />
      </Link>
    </header>

    <main class="android-content">
      <div class="hero-card">
        <div class="hero-text">
          <h3>Hak Akses & Perizinan 🛡️</h3>
          <p>Atur skema role dan permission Spatie secara terpusat untuk seluruh aplikasi.</p>
        </div>
        <var-icon name="shield-account" class="hero-icon" />
      </div>

      <div class="action-bar">
        <Link :href="route('admin.roles.create')" class="action-btn">
          <var-icon name="plus" :size="18" />
          Tambah Role
        </Link>
      </div>

      <div v-if="!roles.length" class="empty-card">
        <var-icon name="shield-off" :size="48" color="#cbd5e1" />
        <p>Belum ada role yang dibuat.</p>
        <Link :href="route('admin.roles.create')" class="empty-link">Tambah Role Baru</Link>
      </div>

      <div v-else class="roles-grid">
        <div v-for="role in roles" :key="role.id" class="role-card">
          <div class="role-header">
            <div class="role-title">
              <div class="role-icon-box">
                <var-icon name="shield-account" :size="20" color="#8b5cf6" />
              </div>
              <h3>{{ role.name }}</h3>
            </div>
            <Link :href="route('admin.roles.edit', role.id)" class="action-link edit-link">Edit</Link>
          </div>

          <div class="permissions-section">
            <span class="section-label">Permissions</span>
            <div class="chips-wrapper" v-if="role.permissions && role.permissions.length">
              <var-chip v-for="p in role.permissions" :key="p.id" size="small" type="success" tonal round>
                {{ p.name }}
              </var-chip>
            </div>
            <span v-else class="no-perms">Tidak ada permission khusus</span>
          </div>
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

.empty-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 48px 32px;
  text-align: center;
  color: #94a3b8;
  border: 1px dashed #cbd5e1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  font-size: 14px;
}

.empty-link {
  color: #4f46e5;
  font-weight: 600;
  text-decoration: none;
}

.roles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 16px;
}

.role-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.role-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}

.role-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 12px;
  border-bottom: 1px solid #f8fafc;
}

.role-title {
  display: flex;
  align-items: center;
  gap: 10px;
}

.role-icon-box {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #ede9fe;
  display: flex;
  align-items: center;
  justify-content: center;
}

.role-title h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  text-transform: capitalize;
}

.action-link {
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  padding: 4px 8px;
  border-radius: 6px;
}

.edit-link {
  color: #d97706;
}

.edit-link:hover {
  background-color: #fef3c7;
}

.permissions-section {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.section-label {
  font-size: 10px;
  color: #94a3b8;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.chips-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.no-perms {
  font-size: 12px;
  color: #cbd5e1;
  font-style: italic;
}
</style>
