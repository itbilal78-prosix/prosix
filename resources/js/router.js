import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/Pages/Home.vue'),
    meta: { public: true, title: 'Home', breadcrumb: 'Home' }
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: () => import('./Pages/CheckoutPage.vue'),
    meta: { breadcrumb: 'Checkout' }
  },
  {
    path: '/user-login',
    name: 'Login',
    component: () => import('@/user-authentication/AuthPage.vue'),
    meta: { guest: true, breadcrumb: 'Login' }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/user-authentication/AuthPage.vue'),
    meta: { guest: true, breadcrumb: 'Register' }
  },
  {
    path: '/otp-verification',
    name: 'OTPVerification',
    component: () => import('@/user-authentication/OTPVerification.vue'),
    meta: { guest: true, breadcrumb: 'OTP Verification' }
  },
{
  path: '/catalogue',
  name: 'Catalogue',
  component: () => import('@/Pages/FlipbookPage.vue'),
  meta: { breadcrumb: 'Catalogue' }
},
{
  path: '/catalogue/:id',
  name: 'CatalogueView',
  component: () => import('@/Pages/FlipbookView.vue'),
  meta: { breadcrumb: 'Catalogue View' }
},
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/user-dashboard/dashboard.vue'),
    meta: { requiresAuth: true, breadcrumb: 'Dashboard' }
  },
  {
    path: '/models/:id',
    name: 'CustomizerModel',
    component: () => import('@/Pages/CustomizerModel.vue'),
    meta: { requiresAuth: true, breadcrumb: 'Model Customizer' }
  },
  {
    path: '/menu/:slug',
    name: 'MenuCategories',
    component: () => import('@/Pages/MenuCategories.vue'),
    meta: { breadcrumb: 'Menu' }
  },
  {
    path: '/category/:id/products',
    name: 'CategoryProducts',
    component: () => import('@/Pages/CategoryProducts.vue'),
    meta: { breadcrumb: 'Products' }
  },
  {
    path: '/category/:id/subcategories',
    name: 'Subcategories',
    component: () => import('@/Pages/Subcategories.vue'),
    meta: { breadcrumb: 'Subcategories' }
  },
  {
    path: '/product/:id',
    name: 'ProductDetails',
    component: () => import('@/Pages/ProductDetails.vue'),
    meta: { breadcrumb: 'Product Details' }
  },
  {
    path: '/blog/:slug',
    name: 'BlogDetail',
    component: () => import('@/Pages/BlogDetail.vue'),
    meta: { breadcrumb: 'Blog Detail' }
  },
  {
    path: '/membership',
    name: 'Membership',
    component: () => import('@/Pages/MembershipForm.vue'),
    meta: { breadcrumb: 'Membership' }
  },
  {
    path: '/artwork',
    name: 'Artworkform',
    component: () => import('@/Pages/Artworkform.vue'),
    meta: { breadcrumb: 'Artwork' }
  },
  {
    path: '/placeorder',
    name: 'PlaceOrder',
    component: () => import('@/Pages/PlaceOrder.vue'),
    meta: { breadcrumb: 'PlaceOrder' }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')

  if (to.meta.guest && token) return next('/dashboard')

  if (to.meta.requiresAuth && !token) {
    return next({
      path: '/user-login',
      query: { redirect: to.fullPath }
    })
  }

  next()
})

export default router
