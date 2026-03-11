<template>
    <nav
        class="navbar navbar-expand-lg fixed-top custom-navbar"
        :class="{ 'navbar-scrolled': isScrolled, 'navbar-full': navigations.length > 8 }"
    >
        <div class="container-fluid px-0">

            <!-- Logo -->
            <router-link to="/" class="navbar-brand navbar-brand-fixed">
                <img src="/public/assets/images/P LOGO BLACK.png" alt="Prosix Logo" class="navbar-logo" :class="{ 'logo-small': isScrolled || navigations.length > 8 }" />
                <img v-if="isScrolled || navigations.length > 8" src="/public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png" alt="Secondary Logo" class="navbar-logo-secondary" />
            </router-link>

            <!-- Mobile top icons -->
            <div class="d-flex align-items-center gap-3 ms-auto me-3 d-lg-none mobile-top-icons">
                <i class="bi bi-search fs-5 text-white" @click="openMobileSearch"></i>
                <div class="position-relative cursor-pointer" @click="showCartSidebar = true">
                    <i class="bi bi-bag fs-5 text-white"></i>
                    <span v-if="cartStore.totalItems > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ cartStore.totalItems }}</span>
                </div>
                <router-link to="/register"><i class="bi bi-person fs-5 text-white"></i></router-link>
                <button class="hamburger-btn" @click="toggleDrawer">
                    <span :class="{ open: drawerOpen }"></span>
                    <span :class="{ open: drawerOpen }"></span>
                    <span :class="{ open: drawerOpen }"></span>
                </button>
            </div>

            <!-- ===== DESKTOP MENU ===== -->
            <div class="collapse navbar-collapse align-items-center nav-right-block" id="mainNavbar">

                <!-- Nav items -->
                <ul class="navbar-nav nav-items-list   flex-grow-1">
                    <li
                        v-for="nav in navigations"
                        :key="nav.id"
                        class="nav-item"
                        :class="{ dropdown: nav.has_dropdown }"
                    >
                        <router-link
                            v-if="!nav.has_dropdown && nav.route"
                            class="nav-link"
                            :to="nav.route"
                        >{{ nav.title }}</router-link>

                      <a
 v-else
  class="nav-link dropdown-toggle"
  href="#"
  @click.prevent="nav.clickable ? goToMenu(nav.slug) : null"
  :style="nav.clickable ? 'cursor:pointer' : 'cursor:default'"
>
  {{ nav.title }}
