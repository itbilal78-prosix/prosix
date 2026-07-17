import axios from 'axios'

axios.defaults.baseURL = import.meta.env.VITE_API_URL
axios.defaults.withCredentials = true
window.axios = axios

//  AUTO LOGOUT INTERCEPTOR
axios.interceptors.response.use(
  response => response,
  error => {
    // if (error.response?.status === 401 || error.response?.status === 403) {
    //   localStorage.removeItem('auth_token')
    //   window.location.href = '/user-login'
    // }
    if (error.response?.status === 401 || error.response?.status === 403) {
  // ✅ verify-password route skip karo
  const url = error.config?.url || ''
  if (url.includes('verify-password')) {
    return Promise.reject(error)
  }
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
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import '../css/app.css';

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
app.use(router);

// Global components
import MyRequestsTab from './user-dashboard/Components/Myrequeststab.vue'
app.component('my-requests-tab', MyRequestsTab)
import MyPlaceOrdersTab from './user-dashboard/Components/Myplaceorderstab.vue'
app.component('my-place-orders-tab', MyPlaceOrdersTab)
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
import ProfileTab from './user-dashboard/Components/ProfileTab.vue';
app.component('profile-tab', ProfileTab);

// =====================================================
//  GLOBAL INSPECT / DEVTOOLS PROTECTION
// =====================================================


document.addEventListener('contextmenu', e => e.preventDefault())

document.addEventListener('keydown', e => {
  if (
    e.key === 'F12' ||
    (e.ctrlKey && e.shiftKey && ['I','J','C'].includes(e.key.toUpperCase())) ||
    (e.ctrlKey && e.key.toUpperCase() === 'U') ||
    (e.metaKey && e.altKey && ['I','J','C'].includes(e.key.toUpperCase()))
  ) {
    e.preventDefault()
    e.stopPropagation()
    return false
  }
})

const devToolsCheck = () => {
  // Real mobile device hai toh skip karo
  const isRealMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
  if (isRealMobile) return

  const threshold = 160
  const widthDiff = window.outerWidth - window.innerWidth
  const heightDiff = window.outerHeight - window.innerHeight

  if (widthDiff > threshold || heightDiff > threshold) {
    document.body.innerHTML = '<h1 style="text-align:center;margin-top:20%;font-family:sans-serif;color:#000;">Access Denied</h1>'
    clearInterval(checkInterval)
  }
}

window.addEventListener('resize', devToolsCheck)
const checkInterval = setInterval(devToolsCheck, 1000)

// Mount app
app.mount('#app');

// =====================================================
// BREADCRUMB LOGIC
// =====================================================

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
  if (to.name === 'Home') {
    localStorage.setItem('breadcrumbs', JSON.stringify([
      { name: 'Home', path: '/' }
    ]))
    return
  }

  let history = []
  try {
    history = JSON.parse(localStorage.getItem('breadcrumbs') || '[]')
  } catch {
    history = [{ name: 'Home', path: '/' }]
  }

  if (!history.length || history[0].path !== '/') {
    history = [{ name: 'Home', path: '/' }]
  }

  const currentPath = to.fullPath
  const displayName = routeNames[to.name] || to.meta?.breadcrumb || to.name || 'Page'

  const existingIdx = history.findIndex(b => b.path === currentPath)

  if (existingIdx !== -1) {
    history = history.slice(0, existingIdx + 1)
  } else {
    history.push({ name: displayName, path: currentPath })
  }

  if (history.length > 6) {
    history = [history[0], ...history.slice(-(5))]
  }

  localStorage.setItem('breadcrumbs', JSON.stringify(history))
})
