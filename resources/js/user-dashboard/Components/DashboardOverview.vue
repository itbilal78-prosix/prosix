<template>
  <div class="overview-root">

    <!-- Page Header with user name -->
    <div class="page-header">
      <div>
        <h1 class="page-title">
          Welcome back, {{ user?.name ? user.name.split(' ')[0] : 'there' }}! 👋
        </h1>
        <p class="page-sub">Here's what's happening with your properties.</p>
      </div>
      <button class="add-property-btn" @click="$emit('navigate-to-list')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Property
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card" v-for="(card, i) in statCards" :key="i" :style="`--accent:${card.color};--light:${card.light}`">
        <div class="stat-icon-wrap">
          <span v-html="card.icon"></span>
        </div>
        <div class="stat-info">
          <p class="stat-label">{{ card.label }}</p>
          <p class="stat-value">{{ card.value }}</p>
        </div>
        <div class="stat-bg-circle"></div>
      </div>
    </div>

    <!-- Recent Properties -->
    <div class="section-card">
      <div class="section-header">
        <h2 class="section-title">Recent Properties</h2>
        <button v-if="recentProperties && recentProperties.length" class="view-all-link" @click="$emit('navigate-to-list')">View All</button>
      </div>

      <div v-if="!recentProperties || recentProperties.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
        </div>
        <h3>No properties listed yet</h3>
        <p>Start by adding your first property listing.</p>
        <button class="cta-btn" @click="$emit('navigate-to-list')">List Your First Property</button>
      </div>

      <div v-else class="property-list">
        <div class="property-row" v-for="property in recentProperties" :key="property.id">
          <div class="prop-img-wrap">
            <img
              :src="property.images && property.images.length ? property.images[0] : '/placeholder-property.jpg'"
              :alt="property.title"
              class="prop-img"
            />
          </div>
          <div class="prop-details">
            <h4 class="prop-title">{{ property.title }}</h4>
            <p class="prop-location">
              <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
              </svg>
              {{ property.location }}
            </p>
          </div>
          <div class="prop-meta">
            <span class="prop-price">{{ formatPrice(property.price) }}</span>
            <span class="prop-status" :class="property.is_active ? 'status-active' : 'status-inactive'">
              {{ property.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>
          <div class="prop-date">{{ formatDate(property.created_at) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  dashboardStats: Object,
  recentProperties: Array,
  // ✅ Added user prop to show name in welcome message
  user: { type: Object, default: () => ({}) }
})
defineEmits(['navigate-to-list'])

const formatPrice = (price) =>
  new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR', minimumFractionDigits: 0 }).format(price || 0)

const formatDate = (d) =>
  new Date(d).toLocaleDateString('en-PK', { year: 'numeric', month: 'short', day: 'numeric' })

const statCards = computed(() => [
  {
    label: 'Total Properties',
    value: props.dashboardStats?.total_properties ?? 0,
    color: '#1a1a2e',
    light: '#e8e8f0',
    icon: `<svg width="22" height="22" fill="none" stroke="#1a1a2e" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>`
  },
  {
    label: 'Active Listings',
    value: props.dashboardStats?.active_properties ?? 0,
    color: '#059669',
    light: '#d1fae5',
    icon: `<svg width="22" height="22" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>`
  },
  {
    label: 'Total Views',
    value: props.dashboardStats?.total_views ?? 0,
    color: '#d97706',
    light: '#fef3c7',
    icon: `<svg width="22" height="22" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`
  },
  {
    label: 'Total Value',
    value: formatPrice(props.dashboardStats?.total_value ?? 0),
    color: '#0f3460',
    light: '#dce8f5',
    icon: `<svg width="22" height="22" fill="none" stroke="#0f3460" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`
  }
])
</script>

<style scoped>
.overview-root { display: flex; flex-direction: column; gap: 1.5rem; }

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}
.page-title { font-size: 1.6rem; font-weight: 700; color: #111827; margin: 0; }
.page-sub { color: #6b7280; margin: 4px 0 0; font-size: 0.9rem; }

.add-property-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #1a1a2e, #0f3460);
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(15,52,96,0.3);
}
.add-property-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(15,52,96,0.4); }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid #f3f4f6;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  position: relative;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
.stat-icon-wrap {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: var(--light);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  z-index: 1;
}
.stat-info { z-index: 1; }
.stat-label { font-size: 0.78rem; color: #9ca3af; margin: 0 0 4px; font-weight: 500; }
.stat-value { font-size: 1.35rem; font-weight: 700; color: #111827; margin: 0; }
.stat-bg-circle {
  position: absolute;
  right: -20px;
  top: -20px;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: var(--light);
  opacity: 0.5;
}

.section-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  overflow: hidden;
}
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f9fafb;
}
.section-title { font-size: 1rem; font-weight: 700; color: #111827; margin: 0; }
.view-all-link {
  background: none;
  border: none;
  color: #0f3460;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  padding: 0;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 48px 20px;
  gap: 8px;
  text-align: center;
}
.empty-icon {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
  color: #9ca3af;
}
.empty-state h3 { font-size: 1rem; font-weight: 600; color: #374151; margin: 0; }
.empty-state p { color: #9ca3af; font-size: 0.875rem; margin: 0; }
.cta-btn {
  margin-top: 12px;
  background: linear-gradient(135deg, #1a1a2e, #0f3460);
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(15,52,96,0.3);
  transition: transform 0.2s;
}
.cta-btn:hover { transform: translateY(-1px); }

.property-list { padding: 8px 0; }
.property-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 14px 24px;
  transition: background 0.15s;
}
.property-row:hover { background: #fafafa; }
.property-row + .property-row { border-top: 1px solid #f9fafb; }

.prop-img-wrap { flex-shrink: 0; }
.prop-img { width: 56px; height: 56px; border-radius: 10px; object-fit: cover; }

.prop-details { flex: 1; min-width: 0; }
.prop-title { font-weight: 600; font-size: 0.9rem; color: #111827; margin: 0 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.prop-location {
  display: flex;
  align-items: center;
  gap: 4px;
  color: #9ca3af;
  font-size: 0.78rem;
  margin: 0;
}

.prop-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
.prop-price { font-weight: 700; font-size: 0.9rem; color: #059669; }
.prop-status {
  font-size: 0.72rem;
  font-weight: 600;
  padding: 2px 10px;
  border-radius: 20px;
}
.status-active { background: #d1fae5; color: #059669; }
.status-inactive { background: #f3f4f6; color: #6b7280; }
.prop-date { color: #d1d5db; font-size: 0.78rem; white-space: nowrap; flex-shrink: 0; }
</style>
