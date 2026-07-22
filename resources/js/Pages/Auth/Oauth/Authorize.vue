<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

defineProps<{
  client: { name: string; id: string }
  authToken: string
  request: { state: string; client_id: string }
}>()
</script>

<template>
  <Head title="Otorisasi Aplikasi" />

  <div class="android-layout">
    <!-- Header App Bar -->
    <header class="top-app-bar">
      <div class="header-placeholder"></div>
      <h1 class="app-bar-title">Otorisasi</h1>
      <div class="header-placeholder"></div>
    </header>

    <main class="android-content">
      <!-- Hero -->
      <div class="hero-card">
        <div class="hero-text">
          <h3>Permintaan Akses 🔐</h3>
          <p>Aplikasi eksternal meminta izin untuk terhubung dengan akun SSO Anda.</p>
        </div>
        <var-icon name="shield-check-outline" class="hero-icon" />
      </div>

      <!-- App Info Card -->
      <div class="app-info-card">
        <div class="app-icon-wrapper">
          <var-icon name="apps-box" :size="32" color="#4f46e5" />
        </div>
        <h2 class="app-name">{{ client.name }}</h2>
        <p class="app-desc">Meminta akses ke akun SSO Anda</p>

        <div class="permission-list">
          <div class="permission-item">
            <var-icon name="email-outline" :size="18" color="#10b981" />
            <span>Lihat alamat email Anda</span>
          </div>
          <div class="permission-item">
            <var-icon name="account-outline" :size="18" color="#10b981" />
            <span>Lihat nama profil Anda</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="action-section">
        <form method="POST" :action="route('passport.authorizations.approve')" class="action-form">
          <input type="hidden" name="_token" :value="$page.props.csrf_token || ''" />
          <input type="hidden" name="state" :value="request.state" />
          <input type="hidden" name="client_id" :value="request.client_id" />
          <input type="hidden" name="auth_token" :value="authToken" />
          <var-button type="primary" block round native-type="submit" class="approve-btn">
            Izinkan Akses
          </var-button>
        </form>

        <form method="POST" :action="route('passport.authorizations.deny')" class="action-form">
          <input type="hidden" name="_method" value="DELETE" />
          <input type="hidden" name="_token" :value="$page.props.csrf_token || ''" />
          <input type="hidden" name="state" :value="request.state" />
          <input type="hidden" name="client_id" :value="request.client_id" />
          <input type="hidden" name="auth_token" :value="authToken" />
          <var-button block round native-type="submit" class="deny-btn">
            Tolak
          </var-button>
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

.app-bar-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.header-placeholder {
  width: 24px;
}

.android-content {
  flex: 1;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 480px;
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
  max-width: 230px;
}

.hero-icon {
  font-size: 46px !important;
  opacity: 0.25;
}

.app-info-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 28px 20px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  text-align: center;
}

.app-icon-wrapper {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: #e0e7ff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px auto;
}

.app-name {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 4px 0;
}

.app-desc {
  font-size: 13px;
  color: #64748b;
  margin: 0 0 20px 0;
}

.permission-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 0 8px;
}

.permission-item {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 13px;
  color: #334155;
  font-weight: 500;
}

.action-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.action-form {
  width: 100%;
}

.approve-btn {
  background: linear-gradient(135deg, #4f46e5, #6366f1) !important;
  font-weight: 700 !important;
  height: 48px !important;
  font-size: 15px !important;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25) !important;
}

.deny-btn {
  border: 1px solid #e2e8f0 !important;
  color: #64748b !important;
  font-weight: 600 !important;
  height: 44px !important;
  font-size: 14px !important;
}
</style>
