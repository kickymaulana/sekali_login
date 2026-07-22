<script setup>
defineProps({
  user: String
})
</script>

<template>
  <div style="padding: 2rem; font-family: sans-serif;">
    <h1>Halo, {{ user }}! 👋</h1>
    <p>Inertia v3 + Laravel 13 berhasil terpasang.</p>
  </div>
</template>
