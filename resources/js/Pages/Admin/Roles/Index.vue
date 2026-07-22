<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface PermissionItem { id: number; name: string }
interface RoleItem { id: number; name: string; permissions: PermissionItem[] }

const props = defineProps<{
  roles: RoleItem[]
  permissions: PermissionItem[]
}>()

const showModal = ref(false)
const editingRole = ref<RoleItem | null>(null)

const form = useForm({
  name: '',
  permissions: [] as string[],
})

const openCreateModal = () => {
  editingRole.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

const openEditModal = (role: RoleItem) => {
  editingRole.value = role
  form.clearErrors()
  form.name = role.name
  form.permissions = role.permissions.map((p) => p.name)
  showModal.value = true
}

const submitForm = () => {
  if (editingRole.value) {
    form.put(route('admin.roles.update', editingRole.value.id), {
      onSuccess: () => {
        showModal.value = false
        Snackbar.success('Role berhasil diperbarui')
      },
    })
  } else {
    form.post(route('admin.roles.store'), {
      onSuccess: () => {
        showModal.value = false
        Snackbar.success('Role baru berhasil dibuat')
      },
    })
  }
}

const confirmDelete = (roleId: number) => {
  Dialog({
    title: 'Hapus Role?',
    message: 'User yang memiliki role ini mungkin akan kehilangan hak akses.',
    onConfirm: () => {
      router.delete(route('admin.roles.destroy', roleId), {
        onSuccess: () => Snackbar.success('Role berhasil dihapus'),
      })
    },
  })
}
</script>

<template>
  <Head title="Roles & Permissions - SSO Admin" />

  <div class="android-layout">
    <!-- Header App Bar -->
    <header class="top-app-bar">
      <Link href="/home" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Roles & Permissions</h1>
      <var-button text round @click="openCreateModal">
        <var-icon name="plus" :size="22" color="#4f46e5" />
      </var-button>
    </header>

    <main class="android-content">
      <!-- Hero -->
      <div class="hero-card">
        <div class="hero-text">
          <h3>Hak Akses & Perizinan 🛡️</h3>
          <p>Atur skema role dan permission Spatie secara terpusat untuk seluruh aplikasi.</p>
        </div>
        <var-icon name="shield-account" class="hero-icon" />
      </div>

      <!-- Empty State -->
      <div v-if="!roles.length" class="empty-card">
        <var-icon name="shield-off" :size="48" color="#cbd5e1" />
        <p>Belum ada role yang dibuat.</p>
        <var-button type="primary" round @click="openCreateModal">Tambah Role Baru</var-button>
      </div>

      <!-- Role Cards -->
      <div v-else class="roles-grid">
        <div v-for="role in roles" :key="role.id" class="role-card">
          <div class="role-header">
            <div class="role-title">
              <div class="role-icon-box">
                <var-icon name="shield-account" :size="20" color="#8b5cf6" />
              </div>
              <h3>{{ role.name }}</h3>
            </div>
            <div class="role-actions">
              <var-button size="mini" type="warning" text @click="openEditModal(role)">Edit</var-button>
              <var-button size="mini" type="danger" text @click="confirmDelete(role.id)">Hapus</var-button>
            </div>
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

    <!-- Modal Form -->
    <var-dialog v-model:show="showModal" :title="editingRole ? 'Edit Role' : 'Tambah Role Baru'">
      <form @submit.prevent="submitForm" class="dialog-form">
        <var-input v-model="form.name" label="Nama Role" placeholder="Contoh: manager / admin" :error-message="form.errors.name">
          <template #prepend-icon><var-icon name="shield-outline" color="#6366f1" /></template>
        </var-input>

        <div class="perms-picker">
          <label class="picker-label">Pilih Permissions:</label>
          <div class="checkbox-grid">
            <var-checkbox
              v-for="perm in permissions"
              :key="perm.id"
              v-model="form.permissions"
              :checked-value="perm.name"
            >
              {{ perm.name }}
            </var-checkbox>
          </div>
        </div>
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

.role-actions {
  display: flex;
  gap: 4px;
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

.dialog-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 12px 0;
}

.perms-picker {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.picker-label {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

.checkbox-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
  max-height: 200px;
  overflow-y: auto;
}
</style>
