<template>
  <div class="profile-root">

    <!-- Header -->
    <div class="page-header">
      <h1 class="page-title">Profile Settings</h1>
      <p class="page-sub">Manage your account information</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-wrap">
      <span class="spinner-lg"></span>
      <p>Loading profile...</p>
    </div>

    <template v-else>
      <!-- Hero Card -->
      <div class="profile-hero-card">
        <div class="avatar-wrap">
          <div class="avatar">{{ userInitials }}</div>
          <div class="avatar-ring"></div>
        </div>
        <div class="hero-text">
          <h2 class="hero-name">{{ user.name || 'Welcome!' }}</h2>
          <p class="hero-email">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
            </svg>
            {{ user.email || 'No email found' }}
          </p>
          <span class="hero-badge">
            <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            Verified User
          </span>
        </div>
      </div>

      <!-- Info Cards Row — Name, Email, Password only -->
      <div class="info-cards-row">
        <div class="info-card">
          <div class="info-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div>
            <p class="info-label">Full Name</p>
            <p class="info-value">{{ user.name || '—' }}</p>
          </div>
        </div>

        <div class="info-card">
          <div class="info-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
            </svg>
          </div>
          <div>
            <p class="info-label">Email Address</p>
            <p class="info-value">{{ user.email || '—' }}</p>
          </div>
        </div>

        <div class="info-card">
          <div class="info-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </div>
          <div>
            <p class="info-label">Password</p>
            <p class="info-value">••••••••</p>
          </div>
        </div>
      </div>

      <!-- Tab Switcher -->
      <div class="tab-row">
        <button class="tab-btn" :class="{ active: activeTab === 'profile' }" @click="activeTab = 'profile'">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
          Edit Profile
        </button>
        <button class="tab-btn" :class="{ active: activeTab === 'password' }" @click="activeTab = 'password'">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          Change Password
        </button>
      </div>

      <!-- Alert Message -->
      <transition name="fade">
        <div v-if="alert.show" :class="['alert', alert.type]">
          <svg v-if="alert.type === 'success'" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <svg v-else width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ alert.message }}
        </div>
      </transition>

      <!-- ====== EDIT PROFILE TAB ====== -->
      <div v-if="activeTab === 'profile'" class="form-card">
        <h3 class="form-section-title">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          Edit Personal Information
        </h3>
        <form @submit.prevent="handleUpdateProfile" class="profile-form">
          <div class="form-grid">

            <div class="field-group">
              <label class="field-label">Full Name</label>
              <div class="input-wrap">
                <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                <input v-model="profileForm.name" type="text" class="field-input" placeholder="Your full name" required />
              </div>
            </div>

            <div class="field-group">
              <label class="field-label">Email Address</label>
              <div class="input-wrap">
                <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                </svg>
                <input v-model="profileForm.email" type="email" class="field-input" placeholder="your.email@example.com" required />
              </div>
            </div>

          </div>
          <div class="form-footer">
            <button type="submit" class="save-btn" :disabled="isUpdating">
              <template v-if="isUpdating">
                <span class="spinner"></span> Updating...
              </template>
              <template v-else>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                Save Changes
              </template>
            </button>
          </div>
        </form>
      </div>

      <!-- ====== CHANGE PASSWORD TAB ====== -->
      <div v-if="activeTab === 'password'" class="form-card">
        <h3 class="form-section-title">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          Change Password
        </h3>
        <form @submit.prevent="handleChangePassword" class="profile-form">
          <div class="form-grid-single">

            <!-- Current Password -->
            <div class="field-group">
              <label class="field-label">Current Password</label>
              <div class="input-wrap">
                <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input
                  v-model="passwordForm.current_password"
                  :type="showCurrent ? 'text' : 'password'"
                  class="field-input"
                  placeholder="Enter current password"
                  required
                />
                <button type="button" class="eye-btn" @click="showCurrent = !showCurrent">
                  <svg v-if="!showCurrent" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- New Password -->
            <div class="field-group">
              <label class="field-label">New Password</label>
              <div class="input-wrap">
                <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input
                  v-model="passwordForm.new_password"
                  :type="showNew ? 'text' : 'password'"
                  class="field-input"
                  placeholder="Min 6 characters"
                  required
                  minlength="6"
                />
                <button type="button" class="eye-btn" @click="showNew = !showNew">
                  <svg v-if="!showNew" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
              <!-- Strength Bar -->
              <div class="strength-wrap" v-if="passwordForm.new_password">
                <div class="strength-bar">
                  <div class="strength-fill" :style="{ width: strengthPercent + '%' }"></div>
                </div>
                <span class="strength-label">{{ strengthLabel }}</span>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="field-group">
              <label class="field-label">Confirm New Password</label>
              <div class="input-wrap">
                <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input
                  v-model="passwordForm.new_password_confirmation"
                  :type="showConfirm ? 'text' : 'password'"
                  class="field-input"
                  :class="{ 'input-error': passwordForm.new_password_confirmation && passwordForm.new_password !== passwordForm.new_password_confirmation }"
                  placeholder="Re-enter new password"
                  required
                />
                <button type="button" class="eye-btn" @click="showConfirm = !showConfirm">
                  <svg v-if="!showConfirm" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
              <p v-if="passwordForm.new_password_confirmation && passwordForm.new_password !== passwordForm.new_password_confirmation" class="match-error">
                ✗ Passwords do not match
              </p>
            </div>

          </div>
          <div class="form-footer">
            <button
              type="submit"
              class="save-btn"
              :disabled="isChangingPassword || (passwordForm.new_password_confirmation && passwordForm.new_password !== passwordForm.new_password_confirmation)"
            >
              <template v-if="isChangingPassword">
                <span class="spinner"></span> Updating...
              </template>
              <template v-else>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Update Password
              </template>
            </button>
          </div>
        </form>
      </div>

    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// ─── State ───────────────────────────────────────────────
