<template>
  <div class="dashboard-root">
    <nav-component />
    <div class="dashboard-layout">
      <dashboard-sidebar
        :user="user"
        :active-tab="activeTab"
        :dashboard-stats="dashboardStats"
        :is-logging-out="isLoggingOut"
        @tab-change="activeTab = $event"
        @logout="logout"
      />

      <main class="dashboard-main">
        <div v-if="isLoading" class="loading-screen">
          <div class="loader-ring">
            <div></div><div></div><div></div><div></div>
          </div>
          <p class="loading-text">Loading your dashboard...</p>
        </div>

        <transition name="tab-fade" mode="out-in">
          <dashboard-overview
            v-if="!isLoading && activeTab === 'overview'"
            :dashboard-stats="dashboardStats"
            :recent-properties="recentProperties"
            :user="user"
            @navigate-to-list="$router.push('/list-property')"
            key="overview"
          />
          <properties-tab
            v-else-if="!isLoading && activeTab === 'properties'"
            :properties="properties"
            @navigate-to-list="$router.push('/list-property')"
            @filter-change="handleFilterChange"
            @edit-property="editProperty"
            @toggle-status="togglePropertyStatus"
            key="properties"
          />
          <profile-tab
            v-else-if="!isLoading && activeTab === 'profile'"
            :user="user"
            :is-updating-profile="isUpdatingProfile"
            @update-profile="updateProfile"
            key="profile"
          />
          <analytics-tab
            v-else-if="!isLoading && activeTab === 'analytics'"
            :dashboard-stats="dashboardStats"
            :recent-properties="recentProperties"
            key="analytics"
          />
        </transition>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api'

const activeTab = ref('overview')
const properties = ref([])
const user = ref({})
const dashboardStats = ref({})
const recentProperties = ref([])
const isLoading = ref(true)
const isLoggingOut = ref(false)
const isUpdatingProfile = ref(false)

const checkAuth = () => {
  const token = localStorage.getItem('auth_token')
  if (!token) { router.push('/user-login'); return false }
  return token
}

// ✅ FIXED: Removed axios, using only fetch consistently
const fetchUserData = async () => {
  const token = checkAuth()
  if (!token) return
  try {
    const res = await fetch(`${API_BASE_URL}/user/profile`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`
      }
    })
    if (res.ok) {
      const d = await res.json()
      if (d.success) user.value = d.data
    } else if (res.status === 401) {
      handleAuthError()
    }
  } catch (e) {
    console.error('Error fetching user data:', e)
  }
}

const fetchDashboardStats = async () => {
  const token = checkAuth()
  if (!token) return
  try {
    const res = await fetch(`${API_BASE_URL}/user/dashboard-stats`, {
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` }
    })
    if (res.ok) {
      const d = await res.json()
      if (d.success) {
        dashboardStats.value = d.data.stats
        recentProperties.value = d.data.recent_properties
      }
    } else if (res.status === 401) handleAuthError()
  } catch (e) { console.error(e) }
}

const fetchUserProperties = async (propertyType = '', status = '') => {
  const token = checkAuth()
  if (!token) return
  try {
    const params = new URLSearchParams()
    if (propertyType) params.append('type', propertyType)
    if (status) params.append('status', status)
    const res = await fetch(`${API_BASE_URL}/user/properties?${params}`, {
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` }
    })
    if (res.ok) {
      const d = await res.json()
      if (d.success) properties.value = d.data
    } else if (res.status === 401) handleAuthError()
  } catch (e) { console.error(e) }
}

const handleAuthError = () => {
  localStorage.removeItem('auth_token')
  alert('Session expired. Please login again.')
  router.push('/user-login')
}

const handleFilterChange = (t, s) => fetchUserProperties(t, s)
const editProperty = (p) => router.push(`/edit-property/${p.id}`)

const togglePropertyStatus = async (property) => {
  const token = checkAuth()
  if (!token) return
  try {
    const res = await fetch(`${API_BASE_URL}/user/properties/${property.id}/toggle-status`, {
      method: 'PATCH',
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` }
    })
    if (res.ok) {
      const d = await res.json()
      if (d.success) {
        property.is_active = !property.is_active
        await fetchDashboardStats()
      }
    } else if (res.status === 401) handleAuthError()
  } catch (e) { console.error(e) }
}

const updateProfile = async (profileData) => {
  const token = checkAuth()
  if (!token) return
  try {
    isUpdatingProfile.value = true
    const res = await fetch(`${API_BASE_URL}/user/profile`, {
      method: 'PUT',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`
      },
      body: JSON.stringify(profileData)
    })
    if (res.ok) {
      const d = await res.json()
      if (d.success) {
        // ✅ Update user object with new data from server or local form
        user.value = { ...user.value, ...profileData }
        alert('Profile updated successfully!')
      }
    } else if (res.status === 401) handleAuthError()
    else {
      const d = await res.json()
      alert(d.message || 'Failed to update profile')
    }
  } catch (e) {
    alert('Failed to update profile')
  } finally {
    isUpdatingProfile.value = false
  }
}

const logout = async () => {
  try {
    isLoggingOut.value = true
    const token = localStorage.getItem('auth_token')
    if (token) {
      await fetch(`${API_BASE_URL}/user/user_logout`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
    }
  } catch (e) {
    console.error(e)
  } finally {
    localStorage.removeItem('auth_token')
    router.push('/')
    isLoggingOut.value = false
  }
}

onMounted(async () => {
  if (checkAuth()) {
    isLoading.value = true
    try {
      await Promise.all([fetchUserData(), fetchDashboardStats(), fetchUserProperties()])
    } finally {
      isLoading.value = false
    }
  }
})
</script>

<style scoped>
.dashboard-root {
  min-height: 100vh;
  background: #f0f2f8;
  font-family: 'Segoe UI', system-ui, sans-serif;
}
.dashboard-layout {
  display: flex;
  min-height: calc(100vh - 64px);
  margin-top: 64px;
}
.dashboard-main {
  flex: 1;
  overflow-x: hidden;
  padding: 2rem;
  min-height: 100%;
}
.loading-screen {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 60vh;
  gap: 1.5rem;
}
.loader-ring {
  display: inline-block;
  position: relative;
  width: 64px;
  height: 64px;
}
.loader-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 52px;
  height: 52px;
  margin: 6px;
  border: 5px solid transparent;
  border-radius: 50%;
  animation: ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-top-color: #4f46e5;
}
.loader-ring div:nth-child(1) { animation-delay: -0.45s; }
.loader-ring div:nth-child(2) { animation-delay: -0.3s; }
.loader-ring div:nth-child(3) { animation-delay: -0.15s; }
@keyframes ring {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.loading-text { color: #6b7280; font-size: 0.95rem; }
.tab-fade-enter-active, .tab-fade-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.tab-fade-enter-from { opacity: 0; transform: translateY(10px); }
.tab-fade-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