</a>

                        <!-- DROPDOWN MENU -->
                        <div
                            v-if="nav.has_dropdown"
                            class="dropdown-menu border-0 shadow-lg"
                            :class="{ 'two-col': totalDropdownItems(nav) > 10 }"
                        >
                            <template v-if="nav.sub_items?.length">
                                <div class="dropdown-col">
                                    <router-link
                                        v-for="sub in nav.sub_items"
                                        :key="sub.id"
                                        class="dropdown-item"
                                        :to="sub.route"
                                    >{{ sub.title }}</router-link>
                                </div>
                            </template>

                            <template v-if="categoriesByNav[nav.id]?.length">
                                <template v-if="totalDropdownItems(nav) > 10">
                                    <div class="dropdown-col">
                                        <div v-for="cat in leftColItems(nav)" :key="cat.id" class="dropdown-submenu position-relative">
                                            <a href="#" class="dropdown-item d-flex justify-content-between align-items-center" @click.prevent="handleCategoryClickInNav(cat)">
                                                {{ cat.name }}
                                                <i v-if="cat.subcategories?.length" class="bi bi-chevron-right ms-2"></i>
                                            </a>
                                            <ul v-if="cat.subcategories?.length" class="sub-dropdown dropdown-menu border-0 shadow-lg">
                                                <li v-for="sub in cat.subcategories" :key="sub.id">
                                                    <a href="#" class="dropdown-item" @click.prevent="handleCategoryClickInNav(sub)">{{ sub.name }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="dropdown-col dropdown-col-right">
                                        <div v-for="cat in rightColItems(nav)" :key="cat.id" class="dropdown-submenu position-relative">
                                            <a href="#" class="dropdown-item d-flex justify-content-between align-items-center" @click.prevent="handleCategoryClickInNav(cat)">
                                                {{ cat.name }}
                                                <i v-if="cat.subcategories?.length" class="bi bi-chevron-right ms-2"></i>
                                            </a>
                                            <ul v-if="cat.subcategories?.length" class="sub-dropdown sub-dropdown-left dropdown-menu border-0 shadow-lg">
                                                <li v-for="sub in cat.subcategories" :key="sub.id">
                                                    <a href="#" class="dropdown-item" @click.prevent="handleCategoryClickInNav(sub)">{{ sub.name }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="dropdown-col">
                                        <div v-for="cat in categoriesByNav[nav.id]" :key="cat.id" class="dropdown-submenu position-relative">
                                            <a href="#" class="dropdown-item d-flex justify-content-between align-items-center" @click.prevent="handleCategoryClickInNav(cat)">
                                                {{ cat.name }}
                                                <i v-if="cat.subcategories?.length" class="bi bi-chevron-right ms-2"></i>
                                            </a>
                                            <ul v-if="cat.subcategories?.length" class="sub-dropdown dropdown-menu border-0 shadow-lg">
                                                <li v-for="sub in cat.subcategories" :key="sub.id">
                                                    <a href="#" class="dropdown-item" @click.prevent="handleCategoryClickInNav(sub)">{{ sub.name }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </li>
                </ul>

                <!-- Desktop right icons -->
                <div class="d-flex align-items-center gap-3 nav-icons ms-auto me-3">

                    <!-- SEARCH ICON -->
                    <div class="desktop-search-trigger" ref="searchTriggerRef">
                        <button class="search-icon-btn" @click="toggleDesktopSearch" :class="{ active: desktopSearchOpen }">
                            <i class="bi" :class="desktopSearchOpen ? 'bi-x' : 'bi-search'"></i>
                        </button>
                    </div>

                    <div class="position-relative cursor-pointer" @click="showCartSidebar = true">
                        <i class="bi bi-bag fs-5"></i>
                        <span v-if="cartStore.totalItems > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ cartStore.totalItems }}</span>
                    </div>
                    <router-link to="/register"><i class="bi bi-person fs-5"></i></router-link>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== DESKTOP SEARCH BAR (drops below navbar, inside black area) ===== -->
    <Transition name="search-drop">
        <div v-if="desktopSearchOpen" class="desktop-search-bar" ref="desktopSearchBarRef"
             :class="{ 'search-full': isScrolled || navigations.length > 8 }">
            <div class="desktop-search-inner">
                <i class="bi bi-search ds-icon"></i>
                <input
                    ref="desktopSearchInputRef"
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search products, categories, blogs..."
                    class="ds-input"
                    @input="onSearchInput"
                    @keydown.escape="closeDesktopSearch"
                />
                <div v-if="searchLoading" class="s-spinner"></div>
                <button v-if="searchQuery" class="s-clear" @click="clearSearch"><i class="bi bi-x"></i></button>
            </div>

            <div v-if="searchQuery.length >= 2" class="ds-results">
                <div v-if="searchLoading" class="s-loading"><div class="s-spinner-sm"></div> Searching...</div>
                <div v-else-if="totalResults === 0" class="s-empty">
                    <i class="bi bi-search"></i> No results for "<strong>{{ searchQuery }}</strong>"
                </div>
                <template v-if="!searchLoading">
                    <template v-if="results.products?.length">
                        <div class="s-label">Products</div>
                        <div v-for="item in results.products" :key="'p'+item.id" class="s-item" @click="goTo(item.url)">
                            <div class="s-thumb"><img v-if="item.image" :src="item.image" :alt="item.name" /><i v-else class="bi bi-box"></i></div>
                            <div class="s-meta"><span class="s-name">{{ item.name }}</span><span class="s-price">${{ Number(item.price).toFixed(2) }}</span></div>
                            <i class="bi bi-arrow-right s-arrow"></i>
                        </div>
                    </template>
                    <template v-if="results.models?.length">
                        <div class="s-label">Models</div>
                        <div v-for="item in results.models" :key="'mo'+item.id" class="s-item" @click="goTo(item.url)">
                            <div class="s-thumb"><img v-if="item.image" :src="item.image" :alt="item.name" /><i v-else class="bi bi-box-seam"></i></div>
                            <div class="s-meta"><span class="s-name">{{ item.name }}</span><span class="s-price">${{ Number(item.price).toFixed(2) }}</span></div>
                            <i class="bi bi-arrow-right s-arrow"></i>
                        </div>
                    </template>
                    <template v-if="results.categories?.length">
                        <div class="s-label">Categories</div>
                        <div v-for="item in results.categories" :key="'c'+item.id" class="s-item" @click="goTo(item.url)">
                            <div class="s-thumb round"><img v-if="item.image" :src="item.image" :alt="item.name" /><i v-else class="bi bi-grid"></i></div>
                            <div class="s-meta"><span class="s-name">{{ item.name }}</span><span class="s-badge">Category</span></div>
                            <i class="bi bi-arrow-right s-arrow"></i>
                        </div>
                    </template>
                    <template v-if="results.blogs?.length">
                        <div class="s-label">Blogs</div>
                        <div v-for="item in results.blogs" :key="'b'+item.id" class="s-item" @click="goTo(item.url)">
                            <div class="s-thumb"><img v-if="item.image" :src="item.image" :alt="item.name" /><i v-else class="bi bi-newspaper"></i></div>
                            <div class="s-meta"><span class="s-name">{{ item.name }}</span><span class="s-badge">Blog</span></div>
                            <i class="bi bi-arrow-right s-arrow"></i>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </Transition>

    <!-- Backdrop for desktop search -->
    <div v-if="desktopSearchOpen" class="s-backdrop" @click="closeDesktopSearch"></div>

    <!-- ===== MOBILE SEARCH ===== -->
    <Transition name="mobile-drop">
        <div v-if="mobileSearchOpen" class="m-search-overlay">
            <div class="m-search-bar">
                <i class="bi bi-search m-search-icon"></i>
                <input
                    ref="mobileSearchRef"
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search products, categories..."
                    class="m-search-input"
                    @input="onSearchInput"
                    @keydown.escape="closeMobileSearch"
                />
                <div v-if="searchLoading" class="s-spinner"></div>
                <button v-if="searchQuery" class="s-clear" @click="clearSearch"><i class="bi bi-x"></i></button>
                <button class="m-search-close" @click="closeMobileSearch"><i class="bi bi-x-lg"></i></button>
            </div>

            <div v-if="searchQuery.length >= 2" class="m-search-results">
                <div v-if="searchLoading" class="s-loading dark"><div class="s-spinner-sm dark"></div> Searching...</div>
                <div v-else-if="totalResults === 0" class="s-empty dark"><i class="bi bi-search"></i> No results for "<strong>{{ searchQuery }}</strong>"</div>

                <template v-if="!searchLoading">
                    <template v-if="results.products?.length">
                        <div class="s-label dark">Products</div>
                        <div v-for="item in results.products" :key="'mp'+item.id" class="s-item dark" @click="goTo(item.url); closeMobileSearch()">
                            <div class="s-thumb"><img v-if="item.image" :src="item.image" :alt="item.name" /><i v-else class="bi bi-box"></i></div>
                            <div class="s-meta"><span class="s-name dark">{{ item.name }}</span><span class="s-price">${{ Number(item.price).toFixed(2) }}</span></div>
                            <i class="bi bi-arrow-right s-arrow dark"></i>
                        </div>
                    </template>
                    <template v-if="results.models?.length">
                        <div class="s-label dark">Models</div>
                        <div v-for="item in results.models" :key="'mm'+item.id" class="s-item dark" @click="goTo(item.url); closeMobileSearch()">
                            <div class="s-thumb"><img v-if="item.image" :src="item.image" :alt="item.name" /><i v-else class="bi bi-box-seam"></i></div>
                            <div class="s-meta"><span class="s-name dark">{{ item.name }}</span><span class="s-price">${{ Number(item.price).toFixed(2) }}</span></div>
                            <i class="bi bi-arrow-right s-arrow dark"></i>
                        </div>
                    </template>
                    <template v-if="results.categories?.length">
                        <div class="s-label dark">Categories</div>
                        <div v-for="item in results.categories" :key="'mc'+item.id" class="s-item dark" @click="goTo(item.url); closeMobileSearch()">
                            <div class="s-thumb round"><img v-if="item.image" :src="item.image" :alt="item.name" /><i v-else class="bi bi-grid"></i></div>
                            <div class="s-meta"><span class="s-name dark">{{ item.name }}</span><span class="s-badge dark">Category</span></div>
                            <i class="bi bi-arrow-right s-arrow dark"></i>
                        </div>
                    </template>
                    <template v-if="results.blogs?.length">
                        <div class="s-label dark">Blogs</div>
                        <div v-for="item in results.blogs" :key="'mb'+item.id" class="s-item dark" @click="goTo(item.url); closeMobileSearch()">
                            <div class="s-thumb"><img v-if="item.image" :src="item.image" :alt="item.name" /><i v-else class="bi bi-newspaper"></i></div>
                            <div class="s-meta"><span class="s-name dark">{{ item.name }}</span><span class="s-badge dark">Blog</span></div>
                            <i class="bi bi-arrow-right s-arrow dark"></i>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </Transition>

    <!-- ===== MOBILE DRAWER ===== -->
    <transition name="fade-overlay">
        <div v-if="drawerOpen" class="drawer-overlay" @click="closeDrawer"></div>
    </transition>
    <transition name="slide-drawer">
        <div v-if="drawerOpen" class="mobile-drawer">
            <div class="drawer-header d-flex align-items-center justify-content-between px-4 py-3">
                <img src="/public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png" alt="Prosix" style="height:28px;" />
                <button class="drawer-close-btn" @click="closeDrawer"><i class="bi bi-x-lg text-white fs-5"></i></button>
            </div>
            <div class="drawer-divider"></div>
            <ul class="drawer-nav list-unstyled m-0 p-0">
                <li v-for="nav in navigations" :key="nav.id" class="drawer-nav-item">
                    <router-link v-if="!nav.has_dropdown && nav.route" class="drawer-link d-flex align-items-center justify-content-between px-4 py-3" :to="nav.route" @click="closeDrawer">
                        <span>{{ nav.title }}</span><i class="bi bi-arrow-right text-white opacity-50"></i>
                    </router-link>
                    <div v-else class="d-flex align-items-center justify-content-between drawer-link-row">
<span class="drawer-link drawer-link-text px-4 py-3 flex-grow-1" @click="nav.clickable ? (goToMenu(nav.slug), closeDrawer()) : null">{{ nav.title }}</span>
                        <button class="drawer-toggle-icon px-3 py-3" @click="toggleAccordion(nav.id)">
                            <i class="bi fs-6 transition-icon" :class="openAccordion === nav.id ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                        </button>
                    </div>
                    <transition name="accordion">
                        <div v-if="nav.has_dropdown && openAccordion === nav.id" class="drawer-accordion-body">
                            <router-link v-for="sub in nav.sub_items" :key="sub.id" class="drawer-sub-link d-flex align-items-center px-5 py-2" :to="sub.route" @click="closeDrawer">
                                <i class="bi bi-dash me-2 opacity-50"></i>{{ sub.title }}
                            </router-link>
                            <div v-for="cat in categoriesByNav[nav.id] || []" :key="cat.id">
                                <div v-if="cat.subcategories?.length" class="d-flex align-items-center drawer-sub-row">
                                    <span class="drawer-sub-link flex-grow-1 d-flex align-items-center px-5 py-2" @click="handleCategoryClickInNav(cat); closeDrawer()" style="cursor:pointer;">
                                        <i class="bi bi-dash me-2 opacity-50"></i>{{ cat.name }}
                                    </span>
                                    <button class="drawer-sub-toggle-icon px-3 py-2" @click="toggleSubAccordion(cat.id)">
                                        <i class="bi fs-6" :class="openSubAccordion === cat.id ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                    </button>
                                </div>
                                <transition name="accordion">
                                    <div v-if="cat.subcategories?.length && openSubAccordion === cat.id" class="drawer-sub-sub-body">
                                        <a v-for="sub in cat.subcategories" :key="sub.id" href="#" class="drawer-subsub-link d-flex align-items-center py-2" style="padding-left:60px!important" @click.prevent="handleCategoryClickInNav(sub); closeDrawer()">
                                            <i class="bi bi-dot me-1 opacity-40"></i>{{ sub.name }}
                                        </a>
                                    </div>
                                </transition>
                                <a v-if="!cat.subcategories?.length" href="#" class="drawer-sub-link d-flex align-items-center px-5 py-2" @click.prevent="handleCategoryClickInNav(cat); closeDrawer()">
                                    <i class="bi bi-dash me-2 opacity-50"></i>{{ cat.name }}
                                </a>
                            </div>
                        </div>
                    </transition>
                    <div class="drawer-item-divider"></div>
                </li>
            </ul>
            <div class="drawer-footer px-4 py-4">
                <router-link to="/register" class="drawer-footer-link d-flex align-items-center gap-3" @click="closeDrawer">
                    <i class="bi bi-person-circle fs-5"></i><span>My Account</span>
                </router-link>
            </div>
        </div>
    </transition>

    <!-- ===== CART SIDEBAR ===== -->
<div v-if="showCartSidebar"
class="cart-sidebar position-fixed end-0 bg-white shadow-lg p-4"
style="width:400px; z-index:1050; top:50px; height:calc(100% - 50px);">        <h4 class="mb-4">Your Cart</h4>
        <button class="btn-close position-absolute top-0 end-0 m-3" @click="showCartSidebar = false"></button>
        <div v-if="cartStore.items.length === 0"><p>Cart is empty</p></div>
        <div v-else>
            <div v-for="item in cartStore.items" :key="`${item.id}-${item.size}`" class="d-flex align-items-center mb-3 border-bottom pb-3">
                <img :src="item.image" class="me-3" style="width:60px;height:60px;object-fit:cover;" />
                <div class="flex-grow-1"><h6>{{ item.name }} ({{ item.size }})</h6><p>${{ Number(item.price) * item.quantity }}</p></div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary" @click="cartStore.updateQuantity(item.id, item.size, item.quantity-1)">-</button>
                    <span>{{ item.quantity }}</span>
                    <button class="btn btn-sm btn-outline-secondary" @click="cartStore.updateQuantity(item.id, item.size, item.quantity+1)">+</button>
                    <button class="btn btn-sm btn-danger" @click="cartStore.removeItem(item.id, item.size)">Remove</button>
                </div>
            </div>
            <div class="mt-4">
                <h5>Total: ${{ cartStore.totalPrice }}</h5>
                <button class="btn btn-dark w-100" @click="goToCheckout">Checkout</button>
            </div>
        </div>
    </div>

    <!-- ===== PASSWORD MODAL ===== -->
    <div v-if="showPasswordModal" class="password-modal-backdrop">
        <div class="password-modal-box">
            <button class="close-btn" @click="closePasswordModal">×</button>
            <h5 class="mb-3 text-center">Enter Password</h5>
            <input type="password" v-model="enteredPassword" class="form-control mb-3" placeholder="Password" />
            <p v-if="passwordError" class="text-danger small text-center">{{ passwordError }}</p>
            <button class="btn btn-dark w-100" @click="submitPassword">Unlock</button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"
import { useCartStore } from "@/store/cart"

const cartStore = useCartStore()
const router = useRouter()
const goToCheckout = () => { showCartSidebar.value = false; router.push('/checkout') }

// ── Scroll
const isScrolled = ref(false)
const handleScroll = () => { isScrolled.value = window.scrollY > 80 }

// ── Search state
const searchQuery       = ref('')
const searchLoading     = ref(false)
const results           = ref({ products: [], categories: [], blogs: [], models: [] })
const mobileSearchOpen  = ref(false)
const mobileSearchRef   = ref(null)
const desktopSearchOpen = ref(false)
const desktopSearchInputRef = ref(null)
const desktopSearchBarRef   = ref(null)
let debounceTimer = null

const totalResults = computed(() =>
  (results.value.products?.length   || 0) +
  (results.value.categories?.length || 0) +
  (results.value.blogs?.length      || 0) +
  (results.value.models?.length     || 0)
)

const emptyResults = () => ({ products: [], categories: [], blogs: [], models: [] })

const onSearchInput = () => {
  clearTimeout(debounceTimer)
  if (searchQuery.value.length < 2) { results.value = emptyResults(); return }
  searchLoading.value = true
  debounceTimer = setTimeout(async () => {
    try {
      const res = await axios.get('/api/search', { params: { q: searchQuery.value, limit: 6 } })
      results.value = res.data
    } catch (e) { console.error(e) }
    finally { searchLoading.value = false }
  }, 350)
}

// ── Desktop search toggle
const toggleDesktopSearch = () => {
  desktopSearchOpen.value = !desktopSearchOpen.value
  if (desktopSearchOpen.value) {
    nextTick(() => desktopSearchInputRef.value?.focus())
  } else {
    clearSearch()
  }
}
const closeDesktopSearch = () => {
  desktopSearchOpen.value = false
  clearSearch()
}

// ── goTo
const goTo = (url) => {
  closeDesktopSearch()
  mobileSearchOpen.value = false
  searchQuery.value = ''
  results.value = emptyResults()
  if (url.startsWith('/')) {
    router.push(url)
  } else {
    window.location.href = url
  }
}

const clearSearch       = () => { searchQuery.value = ''; results.value = emptyResults() }
const openMobileSearch  = () => { mobileSearchOpen.value = true; nextTick(() => mobileSearchRef.value?.focus()) }
const closeMobileSearch = () => { mobileSearchOpen.value = false; clearSearch() }

// ── Cart
const showCartSidebar = ref(false)

// ── Drawer
const drawerOpen       = ref(false)
const openAccordion    = ref(null)
const openSubAccordion = ref(null)
const toggleDrawer       = () => { drawerOpen.value = !drawerOpen.value }
const closeDrawer        = () => { drawerOpen.value = false; openAccordion.value = null; openSubAccordion.value = null }
const toggleAccordion    = (id) => { openAccordion.value = openAccordion.value === id ? null : id; openSubAccordion.value = null }
const toggleSubAccordion = (id) => { openSubAccordion.value = openSubAccordion.value === id ? null : id }
watch(drawerOpen, (val) => { document.body.style.overflow = val ? "hidden" : "" })

// ── Dropdown helpers
const totalDropdownItems = (nav) => {
  const subs = nav.sub_items?.length || 0
  const cats = categoriesByNav.value[nav.id]?.length || 0
  return subs + cats
}
const leftColItems = (nav) => {
  const cats = categoriesByNav.value[nav.id] || []
  return cats.slice(0, 10)
}
const rightColItems = (nav) => {
  const cats = categoriesByNav.value[nav.id] || []
  return cats.slice(10)
}

// ── Password modal
const showPasswordModal  = ref(false)
const enteredPassword    = ref('')
const passwordError      = ref('')
const pendingRoute       = ref(null)
const selectedCategoryId = ref(null)

const handleCategoryClickInNav = (item) => {
  if (item?.password) {
    selectedCategoryId.value = item.id
    pendingRoute.value = item.subcategories?.length > 0
      ? { name: 'Subcategories', params: { id: item.id } }
      : { name: 'CategoryProducts', params: { id: item.id } }
    showPasswordModal.value = true
  } else {
    router.push(item.subcategories?.length > 0
      ? { name: 'Subcategories', params: { id: item.id } }
      : { name: 'CategoryProducts', params: { id: item.id } })
  }
}
const submitPassword = async () => {
  try {
    await axios.post(`/api/categories/${selectedCategoryId.value}/verify-password`, { password: enteredPassword.value })
    showPasswordModal.value = false; enteredPassword.value = ''; passwordError.value = ''
    router.push(pendingRoute.value)
  } catch { passwordError.value = 'Incorrect password. Contact admin.' }
}
const closePasswordModal = () => {
  showPasswordModal.value = false; enteredPassword.value = ''; passwordError.value = ''
  pendingRoute.value = null; selectedCategoryId.value = null
}

// ── Navigation
const navigations     = ref([])
const categoriesByNav = ref({})
const goToMenu = (slug) => { if (slug) router.push({ name: "MenuCategories", params: { slug } }) }

// Close desktop search on outside click
const handleOutsideClick = (e) => {
  if (desktopSearchOpen.value && desktopSearchBarRef.value && !desktopSearchBarRef.value.contains(e.target)) {
    // backdrop handles this
  }
}

onMounted(async () => {
  window.addEventListener("scroll", handleScroll)
  document.addEventListener("click", handleOutsideClick)
  try {
    const navRes = await axios.get("/api/navigations")
    navigations.value = navRes.data
    const catRes = await axios.get("/api/categories-by-navigation")
    categoriesByNav.value = {}
    Object.keys(catRes.data).forEach((key) => { categoriesByNav.value[Number(key)] = catRes.data[key] || [] })
  } catch (err) { console.error("Fetch error:", err) }
})
onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll)
  document.removeEventListener("click", handleOutsideClick)
  clearTimeout(debounceTimer)
})
</script>

