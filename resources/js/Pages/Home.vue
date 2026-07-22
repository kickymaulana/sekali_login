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
    icon: 'apps',
    color: '#6366f1',
    bgColor: '#e0e7ff'
  },
  {
    title: 'Sesi Aktif',
    count: props.summary?.activeSessions ?? 1,
    icon: 'devices',
    color: '#10b981',
    bgColor: '#d1fae5'
  },
  {
    title: 'Token Diterbitkan',
    count: props.summary?.tokensIssued ?? 0,
    icon: 'key-outline',
    color: '#f59e0b',
    bgColor: '#fef3c7'
  },
  {
    title: 'Role Diterima',
    count: props.summary?.rolesCount ?? props.auth.user?.roles.length ?? 0,
    icon: 'shield-account-outline',
    color: '#8b5cf6',
    bgColor: '#ede9fe'
  },
])

const handleLogout = () => {
  router.post(route('logout'))
}

const handleAddClient = () => {
  // Aksi shortcut tambah client app baru (jika ada)
  alert('Fitur Pendaftaran Aplikasi Klien Baru akan datang!')
}

const handleTabChange = (index: number) => {
  // Logika navigasi antar tab mobile jika diperlukan
}
</script>

<template>
  <Head title="SSO Portal - Dashboard" />

  <div class="android-layout">
    <!-- Top App Bar -->
    <header class="top-app-bar">
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

      <div class="header-actions">
        <var-button round transparent @click="handleLogout">
          <var-icon name="logout" :size="22" color="#64748b" />
        </var-button>
      </div>
    </header>

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

      <!-- Quick Action Category Scroll -->
      <div class="section-header">
        <h3 class="section-title">Akses Cepat SSO</h3>
      </div>

      <div class="category-scroll">
        <div class="category-item">
          <div class="cat-icon bg-indigo">
            <var-icon name="shield-lock-outline" />
          </div>
          <span>Ubah Password</span>
        </div>
        <div class="category-item">
          <div class="cat-icon bg-purple">
            <var-icon name="account-group-outline" />
          </div>
          <span>Role & Izin</span>
        </div>
        <div class="category-item">
          <div class="cat-icon bg-green">
            <var-icon name="laptop" />
          </div>
          <span>Sesi Login</span>
        </div>
        <div class="category-item">
          <div class="cat-icon bg-orange">
            <var-icon name="code-json" />
          </div>
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
              <var-button size="mini" type="danger" outline round>
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
      @change="handleTabChange"
      @fab-click="handleAddClient"
    >
      <var-bottom-navigation-item label="Beranda" icon="home-outline" />
      <var-bottom-navigation-item label="Aplikasi" icon="apps" />
      <var-bottom-navigation-item label="Keamanan" icon="shield-outline" />
      <var-bottom-navigation-item label="Profil" icon="account-circle-outline" />

      <template #fab>
        <var-icon name="plus" :size="28" />
      </template>
    </var-bottom-navigation>
  </div>
</template>

<style scoped>
.android-layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #f8fafc;
  font-family: Roboto, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: #1e293b;
  overflow: hidden;
}

.top-app-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px 12px 20px;
  background-color: #ffffff;
  border-bottom: 1px solid #f1f5f9;
  position: sticky;
  top: 0;
  z-index: 10;
}

.user-greeting {
  display: flex;
  align-items: center;
  gap: 12px;
}
.user-info {
  display: flex;
  flex-direction: column;
}
.greeting-subtitle {
  font-size: 11px;
  color: #64748b;
  font-weight: 500;
}
.user-name {
  font-size: 16px;
  font-weight: 700;
  margin: 0;
  color: #0f172a;
}

.android-content {
  flex: 1;
  overflow-y: auto;
  padding: 16px 20px 100px 20px;
  display: flex;
  flex-direction: column;
  gap: 18px;
  max-width: 500px;
  margin: 0 auto;
  width: 100%;
}

/* Gradient Banner untuk SSO Theme */
.welcome-card {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  border-radius: 20px;
  padding: 20px;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.35);
}

.welcome-text h3 {
  margin: 0 0 6px 0;
  font-size: 15px;
  font-weight: 700;
}
.welcome-text p {
  margin: 0;
  font-size: 12px;
  opacity: 0.9;
  line-height: 1.4;
  max-width: 230px;
}
.welcome-icon {
  font-size: 48px !important;
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
.stat-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 14px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.stat-icon-wrapper {
  width: 42px;
  height: 42px;
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
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
}
.stat-title {
  font-size: 11px;
  color: #64748b;
  font-weight: 500;
}

.category-scroll {
  display: flex;
  gap: 16px;
  overflow-x: auto;
  padding-bottom: 4px;
  scrollbar-width: none;
}
.category-scroll::-webkit-scrollbar {
  display: none;
}
.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  min-width: 72px;
  cursor: pointer;
}
.cat-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}
.category-item span {
  font-size: 11px;
  font-weight: 500;
  color: #475569;
  text-align: center;
}

.bg-indigo { background-color: #4f46e5; }
.bg-purple { background-color: #8b5cf6; }
.bg-green { background-color: #10b981; }
.bg-orange { background-color: #f59e0b; }

.empty-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 24px;
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
  font-size: 11px;
  color: #94a3b8;
}
.request-category {
  display: flex;
  align-items: center;
  gap: 4px;
}

.bottom-nav-fixed {
  position: fixed !important;
  bottom: 0 !important;
  left: 0 !important;
  right: 0 !important;
  z-index: 999 !important;
  box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06) !important;
  border-top: 1px solid #f1f5f9;
}
</style>
