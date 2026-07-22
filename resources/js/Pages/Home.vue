<script setup lang="ts">
// Definisikan tipe untuk props
interface Props {
  user: string
}

defineProps<Props>()
</script>

<template>
  <div class="p-8 font-sans">
    <h1 class="text-2xl font-bold text-slate-800">Halo, {{ user }}! 👋</h1>
    <p class="text-slate-600 mt-2">Inertia v3 + Laravel 13 + TypeScript + Tailwind v4 siap digunakan.</p>
  </div>
</template>