<style scoped>
/* ── Navbar base ── */
.custom-navbar {
  height: 50px;
  background: transparent;
  transition: all 0.35s ease;
  z-index: 1000;
  display: flex;
  align-items: center;
}

/*
  The black slanted background covers the RIGHT 72% of navbar.
  Nav items start FROM where the cut begins (28% from left).
  The ::before pseudo element creates the slanted black shape.
*/
.custom-navbar::before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  width: 73%;
  height: 100%;
  background: #000;
  /* polygon: top-left angled, top-right square, bottom-right square, bottom-left square */
  clip-path: polygon(0% 0, 100% 0, 100% 100%, 4% 100%);
  transition: all 0.35s ease;
  z-index: -1;
}
.badge {
  font-size: 12px;
  margin-top: 2px;
  padding: 2px 5px;
  min-width: 15px;
  height: 15px;
  border-radius: 50%;
}
/* When scrolled OR more than 8 items → full black navbar */
.navbar-scrolled,
.navbar-full {
  height: 50px;
  background: #000;
}
.navbar-scrolled::before,
.navbar-full::before {
  width: 100%;
  clip-path: none;
}

/* Logo */
.navbar-brand-fixed {
  /* Logo sits in the transparent (white) left 28% area */
  width: 28%;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  padding-left: 16px;
}
.navbar-logo { height: 80px; margin-top: 70px; transition: all 0.35s ease; }
.navbar-logo-secondary { height: 24px; margin-left: 30px; }
.logo-small { height: 24px; margin-bottom: 70px; filter: brightness(0) invert(1); }

