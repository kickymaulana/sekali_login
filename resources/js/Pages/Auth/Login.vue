<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3'
import { Snackbar } from '@varlet/ui'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onError: () => {
      if (form.errors.email) {
        Snackbar.error(form.errors.email)
      } else if (form.errors.password) {
        Snackbar.error(form.errors.password)
      }
    },
  })
}
</script>

<template>
  <Head title="Login - SSO Server" />

  <div class="min-h-screen flex items-center justify-center bg-slate-100 p-4">
    <var-card class="w-full max-w-md p-6 shadow-md rounded-2xl bg-white">
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">SSO SERVER</h1>
        <p class="text-sm text-slate-500 mt-1">Sekali Login Untuk Semua Aplikasi</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Email Input -->
        <var-input
          v-model="form.email"
          label="Email"
          placeholder="Masukkan email Anda"
          :error-message="form.errors.email"
          :states="form.errors.email ? 'error' : undefined"
          clearable
        />

        <!-- Password Input -->
        <var-input
          type="password"
          v-model="form.password"
          label="Kata Sandi"
          placeholder="Masukkan kata sandi"
          :error-message="form.errors.password"
          :states="form.errors.password ? 'error' : undefined"
          clearable
        />

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-2">
          <var-checkbox v-model="form.remember">
            Ingat Saya
          </var-checkbox>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
          <var-button
            type="primary"
            block
            native-type="submit"
            :loading="form.processing"
          >
            Masuk
          </var-button>
        </div>
      </form>
    </var-card>
  </div>
</template>
