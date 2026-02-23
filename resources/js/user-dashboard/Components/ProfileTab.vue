<template>
  <div class="profile-root">

    <div class="page-header">
      <div>
        <h1 class="page-title">Profile Settings</h1>
        <p class="page-sub">Manage your account information</p>
      </div>
    </div>

    <!-- Avatar + Info Card -->
    <div class="profile-hero-card">
      <div class="avatar-wrap">
        <div class="avatar">{{ userInitials }}</div>
        <div class="avatar-ring"></div>
      </div>
      <div>
        <h2 class="hero-name">{{ user.name || 'User' }}</h2>
        <p class="hero-email">{{ user.email || '' }}</p>
        <span class="hero-badge">Property Owner</span>
      </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
      <h3 class="form-section-title">Personal Information</h3>
      <form @submit.prevent="handleUpdateProfile" class="profile-form">

        <div class="form-grid">
          <div class="field-group">
            <label class="field-label">Full Name</label>
            <div class="input-wrap">
              <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
              </svg>
              <input v-model="form.name" type="text" class="field-input" placeholder="Your full name" />
            </div>
          </div>

          <div class="field-group">
            <label class="field-label">Email Address</label>
            <div class="input-wrap">
              <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
              </svg>
              <input v-model="form.email" type="email" class="field-input" placeholder="your.email@example.com" />
            </div>
          </div>

          <div class="field-group">
            <label class="field-label">Phone Number</label>
            <div class="input-wrap">
              <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.62 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 17z"/>
              </svg>
              <input v-model="form.phone" type="tel" class="field-input" placeholder="+92 300 1234567" />
            </div>
          </div>

          <div class="field-group">
            <label class="field-label">Location</label>
            <div class="input-wrap">
              <svg class="field-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
              </svg>
              <input v-model="form.location" type="text" class="field-input" placeholder="Kasur, Punjab" />
            </div>
          </div>
        </div>

        <div class="form-footer">
          <button type="submit" class="save-btn" :disabled="isUpdatingProfile">
            <template v-if="isUpdatingProfile">
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

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  isUpdatingProfile: { type: Boolean, default: false }
})
const emit = defineEmits(['update-profile'])

const form = ref({ name: '', email: '', phone: '', location: '' })

const userInitials = computed(() => {
  if (!props.user?.name) return 'U'
  return props.user.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

watch(() => props.user, (u) => {
  if (u && Object.keys(u).length) {
    form.value = { name: u.name || '', email: u.email || '', phone: u.phone || '', location: u.location || '' }
  }
}, { immediate: true, deep: true })

const handleUpdateProfile = () => emit('update-profile', { ...form.value })
</script>

<style scoped>
.profile-root { display: flex; flex-direction: column; gap: 1.5rem; }

.page-header { margin-bottom: 0; }
.page-title { font-size: 1.6rem; font-weight: 700; color: #111827; margin: 0; }
.page-sub { color: #6b7280; margin: 4px 0 0; font-size: 0.9rem; }

.profile-hero-card {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  border-radius: 16px;
  padding: 28px 32px;
  display: flex;
  align-items: center;
  gap: 24px;
  color: white;
}
.avatar-wrap { position: relative; flex-shrink: 0; }
.avatar {
  width: 72px; height: 72px; border-radius: 50%;
  background: rgba(255,255,255,0.2);
  backdrop-filter: blur(10px);
  border: 3px solid rgba(255,255,255,0.4);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem; font-weight: 700; color: white;
}
.avatar-ring {
  position: absolute; inset: -4px;
  border-radius: 50%; border: 2px dashed rgba(255,255,255,0.3);
  animation: spin 8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.hero-name { font-size: 1.3rem; font-weight: 700; margin: 0 0 4px; }
.hero-email { font-size: 0.875rem; opacity: 0.8; margin: 0 0 8px; }
.hero-badge {
  display: inline-block;
  background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 3px 12px;
}

.form-card {
  background: white; border-radius: 16px; padding: 24px 28px;
  border: 1px solid #f3f4f6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.form-section-title {
  font-size: 1rem; font-weight: 700; color: #111827;
  margin: 0 0 20px; padding-bottom: 12px;
  border-bottom: 1px solid #f3f4f6;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 0.78rem; font-weight: 600; color: #374151; text-transform: uppercase; letter-spacing: 0.04em; }
.input-wrap { position: relative; }
.field-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  color: #9ca3af; pointer-events: none;
}
.field-input {
  width: 100%; padding: 10px 12px 10px 38px;
  border: 1.5px solid #e5e7eb; border-radius: 10px;
  font-size: 0.875rem; color: #111827; background: #fafafa;
  transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;
}
.field-input:focus {
  outline: none; border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
  background: white;
}
.field-input::placeholder { color: #d1d5db; }

.form-footer { margin-top: 20px; display: flex; justify-content: flex-end; }
.save-btn {
  display: flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: white; border: none; padding: 11px 24px;
  border-radius: 10px; font-weight: 600; font-size: 0.875rem;
  cursor: pointer; box-shadow: 0 4px 12px rgba(79,70,229,0.3);
  transition: all 0.2s;
}
.save-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(79,70,229,0.4); }
.save-btn:disabled { opacity: 0.7; cursor: not-allowed; }

.spinner {
  width: 15px; height: 15px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  animation: spin 0.7s linear infinite;
}
</style>