/*
  nav-right-block: this is the collapsible area.
  It starts exactly at the 28% mark (where black cut begins),
  and stretches to the right end.
  We achieve this by making the navbar brand take ~28% and the rest auto.
*/
.nav-right-block {
  display: flex !important;
  align-items: center;
  flex: 1;
  width: 72%;
  min-width: 0;
  justify-content: flex-start;
  padding-left: 0;
}

/* ── Nav items list ── */
.nav-items-list {
  display: flex;
  align-items: center;
  gap: clamp(6px, 1vw, 22px);
  flex-wrap: nowrap;
  list-style: none;
  padding: 0;
  margin: 0 !important;
  padding-left: 30px;
  justify-content: flex-start;
}

.nav-link {
  color: #fff !important;
  font-weight: 600;
  font-size: clamp(0.85rem, 1.1vw, 1.05rem);
  white-space: nowrap;
  padding: 6px 2px !important;
}
.nav-link:hover { color: #ddd !important; }
.nav-icons i { color: #fff; cursor: pointer; font-size: clamp(0.9rem, 1.1vw, 1.15rem); }

/* ── Desktop Search Icon Button ── */
.desktop-search-trigger { position: relative; }
.search-icon-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.1rem;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 50%;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
}
.search-icon-btn:hover, .search-icon-btn.active { background: rgba(255,255,255,0.15); }

/* ── Desktop Search Bar ──
   Default: aligned to right 72% (same as black cut area)
   When scrolled or >8 items: full width
*/
.desktop-search-bar {
  position: fixed;
  top: 50px;
  left: 60%;
  right: 0;
  background: #000000;
  z-index: 1080;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  transition: left 0.35s ease;

}

/* when navbar becomes full width */
.desktop-search-bar.search-full {
  left: 0;

  /* remove angle */
  clip-path: none;
}

.desktop-search-inner {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 40px;
}
.ds-icon { color: rgba(255,255,255,0.5); font-size: 1rem; flex-shrink: 0; }
.ds-input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  color: #fff;
  font-size: 1rem;
  font-family: 'Poppins', sans-serif;
}
.ds-input::placeholder { color: rgba(255,255,255,0.3); }
.ds-results {
  max-height: 420px;
  overflow-y: auto;
  border-top: 1px solid rgba(255,255,255,0.08);
  padding-bottom: 8px;
}
.ds-results::-webkit-scrollbar { width: 3px; }
.ds-results::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }

