<template>
  <div class="dashboard-root">
    <div
      class="dashboard-layout"
      :class="{ 'sidebar-collapsed': sidebarCollapsed }"
    >
      <dashboard-sidebar
        :user="user"
        :active-tab="activeTab"
        :dashboard-stats="dashboardStats"
        :is-logging-out="isLoggingOut"
        :is-collapsed="sidebarCollapsed"
        @tab-change="onTabChange"
        @logout="logout"
      />

      <main class="dashboard-main">
        <dashboard-header
          :user="user"
          @toggle-sidebar="toggleSidebar"
          @logout="logout"
          @go-profile="onTabChange('profile')"
        />

        <!-- Loading Screen -->
        <div v-if="isLoading" class="loading-screen">
          <div class="loader-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
          </div>

          <p class="loading-text">
            Loading your dashboard...
          </p>
        </div>

        <!-- Dashboard Tabs -->
        <transition name="tab-fade" mode="out-in">
          <dashboard-overview
            v-if="!isLoading && activeTab === 'overview'"
            :dashboard-stats="dashboardStats"
            :recent-properties="recentProperties"
            :user="user"
            key="overview"
          />

          <profile-tab
            v-else-if="!isLoading && activeTab === 'profile'"
            :user="user"
            :is-updating-profile="isUpdatingProfile"
            @update-profile="updateProfile"
            key="profile"
          />

          <my-requests-tab
            v-else-if="!isLoading && activeTab === 'my-requests'"
            :requests="myRequests"
            :is-loading="requestsLoading"
            key="my-requests"
          />

          <my-place-orders-tab
            v-else-if="!isLoading && activeTab === 'my-place-orders'"
            :orders="myPlaceOrders"
            :is-loading="placeOrdersLoading"
            key="my-place-orders"
          />

          <my-orders-tab
            v-else-if="!isLoading && activeTab === 'my-orders'"
            :orders="myOrders"
            :is-loading="ordersLoading"
            key="my-orders"
          />

          <mydesigntab
            v-else-if="!isLoading && activeTab === 'my-design'"
            key="my-design"
          />

          <favorite-designs-tab
            v-else-if="!isLoading && activeTab === 'favorite-designs'"
            key="favorite-designs"
          />
        </transition>
      </main>
    </div>
  </div>
</template>

<script setup>
import {
  ref,
  onMounted,
  onBeforeUnmount
} from 'vue'

import { useRouter } from 'vue-router'

import MyOrdersTab from './Components/MyOrdersTab.vue'
import MyPlaceOrdersTab from './Components/MyPlaceOrdersTab.vue'
import Mydesigntab from './Components/Mydesigntab.vue'
import DashboardHeader from './Components/DashboardHeader.vue'
import FavoriteDesignsTab from './Components/FavoriteDesignsTab.vue'

const router = useRouter()

const API_BASE_URL =
  import.meta.env.VITE_API_BASE_URL || '/api'

/*
|--------------------------------------------------------------------------
| Dashboard State
|--------------------------------------------------------------------------
*/

const activeTab = ref('overview')

const user = ref({})
const dashboardStats = ref({})
const recentProperties = ref([])

const isLoading = ref(true)
const isLoggingOut = ref(false)
const isUpdatingProfile = ref(false)

const sidebarCollapsed = ref(false)
const isMobileScreen = ref(false)

/*
|--------------------------------------------------------------------------
| Tab Data
|--------------------------------------------------------------------------
*/

const myRequests = ref([])
const requestsLoading = ref(false)

const myPlaceOrders = ref([])
const placeOrdersLoading = ref(false)

const myOrders = ref([])
const ordersLoading = ref(false)

/*
|--------------------------------------------------------------------------
| Responsive Sidebar
|--------------------------------------------------------------------------
*/

const checkScreenSize = () => {
  isMobileScreen.value = window.innerWidth <= 991

  /*
   * Mobile aur tablet par sidebar hamesha
   * collapsed icon rail mein rahega.
   */
  if (isMobileScreen.value) {
    sidebarCollapsed.value = true
  }
}

const toggleSidebar = () => {
  /*
   * Mobile par sidebar full open nahi hoga.
   * Sirf desktop par collapse/expand hoga.
   */
  if (isMobileScreen.value) {
    sidebarCollapsed.value = true
    return
  }

  sidebarCollapsed.value = !sidebarCollapsed.value
}

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

