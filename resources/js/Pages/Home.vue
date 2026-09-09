<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

// Interfaces
interface AuthUser {
  id: number
  name: string
  email: string
  roles: string[]
  permissions: string[]
}

interface ConnectedApp {
  id: string
  name: string
  category: string
  connectedAt: string
  status: string
  url: string
}

interface Props {
  auth: {
    user: AuthUser | null
  }
  connectedApps?: ConnectedApp[]
}

const props = defineProps<Props>()

// Bottom Navigation Active State (0: Beranda, 1: Aplikasi, 2: Keamanan, 3: Profil)
const activeTab = ref(0)

const handleLogout = () => {
  router.post(route('logout'))
}

const handleTabChange = (index: number) => {
  if (index === 1) {
    router.get(route('aplikasi-terhubung'))
  } else if (index === 2) {
    router.get(route('security'))
  } else if (index === 3) {
    router.get(route('profile'))
  }
}

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


      <!-- Token Aktif List -->
      <div class="section-header space-between">
        <h3 class="section-title">Aplikasi Terhubung</h3>
        <Link :href="route('aplikasi-terhubung')" class="see-all-link">Lihat Semua</Link>
      </div>

      <!-- State Jika Belum Ada Aplikasi -->
      <div v-if="!connectedApps || connectedApps.length === 0" class="empty-card">
        <var-icon name="apps-box" :size="48" color="#cbd5e1" />
        <p>Belum ada aplikasi eksternal yang terhubung.</p>
      </div>

      <!-- List Token Aktif dari Database -->
       <div v-else class="app-grid">
         <a v-for="app in connectedApps" :key="app.id" :href="app.url" target="_blank" rel="noopener noreferrer" class="app-card">
           <div class="app-icon-box">
             <var-icon name="apps-box" :size="28" color="#4f46e5" />
           </div>
           <div class="app-card-info">
             <h4>{{ app.name }}</h4>
             <span>{{ app.category }}</span>
           </div>
           <var-icon name="open-in-new" :size="20" color="#94a3b8" />
         </a>
       </div>
    </main>

    <!-- Bottom Navigation -->
    <var-bottom-navigation
      v-model:active="activeTab"
      class="bottom-nav-fixed"
      fixed
      placeholder
      @change="handleTabChange"

    >
      <var-bottom-navigation-item label="Beranda" icon="home-outline" />
      <var-bottom-navigation-item label="Aplikasi" icon="cellphone" />
      <var-bottom-navigation-item label="Keamanan" icon="lock" />
      <var-bottom-navigation-item label="Profil" icon="account-circle-outline" />


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

.app-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 12px;
}

.app-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  text-decoration: none;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
  transition: transform 0.2s, box-shadow 0.2s;
}

.app-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(79, 70, 229, 0.12);
}

.app-icon-box {
  width: 52px;
  height: 52px;
  flex: 0 0 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 16px;
  background: #e0e7ff;
}

.app-card-info {
  flex: 1;
  min-width: 0;
}

.app-card-info h4 {
  margin: 0 0 4px;
  color: #0f172a;
  font-size: 15px;
  font-weight: 700;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.app-card-info span {
  color: #64748b;
  font-size: 12px;
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
