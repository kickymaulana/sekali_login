<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

// Interfaces
interface AuthUser {
  id: number
  name: string
  email: string
  roles: string[]
  permissions: string[]
}

interface SummaryCount {
  activeApps: number
  activeSessions: number
  tokensIssued: number
  rolesCount: number
}

interface ConnectedApp {
  id: number
  name: string
  category: string
  connectedAt: string
  status: string
  icon: string
}

interface Props {
  auth: {
    user: AuthUser | null
  }
  summary?: SummaryCount
  connectedApps?: ConnectedApp[]
}

const props = defineProps<Props>()

// Bottom Navigation Active State (0: Beranda, 1: Aplikasi, 2: Keamanan, 3: Profil)
const activeTab = ref(0)

// Format data statistik SSO
const summaryData = computed(() => [
  {
    title: 'App Terhubung',
    count: props.summary?.activeApps ?? 1,
    icon: 'cellphone',
    color: '#6366f1',
    bgColor: '#e0e7ff',
  },
  {
    title: 'Sesi Aktif',
    count: props.summary?.activeSessions ?? 1,
    icon: 'star',
    color: '#10b981',
    bgColor: '#d1fae5',
  },
  {
    title: 'Token Diterbitkan',
    count: props.summary?.tokensIssued ?? 0,
    icon: 'code-json',
    color: '#f59e0b',
    bgColor: '#fef3c7',
  },
  {
    title: 'Role Diterima',
    count: props.summary?.rolesCount ?? props.auth.user?.roles.length ?? 0,
    icon: 'download',
    color: '#8b5cf6',
    bgColor: '#ede9fe',
  },
])

const handleLogout = () => {
  router.post(route('logout'))
}

const handleAddClient = () => {
  if (isAdmin.value) {
    router.get(route('admin.clients.index'))
  } else {
    alert('Fitur Pendaftaran Aplikasi Klien Baru hanya untuk Admin!')
  }
}

const handleTabChange = (index: number) => {
  if (index === 3) {
    router.get(route('profile'))
  }
}

// Helper untuk mengecek apakah user punya role 'admin'
const isAdmin = computed(() => {
  return props.auth.user?.roles?.includes('admin') ?? false
})
</script>

<template>
  <Head title="SSO Portal - Dashboard" />

  <div class="android-layout">
    <!-- Top App Bar Menggunakan Component Varlet -->
    <var-app-bar
      type="surface"
      :elevation="false"
      border
      fixed
      placeholder
      class="custom-app-bar"
    >
      <template #left>
        <div class="user-greeting">
          <var-avatar
            src="https://varletjs.org/cat.jpg"
            size="small"
            round
          />
          <div class="user-info">
            <span class="greeting-subtitle">Pusat Autentikasi SSO 👋</span>
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
      <!-- Welcome Hero Banner -->
      <div class="welcome-card">
        <div class="welcome-text">
          <h3>Satu Identitas untuk Semua Layanan 🚀</h3>
          <p>Kelola akses keamanan, sesi login, dan otorisasi aplikasi terhubung dalam satu tempat.</p>
        </div>
        <var-icon name="shield-check" class="welcome-icon" />
      </div>

      <!-- Stats Grid -->
      <div class="section-header">
        <h3 class="section-title">Ringkasan Akun</h3>
      </div>

      <div class="stats-grid">
        <div v-for="(stat, index) in summaryData" :key="index" class="stat-card">
          <div class="stat-icon-wrapper" :style="{ backgroundColor: stat.bgColor }">
            <var-icon :name="stat.icon" :size="22" :color="stat.color" />
          </div>
          <div class="stat-info">
            <span class="stat-count">{{ stat.count }}</span>
            <span class="stat-title">{{ stat.title }}</span>
          </div>
        </div>
      </div>

      <!-- Khusus Tampilan Admin (Navigasi CRUD) -->
      <div v-if="isAdmin" class="admin-section">
        <div class="section-header">
          <h3 class="section-title">Panel Admin SSO 🛠️</h3>
        </div>

        <div class="admin-menu-grid">
          <Link :href="route('admin.clients.index')" class="admin-menu-card">
            <div class="menu-icon-box bg-indigo">
              <var-icon name="apps-box" :size="24" color="#4f46e5" />
            </div>
            <div class="menu-info">
              <h4>OAuth Clients</h4>
              <p>Kelola Client ID & Secret App Eksternal</p>
            </div>
          </Link>

          <Link :href="route('admin.users.index')" class="admin-menu-card">
            <div class="menu-icon-box bg-emerald">
              <var-icon name="account-group" :size="24" color="#10b981" />
            </div>
            <div class="menu-info">
              <h4>User Management</h4>
              <p>Tambah, Edit & Reset Password Pengguna</p>
            </div>
          </Link>

          <Link :href="route('admin.roles.index')" class="admin-menu-card">
            <div class="menu-icon-box bg-purple">
              <var-icon name="shield-account" :size="24" color="#8b5cf6" />
            </div>
            <div class="menu-info">
              <h4>Roles & Permissions</h4>
              <p>Atur Hak Akses Spatie Secara Terpusat</p>
            </div>
          </Link>
        </div>
      </div>

      <!-- Quick Action Category Scroll -->
      <div class="section-header">
        <h3 class="section-title">Akses Cepat SSO</h3>
      </div>

      <div class="category-scroll">
        <div class="category-item">
          <var-button type="primary" fab tonal :elevation="false">
            <var-icon name="checkbox-marked-circle" :size="24" />
          </var-button>
          <span>Ubah Password</span>
        </div>

        <div class="category-item">
          <var-button type="info" fab tonal :elevation="false">
            <var-icon name="cake-variant" :size="24" />
          </var-button>
          <span>Role & Izin</span>
        </div>

        <div class="category-item">
          <var-button type="success" fab tonal :elevation="false">
            <var-icon name="card-account-details-outline" :size="24" />
          </var-button>
          <span>Sesi Login</span>
        </div>

        <div class="category-item">
          <var-button type="warning" fab tonal :elevation="false">
            <var-icon name="code-json" :size="24" />
          </var-button>
          <span>API Tokens</span>
        </div>
      </div>

      <!-- Connected Apps List -->
      <div class="section-header space-between">
        <h3 class="section-title">Aplikasi Terhubung</h3>
        <span class="see-all-link">Lihat Semua</span>
      </div>

      <!-- State Jika Belum Ada Aplikasi -->
      <div v-if="!connectedApps || connectedApps.length === 0" class="empty-card">
        <var-icon name="apps-box" :size="48" color="#cbd5e1" />
        <p>Belum ada aplikasi eksternal yang terhubung.</p>
      </div>

      <!-- List Aplikasi Terhubung dari Database -->
      <div v-else class="request-list">
        <div v-for="app in connectedApps" :key="app.id" class="request-card">
          <div class="request-main">
            <div class="request-header">
              <span class="request-code">{{ app.category }}</span>
              <var-chip type="success" size="small" round>
                {{ app.status }}
              </var-chip>
            </div>
            <h4 class="request-item-title">{{ app.name }}</h4>
            <div class="request-footer">
              <span class="request-category">
                <var-icon name="calendar-month-outline" :size="14" /> Diberi Izin: {{ app.connectedAt }}
              </span>
              <var-button size="mini" type="danger">
                Revoke
              </var-button>
            </div>
          </div>
        </div>
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
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
  box-sizing: border-box;
}

