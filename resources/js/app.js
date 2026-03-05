
import axios from 'axios'

// axios.defaults.baseURL = 'https://prosix.com'
axios.defaults.baseURL = import.meta.env.VITE_API_URL
axios.defaults.withCredentials = true
window.axios = axios

// 🔴 AUTO LOGOUT INTERCEPTOR
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401 || error.response?.status === 403) {
      localStorage.removeItem('auth_token')
      window.location.href = '/user-login'
    }
    return Promise.reject(error)
  }
)

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'

// Vue & Pinia
import { createApp } from 'vue';
import { createPinia } from 'pinia';  // ← Yeh import zaroori hai
import App from './App.vue';
import router from './router';
import '../css/app.css';  // Agar custom CSS hai
// Pinia create aur register karo (sab se pehle!)
const pinia = createPinia();
const app = createApp(App);

// Pinia ko app mein use karo – yeh line missing thi ya order galat tha
app.use(pinia);

// Router use karo
app.use(router);

// Global components (nav, footer, dashboard waghera)
import nav from './Component/nav.vue';
app.component('nav-component', nav);
import Breadcrumb from './Component/Breadcrumb.vue';
app.component('breadcrumb-component', Breadcrumb);
import footer from './Component/footer.vue';
app.component('footer-component', footer);

import DashboardSidebar from './user-dashboard/Components/DashboardSidebar.vue';
app.component('dashboard-sidebar', DashboardSidebar);

import DashboardOverview from './user-dashboard/Components/DashboardOverview.vue';
app.component('dashboard-overview', DashboardOverview);

import PropertiesTab from './user-dashboard/Components/PropertiesTab.vue';
app.component('properties-tab', PropertiesTab);

import ProfileTab from './user-dashboard/Components/ProfileTab.vue';
app.component('profile-tab', ProfileTab);

import AnalyticsTab from './user-dashboard/Components/AnalyticsTab.vue';
app.component('analytics-tab', AnalyticsTab);

// Mount app
app.mount('#app');
// =====================================================
// BREADCRUMB LOGIC — app.js mein router.afterEach replace karo
// =====================================================

// Yeh ROUTE NAME → Display Name mapping hai
const routeNames = {
  'Home':            'Home',
  'MenuCategories':  'Menu',
  'Subcategories':   'Categories',
  'CategoryProducts':'Products',
  'ProductDetails':  'Product Details',
  'Checkout':        'Checkout',
  'Dashboard':       'Dashboard',
  'Login':           'Login',
  'Register':        'Register',
  'CustomizerModel': 'Customizer',
  'Flipbooks':       'Flipbooks',
  'FlipbookView':    'Flipbook',
  'BlogDetail':      'Blog',
  'Membership':      'Membership',
  'Artworkform':     'Artwork',
}

router.afterEach((to, from) => {
  // Agar Home par aaye toh history reset karo
  if (to.name === 'Home') {
    localStorage.setItem('breadcrumbs', JSON.stringify([
      { name: 'Home', path: '/' }
    ]))
    return
  }

  // Existing history lo
  let history = []
  try {
    history = JSON.parse(localStorage.getItem('breadcrumbs') || '[]')
  } catch {
    history = [{ name: 'Home', path: '/' }]
  }

  // Agar Home nahi hai toh add karo
  if (!history.length || history[0].path !== '/') {
    history = [{ name: 'Home', path: '/' }]
  }

  const currentPath = to.fullPath
  const displayName = routeNames[to.name] || to.meta?.breadcrumb || to.name || 'Page'

  // Check karo yeh page pehle se history mein hai
  const existingIdx = history.findIndex(b => b.path === currentPath)

  if (existingIdx !== -1) {
    // Pehle se hai — uske baad wala sab cut karo (back navigation)
    history = history.slice(0, existingIdx + 1)
  } else {
    // Naya page — add karo
    history.push({
      name: displayName,
      path: currentPath
    })
  }

  // Max 6 items rakho
  if (history.length > 6) {
    history = [history[0], ...history.slice(-(5))]
  }

  localStorage.setItem('breadcrumbs', JSON.stringify(history))
})
