<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

// Ikut digunakan untuk akses ke halaman Ubah Password
// route, router, Link sudah diimpor di atas

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
const activeTab = ref(3)

const handleLogout = () => {
  router.post(route('logout'))
}

const handleTabChange = (index: number) => {
  if (index === 0) {
    router.get(route('dashboard'))
  } else if (index === 3) {
    // stay
  }
}

const handleAddClient = () => {
  router.get(route('admin.clients.index'))
}
</script>

<template>
  <Head title="Profil Saya - SSO Portal" />

  <div class="android-layout">
    <!-- Top App Bar -->
    <var-app-bar type="surface" :elevation="false" border fixed placeholder class="custom-app-bar">
      <template #left>
        <div class="user-greeting">
          <var-avatar
            src="https://varletjs.org/cat.jpg"
            size="small"
            round
          />
          <div class="user-info">
            <span class="greeting-subtitle">Akun SSO</span>
            <h2 class="user-name">{{ props.auth.user?.name || 'Kicky Maulana' }}</h2>
          </div>
        </div>
      </template>
      <template #right>
        <var-button round text @click="handleLogout">
          <var-icon name="logout" :size="22" color="#64748b" />
        </var-button>
      </template>
    </var-app-bar>

    <main class="android-content">
      <!-- Profile Avatar Card -->
      <div class="profile-card">
        <div class="avatar-section">
          <var-avatar
            src="https://varletjs.org/cat.jpg"
            size="72"
            round
          />
          <div class="avatar-info">
            <h2 class="profile-name">{{ props.auth.user?.name }}</h2>
            <span class="profile-email">{{ props.auth.user?.email }}</span>
            <div class="profile-roles">
              <var-chip
                v-for="role in props.auth.user?.roles"
                :key="role"
                size="small"
                type="primary"
                round
                class="role-chip"
              >
                {{ role }}
              </var-chip>
            </div>
          </div>
        </div>
      </div>

      <!-- Account Info -->
      <div class="section-header">
        <h3 class="section-title">Informasi Akun</h3>
      </div>

      <div class="info-card">
        <div class="info-row">
          <div class="info-icon-box bg-indigo">
            <var-icon name="account-outline" :size="20" color="#4f46e5" />
          </div>
          <div class="info-content">
            <span class="info-label">Nama Lengkap</span>
            <span class="info-value">{{ props.auth.user?.name }}</span>
          </div>
        </div>
        <div class="divider"></div>
        <div class="info-row">
          <div class="info-icon-box bg-emerald">
            <var-icon name="email-outline" :size="20" color="#10b981" />
          </div>
          <div class="info-content">
            <span class="info-label">Alamat Email</span>
            <span class="info-value">{{ props.auth.user?.email }}</span>
          </div>
        </div>
        <div class="divider"></div>
        <div class="info-row">
          <div class="info-icon-box bg-purple">
            <var-icon name="shield-account" :size="20" color="#8b5cf6" />
          </div>
          <div class="info-content">
            <span class="info-label">Role SSO</span>
            <div class="info-value">
              <var-chip
                v-for="role in props.auth.user?.roles"
                :key="role"
                size="small"
                type="info"
                round
              >
                {{ role }}
              </var-chip>
            </div>
          </div>
        </div>
        <div class="divider"></div>
        <div class="info-row">
          <div class="info-icon-box bg-amber">
            <var-icon name="lock-outline" :size="20" color="#f59e0b" />
          </div>
          <div class="info-content">
            <span class="info-label">User ID</span>
            <span class="info-value mono">{{ props.auth.user?.id }}</span>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="section-header">
        <h3 class="section-title">Pengaturan</h3>
      </div>

      <div class="menu-list">
        <Link :href="route('password.change')" class="menu-item">
          <div class="menu-icon-box bg-rose">
            <var-icon name="lock-reset" :size="20" color="#e11d48" />
          </div>
          <span class="menu-label">Ubah Password</span>
          <var-icon name="chevron-right" :size="20" color="#cbd5e1" />
        </Link>
        <div class="menu-item">
          <div class="menu-icon-box bg-sky">
            <var-icon name="cellphone" :size="20" color="#0284c7" />
          </div>
          <span class="menu-label">Aplikasi Terhubung</span>
          <var-icon name="chevron-right" :size="20" color="#cbd5e1" />
        </div>
        <Link :href="route('admin.users.index')" class="menu-item" v-if="props.auth.user?.roles?.includes('admin')">
          <div class="menu-icon-box bg-indigo">
            <var-icon name="account-group" :size="20" color="#4f46e5" />
          </div>
          <span class="menu-label">Panel Admin</span>
          <var-icon name="chevron-right" :size="20" color="#cbd5e1" />
        </Link>
      </div>

      <!-- Logout Button -->
      <div class="logout-section">
        <var-button
          block
          type="danger"
          outline
          class="logout-btn"
          @click="handleLogout"
        >
          <template #default>
            <var-icon name="logout" :size="18" />
            Keluar dari SSO
          </template>
        </var-button>
      </div>
    </main>

    <!-- Bottom Navigation -->
    <var-bottom-navigation
      v-model:active="activeTab"
      class="bottom-nav-fixed"
      fixed
      placeholder
      @change="handleTabChange"
      @fab-click="handleAddClient"
    >
      <var-bottom-navigation-item label="Beranda" icon="home-outline" />
      <var-bottom-navigation-item label="Aplikasi" icon="cellphone" />
      <var-bottom-navigation-item label="Keamanan" icon="lock" />
      <var-bottom-navigation-item label="Profil" icon="account-circle-outline" />
      <template #fab>
        <var-icon name="plus" :size="28" />
      </template>
    </var-bottom-navigation>
  </div>