.welcome-card {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  border-radius: 20px;
  padding: 24px;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.35);
  width: 100%;
  box-sizing: border-box;
}

.welcome-text h3 {
  margin: 0 0 6px 0;
  font-size: 16px;
  font-weight: 700;
}
.welcome-text p {
  margin: 0;
  font-size: 13px;
  opacity: 0.9;
  line-height: 1.4;
  max-width: 500px;
}
.welcome-icon {
  font-size: 56px !important;
  opacity: 0.25;
}

.section-header {
  display: flex;
  align-items: center;
  margin-top: 4px;
}
.section-header.space-between {
  justify-content: space-between;
}
.section-title {
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}
.see-all-link {
  font-size: 12px;
  color: #4f46e5;
  font-weight: 600;
  cursor: pointer;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

@media (min-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.stat-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.stat-icon-wrapper {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-info {
  display: flex;
  flex-direction: column;
}
.stat-count {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
}
.stat-title {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

/* Panel Admin Styles */
.admin-menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 12px;
  margin-top: 10px;
}

.admin-menu-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 16px;
  border: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.admin-menu-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}

.menu-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.bg-indigo { background-color: #e0e7ff; }
.bg-emerald { background-color: #d1fae5; }
.bg-purple { background-color: #ede9fe; }

.menu-info h4 {
  margin: 0;
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.menu-info p {
  margin: 2px 0 0 0;
  font-size: 11px;
  color: #64748b;
}

.category-scroll {
  display: flex;
  gap: 20px;
  overflow-x: auto;
  padding: 4px 0;
  scrollbar-width: none;
}
.category-scroll::-webkit-scrollbar {
  display: none;
}
.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  min-width: 80px;
  cursor: pointer;
}
.category-item span {
  font-size: 12px;
  font-weight: 500;
  color: #475569;
  text-align: center;
  white-space: nowrap;
}

.empty-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 32px;
  text-align: center;
  border: 1px dashed #cbd5e1;
  color: #94a3b8;
  font-size: 13px;
}

.request-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.request-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 16px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}
.request-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 6px;
}
.request-code {
  font-size: 11px;
  font-family: monospace;
  font-weight: 700;
  color: #4f46e5;
  background-color: #e0e7ff;
  padding: 2px 8px;
  border-radius: 6px;
}
.request-item-title {
  margin: 0 0 10px 0;
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
}
.request-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  color: #94a3b8;
}
.request-category {
  display: flex;
  align-items: center;
  gap: 4px;
}

.bottom-nav-fixed {
  box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
  border-top: 1px solid #f1f5f9;
}
</style>
