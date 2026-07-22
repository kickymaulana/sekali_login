<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { Snackbar, Dialog } from '@varlet/ui'

interface PermissionItem { id: number; name: string }
interface RoleItem { id: number; name: string; permissions: PermissionItem[] }

const props = defineProps<{
  role: RoleItem
  permissions: PermissionItem[]
}>()

const form = useForm({
  name: props.role.name,
  permissions: props.role.permissions.map((p) => p.name),
})

const submit = () => {
  form.put(route('admin.roles.update', props.role.id), {
    onSuccess: () => Snackbar.success('Role berhasil diperbarui'),
    onError: () => {
      if (form.errors.name) Snackbar.error(form.errors.name)
    },
  })
}

const confirmDelete = () => {
  Dialog({
    title: 'Hapus Role?',
    message: `Role "${props.role.name}" akan dihapus. User yang memiliki role ini mungkin akan kehilangan hak akses.`,
    onConfirm: () => {
      router.delete(route('admin.roles.destroy', props.role.id), {
        onSuccess: () => {
          Snackbar.success('Role berhasil dihapus')
          router.get(route('admin.roles.index'))
        },
      })
    },
  })
}
</script>

<template>
  <Head :title="'Edit Role - ' + role.name" />

  <div class="android-layout">
    <header class="top-app-bar">
      <Link :href="route('admin.roles.index')" class="back-button">
        <var-icon name="chevron-left" :size="24" color="#0f172a" />
      </Link>
      <h1 class="app-bar-title">Edit Role</h1>
      <var-button text round @click="confirmDelete">
        <var-icon name="delete-outline" :size="22" color="#ef4444" />
      </var-button>
    </header>

    <main class="android-content">
      <div class="hero-card">
        <div class="hero-text">
          <h3>Edit Role ✏️</h3>
          <p>Perbarui nama dan permission untuk role <strong>{{ role.name }}</strong>.</p>
        </div>
        <var-icon name="shield-edit" class="hero-icon" />
      </div>

      <div class="form-container">
        <form @submit.prevent="submit">
          <var-input v-model="form.name" label="Nama Role" placeholder="Contoh: manager / editor" :error-message="form.errors.name">
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

          <div class="form-actions">
            <Link :href="route('admin.roles.index')" class="cancel-btn">Batal</Link>
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
  max-width: 560px;
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
  max-height: 220px;
  overflow-y: auto;
  border: 1px solid #f1f5f9;
  border-radius: 10px;
  padding: 12px;
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