</template>

<style scoped>
.android-layout {
  min-height: 100vh;
  background-color: #f8fafc;
  font-family: Roboto, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: #1e293b;
  width: 100%;
}

.custom-app-bar {
  padding: 8px 16px;
}

.user-greeting {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.greeting-subtitle {
  font-size: 11px;
  color: #64748b;
  font-weight: 500;
  line-height: 1.2;
}

.user-name {
  font-size: 15px;
  font-weight: 700;
  margin: 0;
  color: #0f172a;
  line-height: 1.2;
}

.android-content {
  padding: 20px 24px 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 600px;
  margin: 0 auto;
  width: 100%;
  box-sizing: border-box;
}

.profile-card {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  border-radius: 20px;
  padding: 24px;
  color: #ffffff;
  box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.35);
}

.avatar-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.avatar-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.profile-name {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: #ffffff;
}

.profile-email {
  font-size: 13px;
  opacity: 0.85;
}

.profile-roles {
  display: flex;
  gap: 4px;
  margin-top: 2px;
}

.role-chip {
  --chip-font-size: 10px !important;
}

.section-header {
  display: flex;
  align-items: center;
  margin-top: 4px;
}

.section-title {
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.info-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
  overflow: hidden;
}

.info-row {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
}

.info-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.bg-indigo { background-color: #e0e7ff; }
.bg-emerald { background-color: #d1fae5; }
.bg-purple { background-color: #ede9fe; }
.bg-amber { background-color: #fef3c7; }
.bg-rose { background-color: #ffe4e6; }
.bg-sky { background-color: #e0f2fe; }

.info-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.info-label {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
}

.info-value.mono {
  font-family: monospace;
  color: #64748b;
}

.divider {
  height: 1px;
  background-color: #f8fafc;
  margin: 0 16px;
}

.menu-list {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
  overflow: hidden;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  cursor: pointer;
  transition: background-color 0.2s;
  text-decoration: none;
  color: inherit;
}

.menu-item:hover {
  background-color: #f8fafc;
}

.menu-item:not(:last-child) {
  border-bottom: 1px solid #f8fafc;
}

.menu-icon-box {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.menu-label {
  flex: 1;
  font-size: 14px;
  font-weight: 500;
  color: #1e293b;
}

.logout-section {
  padding-top: 4px;
}

.logout-btn {
  --button-text-color: #ef4444 !important;
  --button-border-color: #fca5a5 !important;
  font-weight: 600 !important;
  height: 44px !important;
}

.bottom-nav-fixed {
  box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
  border-top: 1px solid #f1f5f9;
}
</style>
