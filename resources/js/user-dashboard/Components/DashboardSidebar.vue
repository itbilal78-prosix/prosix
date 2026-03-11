<template>
  <aside class="dash-sidebar">

    <div class="back-to-site">
      <router-link to="/" class="back-btn">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back
      </router-link>
    </div>

    <div class="sidebar-header">
      <div class="user-avatar">{{ userInitials }}</div>
      <div class="user-info">
        <span class="user-name">{{ user.name || 'User' }}</span>
        <span class="user-email">{{ user.email || 'user@example.com' }}</span>
      </div>
    </div>

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
      </button>
    </nav>

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
    tab: 'profile',
    label: 'Profile Settings',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>`
  },
  // ✅ NAYA — My Requests tab
  {
    tab: 'my-requests',
    label: 'My Requests',
    icon: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>`
  },
])
</script>

<style scoped>
.dash-sidebar { width:260px; height:100vh; position:fixed; top:0; left:0; z-index:200; display:flex; flex-direction:column; overflow:hidden; background:#0a0a0a; border-right:1px solid #1f1f1f; box-shadow:2px 0 16px rgba(0,0,0,0.4); }
.back-to-site { padding:12px; border-bottom:1px solid #1f1f1f; flex-shrink:0; }
.back-btn { display:flex; align-items:center; gap:8px; color:rgba(255,255,255,0.75); text-decoration:none; font-size:0.82rem; font-weight:600; padding:9px 12px; border-radius:8px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); transition:all 0.2s; width:100%; box-sizing:border-box; }
.back-btn:hover { background:rgba(255,255,255,0.13); color:#fff; border-color:rgba(255,255,255,0.22); }
.sidebar-header { display:flex; align-items:center; gap:12px; padding:20px 20px 16px; border-bottom:1px solid #1f1f1f; background:#0a0a0a; flex-shrink:0; }
.user-avatar { width:44px; height:44px; border-radius:12px; background:#ffffff; color:#000000; font-weight:700; font-size:1rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:2px solid rgba(255,255,255,0.15); }
.user-info { display:flex; flex-direction:column; min-width:0; }
.user-name { color:#ffffff; font-weight:600; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.user-email { color:rgba(255,255,255,0.45); font-size:0.72rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sidebar-nav { flex:1; min-height:0; overflow-y:auto; padding:16px 12px; display:flex; flex-direction:column; gap:4px; }
.sidebar-nav::-webkit-scrollbar { width:3px; }
.sidebar-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.1); border-radius:99px; }
.nav-btn { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; border:none; background:transparent; color:rgba(255,255,255,0.5); font-size:0.875rem; font-weight:500; cursor:pointer; transition:all 0.2s ease; text-align:left; width:100%; flex-shrink:0; }
.nav-btn:hover { background:rgba(255,255,255,0.07); color:#ffffff; }
.nav-btn.active { background:#ffffff; color:#000000; font-weight:700; }
.nav-icon { display:flex; align-items:center; flex-shrink:0; }
.nav-label { flex:1; }
.sidebar-footer { padding:16px 12px; border-top:1px solid #1f1f1f; flex-shrink:0; }
.logout-btn { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid rgba(255,255,255,0.2); background:transparent; color:#ffffff; font-size:0.875rem; font-weight:600; cursor:pointer; transition:all 0.2s ease; }
.logout-btn:hover:not(:disabled) { background:rgba(255,255,255,0.1); border-color:rgba(255,255,255,0.35); }
.logout-btn:disabled { opacity:0.5; cursor:not-allowed; }
</style>
