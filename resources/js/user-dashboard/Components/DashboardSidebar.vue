<template>
  <aside class="dash-sidebar" :class="{ collapsed: isMobileOpen }">
    <!-- Brand Header -->
    <div class="sidebar-header">
      <div class="user-avatar">{{ userInitials }}</div>
      <div class="user-info">
        <span class="user-name">{{ user.name || 'User' }}</span>
        <span class="user-email">{{ user.email || 'user@example.com' }}</span>
      </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
      <button
        v-for="item in navItems"
        :key="item.tab"
        class="nav-btn"
        :class="{ active: activeTab === item.tab }"
        @click="$emit('tab-change', item.tab)"
      >
        <span class="nav-icon" v-html="item.icon"></span>
        <span class="nav-label">{{ item.label }}</span>
        <span v-if="item.badge !== undefined" class="nav-badge">{{ item.badge }}</span>
      </button>
    </nav>

    <!-- Logout -->
    <div class="sidebar-footer">
      <button class="logout-btn" :disabled="isLoggingOut" @click="$emit('logout')">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
        </svg>
        <span>{{ isLoggingOut ? 'Logging out...' : 'Logout' }}</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  user: Object,
  activeTab: String,
  dashboardStats: Object,
  isLoggingOut: Boolean
})
defineEmits(['tab-change', 'logout'])

const userInitials = computed(() => {
  if (!props.user?.name) return 'U'
  return props.user.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const navItems = computed(() => [
  {
    tab: 'overview',
    label: 'Overview',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>`
  },
  {
    tab: 'properties',
    label: 'My Properties',
    badge: props.dashboardStats?.total_properties ?? 0,
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`
  },
  {
    tab: 'profile',
    label: 'Profile Settings',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>`
  },
  {
    tab: 'analytics',
    label: 'Analytics',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>`
  }
])
</script>

<style scoped>
.dash-sidebar {
  width: 260px;
  min-height: calc(100vh - 64px);
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 64px;
  height: calc(100vh - 64px);
  box-shadow: 2px 0 12px rgba(0,0,0,0.04);
}

.sidebar-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 20px 20px 16px;
  border-bottom: 1px solid #f3f4f6;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.user-avatar {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255,255,255,0.25);
  backdrop-filter: blur(10px);
  color: white;
  font-weight: 700;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid rgba(255,255,255,0.3);
  flex-shrink: 0;
}

.user-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.user-name {
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.user-email {
  color: rgba(255,255,255,0.75);
  font-size: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  overflow-y: auto;
}

.nav-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 10px;
  border: none;
  background: transparent;
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
  width: 100%;
  position: relative;
}
.nav-btn:hover {
  background: #f3f4f6;
  color: #111827;
}
.nav-btn.active {
  background: linear-gradient(135deg, #ede9fe, #e0e7ff);
  color: #4f46e5;
  font-weight: 600;
}
.nav-icon {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}
.nav-label { flex: 1; }
.nav-badge {
  background: #4f46e5;
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 1px 7px;
  border-radius: 20px;
  min-width: 20px;
  text-align: center;
}
.nav-btn.active .nav-badge { background: #4f46e5; }

.sidebar-footer {
  padding: 16px 12px;
  border-top: 1px solid #f3f4f6;
}

.logout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 10px 14px;
  border-radius: 10px;
  border: 1.5px solid #fecaca;
  background: #fff5f5;
  color: #dc2626;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}
.logout-btn:hover:not(:disabled) {
  background: #dc2626;
  border-color: #dc2626;
  color: white;
}
.logout-btn:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
