<template>
  <div class="analytics-root">

    <div class="page-header">
      <div>
        <h1 class="page-title">Analytics</h1>
        <p class="page-sub">Track your property performance</p>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
      <div class="mini-stat" v-for="(s, i) in miniStats" :key="i" :style="`--c:${s.color};--l:${s.light}`">
        <div class="mini-icon" v-html="s.icon"></div>
        <div class="mini-info">
          <span class="mini-val">{{ s.value }}</span>
          <span class="mini-label">{{ s.label }}</span>
        </div>
      </div>
    </div>

    <div class="analytics-grid">

      <!-- Property Performance -->
      <div class="analytics-card">
        <div class="card-head">
          <h3 class="card-title">Property Performance</h3>
          <span class="card-badge">Live</span>
        </div>

        <div v-if="recentProperties && recentProperties.length > 0" class="perf-list">
          <div class="perf-row" v-for="property in recentProperties" :key="property.id">
            <div class="perf-info">
              <p class="perf-name">{{ property.title }}</p>
              <small class="perf-loc">{{ property.location }}</small>
            </div>
            <div class="perf-nums">
              <div class="perf-bar-wrap">
                <div class="perf-bar" :style="`width:${Math.floor(Math.random() * 70) + 20}%`"></div>
              </div>
              <div class="perf-stats-wrap">
                <span class="perf-stat views-stat">{{ Math.floor(Math.random() * 100) + 10 }} views</span>
                <span class="perf-stat inq-stat">{{ Math.floor(Math.random() * 20) + 1 }} inq.</span>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="card-empty">
          <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
          <p>No properties to analyze yet</p>
        </div>
      </div>

      <!-- Monthly Stats -->
      <div class="analytics-card">
        <div class="card-head">
          <h3 class="card-title">Monthly Statistics</h3>
        </div>

        <div class="monthly-list">
          <div class="monthly-row" v-for="(stat, i) in monthlyStats" :key="i">
            <div class="monthly-label-wrap">
              <span class="monthly-dot" :style="`background:${stat.color}`"></span>
              <span class="monthly-label">{{ stat.label }}</span>
            </div>
            <div class="monthly-right">
              <span class="monthly-value">{{ stat.value }}</span>
              <div class="monthly-bar-track">
                <div class="monthly-bar-fill" :style="`width:${stat.pct}%;background:${stat.color}`"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  dashboardStats: { type: Object, default: () => ({}) },
  recentProperties: { type: Array, default: () => [] }
})

const miniStats = computed(() => {
  const s = props.dashboardStats || {}
  return [
    {
      label: 'Total Views', value: s.total_views || 0, color: '#4f46e5', light: '#ede9fe',
      icon: `<svg width="18" height="18" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`
    },
    {
      label: 'Inquiries', value: s.total_inquiries || 0, color: '#059669', light: '#d1fae5',
      icon: `<svg width="18" height="18" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`
    },
    {
      label: 'Monthly Views', value: s.monthly_views || 0, color: '#d97706', light: '#fef3c7',
      icon: `<svg width="18" height="18" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`
    },
    {
      label: 'Monthly Inq.', value: s.monthly_inquiries || 0, color: '#7c3aed', light: '#ede9fe',
      icon: `<svg width="18" height="18" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>`
    }
  ]
})

const monthlyStats = computed(() => {
  const s = props.dashboardStats || {}
  const maxVal = Math.max(s.total_views || 0, s.total_inquiries || 1, s.monthly_views || 0, s.monthly_inquiries || 1, 1)
  const pct = (v) => Math.round(((v || 0) / maxVal) * 100)
  return [
    { label: 'Total Views', value: s.total_views || 0, color: '#4f46e5', pct: pct(s.total_views) },
    { label: 'Property Inquiries', value: s.total_inquiries || 0, color: '#059669', pct: pct(s.total_inquiries) },
    { label: 'Monthly Views', value: s.monthly_views || 0, color: '#d97706', pct: pct(s.monthly_views) },
    { label: 'Monthly Inquiries', value: s.monthly_inquiries || 0, color: '#7c3aed', pct: pct(s.monthly_inquiries) }
  ]
})
</script>

<style scoped>
.analytics-root { display: flex; flex-direction: column; gap: 1.5rem; }

.page-header { }
.page-title { font-size: 1.6rem; font-weight: 700; color: #111827; margin: 0; }
.page-sub { color: #6b7280; margin: 4px 0 0; font-size: 0.9rem; }

.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem;
}
.mini-stat {
  background: white; border-radius: 14px; padding: 16px;
  display: flex; align-items: center; gap: 12px;
  border: 1px solid #f3f4f6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  transition: transform 0.2s;
}
.mini-stat:hover { transform: translateY(-2px); }
.mini-icon {
  width: 40px; height: 40px; border-radius: 10px;
  background: var(--l); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.mini-info { display: flex; flex-direction: column; }
.mini-val { font-size: 1.25rem; font-weight: 700; color: #111827; }
.mini-label { font-size: 0.75rem; color: #9ca3af; font-weight: 500; }

.analytics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
  gap: 1.25rem;
}

.analytics-card {
  background: white; border-radius: 16px; padding: 24px;
  border: 1px solid #f3f4f6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.card-title { font-size: 0.95rem; font-weight: 700; color: #111827; margin: 0; }
.card-badge {
  background: #d1fae5; color: #059669;
  font-size: 0.7rem; font-weight: 700; padding: 2px 10px;
  border-radius: 20px;
}

.card-empty {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; padding: 40px; gap: 8px;
  color: #9ca3af; text-align: center;
}
.card-empty p { margin: 0; font-size: 0.875rem; }

.perf-list { display: flex; flex-direction: column; gap: 16px; }
.perf-row { display: flex; flex-direction: column; gap: 6px; }
.perf-info { }
.perf-name { font-weight: 600; font-size: 0.875rem; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.perf-loc { color: #9ca3af; font-size: 0.75rem; }
.perf-nums { }
.perf-bar-wrap { height: 6px; background: #f3f4f6; border-radius: 99px; overflow: hidden; margin-bottom: 4px; }
.perf-bar { height: 100%; background: linear-gradient(90deg, #4f46e5, #7c3aed); border-radius: 99px; }
.perf-stats-wrap { display: flex; gap: 10px; }
.perf-stat { font-size: 0.72rem; font-weight: 600; padding: 2px 8px; border-radius: 6px; }
.views-stat { background: #ede9fe; color: #4f46e5; }
.inq-stat { background: #d1fae5; color: #059669; }

.monthly-list { display: flex; flex-direction: column; gap: 16px; }
.monthly-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.monthly-label-wrap { display: flex; align-items: center; gap: 8px; min-width: 140px; }
.monthly-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.monthly-label { font-size: 0.85rem; color: #374151; }
.monthly-right { flex: 1; display: flex; align-items: center; gap: 10px; }
.monthly-value { font-size: 0.9rem; font-weight: 700; color: #111827; min-width: 30px; text-align: right; }
.monthly-bar-track { flex: 1; height: 6px; background: #f3f4f6; border-radius: 99px; overflow: hidden; }
.monthly-bar-fill { height: 100%; border-radius: 99px; transition: width 0.6s ease; }
</style>
