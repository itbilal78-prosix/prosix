<template>
  <div class="overview-root">

    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">
          Welcome back, {{ user?.name ? user.name.split(' ')[0] : 'there' }}!
        </h1>
        <p class="page-sub">Here's your store activity at a glance.</p>
      </div>
      <router-link to="/" class="add-property-btn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        Visit Store
      </router-link>
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
   <!-- Recent Orders -->
<div class="section-card">
  <div class="section-header">
    <h2 class="section-title">Recent Orders</h2>
    <router-link to="#" class="view-all-link">View All</router-link>
  </div>

  <div v-if="!recentOrders || recentOrders.length === 0" class="empty-state">
    <div class="empty-icon">
      <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
        <line x1="3" y1="6" x2="21" y2="6"/>
        <path d="M16 10a4 4 0 0 1-8 0"/>
      </svg>
    </div>
    <h3>No orders yet</h3>
    <p>Your recent orders will appear here.</p>
    <router-link to="/" class="cta-btn">Start Shopping</router-link>
  </div>

  <div v-else class="order-list">
    <div class="order-row" v-for="order in recentOrders" :key="order.id">

      <!-- Order Number -->
      <div class="order-details">
        <h4 class="order-id">Order #{{ order.id }}</h4>
        <p class="order-date-text">{{ formatDate(order.created_at) }}</p>
      </div>

      <!-- Price + Status only -->
      <div class="order-meta">
        <span class="order-price">{{ formatPrice(order.total) }}</span>
        <span class="order-status" :class="statusClass(order.status)">
          {{ order.status || 'Pending' }}
        </span>
      </div>

    </div>
  </div>
</div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  dashboardStats: {
    type: Object,
    default: () => ({})
  },
  user: {
    type: Object,
    default: () => ({})
  }
})

const recentOrders = ref([])
const favoriteCount = ref(0)
const myDesignCount = ref(0)
const myRequestCount = ref(0)

/*
|--------------------------------------------------------------------------
| Place Order counts
|--------------------------------------------------------------------------
| Place Orders = tamam non-completed orders
| My Orders    = sirf completed orders
*/
const placeOrdersCount = ref(0)
const myOrdersCount = ref(0)

const token = () => localStorage.getItem('auth_token')

const authHeaders = () => {
  const authToken = token()

  return {
    Accept: 'application/json',
    ...(authToken
      ? { Authorization: `Bearer ${authToken}` }
      : {})
  }
}

const normalizeResponseArray = payload => {
  if (Array.isArray(payload)) {
    return payload
  }

  if (Array.isArray(payload?.data)) {
    return payload.data
  }

  return []
}

const normalizeStatus = status => {
  return String(status || '')
    .trim()
    .toLowerCase()
    .replaceAll('_', ' ')
    .replaceAll('-', ' ')
}

const loadPlaceOrderCounts = async () => {
  try {
    const response = await fetch('/api/place-order/my-orders', {
      headers: authHeaders()
    })

    if (!response.ok) {
      placeOrdersCount.value = 0
      myOrdersCount.value = 0
      return
    }

    const payload = await response.json()
    const orders = normalizeResponseArray(payload)

    myOrdersCount.value = orders.filter(order => {
      return normalizeStatus(order.status) === 'completed'
    }).length

    placeOrdersCount.value = orders.filter(order => {
      return normalizeStatus(order.status) !== 'completed'
    }).length
  } catch (error) {
    console.error('Place order counts load error:', error)
    placeOrdersCount.value = 0
    myOrdersCount.value = 0
  }
}

const loadMyRequestsCount = async () => {
  try {
    const response = await fetch('/api/user/my-requests', {
      headers: authHeaders()
    })

    if (response.ok) {
      const data = await response.json()
      myRequestCount.value = Array.isArray(data?.data)
        ? data.data.length
        : 0
    }
  } catch (error) {
    console.error('My requests count load error:', error)
  }
}

const loadFavorites = () => {
  try {
    const likes = JSON.parse(
      localStorage.getItem('favorite_designs') || '[]'
    )

    favoriteCount.value = Array.isArray(likes)
      ? likes.length
      : 0
  } catch (error) {
    console.error('Favorite designs count error:', error)
    favoriteCount.value = 0
  }
}

const fetchRecentOrders = async () => {
  try {
    const response = await fetch('/api/user/orders?limit=5', {
      headers: authHeaders()
    })

    if (response.ok) {
      const data = await response.json()
      recentOrders.value = Array.isArray(data?.data)
        ? data.data
        : []
    }
  } catch (error) {
    console.error('Recent orders load error:', error)
  }
}

const loadMyDesigns = async () => {
  try {
    const response = await fetch('/api/user/designs', {
      headers: authHeaders()
    })

    if (response.ok) {
      const data = await response.json()

      myDesignCount.value = Array.isArray(data?.data)
        ? data.data.length
        : 0
    }
  } catch (error) {
    console.error('My designs count load error:', error)
  }
}

onMounted(() => {
  fetchRecentOrders()
  loadPlaceOrderCounts()
  loadFavorites()
  loadMyRequestsCount()
  loadMyDesigns()
})

const formatPrice = price => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2
  }).format(price || 0)
}

