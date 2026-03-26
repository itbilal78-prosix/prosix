<template>
  <aside class="dash-sidebar" :class="{ collapsed: isCollapsed }">

    <!-- Back to site -->
    <div class="back-to-site">
      <router-link to="/" class="back-btn">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        <span class="back-label">Back to Site</span>
      </router-link>
    </div>

    <!-- User Info -->
    <div class="sidebar-header">
      <div class="user-avatar">{{ userInitials }}</div>
      <div class="user-info">
        <span class="user-name">{{ user?.name || 'User' }}</span>
        <span class="user-email">{{ user?.email || 'user@example.com' }}</span>
      </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
      <button
        v-for="item in navItems"
        :key="item.tab"
        class="nav-btn"
        :class="{ active: activeTab === item.tab }"
        :title="isCollapsed ? item.label : ''"
        @click="$emit('tab-change', item.tab)"
      >
        <span class="nav-icon" v-html="item.icon"></span>
        <span class="nav-label">{{ item.label }}</span>
      </button>
    </nav>

    <!-- Footer Logout -->
    <div class="sidebar-footer">
      <button class="logout-btn" :disabled="isLoggingOut" @click="$emit('logout')">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
        </svg>
        <span class="nav-label">{{ isLoggingOut ? 'Logging out...' : 'Logout' }}</span>
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
  isLoggingOut: Boolean,
  isCollapsed: Boolean
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
    tab: 'profile',
    label: 'Profile Settings',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>`
  },
  {
    tab: 'my-requests',
    label: 'My Requests',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>`
  },
  {
    tab: 'my-place-orders',
    label: 'Place Orders',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>`
  },
  {
    tab: 'my-orders',
    label: 'My Orders',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/></svg>`
  },
  {
    tab: 'my-design',
    label: 'My Design',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 3l3 3-9 9-3-3 9-9z"/><path d="M15 6l3 3"/><path d="M2 22s4-1 6-3 3-6 3-6"/></svg>`
  }
])
</script>

<style scoped>
.dash-sidebar {
  width: 260px;
  height: 100vh;
  position: fixed;
  top: 0; left: 0;
  z-index: 200;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: #0a0a0a;
  border-right: 1px solid #1f1f1f;
  box-shadow: 2px 0 16px rgba(0,0,0,0.4);
  transition: width 0.3s ease;
}

/* ── COLLAPSED ── */
.dash-sidebar.collapsed { width: 60px; }

.dash-sidebar.collapsed .back-label,
.dash-sidebar.collapsed .nav-label,
.dash-sidebar.collapsed .user-info { display: none; }

.dash-sidebar.collapsed .back-btn {
  justify-content: center;
  padding: 9px;
  gap: 0;
}
.dash-sidebar.collapsed .nav-btn {
  justify-content: center;
  padding: 10px 0;
  gap: 0;
}
.dash-sidebar.collapsed .logout-btn {
  justify-content: center;
  padding: 10px 0;
  gap: 0;
}
.dash-sidebar.collapsed .sidebar-header {
  justify-content: center;
  padding: 14px 8px;
  gap: 0;
}

/* ── Back to Site ── */
.back-to-site {
  padding: 12px;
  border-bottom: 1px solid #1f1f1f;
  flex-shrink: 0;
  margin-top: 62px; /* header height ke barabar */
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  color: rgba(255,255,255,0.75);
  text-decoration: none;
  font-size: 0.82rem;
  font-weight: 600;
  padding: 9px 12px;
  border-radius: 8px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  transition: all 0.2s;
  width: 100%;
  box-sizing: border-box;
}
.back-btn:hover {
  background: rgba(255,255,255,0.13);
  color: #fff;
  border-color: rgba(255,255,255,0.22);
}

/* ── Sidebar Header (User Info) ── */
.sidebar-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  border-bottom: 1px solid #1f1f1f;
  background: #0a0a0a;
  flex-shrink: 0;
  transition: all 0.3s ease;
  overflow: hidden;
}

.user-avatar {
  width: 40px; height: 40px;
  border-radius: 10px;
  background: #fff;
  color: #000;
  font-weight: 700;
  font-size: 1rem;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  border: 2px solid rgba(255,255,255,0.15);
}

.user-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
  overflow: hidden;
}

.user-name {
  color: #fff;
  font-weight: 600;
  font-size: 0.875rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-email {
  color: rgba(255,255,255,0.4);
  font-size: 0.7rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ── Nav ── */
.sidebar-nav {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  padding: 12px 10px;
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.sidebar-nav::-webkit-scrollbar { width: 3px; }
.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 99px; }

.nav-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 10px;
  border: none;
  background: transparent;
  color: rgba(255,255,255,0.45);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
  width: 100%;
  flex-shrink: 0;
  white-space: nowrap;
  overflow: hidden;
}
.nav-btn:hover { background: rgba(255,255,255,0.07); color: #fff; }
.nav-btn.active { background: #fff; color: #000; font-weight: 700; }

.nav-icon {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.nav-label { overflow: hidden; text-overflow: ellipsis; }

/* ── Footer ── */
.sidebar-footer {
  padding: 12px 10px;
  border-top: 1px solid #1f1f1f;
  flex-shrink: 0;
}

.logout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 10px 14px;
  border-radius: 10px;
  border: 1.5px solid rgba(255,255,255,0.15);
  background: transparent;
  color: #fff;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  overflow: hidden;
}
.logout-btn:hover:not(:disabled) {
  background: rgba(255,255,255,0.08);
  border-color: rgba(255,255,255,0.3);
}
.logout-btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
