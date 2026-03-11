<template>
  <div class="requests-page">

    <div class="page-header mb-4">
      <h4 class="fw-bold mb-1">My Requests</h4>
      <p class="text-muted small">Aapki saari submitted requests yahan dikhti hain</p>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-dark"></div>
      <p class="mt-2 text-muted small">Loading...</p>
    </div>

    <!-- Empty -->
    <div v-else-if="requests.length === 0" class="empty-state">
      <div class="empty-icon">📋</div>
      <h5>Koi request nahi mili</h5>
      <p class="text-muted small">Abhi tak koi artwork ya membership request submit nahi ki</p>
      <div class="d-flex gap-2 justify-content-center mt-3">
        <router-link to="/artwork" class="btn btn-dark btn-sm">Artwork Request</router-link>
        <router-link to="/membership" class="btn btn-outline-dark btn-sm">Special Deals</router-link>
      </div>
    </div>

    <!-- List -->
    <div v-else>
      <!-- Filter -->
      <div class="filter-tabs mb-4">
        <button
          v-for="tab in filterTabs" :key="tab.value"
          class="filter-btn"
          :class="{ active: activeFilter === tab.value }"
          @click="activeFilter = tab.value"
        >
          {{ tab.label }}
          <span class="count-badge">{{ getCount(tab.value) }}</span>
        </button>
      </div>

      <!-- Cards -->
      <div class="requests-grid">
        <div v-for="req in filteredRequests" :key="req.id + req.type" class="request-card">

          <!-- Top -->
          <div class="card-top">
            <span class="type-badge" :class="req.type === 'artwork' ? 'type-artwork' : 'type-membership'">
              {{ req.type === 'artwork' ? ' Artwork' : ' Membership' }}
            </span>
            <span class="date-text">{{ formatDate(req.created_at) }}</span>
          </div>

          <!-- Info rows -->
          <div class="info-list">
            <div class="info-row">
              <span class="lbl">Name</span>
              <span class="val">{{ req.full_name || '—' }}</span>
            </div>
            <div class="info-row" v-if="req.team_name">
              <span class="lbl">Team</span>
              <span class="val">{{ req.team_name }}</span>
            </div>
            <div class="info-row" v-if="req.organization">
              <span class="lbl">Organization</span>
              <span class="val">{{ req.organization }}</span>
            </div>
            <div class="info-row" v-if="req.design_style">
              <span class="lbl">Design Style</span>
              <span class="val">{{ req.design_style }}</span>
            </div>
            <div class="info-row" v-if="req.material">
              <span class="lbl">Material</span>
              <span class="val">{{ req.material }}</span>
            </div>
            <div class="info-row" v-if="req.sports">
              <span class="lbl">Sports</span>
              <span class="val">{{ req.sports }}</span>
            </div>
            <div class="info-row" v-if="req.level">
              <span class="lbl">Level</span>
              <span class="val">{{ req.level }}</span>
            </div>
            <div class="info-row" v-if="req.quantity">
              <span class="lbl">Quantity</span>
              <span class="val">{{ req.quantity }}</span>
            </div>
            <div class="info-row" v-if="req.team_color">
              <span class="lbl">Colors</span>
              <span class="val">{{ req.team_color }}</span>
            </div>
          </div>

          <!-- Products chips (artwork only) -->
          <div v-if="req.type === 'artwork' && req.products?.length" class="chips-row">
            <span v-for="p in req.products" :key="p" class="chip">{{ p }}</span>
          </div>

          <!-- Notes -->
          <div v-if="req.additional" class="notes-box">
            <span class="lbl">Notes: </span>{{ req.additional }}
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  requests:  { type: Array,   default: () => [] },
  isLoading: { type: Boolean, default: false },
})

const activeFilter = ref('all')

const filterTabs = [
  { label: 'All',        value: 'all' },
  { label: 'Artwork',    value: 'artwork' },
  { label: 'Membership', value: 'membership' },
]

const filteredRequests = computed(() =>
  activeFilter.value === 'all'
    ? props.requests
    : props.requests.filter(r => r.type === activeFilter.value)
)

const getCount = (f) =>
  f === 'all' ? props.requests.length : props.requests.filter(r => r.type === f).length

const formatDate = (d) => {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-PK', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.requests-page { max-width: 1100px; }
.empty-state { text-align:center; padding:3rem 1rem; background:#fff; border-radius:16px; border:2px dashed #ddd; }
.empty-icon  { font-size:3rem; margin-bottom:1rem; }
.filter-tabs { display:flex; gap:8px; flex-wrap:wrap; }
.filter-btn  { display:flex; align-items:center; gap:6px; padding:8px 18px; border-radius:999px; border:1.5px solid #ddd; background:#fff; font-size:0.85rem; font-weight:600; color:#555; cursor:pointer; transition:all 0.2s; }
.filter-btn:hover { border-color:#000; color:#000; }
.filter-btn.active { background:#000; color:#fff; border-color:#000; }
.count-badge { background:#f0f0f0; font-size:0.75rem; padding:1px 7px; border-radius:999px; }
.filter-btn.active .count-badge { background:rgba(255,255,255,0.2); color:#fff; }
.requests-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:1.25rem; }
.request-card { background:#fff; border-radius:14px; border:1px solid #e8e8e8; padding:1.25rem; box-shadow:0 2px 12px rgba(0,0,0,0.05); transition:box-shadow 0.2s; }
.request-card:hover { box-shadow:0 6px 24px rgba(0,0,0,0.1); }
.card-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
.type-badge { font-size:0.8rem; font-weight:700; padding:4px 10px; border-radius:8px; }
.type-artwork    { background:#e8f4fd; color:#1a6fa8; }
.type-membership { background:#fef3e2; color:#b45309; }
.date-text { font-size:0.75rem; color:#999; }
.info-list { display:flex; flex-direction:column; gap:6px; }
.info-row  { display:flex; justify-content:space-between; font-size:0.85rem; }
.lbl { color:#888; font-weight:500; }
.val { color:#222; font-weight:600; text-align:right; }
.chips-row { display:flex; flex-wrap:wrap; gap:5px; padding-top:0.75rem; border-top:1px solid #f0f0f0; margin-top:0.75rem; }
.chip { background:#f4f4f4; color:#444; font-size:0.75rem; padding:3px 10px; border-radius:999px; font-weight:500; }
.notes-box { margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid #f0f0f0; font-size:0.82rem; color:#555; font-style:italic; }
@media (max-width:768px) { .requests-grid { grid-template-columns:1fr; } }
</style>
