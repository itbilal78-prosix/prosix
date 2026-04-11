<template>
  <div class="bc-bar" v-if="crumbs.length > 1">
    <div class="bc-inner">
      <span v-for="(item, i) in crumbs" :key="i" class="bc-item">
        <span v-if="i === crumbs.length - 1" class="bc-current">{{ item.name }}</span>
        <router-link v-else :to="item.path" class="bc-link">{{ item.name }}</router-link>
        <i v-if="i !== crumbs.length - 1" class="bi bi-chevron-right bc-sep"></i>
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const crumbs = computed(() => {
  try {
    const saved = localStorage.getItem('breadcrumbs')
    if (!saved) return []
    return JSON.parse(saved)
  } catch { return [] }
})
</script>

<style scoped>
.bc-bar {
  padding: 8px 40px;
  background: transparent;
  font-family: 'Montserrat', sans-serif;
  margin-top: 120px;
}
.bc-inner {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 2px;
}
.bc-item { display: flex; align-items: center; gap: 4px; }
.bc-link { font-size: 17px; color: #888; text-decoration: none; font-weight: 500; transition: color 0.2s; }
.bc-link:hover { color: #000; }
.bc-current { font-size: 17px; color: #111; font-weight: 700; }
.bc-sep { font-size: 9px; color: #ccc; margin: 0 2px; }
</style>
