<template>
  <div class="po-tab-root">
    <div class="po-tab-header">
      <h2 class="po-tab-title">My Place Orders</h2>
      <span class="po-tab-count">{{ orders.length }} order{{ orders.length !== 1 ? 's' : '' }}</span>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="po-loading">
      <div class="po-spinner-wrap">
        <div class="po-spin"></div>
      </div>
      <p>Loading your orders...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="orders.length === 0" class="po-empty">
      <div class="po-empty-icon">
        <svg viewBox="0 0 64 64" fill="none" width="64" height="64">
          <rect x="10" y="8" width="44" height="50" rx="4" stroke="#d1d5db" stroke-width="2.5"/>
          <path d="M20 22h24M20 31h24M20 40h14" stroke="#d1d5db" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
      </div>
      <h4>No Orders Yet</h4>
      <p>You haven't placed any orders. Start by placing your first order!</p>
      <router-link to="/place-order" class="po-place-btn">Place an Order</router-link>
    </div>

    <!-- Orders List -->
    <div v-else class="po-orders-grid">
      <div
        v-for="order in orders"
        :key="order.id"
        class="po-order-card"
      >
        <!-- Card Header -->
        <div class="po-card-header">
          <div class="po-order-num">
            <span class="po-label-sm">Order #</span>
            <span class="po-num-val">{{ order.order_number }}</span>
          </div>
          <span class="po-status-badge" :class="'status-' + order.status">
            {{ capitalize(order.status) }}
          </span>
        </div>

        <!-- Card Body -->
        <div class="po-card-body">
          <div class="po-info-grid">
            <div class="po-info-item">
              <span class="po-info-label">Name</span>
              <span class="po-info-val">{{ order.full_name }}</span>
            </div>
            <div class="po-info-item">
              <span class="po-info-label">Email</span>
              <span class="po-info-val">{{ order.email }}</span>
            </div>
            <div class="po-info-item">
              <span class="po-info-label">Order Date</span>
              <span class="po-info-val">{{ order.order_date }}</span>
            </div>
            <div class="po-info-item">
              <span class="po-info-label">Delivery Date</span>
              <span class="po-info-val">{{ order.delivery_date || '—' }}</span>
            </div>
            <div class="po-info-item">
              <span class="po-info-label">Sales Rep</span>
              <span class="po-info-val">{{ order.sales_rep || '—' }}</span>
            </div>
            <div class="po-info-item">
              <span class="po-info-label">Team Colors</span>
              <span class="po-info-val">{{ order.team_colors || '—' }}</span>
            </div>
            <div class="po-info-item">
              <span class="po-info-label">Submitted</span>
              <span class="po-info-val">{{ order.created_at }}</span>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="order.notes" class="po-notes-section">
            <span class="po-info-label">Notes</span>
            <div class="po-notes-text" v-html="order.notes"></div>
          </div>

          <!-- Files -->
          <div class="po-files-row">
            <div v-if="order.mockup_files && order.mockup_files.length" class="po-file-group">
              <span class="po-file-label">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                Mockups ({{ order.mockup_files.length }})
              </span>
              <div class="po-file-chips">
                <a
                  v-for="(f, i) in order.mockup_files"
                  :key="i"
                  :href="'/uploads/orders/mockup/' + f"
                  target="_blank"
                  class="po-file-chip"
                >
                  {{ getExt(f) }}
                </a>
              </div>
            </div>

            <div v-if="order.roster_files && order.roster_files.length" class="po-file-group">
              <span class="po-file-label">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Roster ({{ order.roster_files.length }})
              </span>
              <div class="po-file-chips">
                <a
                  v-for="(f, i) in order.roster_files"
                  :key="i"
                  :href="'/uploads/orders/roster/' + f"
                  target="_blank"
                  class="po-file-chip"
                >
                  {{ getExt(f) }}
                </a>
              </div>
            </div>

            <div v-if="order.quote_files && order.quote_files.length" class="po-file-group">
              <span class="po-file-label">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Quote/Inv ({{ order.quote_files.length }})
              </span>
              <div class="po-file-chips">
                <a
                  v-for="(f, i) in order.quote_files"
                  :key="i"
                  :href="'/uploads/orders/quote/' + f"
                  target="_blank"
                  class="po-file-chip"
                >
                  {{ getExt(f) }}
                </a>
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
  orders: {
    type: Array,
    default: () => []
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : ''

const getExt = (filename) => {
  if (!filename) return 'FILE'
  const ext = filename.split('.').pop().toUpperCase()
  return ext.slice(0, 4)
}
</script>

<style scoped>
.po-tab-root {
  padding: 0;
  font-family: 'Segoe UI', system-ui, sans-serif;
}

.po-tab-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.po-tab-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #111;
  margin: 0;
}

.po-tab-count {
  background: #f3f4f6;
  color: #6b7280;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 99px;
}

/* Loading */
.po-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  padding: 80px 20px;
  color: #9ca3af;
  font-size: 0.9rem;
}
.po-spinner-wrap { display: flex; justify-content: center; }
.po-spin {
  width: 36px; height: 36px;
  border: 3px solid #e5e7eb;
  border-top-color: #4f46e5;
  border-radius: 50%;
  animation: spin 0.75s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Empty */
.po-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 80px 20px;
  text-align: center;
}
.po-empty-icon { margin-bottom: 8px; }
.po-empty h4 { font-size: 1.1rem; font-weight: 700; color: #374151; margin: 0; }
.po-empty p  { color: #9ca3af; font-size: 0.88rem; margin: 0; max-width: 280px; }
.po-place-btn {
  margin-top: 8px;
  background: #111;
  color: #fff;
  border-radius: 8px;
  padding: 10px 28px;
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.2s;
}
.po-place-btn:hover { background: #333; }

/* Orders Grid */
.po-orders-grid {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

/* Order Card */
.po-order-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  transition: box-shadow 0.2s;
}
.po-order-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.09); }

/* Card Header */
.po-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 20px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}
.po-order-num { display: flex; flex-direction: column; gap: 2px; }
.po-label-sm  { font-size: 0.7rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.po-num-val   { font-size: 0.95rem; font-weight: 700; color: #111; }

/* Status Badge */
.po-status-badge {
  font-size: 0.75rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 99px;
  text-transform: capitalize;
}
.status-pending    { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-completed  { background: #d1fae5; color: #065f46; }
.status-cancelled  { background: #fee2e2; color: #991b1b; }

/* Card Body */
.po-card-body { padding: 18px 20px; }

.po-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 14px 20px;
  margin-bottom: 16px;
}
.po-info-item { display: flex; flex-direction: column; gap: 3px; }
.po-info-label { font-size: 0.7rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
.po-info-val   { font-size: 0.875rem; color: #111; font-weight: 500; }

/* Notes */
.po-notes-section {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px 14px;
  margin-bottom: 16px;
}
.po-notes-text {
  font-size: 0.85rem;
  color: #374151;
  line-height: 1.6;
  margin-top: 6px;
}

/* Files Row */
.po-files-row {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}
.po-file-group { display: flex; flex-direction: column; gap: 6px; }
.po-file-label {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.72rem;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}
.po-file-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.po-file-chip {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 3px 10px;
  font-size: 0.72rem;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.15s;
  cursor: pointer;
}
.po-file-chip:hover {
  background: #111;
  color: #fff;
  border-color: #111;
}

@media (max-width: 640px) {
  .po-info-grid { grid-template-columns: 1fr 1fr; }
}
</style>

