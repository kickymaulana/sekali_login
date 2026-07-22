<script setup lang="ts">
import { router, Head } from '@inertiajs/vue3'

interface AuthUser {
  id: number
  name: string
  email: string
  roles: string[]
  permissions: string[]
}

interface Props {
  auth: {
    user: AuthUser | null
  }
}

const props = defineProps<Props>()

const handleLogout = () => {
  // ✅ Menggunakan helper route('logout') dari Ziggy
  router.post(route('logout'))
}
</script>

<template>
  <Head title="Dashboard" />

  <div class="min-h-screen bg-slate-50 p-6">
    <div class="max-w-2xl mx-auto">
      <var-card
        title="Dashboard Utama"
        :subtitle="`Selamat datang, ${props.auth.user?.name}!`"
        class="p-4 shadow-sm"
      >
        <div class="space-y-3 my-4 text-slate-700">
          <p><strong>Email:</strong> {{ props.auth.user?.email }}</p>
          <p><strong>Role:</strong>
            <var-chip type="success" size="small" class="ml-2" v-for="role in props.auth.user?.roles" :key="role">
              {{ role }}
            </var-chip>
          </p>
          <p><strong>Permissions:</strong>
            <var-chip type="info" size="small" class="ml-2 mr-1" v-for="perm in props.auth.user?.permissions" :key="perm">
              {{ perm }}
            </var-chip>
          </p>
        </div>

        <template #extra>
          <var-button type="danger" @click="handleLogout">
            Logout
          </var-button>
        </template>
      </var-card>
    </div>
  </div>
</template>
