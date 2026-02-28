<template>
  <div class="props-root">

    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">My Properties</h1>
        <p class="page-sub">Manage your property listings</p>
      </div>
      <button class="add-property-btn" @click="$emit('navigate-to-list')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add New Property
      </button>
    </div>

    <!-- Filters -->
    <div class="filters-card">
      <div class="filter-group">
        <label class="filter-label">Property Type</label>
        <select v-model="propertyFilter" @change="$emit('filter-change', propertyFilter, statusFilter)" class="filter-select">
          <option value="">All Properties</option>
          <option value="house">Houses</option>
          <option value="apartment">Apartments</option>
          <option value="commercial">Commercial</option>
          <option value="plot">Plots</option>
          <option value="farmhouse">Farm Houses</option>
        </select>
      </div>
      <div class="filter-group">
        <label class="filter-label">Status</label>
        <select v-model="statusFilter" @change="$emit('filter-change', propertyFilter, statusFilter)" class="filter-select">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Empty -->
    <div v-if="properties.length === 0" class="empty-state">
      <div class="empty-icon">
        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
      <h3>No properties found</h3>
      <p>No properties match your selected filters.</p>
      <button class="cta-btn" @click="$emit('navigate-to-list')">List Your First Property</button>
    </div>

    <!-- Grid -->
    <div v-else class="properties-grid">
      <div class="prop-card" v-for="property in properties" :key="property.id">
        <div class="prop-image-wrap">
          <img
            :src="property.images && property.images.length ? property.images[0] : '/placeholder-property.jpg'"
            :alt="property.title"
            class="prop-image"
          />
          <span class="prop-badge status-badge" :class="property.is_active ? 'badge-active' : 'badge-inactive'">
            {{ property.is_active ? 'Active' : 'Inactive' }}
          </span>
          <span class="prop-badge type-badge">{{ property.property_type?.replace('_', ' ') }}</span>
        </div>

        <div class="prop-body">
          <h3 class="prop-title">{{ property.title }}</h3>
          <p class="prop-location">
            
            <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            {{ property.location }}
          </p>

          <div class="prop-stats">
            <span class="prop-price">{{ formatPrice(property.price) }}</span>
            <span class="prop-area">{{ property.area }} sq ft</span>
          </div>

          <div v-if="property.bedrooms || property.bathrooms" class="prop-amenities">
            <span v-if="property.bedrooms" class="amenity">
              <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/></svg>
              {{ property.bedrooms }} Bed
            </span>
            <span v-if="property.bathrooms" class="amenity">
              <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M9 7H7V3c0-1.1.9-2 2-2h2c1.1 0 2 .9 2 2v1h-2V3H9v4zm12 5H4V5c0-.55-.45-1-1-1s-1 .45-1 1v7c0 2.63 1.71 4.86 4.07 5.66L5.02 21H7l1-3h8l1 3h1.98l-1.05-3.34C20.29 16.86 22 14.63 22 12z"/></svg>
              {{ property.bathrooms }} Bath
            </span>
          </div>

          <div class="prop-footer">
            <span class="prop-date">{{ formatDate(property.created_at) }}</span>
            <div class="prop-actions">
              <button class="action-btn btn-edit" @click="$emit('edit-property', property)">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit
              </button>
              <button
                class="action-btn"
                :class="property.is_active ? 'btn-deactivate' : 'btn-activate'"
                @click="$emit('toggle-status', property)"
              >
                {{ property.is_active ? 'Deactivate' : 'Activate' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({ properties: { type: Array, default: () => [] } })
defineEmits(['navigate-to-list', 'filter-change', 'edit-property', 'toggle-status'])

const propertyFilter = ref('')
const statusFilter = ref('')

const formatPrice = (price) =>
  new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR', minimumFractionDigits: 0 }).format(price || 0)

const formatDate = (d) =>
  new Date(d).toLocaleDateString('en-PK', { year: 'numeric', month: 'short', day: 'numeric' })
</script>

<style scoped>
.props-root { display: flex; flex-direction: column; gap: 1.5rem; }

.page-header {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 1rem;
}
.page-title { font-size: 1.6rem; font-weight: 700; color: #111827; margin: 0; }
.page-sub { color: #6b7280; margin: 4px 0 0; font-size: 0.9rem; }

.add-property-btn {
  display: flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: white; border: none; padding: 10px 18px; border-radius: 10px;
  font-weight: 600; font-size: 0.875rem; cursor: pointer;
  box-shadow: 0 4px 12px rgba(79,70,229,0.3); transition: all 0.2s;
}
.add-property-btn:hover { transform: translateY(-1px); }

.filters-card {
  background: white; border-radius: 14px; padding: 16px 20px;
  display: flex; gap: 16px; flex-wrap: wrap;
  border: 1px solid #f3f4f6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.filter-group { display: flex; flex-direction: column; gap: 6px; min-width: 180px; }
.filter-label { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
.filter-select {
  border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px;
  font-size: 0.875rem; color: #374151; background: #fafafa; cursor: pointer;
  transition: border-color 0.2s;
}
.filter-select:focus { outline: none; border-color: #4f46e5; }

.empty-state {
  background: white; border-radius: 16px; padding: 60px 20px;
  display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center;
  border: 1px solid #f3f4f6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.empty-icon {
  width: 72px; height: 72px; border-radius: 50%; background: #f3f4f6;
  display: flex; align-items: center; justify-content: center; margin-bottom: 8px; color: #9ca3af;
}
.empty-state h3 { font-size: 1rem; font-weight: 600; color: #374151; margin: 0; }
.empty-state p { color: #9ca3af; font-size: 0.875rem; margin: 0; }
.cta-btn {
  margin-top: 12px; background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: white; border: none; padding: 10px 24px; border-radius: 10px;
  font-weight: 600; font-size: 0.875rem; cursor: pointer;
  box-shadow: 0 4px 12px rgba(79,70,229,0.3); transition: transform 0.2s;
}
.cta-btn:hover { transform: translateY(-1px); }

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.25rem;
}

.prop-card {
  background: white; border-radius: 16px; overflow: hidden;
  border: 1px solid #f3f4f6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex; flex-direction: column;
}
.prop-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,0.1); }

.prop-image-wrap { position: relative; }
.prop-image { width: 100%; height: 200px; object-fit: cover; display: block; }

.prop-badge {
  position: absolute; font-size: 0.7rem; font-weight: 700;
  padding: 3px 10px; border-radius: 20px; backdrop-filter: blur(6px);
}
.status-badge { top: 10px; left: 10px; }
.type-badge {
  top: 10px; right: 10px;
  background: rgba(79,70,229,0.85); color: white; text-transform: capitalize;
}
.badge-active { background: rgba(5,150,105,0.9); color: white; }
.badge-inactive { background: rgba(107,114,128,0.85); color: white; }

.prop-body { padding: 16px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
.prop-title { font-weight: 700; font-size: 0.95rem; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.prop-location {
  display: flex; align-items: center; gap: 4px;
  color: #9ca3af; font-size: 0.8rem; margin: 0;
}
.prop-stats { display: flex; justify-content: space-between; align-items: center; }
.prop-price { font-weight: 700; color: #059669; font-size: 1rem; }
.prop-area { color: #9ca3af; font-size: 0.78rem; }

.prop-amenities { display: flex; gap: 12px; }
.amenity {
  display: flex; align-items: center; gap: 4px;
  font-size: 0.78rem; color: #6b7280; background: #f9fafb;
  padding: 3px 8px; border-radius: 6px;
}

.prop-footer {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: auto; padding-top: 8px; border-top: 1px solid #f9fafb;
}
.prop-date { color: #d1d5db; font-size: 0.75rem; }
.prop-actions { display: flex; gap: 6px; }

.action-btn {
  font-size: 0.75rem; font-weight: 600; padding: 5px 12px;
  border-radius: 7px; cursor: pointer; display: flex;
  align-items: center; gap: 4px; border: none; transition: all 0.2s;
}
.btn-edit { background: #ede9fe; color: #4f46e5; }
.btn-edit:hover { background: #4f46e5; color: white; }
.btn-deactivate { background: #fff1f2; color: #e11d48; }
.btn-deactivate:hover { background: #e11d48; color: white; }
.btn-activate { background: #d1fae5; color: #059669; }
.btn-activate:hover { background: #059669; color: white; }
</style>