const checkAuth = () => {
  const token = localStorage.getItem('auth_token')

  if (!token) {
    router.push('/user-login')
    return false
  }

  return token
}

const handleAuthError = () => {
  localStorage.removeItem('auth_token')

  alert('Session expired. Please login again.')

  router.push('/user-login')
}

/*
|--------------------------------------------------------------------------
| Tab Change
|--------------------------------------------------------------------------
*/

const onTabChange = tab => {
  activeTab.value = tab

  localStorage.setItem(
    'dashboard_active_tab',
    tab
  )

  if (
    tab === 'my-requests' &&
    myRequests.value.length === 0
  ) {
    fetchMyRequests()
  }

  if (
    tab === 'my-place-orders' &&
    myPlaceOrders.value.length === 0
  ) {
    fetchMyPlaceOrders()
  }

  if (
    tab === 'my-orders' &&
    myOrders.value.length === 0
  ) {
    fetchMyOrders()
  }
}

/*
|--------------------------------------------------------------------------
| Fetch User Orders
|--------------------------------------------------------------------------
*/

const fetchMyOrders = async () => {
  const token = checkAuth()

  if (!token) return

  ordersLoading.value = true

  try {
    const response = await fetch(
      `${API_BASE_URL}/user/orders`,
      {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        }
      }
    )

    if (response.status === 401) {
      handleAuthError()
      return
    }

    if (response.ok) {
      const data = await response.json()

      myOrders.value = Array.isArray(data)
        ? data
        : Array.isArray(data?.data)
          ? data.data
          : []
    }
  } catch (error) {
    console.error(
      'My orders fetch error:',
      error
    )
  } finally {
    ordersLoading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Fetch User Profile
|--------------------------------------------------------------------------
*/

const fetchUserData = async () => {
  const token = checkAuth()

  if (!token) return

  try {
    const response = await fetch(
      `${API_BASE_URL}/user/profile`,
      {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        }
      }
    )

    if (response.status === 401) {
      handleAuthError()
      return
    }

    if (response.ok) {
      const data = await response.json()

      if (data.status) {
        user.value = data.data || {}
      } else if (data.data) {
        user.value = data.data
      }
    }
  } catch (error) {
    console.error(
      'User profile fetch error:',
      error
    )
  }
}

/*
|--------------------------------------------------------------------------
| Fetch Dashboard Stats
|--------------------------------------------------------------------------
*/

const fetchDashboardStats = async () => {
  const token = checkAuth()

  if (!token) return

  try {
    const response = await fetch(
      `${API_BASE_URL}/dashboard/stats`,
      {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        }
      }
    )

    if (response.status === 401) {
      handleAuthError()
      return
    }

    if (response.ok) {
      const data = await response.json()

      dashboardStats.value =
        data?.data || data || {}
    }
  } catch (error) {
    console.error(
      'Dashboard stats fetch error:',
      error
    )
  }
}

/*
|--------------------------------------------------------------------------
| Fetch My Requests
|--------------------------------------------------------------------------
*/

