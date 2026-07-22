<script setup lang="ts">
import { Snackbar } from '@varlet/ui'

interface Props {
  user: string
}

defineProps<Props>()

const showToast = () => {
  Snackbar.success('Varlet UI berhasil dipasang!')
}
</script>

<template>
  <div class="p-8 max-w-md mx-auto space-y-4">
    <var-card
      title="Inertia v3 + Varlet UI"
      :subtitle="`Halo, ${user}!`"
      description="Material Design komponen siap digunakan di Laravel 13."
    >
      <template #extra>
        <var-space>
          <var-button type="primary" @click="showToast">
            Klik Saya
          </var-button>
          <var-button type="success">
            Tombol Varlet
          </var-button>
        </var-space>
      </template>
    </var-card>
  </div>
</template>
