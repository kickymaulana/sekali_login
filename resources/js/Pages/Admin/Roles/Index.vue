<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface PermissionItem { id: number; name: string }
interface RoleItem { id: number; name: string; permissions: PermissionItem[] }

// 🟢 Perbaikan sintaks defineProps
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

  <div class="admin-container">
    <div class="header-section">
      <div>
        <h1 class="page-title">Roles & Permissions</h1>
        <p class="page-subtitle">Atur skema hak akses dan perizinan Spatie secara terpusat</p>
      </div>
      <var-button type="primary" round @click="openCreateModal">
        <template #icon><var-icon name="plus" /></template>
        Tambah Role
      </var-button>
    </div>

    <!-- Role List Cards -->
    <div class="roles-grid">
      <div v-for="role in roles" :key="role.id" class="role-card">
        <div class="role-header">
          <h3>{{ role.name }}</h3>
          <div>
            <var-button size="mini" type="warning" text @click="openEditModal(role)">Edit</var-button>
            <var-button size="mini" type="danger" text @click="confirmDelete(role.id)">Hapus</var-button>
          </div>
        </div>

        <div class="permissions-list">
          <span class="section-label">Permissions Assigned:</span>
          <div class="chips-wrapper" v-if="role.permissions && role.permissions.length">
            <var-chip v-for="p in role.permissions" :key="p.id" size="small" type="success" tonal>
              {{ p.name }}
            </var-chip>
          </div>
          <span v-else class="no-perms">Tidak ada permission khusus</span>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <var-dialog v-model:show="showModal" :title="editingRole ? 'Edit Role' : 'Tambah Role Baru'">
      <form @submit.prevent="submitForm" class="dialog-form">
        <var-input v-model="form.name" label="Nama Role" placeholder="Contoh: manager / admin" :error-message="form.errors.name" />

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
.admin-container { padding: 24px; max-width: 1200px; margin: 0 auto; }
.header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
.page-subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }
.roles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; }
.role-card { background: #fff; border-radius: 16px; border: 1px solid #f1f5f9; padding: 16px; display: flex; flex-direction: column; gap: 12px; }
.role-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 8px; }
.role-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; text-transform: capitalize; }
.permissions-list { display: flex; flex-direction: column; gap: 8px; }
.section-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
.chips-wrapper { display: flex; flex-wrap: wrap; gap: 6px; }
.no-perms { font-size: 12px; color: #cbd5e1; font-style: italic; }
.dialog-form { display: flex; flex-direction: column; gap: 16px; padding: 12px 0; }
.perms-picker { display: flex; flex-direction: column; gap: 8px; }
.picker-label { font-size: 13px; font-weight: 600; color: #334155; }
.checkbox-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; max-height: 200px; overflow-y: auto; }
</style>