const user               = ref({})
const loading            = ref(true)
const isUpdating         = ref(false)
const isChangingPassword = ref(false)
const activeTab          = ref('profile')

const showCurrent = ref(false)
const showNew     = ref(false)
const showConfirm = ref(false)

const alert = ref({ show: false, type: 'success', message: '' })

const profileForm  = ref({ name: '', email: '' })
const passwordForm = ref({ current_password: '', new_password: '', new_password_confirmation: '' })

// ─── Helpers ─────────────────────────────────────────────
const token = () => localStorage.getItem('auth_token')

const showAlert = (type, message) => {
  alert.value = { show: true, type, message }
  setTimeout(() => { alert.value.show = false }, 4000)
}

// ─── Computed ────────────────────────────────────────────
const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  return user.value.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const strengthPercent = computed(() => {
  const p = passwordForm.value.new_password
  if (!p) return 0
  let score = 0
  if (p.length >= 6)  score += 25
  if (p.length >= 10) score += 25
  if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score += 25
  if (/[0-9]/.test(p) && /[^A-Za-z0-9]/.test(p)) score += 25
  return score
})

const strengthLabel = computed(() => {
  const s = strengthPercent.value
  if (s <= 25) return 'Weak'
  if (s <= 50) return 'Fair'
  if (s <= 75) return 'Good'
  return 'Strong'
})

// ─── API ─────────────────────────────────────────────────
const fetchProfile = async () => {
  loading.value = true
  try {
    const res  = await fetch('/api/user/profile', {
      headers: { 'Authorization': `Bearer ${token()}`, 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (data.status) {
      user.value    = data.data
      profileForm.value = { name: data.data.name || '', email: data.data.email || '' }
    }
  } catch {
    showAlert('error', 'Failed to load profile.')
  } finally {
    loading.value = false
  }
}

const handleUpdateProfile = async () => {
  isUpdating.value = true
  try {
    const res  = await fetch('/api/profile', {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token()}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(profileForm.value)
    })
    const data = await res.json()
    if (data.status) {
      user.value = { ...user.value, ...profileForm.value }
      showAlert('success', 'Profile updated successfully!')
    } else {
      showAlert('error', data.message || 'Update failed.')
    }
  } catch {
    showAlert('error', 'Something went wrong.')
  } finally {
    isUpdating.value = false
  }
}

const handleChangePassword = async () => {
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    showAlert('error', 'Passwords do not match.')
    return
  }
  isChangingPassword.value = true
  try {
    const res  = await fetch('/api/user/change-password', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token()}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(passwordForm.value)
    })
    const data = await res.json()
    if (data.status) {
      showAlert('success', 'Password changed successfully!')
      passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' }
    } else {
      showAlert('error', data.message || 'Password change failed.')
    }
  } catch {
    showAlert('error', 'Something went wrong.')
  } finally {
    isChangingPassword.value = false
  }
}

