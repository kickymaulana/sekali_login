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

  <div class="min-h-screen flex items-center justify-center bg-slate-100 p-4">
    <var-card class="w-full max-w-md p-6 shadow-md rounded-2xl bg-white text-center">
      <h2 class="text-xl font-bold text-slate-800 mb-2">Permintaan Akses SSO</h2>
      <p class="text-slate-600 text-sm mb-6">
        Aplikasi <strong>{{ client.name }}</strong> meminta izin untuk mengakses akun Anda.
      </p>

      <div class="flex justify-center gap-4">
        <!-- Form Setuju (Approve) -->
        <form method="POST" action="/oauth/authorize">
          <input type="hidden" name="_token" :value="$page.props.csrf_token || ''" />
          <input type="hidden" name="state" :value="request.state" />
          <input type="hidden" name="client_id" :value="request.client_id" />
          <input type="hidden" name="auth_token" :value="authToken" />
          <var-button type="primary" native-type="submit">Izinkan</var-button>
        </form>

        <!-- Form Tolak (Deny) -->
        <form method="POST" action="/oauth/authorize">
          <input type="hidden" name="_method" value="DELETE" />
          <input type="hidden" name="state" :value="request.state" />
          <input type="hidden" name="client_id" :value="request.client_id" />
          <input type="hidden" name="auth_token" :value="authToken" />
          <var-button type="danger" native-type="submit">Tolak</var-button>
        </form>
      </div>
    </var-card>
  </div>
</template>
