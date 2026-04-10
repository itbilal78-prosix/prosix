<template>
  <header class="dashboard-header">

    <!-- Left: Menu toggle -->
    <div class="header-left">
      <button class="icon-btn" @click="$emit('toggle-sidebar')">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
    </div>

    <!-- Center: 3D Flip Logo -->
   <div class="header-center">
  <a href="/">
    <div class="logo-scene">
      <div class="logo-cube">
        <div class="logo-face logo-front">
          <img src="/public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png" class="logo-img" />
        </div>
        <div class="logo-face logo-back">
          <img src="/public/assets/images/P LOGO WHITE.png" class="logo-img" />
        </div>
      </div>
    </div>
  </a>
</div>

    <!-- Right: Profile -->
    <div class="header-right">
      <div class="user-wrap" v-click-outside="closeDropdown">

        <button class="profile-btn" @click="dropdownOpen = !dropdownOpen">
          <div class="avatar-circle">{{ userInitials }}</div>
          <span class="profile-name">{{ user?.name || 'User' }}</span>
          <svg class="chevron-icon" :class="{ open: dropdownOpen }"
            width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M6 9l6 6 6-6"/>
          </svg>
        </button>

        <transition name="drop">
          <div v-if="dropdownOpen" class="dropdown-box">

            <!-- Top user info -->
            <div class="drop-top">
              <div class="drop-avatar">{{ userInitials }}</div>
              <div class="drop-info">
                <div class="drop-name">{{ user?.name || 'User' }}</div>
                <div class="drop-email">{{ user?.email }}</div>
              </div>
              <span class="online-badge"></span>
            </div>

            <div class="drop-line"></div>

            <!-- Profile Settings -->
            <div class="drop-body">
              <button class="drop-item" @click="goProfile">
                <div class="drop-item-icon">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                </div>
                <span>Profile Settings</span>
                <svg class="drop-arrow" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M9 18l6-6-6-6"/>
                </svg>
              </button>
            </div>

            <div class="drop-line"></div>

            <!-- Logout -->
            <div class="drop-body">
              <button class="drop-item drop-logout" @click="handleLogout">
                <div class="drop-item-icon logout-icon">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                  </svg>
                </div>
                <span>Logout</span>
              </button>
            </div>

          </div>
        </transition>

      </div>
    </div>

  </header>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  user: Object
})

const emit = defineEmits(['logout', 'toggle-sidebar', 'go-profile'])

const dropdownOpen = ref(false)

const userInitials = computed(() => {
  if (!props.user?.name) return 'U'
  return props.user.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

function closeDropdown() {
  dropdownOpen.value = false
}

function goProfile() {
  dropdownOpen.value = false
  emit('go-profile')
}

function handleLogout() {
  dropdownOpen.value = false
  emit('logout')
}
</script>

<style scoped>
.dashboard-header {
  position: fixed;
  top: 0; left: 0; right: 0;
  height: 62px;
  background: #000;
  border-bottom: 1px solid #1a1a1a;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  z-index: 300;
}

/* ── Left ── */
.header-left { display: flex; align-items: center; }

.icon-btn {
  background: none;
  border: 1px solid #2a2a2a;
  color: #666;
  width: 38px; height: 38px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s;
}
.icon-btn:hover { border-color: #fff; color: #fff; background: #111; }

/* ── Center Logo ── */
.header-center {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
}

.logo-scene {
  width: 130px;
  height: 44px;
  perspective: 800px;
}

.logo-cube {
  width: 100%;
  height: 100%;
  position: relative;
  transform-style: preserve-3d;
  animation: flipLogo 5s ease-in-out infinite;
}

@keyframes flipLogo {
  0%    { transform: rotateY(0deg); }
  38%   { transform: rotateY(0deg); }
  50%   { transform: rotateY(180deg); }
  88%   { transform: rotateY(180deg); }
  100%  { transform: rotateY(360deg); }
}

.logo-face {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  backface-visibility: hidden;
}

.logo-front { transform: rotateY(0deg); }
.logo-back  { transform: rotateY(180deg); }

.logo-img {
  height: 36px;
  width: auto;
  object-fit: contain;
}

.logo-img-full {
  height: 30px;
}

/* ── Right ── */
.header-right { display: flex; align-items: center; }

.user-wrap { position: relative; }

.profile-btn {
  display: flex;
  align-items: center;
  gap: 9px;
  background: #0d0d0d;
  border: 1px solid #2a2a2a;
  border-radius: 10px;
  padding: 5px 11px 5px 5px;
  cursor: pointer;
  transition: all 0.2s;
  height: 40px;
}
.profile-btn:hover { border-color: #3a3a3a; background: #151515; }

.avatar-circle {
  width: 28px; height: 28px;
  border-radius: 7px;
  background: #fff;
  color: #000;
  font-size: 10px;
  font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}

.profile-name {
  color: #ccc;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  max-width: 90px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.chevron-icon {
  color: #444;
  transition: transform 0.25s ease;
  flex-shrink: 0;
}
.chevron-icon.open { transform: rotate(180deg); }

/* ── Dropdown ── */
.dropdown-box {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  background: #0a0a0a;
  border: 1px solid #1e1e1e;
  border-radius: 14px;
  min-width: 230px;
  z-index: 500;
  overflow: hidden;
  box-shadow: 0 24px 60px rgba(0,0,0,0.7);
}

.drop-top {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 14px 16px;
  position: relative;
}

.drop-avatar {
  width: 38px; height: 38px;
  border-radius: 10px;
  background: #fff;
  color: #000;
  font-size: 13px;
  font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}

.drop-info { flex: 1; min-width: 0; }

.drop-name {
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ── Email WHITE ── */
.drop-email {
  color: #fff;
  font-size: 11px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 2px;
  opacity: 0.55;
}

.online-badge {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #22c55e;
  border: 2px solid #0a0a0a;
  flex-shrink: 0;
}

.drop-line { height: 1px; background: #181818; }

.drop-body { padding: 5px; }

.drop-item {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 9px 10px;
  border-radius: 8px;
  border: none;
  background: none;
  color: #999;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.18s;
  text-align: left;
}
.drop-item:hover { background: #141414; color: #fff; }

.drop-item-icon {
  width: 28px; height: 28px;
  border-radius: 7px;
  background: #141414;
  border: 1px solid #222;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: all 0.18s;
}
.drop-item:hover .drop-item-icon { background: #1e1e1e; border-color: #2e2e2e; }

.drop-arrow {
  margin-left: auto;
  color: #2a2a2a;
  transition: all 0.18s;
}
.drop-item:hover .drop-arrow { color: #555; transform: translateX(2px); }

.drop-logout { color: #e24b4a; }
.drop-logout:hover { background: rgba(226,75,74,0.07); color: #ff6b6b; }
.logout-icon {
  background: rgba(226,75,74,0.06);
  border-color: rgba(226,75,74,0.15);
}
.drop-logout:hover .logout-icon {
  background: rgba(226,75,74,0.12);
  border-color: rgba(226,75,74,0.25);
}

.drop-enter-active { transition: opacity 0.18s ease, transform 0.18s ease; }
.drop-leave-active { transition: opacity 0.13s ease, transform 0.13s ease; }
.drop-enter-from  { opacity: 0; transform: translateY(-8px) scale(0.96); }
.drop-leave-to    { opacity: 0; transform: translateY(-5px) scale(0.97); }
</style>