onMounted(fetchProfile)
</script>

<style scoped>
/* ── Root ── */
.profile-root { display: flex; flex-direction: column; gap: 1.5rem; }

/* ── Header ── */
.page-title { font-size: 1.6rem; font-weight: 700; color: #0a0a0a; margin: 0; letter-spacing: -0.02em; }
.page-sub   { color: #6b6b6b; margin: 4px 0 0; font-size: 0.875rem; }

/* ── Loading ── */
.loading-wrap {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; gap: 12px; padding: 60px;
  color: #6b6b6b; font-size: 0.9rem;
}
.spinner-lg {
  width: 36px; height: 36px; border-radius: 50%;
  border: 3px solid #e0e0e0; border-top-color: #0a0a0a;
  animation: spin 0.8s linear infinite;
}

/* ── Hero Card ── */
.profile-hero-card {
  background: #0a0a0a;
  border-radius: 16px; padding: 28px 32px;
  display: flex; align-items: center; gap: 24px; color: #ffffff;
  position: relative; overflow: hidden;
}
.profile-hero-card::before {
  content: ''; position: absolute; top: -50px; right: -50px;
  width: 200px; height: 200px; border-radius: 50%;
  background: rgba(255,255,255,0.04); pointer-events: none;
}
.profile-hero-card::after {
  content: ''; position: absolute; bottom: -70px; right: 100px;
  width: 260px; height: 260px; border-radius: 50%;
  background: rgba(255,255,255,0.03); pointer-events: none;
}

.avatar-wrap { position: relative; flex-shrink: 0; }
.avatar {
  width: 72px; height: 72px; border-radius: 50%;
  background: rgba(255,255,255,0.12);
  border: 2px solid rgba(255,255,255,0.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem; font-weight: 700; color: white; letter-spacing: -0.02em;
}
.avatar-ring {
  position: absolute; inset: -5px; border-radius: 50%;
  border: 1.5px dashed rgba(255,255,255,0.18);
  animation: spin 10s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.hero-text  { display: flex; flex-direction: column; gap: 5px; }
.hero-name  { font-size: 1.25rem; font-weight: 700; margin: 0; letter-spacing: -0.02em; }
.hero-email { font-size: 0.825rem; color: rgba(255,255,255,0.6); margin: 0; display: flex; align-items: center; gap: 5px; }
.hero-badge {
  display: inline-flex; align-items: center; gap: 5px;
  background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.18);
  border-radius: 20px; font-size: 0.7rem; font-weight: 600;
  padding: 3px 10px; margin-top: 2px; width: fit-content; color: rgba(255,255,255,0.8);
}

/* ── Info Cards ── */
.info-cards-row {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px;
}
.info-card {
  background: #ffffff; border: 1.5px solid #e8e8e8; border-radius: 12px;
  padding: 16px; display: flex; align-items: center; gap: 12px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05); transition: border-color 0.2s, box-shadow 0.2s;
}
.info-card:hover { border-color: #b0b0b0; box-shadow: 0 3px 10px rgba(0,0,0,0.08); }
.info-icon {
  width: 38px; height: 38px; border-radius: 10px;
  background: #f2f2f2; color: #0a0a0a;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.info-label {
  font-size: 0.68rem; color: #9a9a9a; font-weight: 600;
  text-transform: uppercase; letter-spacing: 0.06em; margin: 0 0 3px;
}
.info-value {
  font-size: 0.875rem; color: #0a0a0a; font-weight: 600; margin: 0;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px;
}

/* ── Tabs ── */
.tab-row { display: flex; gap: 8px; }
.tab-btn {
  display: flex; align-items: center; gap: 7px;
  padding: 9px 18px; border-radius: 10px; font-size: 0.855rem;
  font-weight: 600; cursor: pointer;
  border: 1.5px solid #e0e0e0; background: #ffffff; color: #6b6b6b;
  transition: all 0.2s;
}
.tab-btn:hover { border-color: #0a0a0a; color: #0a0a0a; }
.tab-btn.active {
  background: #0a0a0a; color: #ffffff; border-color: #0a0a0a;
  box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}

/* ── Alert ── */
.alert {
  display: flex; align-items: center; gap: 8px;
  padding: 12px 16px; border-radius: 10px; font-size: 0.875rem; font-weight: 500;
}
.alert.success { background: #f4f4f4; color: #1a1a1a; border: 1.5px solid #d0d0d0; }
.alert.error   { background: #fff0f0; color: #991b1b; border: 1.5px solid #fca5a5; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* ── Form Card ── */
.form-card {
  background: #ffffff; border-radius: 16px; padding: 24px 28px;
  border: 1.5px solid #e8e8e8; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.form-section-title {
  display: flex; align-items: center; gap: 8px;
  font-size: 0.975rem; font-weight: 700; color: #0a0a0a;
  margin: 0 0 20px; padding-bottom: 14px; border-bottom: 1.5px solid #f0f0f0;
  letter-spacing: -0.01em;
}

.form-grid        { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
.form-grid-single { display: grid; grid-template-columns: 1fr; gap: 16px; max-width: 460px; }

.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 0.72rem; font-weight: 700; color: #4a4a4a; text-transform: uppercase; letter-spacing: 0.05em; }
.input-wrap  { position: relative; }

.field-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  color: #b0b0b0; pointer-events: none;
}
.eye-btn {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; color: #b0b0b0;
  padding: 0; display: flex; transition: color 0.15s;
}
.eye-btn:hover { color: #0a0a0a; }

.field-input {
  width: 100%; padding: 10px 38px 10px 38px;
  border: 1.5px solid #e0e0e0; border-radius: 10px;
  font-size: 0.875rem; color: #0a0a0a; background: #fafafa;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  box-sizing: border-box;
}
.field-input:focus {
  outline: none; border-color: #0a0a0a;
  box-shadow: 0 0 0 3px rgba(10,10,10,0.08); background: #ffffff;
}
.field-input::placeholder { color: #c8c8c8; }
.field-input.input-error  { border-color: #f87171; }

/* ── Password Strength ── */
.strength-wrap  { display: flex; align-items: center; gap: 8px; margin-top: 5px; }
.strength-bar   { flex: 1; height: 4px; background: #ebebeb; border-radius: 99px; overflow: hidden; }
.strength-fill  { height: 100%; border-radius: 99px; background: #0a0a0a; transition: width 0.35s ease; }
.strength-label { font-size: 0.7rem; font-weight: 700; color: #4a4a4a; min-width: 40px; }
.match-error    { font-size: 0.75rem; color: #ef4444; margin: 0; }

/* ── Footer & Button ── */
.form-footer { margin-top: 22px; display: flex; justify-content: flex-end; }
.save-btn {
  display: flex; align-items: center; gap: 8px;
  background: #0a0a0a; color: #ffffff; border: none; padding: 11px 24px;
  border-radius: 10px; font-weight: 700; font-size: 0.855rem;
  cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,0.15);
  transition: all 0.2s; letter-spacing: 0.01em;
}
.save-btn:hover:not(:disabled) {
  background: #1f1f1f; transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.22);
}
.save-btn:active:not(:disabled) { transform: translateY(0); }
.save-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.spinner {
  width: 15px; height: 15px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.3); border-top-color: white;
  animation: spin 0.7s linear infinite;
}
</style>
