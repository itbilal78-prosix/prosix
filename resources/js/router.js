import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/Pages/Home.vue'),
    meta: { public: true, title: 'Home' }
  },
  {
    path: '/user-login',
    name: 'Login',
    component: () => import('@/user-authentication/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/user-authentication/Register.vue'),
    meta: { guest: true }
  },
  {
    path: '/otp-verification',
    name: 'OTPVerification',
    component: () => import('@/user-authentication/OTPVerification.vue'),
    meta: { guest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/user-dashboard/dashboard.vue'),
    meta: { requiresAuth: true }
  },

  {
    path: '/models/:id',
    name: 'CustomizerModel',
    component: () => import('@/Pages/CustomizerModel.vue'),
    meta: { requiresAuth: true }
  },

  {
    path: '/menu/:slug',
    name: 'MenuCategories',
component: () => import('@/Pages/MenuCategories.vue')
  },

  {
    path: '/category/:id/products',
    name: 'CategoryProducts',
component: () => import('@/Pages/CategoryProducts.vue')
  },
  {
    path: '/category/:id/subcategories',
    name: 'Subcategories',
    component: () => import('@/Pages/Subcategories.vue')
  },
  {
    path: '/product/:id',
    name: 'ProductDetails',
    component: () => import('@/Pages/ProductDetails.vue')
  },
  {
    path: '/blog/:slug',
    name: 'BlogDetail',
    component: () => import('@/Pages/BlogDetail.vue')
  },
  {
    path: '/membership',
    name: 'Membership',
    component: () => import('@/Pages/MembershipForm.vue')
  },
  {
    path: '/artwork',
    name: 'Artworkform',
    component: () => import('@/pages@/Pages/Artworkform.vue')
  },


]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')

  if (to.meta.guest && token) {
    return next('/dashboard')
  }

  if (to.meta.requiresAuth && !token) {
    return next({
      path: '/user-login',
      query: { redirect: to.fullPath }
    })
  }

  next()
})

export default router

