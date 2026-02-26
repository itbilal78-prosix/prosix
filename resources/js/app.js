
import axios from 'axios'

axios.defaults.baseURL = 'https://prosix.com'
axios.defaults.withCredentials = true
axios.defaults.baseURL = import.meta.env.VITE_API_URL
window.axios = axios
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

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