const fetchMyRequests = async () => {
  const token = checkAuth()

  if (!token) return

  requestsLoading.value = true

  try {
    const response = await fetch(
      `${API_BASE_URL}/user/my-requests`,
      {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        }
      }
    )

    if (response.status === 401) {
      handleAuthError()
      return
    }

    if (response.ok) {
      const data = await response.json()

      myRequests.value = Array.isArray(data)
        ? data
        : Array.isArray(data?.data)
          ? data.data
          : []
    }
  } catch (error) {
    console.error(
      'My requests fetch error:',
      error
    )
  } finally {
    requestsLoading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Fetch Place Orders
|--------------------------------------------------------------------------
*/

const fetchMyPlaceOrders = async () => {
  const token = checkAuth()

  if (!token) return

  placeOrdersLoading.value = true

  try {
    const response = await fetch(
      `${API_BASE_URL}/place-order/my-orders`,
      {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        }
      }
    )

    if (response.status === 401) {
      handleAuthError()
      return
    }

    if (response.ok) {
      const data = await response.json()

      myPlaceOrders.value = Array.isArray(data)
        ? data
        : Array.isArray(data?.data)
          ? data.data
          : []
    }
  } catch (error) {
    console.error(
      'Place orders fetch error:',
      error
    )
  } finally {
    placeOrdersLoading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Update Profile
|--------------------------------------------------------------------------
*/

const updateProfile = async profileData => {
  const token = checkAuth()

  if (!token) return

  isUpdatingProfile.value = true

  try {
    const response = await fetch(
      `${API_BASE_URL}/user/profile`,
      {
        method: 'PUT',

        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },

        body: JSON.stringify(profileData)
      }
    )

    if (response.status === 401) {
      handleAuthError()
      return
    }

    const data = await response
      .json()
      .catch(() => ({}))

    if (response.ok) {
      user.value = {
        ...user.value,
        ...(data?.data || profileData)
      }

      alert('Profile updated successfully!')
    } else {
      alert(
        data.message ||
        'Failed to update profile'
      )
    }
  } catch (error) {
    console.error(
      'Profile update error:',
      error
    )

    alert('Failed to update profile')
  } finally {
    isUpdatingProfile.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

const logout = async () => {
  try {
    isLoggingOut.value = true

    const token =
      localStorage.getItem('auth_token')

    if (token) {
      await fetch(
        `${API_BASE_URL}/user/user_logout`,
        {
          method: 'POST',

          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        }
      )
    }
  } catch (error) {
    console.error(
      'Logout error:',
      error
    )
  } finally {
    localStorage.removeItem('auth_token')
    localStorage.removeItem(
      'dashboard_active_tab'
    )

    isLoggingOut.value = false

    router.push('/')
  }
}

/*
|--------------------------------------------------------------------------
| Component Mounted
|--------------------------------------------------------------------------
*/

onMounted(async () => {
  checkScreenSize()

  window.addEventListener(
    'resize',
    checkScreenSize
  )

  const urlParams =
    new URLSearchParams(window.location.search)

  const token = urlParams.get('token')
  const tabFromUrl = urlParams.get('tab')

  const savedTab = localStorage.getItem(
    'dashboard_active_tab'
  )

  if (token) {
    localStorage.setItem(
      'auth_token',
      token
    )

    window.history.replaceState(
      {},
      document.title,
      '/dashboard'
    )
  }

  if (tabFromUrl) {
    activeTab.value = tabFromUrl
  } else if (savedTab) {
    activeTab.value = savedTab
  }

  if (!checkAuth()) {
    isLoading.value = false
    return
  }

  isLoading.value = true

  try {
    await Promise.all([
      fetchUserData(),
      fetchDashboardStats()
    ])

    /*
     * Saved active tab ka data bhi initial
     * load par fetch karna zaroori hai.
     */
    if (activeTab.value === 'my-requests') {
      await fetchMyRequests()
    }

    if (
      activeTab.value ===
      'my-place-orders'
    ) {
      await fetchMyPlaceOrders()
    }

    if (activeTab.value === 'my-orders') {
      await fetchMyOrders()
    }
  } finally {
    isLoading.value = false
  }
})

onBeforeUnmount(() => {
  window.removeEventListener(
    'resize',
    checkScreenSize
  )
})
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.dashboard-root {
  width: 100%;
  min-height: 100vh;
  overflow-x: hidden;
  background: #f0f2f8;
  font-family:
    "Segoe UI",
    system-ui,
    -apple-system,
    BlinkMacSystemFont,
    sans-serif;
}

.dashboard-layout {
  display: flex;
  width: 100%;
  min-height: 100vh;
  padding-left: 260px;
  transition: padding-left 0.3s ease;
}

.dashboard-layout.sidebar-collapsed {
  padding-left: 60px;
}

.dashboard-main {
  flex: 1;
  width: 100%;
  min-width: 0;
  min-height: 100vh;
  margin-top: 62px;
  padding: 32px;
  overflow-x: hidden;
}

/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/

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
  border-top-color: #000;
  animation:
    ring 1.2s
    cubic-bezier(0.5, 0, 0.5, 1)
    infinite;
}

.loader-ring div:nth-child(1) {
  animation-delay: -0.45s;
}

.loader-ring div:nth-child(2) {
  animation-delay: -0.3s;
}

.loader-ring div:nth-child(3) {
  animation-delay: -0.15s;
}

@keyframes ring {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

.loading-text {
  margin: 0;
  color: #6b7280;
  font-size: 0.95rem;
}

/*
|--------------------------------------------------------------------------
| Tab Animation
|--------------------------------------------------------------------------
*/

.tab-fade-enter-active,
.tab-fade-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}

.tab-fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.tab-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

/*
|--------------------------------------------------------------------------
| Medium Desktop
|--------------------------------------------------------------------------
*/

@media (max-width: 1199px) {
  .dashboard-main {
    padding: 24px;
  }
}

/*
|--------------------------------------------------------------------------
| Mobile and Tablet
|--------------------------------------------------------------------------
*/

@media (max-width: 991px) {
  .dashboard-layout,
  .dashboard-layout.sidebar-collapsed {
    padding-left: 60px;
  }

  .dashboard-main {
    width: calc(100vw - 60px);
    min-width: 0;
    margin-top: 62px;
    padding: 20px 16px;
    overflow-x: hidden;
  }

  /*
   * Mobile par sidebar hamesha
   * 60px collapsed rail rahega.
   */
  :deep(.dash-sidebar),
  :deep(.dash-sidebar.collapsed) {
    width: 60px !important;
    min-width: 60px !important;
    max-width: 60px !important;
    transform: none !important;
    overflow: visible !important;
  }

  :deep(.dash-sidebar .back-label),
  :deep(.dash-sidebar .nav-label),
  :deep(.dash-sidebar .user-info) {
    display: none !important;
  }

  :deep(.dash-sidebar .back-btn),
  :deep(.dash-sidebar .nav-btn),
  :deep(.dash-sidebar .logout-btn) {
    justify-content: center !important;
    gap: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  :deep(.dash-sidebar .sidebar-header) {
    justify-content: center !important;
    padding-left: 8px !important;
    padding-right: 8px !important;
    gap: 0 !important;
  }

  :deep(.dash-sidebar .sidebar-nav) {
    overflow-x: visible !important;
  }
}

/*
|--------------------------------------------------------------------------
| Mobile Child Components
|--------------------------------------------------------------------------
*/

@media (max-width: 768px) {
  .dashboard-main {
    padding: 16px 12px;
  }

  /*
   * Dashboard Overview Header
   */
  :deep(.overview-root .page-header),
  :deep(.po-tab-header),
  :deep(.my-design-wrapper .page-header),
  :deep(.fav-header),
  :deep(.requests-page .page-header) {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    gap: 10px;
  }

  :deep(.page-title),
  :deep(.po-tab-title),
  :deep(.my-design-wrapper .title),
  :deep(.fav-header h2) {
    font-size: 1.3rem !important;
    line-height: 1.25;
  }

  :deep(.page-sub),
  :deep(.po-tab-subtitle),
  :deep(.my-design-wrapper .subtitle),
  :deep(.fav-header p) {
    font-size: 0.8rem !important;
    line-height: 1.45;
  }

  :deep(.add-property-btn) {
    width: 100%;
    justify-content: center;
  }

  /*
   * Overview Stats
   */
  :deep(.stats-grid) {
    grid-template-columns:
      repeat(2, minmax(0, 1fr))
      !important;
    gap: 10px !important;
  }

  :deep(.stat-card) {
    min-width: 0;
    padding: 14px !important;
    gap: 10px !important;
    border-radius: 14px !important;
  }

  :deep(.stat-icon-wrap) {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
  }

  :deep(.stat-info) {
    min-width: 0;
  }

  :deep(.stat-label) {
    font-size: 0.68rem !important;
    overflow-wrap: anywhere;
  }

  :deep(.stat-value) {
    font-size: 1rem !important;
    overflow-wrap: anywhere;
  }

  /*
   * Recent Orders
   */
  :deep(.section-card) {
    padding: 15px !important;
    border-radius: 14px !important;
  }

  :deep(.section-header) {
    gap: 10px;
  }

  :deep(.section-title) {
    font-size: 1rem !important;
  }

  :deep(.order-row) {
    align-items: flex-start !important;
    gap: 10px !important;
  }

  :deep(.order-details),
  :deep(.order-meta) {
    min-width: 0;
  }

  :deep(.order-meta) {
    align-items: flex-end;
    text-align: right;
  }

  /*
   * Requests
   */
  :deep(.requests-page) {
    width: 100%;
    max-width: 100%;
  }

  :deep(.requests-grid) {
    grid-template-columns: 1fr !important;
    gap: 12px !important;
  }

  :deep(.request-card) {
    width: 100%;
    min-width: 0;
    padding: 15px !important;
  }

  :deep(.filter-tabs) {
    display: grid !important;
    grid-template-columns:
      repeat(3, minmax(0, 1fr));
    width: 100%;
    gap: 6px !important;
  }

  :deep(.filter-btn) {
    justify-content: center;
    width: 100%;
    min-width: 0;
    padding: 8px 4px !important;
    font-size: 0.7rem !important;
  }

  :deep(.count-badge) {
    padding: 1px 5px !important;
  }

  :deep(.info-row) {
    gap: 12px;
  }

  :deep(.val) {
    max-width: 60%;
    overflow-wrap: anywhere;
  }

  /*
   * Favorite Designs
   */
  :deep(.fav-grid) {
    grid-template-columns:
      repeat(2, minmax(0, 1fr))
      !important;
    gap: 10px !important;
  }

  :deep(.fav-card) {
    min-width: 0;
    padding: 10px !important;
    border-radius: 12px !important;
  }

  :deep(.fav-card img) {
    height: 145px !important;
  }

  :deep(.fav-card h3) {
    font-size: 0.82rem !important;
    overflow-wrap: anywhere;
  }

  :deep(.empty-box) {
    padding: 28px 16px !important;
  }

  /*
   * My Designs
   */
  :deep(.my-design-wrapper) {
    width: 100%;
    padding: 0 !important;
    background: transparent !important;
  }

  :deep(.design-grid) {
    grid-template-columns:
      repeat(2, minmax(0, 1fr))
      !important;
    gap: 10px !important;
  }

  :deep(.design-card) {
    min-width: 0;
    padding: 10px !important;
  }

  :deep(.thumb-wrap) {
    height: 155px !important;
  }

  :deep(.design-info h4) {
    font-size: 0.8rem !important;
    overflow-wrap: anywhere;
  }

  :deep(.design-meta) {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 4px !important;
  }

  :deep(.btn-row) {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 6px !important;
  }

  :deep(.edit-btn),
  :deep(.delete-btn) {
    width: 100%;
    min-height: 38px;
    justify-content: center;
    font-size: 0.72rem !important;
  }

  /*
   * Place Orders and My Orders
   */
  :deep(.po-tab-count),
  :deep(.design-count) {
    align-self: flex-start;
  }

  :deep(.po-orders-grid) {
    grid-template-columns: 1fr !important;
  }

  :deep(.po-order-card) {
    width: 100%;
    min-width: 0;
  }

  :deep(.po-card-header) {
    align-items: flex-start !important;
    gap: 10px;
  }

  :deep(.po-info-grid) {
    grid-template-columns: 1fr !important;
  }

  :deep(.po-info-item) {
    min-width: 0;
  }

  :deep(.po-info-val),
  :deep(.po-break-text) {
    max-width: 100%;
    overflow-wrap: anywhere;
    word-break: break-word;
  }

  :deep(.po-file-gallery) {
    grid-template-columns:
      repeat(2, minmax(0, 1fr))
      !important;
    gap: 8px !important;
  }

  :deep(.po-thumbnail) {
    height: 115px !important;
  }
}

/*
|--------------------------------------------------------------------------
| Small Mobile
|--------------------------------------------------------------------------
*/

@media (max-width: 480px) {
  .dashboard-main {
    width: calc(100vw - 60px);
    padding: 14px 10px;
  }

  :deep(.stats-grid) {
    grid-template-columns: 1fr !important;
    gap: 10px !important;
  }

  :deep(.stat-card) {
    flex-direction: row;
    align-items: center !important;
    min-height: 88px;
    padding: 14px !important;
  }

  :deep(.stat-info) {
    width: 100%;
  }

  :deep(.fav-grid),
  :deep(.design-grid) {
    grid-template-columns: 1fr !important;
  }

  :deep(.fav-card img) {
    height: 210px !important;
  }

  :deep(.thumb-wrap) {
    height: 220px !important;
  }

  :deep(.filter-tabs) {
    grid-template-columns: 1fr !important;
  }

  :deep(.filter-btn) {
    min-height: 40px;
  }

  :deep(.card-top),
  :deep(.po-card-header) {
    flex-direction: column;
    align-items: flex-start !important;
  }

  :deep(.date-text) {
    width: 100%;
  }

  :deep(.info-row) {
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f1f1;
  }

  :deep(.val) {
    max-width: 100%;
    text-align: left !important;
  }

  :deep(.po-file-gallery) {
    grid-template-columns: 1fr !important;
  }
}
</style>