const formatDate = date => {
  if (!date) {
    return '—'
  }

  const parsedDate = new Date(date)

  if (Number.isNaN(parsedDate.getTime())) {
    return date
  }

  return parsedDate.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const statusClass = status => {
  const value = String(status || '').toLowerCase()

  if (value === 'delivered' || value === 'completed') {
    return 'status-delivered'
  }

  if (value === 'shipped' || value === 'processing') {
    return 'status-shipped'
  }

  if (value === 'cancelled' || value === 'canceled') {
    return 'status-cancelled'
  }

  return 'status-pending'
}

const statCards = computed(() => [
  {
    label: 'Place Orders',
    value: placeOrdersCount.value,
    color: '#2563eb',
    light: '#dbeafe',
    icon: `<svg width="22" height="22" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>`
  },
  {
    label: 'My Orders',
    value: myOrdersCount.value,
    color: '#059669',
    light: '#d1fae5',
    icon: `<svg width="22" height="22" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>`
  },
  {
    label: 'Total Spent',
    value: formatPrice(props.dashboardStats?.total_spent ?? 0),
    color: '#0f3460',
    light: '#dce8f5',
    icon: `<svg width="22" height="22" fill="none" stroke="#0f3460" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`
  },
  {
    label: 'My Requests',
    value: myRequestCount.value,
    color: '#7c3aed',
    light: '#ede9fe',
    icon: `<svg width="22" height="22" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>`
  },
  {
    label: 'Favorite Designs',
    value: favoriteCount.value,
    color: '#e11d48',
    light: '#ffe4e6',
    icon: `<svg width="22" height="22" fill="none" stroke="#e11d48" stroke-width="2" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>`
  },
  {
    label: 'My Designs',
    value: myDesignCount.value,
    color: '#0f172a',
    light: '#e2e8f0',
    icon: `<svg width="22" height="22" fill="none" stroke="#0f172a" stroke-width="2" viewBox="0 0 24 24"><path d="M18 3l3 3-9 9-3-3 9-9z"/><path d="M15 6l3 3"/><path d="M2 22s4-1 6-3 3-6 3-6"/></svg>`
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
  background: #0a0a0a;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.add-property-btn:hover { background: #1f1f1f; transform: translateY(-1px); }

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
  width: 48px; height: 48px; border-radius: 12px;
  background: var(--light);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; z-index: 1;
}
.stat-info { z-index: 1; }
.stat-label { font-size: 0.78rem; color: #9ca3af; margin: 0 0 4px; font-weight: 500; }
.stat-value { font-size: 1.35rem; font-weight: 700; color: #111827; margin: 0; }
.stat-bg-circle {
  position: absolute; right: -20px; top: -20px;
  width: 80px; height: 80px; border-radius: 50%;
  background: var(--light); opacity: 0.5;
}

.section-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  overflow: hidden;
}
.section-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f9fafb;
}
.section-title { font-size: 1rem; font-weight: 700; color: #111827; margin: 0; }
.view-all-link {
  color: #0a0a0a; font-weight: 600; font-size: 0.85rem;
  text-decoration: none; transition: opacity 0.2s;
}
.view-all-link:hover { opacity: 0.6; }

.empty-state {
  display: flex; flex-direction: column; align-items: center;
  padding: 48px 20px; gap: 8px; text-align: center;
}
.empty-icon {
  width: 72px; height: 72px; border-radius: 50%;
  background: #f3f4f6;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 8px; color: #9ca3af;
}
.empty-state h3 { font-size: 1rem; font-weight: 600; color: #374151; margin: 0; }
.empty-state p  { color: #9ca3af; font-size: 0.875rem; margin: 0; }
.cta-btn {
  margin-top: 12px;
  background: #0a0a0a; color: white;
  border: none; padding: 10px 24px; border-radius: 10px;
  font-weight: 600; font-size: 0.875rem; cursor: pointer;
  text-decoration: none; display: inline-block;
  transition: transform 0.2s;
}
.cta-btn:hover { transform: translateY(-1px); }

.order-list { padding: 8px 0; }
.order-row {
  display: flex; align-items: center; gap: 16px;
  padding: 14px 24px;
  transition: background 0.15s;
}
.order-row:hover { background: #fafafa; }
.order-row + .order-row { border-top: 1px solid #f9fafb; }

.order-icon-wrap {
  width: 44px; height: 44px; border-radius: 10px;
  background: #f3f4f6; color: #6b7280;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.order-details { flex: 1; min-width: 0; }
.order-id   { font-weight: 600; font-size: 0.9rem; color: #111827; margin: 0 0 3px; }
.order-items { color: #9ca3af; font-size: 0.78rem; margin: 0; }
.order-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
.order-price { font-weight: 700; font-size: 0.9rem; color: #111827; }
.order-status {
  font-size: 0.72rem; font-weight: 600;
  padding: 2px 10px; border-radius: 20px;
  text-transform: capitalize;
}
.status-delivered { background: #d1fae5; color: #059669; }
.status-shipped   { background: #dbeafe; color: #2563eb; }
.status-pending   { background: #fef3c7; color: #d97706; }
.status-cancelled { background: #fee2e2; color: #dc2626; }
.order-date { color: #d1d5db; font-size: 0.78rem; white-space: nowrap; flex-shrink: 0; }
</style>