/* Search drop animation */
.search-drop-enter-active { transition: all 0.28s cubic-bezier(0.34,1.3,0.64,1); }
.search-drop-leave-active { transition: all 0.18s ease; }
.search-drop-enter-from { opacity: 0; transform: translateY(-12px); }
.search-drop-leave-to { opacity: 0; transform: translateY(-6px); }

/* Shared search result styles */
.s-spinner { width: 13px; height: 13px; border: 2px solid rgba(255,255,255,0.2); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; flex-shrink: 0; }
@keyframes spin { to { transform: rotate(360deg); } }
.s-clear { background: none; border: none; color: rgba(255,255,255,0.5); font-size: 1rem; cursor: pointer; padding: 0; line-height: 1; flex-shrink: 0; transition: color 0.2s; }
.s-clear:hover { color: #fff; }
.s-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.35); padding: 10px 40px 4px; }
.s-label.dark { color: rgba(255,255,255,0.35); }
.s-item { display: flex; align-items: center; gap: 10px; padding: 9px 40px; cursor: pointer; transition: background 0.15s; }
.s-item:hover { background: rgba(255,255,255,0.07); }
.s-item.dark:hover { background: rgba(255,255,255,0.07); }
.s-thumb { width: 38px; height: 38px; border-radius: 8px; overflow: hidden; background: rgba(255,255,255,0.1); flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.4); font-size: 1rem; }
.s-thumb img { width: 100%; height: 100%; object-fit: cover; }
.s-thumb.round { border-radius: 50%; }
.s-meta { flex: 1; min-width: 0; }
.s-name { display: block; font-size: 0.84rem; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.s-name.dark { color: #fff; }
.s-price { display: block; font-size: 0.76rem; font-weight: 700; color: rgba(255,255,255,0.6); margin-top: 1px; }
.s-badge { display: block; font-size: 0.72rem; color: rgba(255,255,255,0.4); margin-top: 1px; }
.s-badge.dark { color: rgba(255,255,255,0.4); }
.s-arrow { color: rgba(255,255,255,0.25); font-size: 0.8rem; flex-shrink: 0; transition: all 0.15s; }
.s-item:hover .s-arrow { color: #fff; transform: translateX(2px); }
.s-arrow.dark { color: rgba(255,255,255,0.25); }
.s-item.dark:hover .s-arrow { color: #fff; }
.s-loading { display: flex; align-items: center; gap: 10px; padding: 16px 40px; font-size: 0.84rem; color: rgba(255,255,255,0.45); }
.s-loading.dark { color: rgba(255,255,255,0.45); }
.s-spinner-sm { width: 13px; height: 13px; border: 2px solid rgba(255,255,255,0.2); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; flex-shrink: 0; }
.s-spinner-sm.dark { border-color: rgba(255,255,255,0.2); border-top-color: #fff; }
.s-empty { padding: 18px 40px; font-size: 0.84rem; color: rgba(255,255,255,0.4); display: flex; align-items: center; gap: 8px; }
.s-empty.dark { color: rgba(255,255,255,0.4); }
.s-backdrop { position: fixed; inset: 0; z-index: 1070; }

/* ── Mobile search ── */
.m-search-overlay { position: fixed; top: 56px; left: 0; right: 0; background: #111; z-index: 1099; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
.m-search-bar { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.08); }
.m-search-icon { color: rgba(255,255,255,0.4); font-size: 0.95rem; flex-shrink: 0; }
.m-search-input { flex: 1; background: transparent; border: none; outline: none; color: #fff; font-size: 0.95rem; font-family: 'Poppins', sans-serif; }
.m-search-input::placeholder { color: rgba(255,255,255,0.3); }
.m-search-close { background: none; border: none; color: rgba(255,255,255,0.5); font-size: 0.95rem; cursor: pointer; padding: 0; flex-shrink: 0; }
.m-search-close:hover { color: #fff; }
.m-search-results { max-height: 55vh; overflow-y: auto; }
.m-search-results::-webkit-scrollbar { width: 3px; }
.m-search-results::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
.m-search-results .s-label { padding-left: 16px; }
.m-search-results .s-item { padding-left: 16px; padding-right: 16px; }
.m-search-results .s-loading { padding-left: 16px; }
.m-search-results .s-empty { padding-left: 16px; }

.mobile-drop-enter-active { transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1); }
.mobile-drop-leave-active { transition: all 0.18s ease; }
.mobile-drop-enter-from { opacity: 0; transform: translateY(-10px); }
.mobile-drop-leave-to { opacity: 0; transform: translateY(-6px); }

/* ── Dropdown menu ── */
.dropdown-menu {
  display: none;
  margin-top: 0;
  border-radius: 6px;
  background: #000;
  min-width: 220px;
  padding: 6px 0;
}
.nav-item.dropdown:hover > .dropdown-menu { display: flex; flex-direction: row; }
.dropdown-menu:not(.two-col) { flex-direction: column !important; }
.dropdown-col { display: flex; flex-direction: column; min-width: 200px; }
.dropdown-col-right { border-left: 1px solid rgba(255,255,255,0.08); }
.dropdown-item { padding: 8px 16px; color: #fff !important; transition: all 0.15s; font-size: clamp(0.88rem, 1vw, 1rem);
         white-space: nowrap; }
.dropdown-item:hover { background-color: #222 !important; }

.dropdown-submenu { position: relative; }
.dropdown-submenu:hover > .sub-dropdown { display: block !important; }
.sub-dropdown {
  display: none;
  position: absolute !important;
  top: 0;
  left: 100%;
  min-width: 200px;
  margin-left: 2px;
  background: #111;
  z-index: 10;
}
.sub-dropdown-left {
  left: auto !important;
  right: 100% !important;
  margin-left: 0 !important;
  margin-right: 2px !important;
}

/* ── Mobile Drawer ── */
.mobile-top-icons a { color: inherit; text-decoration: none; }
.hamburger-btn { background: none; border: none; cursor: pointer; display: flex; flex-direction: column; gap: 5px; padding: 4px; }
.hamburger-btn span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 2px; transition: all 0.3s ease; transform-origin: center; }
.hamburger-btn span:nth-child(1).open { transform: translateY(7px) rotate(45deg); }
.hamburger-btn span:nth-child(2).open { opacity: 0; transform: scaleX(0); }
.hamburger-btn span:nth-child(3).open { transform: translateY(-7px) rotate(-45deg); }

.drawer-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 1100; backdrop-filter: blur(2px); }
.mobile-drawer { position: fixed; top: 0; left: 0; width: 300px; height: 100dvh; background: #000; z-index: 1200; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }
.drawer-header { background: #0a0a0a; min-height: 58px; border-bottom: 1px solid rgba(255,255,255,0.08); }
.drawer-close-btn { background: none; border: none; cursor: pointer; padding: 4px; line-height: 1; }
.drawer-divider { height: 1px; background: rgba(255,255,255,0.06); }
.drawer-nav { flex: 1; }
.drawer-nav-item { border: none; }
.drawer-link-row { width: 100%; }
.drawer-link { color: #fff !important; text-decoration: none; font-size: 1rem; font-weight: 600; letter-spacing: 0.03em; text-transform: uppercase; background: transparent; border: none; cursor: pointer; transition: background 0.2s; width: 100%; }
.drawer-link:hover { background: rgba(255,255,255,0.05); }
.drawer-link-text { color: #fff !important; font-size: 1rem; font-weight: 600; letter-spacing: 0.03em; text-transform: uppercase; cursor: pointer; transition: background 0.2s; display: block; }
.drawer-link-text:hover { background: rgba(255,255,255,0.05); }
.drawer-toggle-icon { background: transparent; border: none; border-left: 1px solid rgba(255,255,255,0.08); cursor: pointer; color: #fff; min-width: 48px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; flex-shrink: 0; }
.drawer-toggle-icon:hover { background: rgba(255,255,255,0.08); }
.transition-icon { transition: transform 0.25s ease; color: #fff; }
.drawer-accordion-body { background: #0d0d0d; }
.drawer-sub-link { color: rgba(255,255,255,0.75) !important; text-decoration: none; font-size: 0.9rem; font-weight: 500; background: transparent; border: none; cursor: pointer; width: 100%; transition: background 0.2s, color 0.2s; text-align: left; }
.drawer-sub-link:hover { background: rgba(255,255,255,0.07); color: #fff !important; }
.drawer-sub-row { width: 100%; }
.drawer-sub-toggle-icon { background: transparent; border: none; border-left: 1px solid rgba(255,255,255,0.06); cursor: pointer; color: rgba(255,255,255,0.6); min-width: 40px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; flex-shrink: 0; }
.drawer-sub-sub-body { background: #111; }
.drawer-subsub-link { color: rgba(255,255,255,0.55) !important; text-decoration: none; font-size: 0.85rem; display: flex; background: transparent; border: none; width: 100%; cursor: pointer; transition: background 0.2s; }
.drawer-subsub-link:hover { background: rgba(255,255,255,0.05); color: #fff !important; }
.drawer-item-divider { height: 1px; background: rgba(255,255,255,0.06); }
.drawer-footer { border-top: 1px solid rgba(255,255,255,0.08); margin-top: auto; }
.drawer-footer-link { color: rgba(255,255,255,0.7) !important; text-decoration: none; font-size: 0.95rem; font-weight: 600; transition: color 0.2s; }
.drawer-footer-link:hover { color: #fff !important; }
.slide-drawer-enter-active, .slide-drawer-leave-active { transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
.slide-drawer-enter-from, .slide-drawer-leave-to { transform: translateX(-100%); }
.fade-overlay-enter-active, .fade-overlay-leave-active { transition: opacity 0.3s ease; }
.fade-overlay-enter-from, .fade-overlay-leave-to { opacity: 0; }
.accordion-enter-active, .accordion-leave-active { transition: max-height 0.3s ease, opacity 0.25s ease; overflow: hidden; max-height: 600px; opacity: 1; }
.accordion-enter-from, .accordion-leave-to { max-height: 0; opacity: 0; }

/* ── Cart & Password modal ── */
.cart-sidebar { overflow-y: auto; }
.password-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 2000; }
.password-modal-box { background: #fff; padding: 30px 25px; border-radius: 12px; max-width: 400px; width: 90%; box-shadow: 0 15px 40px rgba(0,0,0,0.4); position: relative; animation: fadeInModal 0.3s ease; }
.close-btn { position: absolute; top: 12px; right: 12px; background: none; border: none; font-size: 1.3rem; cursor: pointer; }
@keyframes fadeInModal { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

/* ── Responsive ── */
@media (max-width: 1200px) {
  .nav-items-list { gap: clamp(4px, 0.7vw, 14px); }
  .nav-link { font-size: clamp(0.80rem, 0.95vw, 0.95rem); }
}
@media (max-width: 991px) {
  .custom-navbar { height: 80px; background: #000 !important; }
  .custom-navbar::before { display: none; }
  .navbar-logo { height: 28px; margin-top: 0; filter: brightness(0) invert(1); }
  .logo-small { margin-bottom: 0; }
  #mainNavbar { display: none !important; }
  .cart-sidebar { width: 100% !important; }
  .desktop-search-bar { top: 56px; left: 0 !important; }
}
@media (max-width: 400px) { .mobile-drawer { width: 85vw; } }
</style>
