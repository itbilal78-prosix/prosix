<template>
    <nav
        class="navbar navbar-expand-lg fixed-top custom-navbar"
        :class="{ 'navbar-scrolled': isScrolled }"
    >
        <div class="container-fluid px-4 px-lg-5">
            <!-- Logo -->
            <router-link to="/" class="navbar-brand">
                <img
                    src="/public/assets/images/P LOGO BLACK.png"
                    alt="Prosix Logo"
                    class="navbar-logo"
                    :class="{ 'logo-small': isScrolled }"
                />
                <img
                    v-if="isScrolled"
                    src="/public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png"
                    alt="Secondary Logo"
                    class="navbar-logo-secondary"
                />
            </router-link>

            <!-- Right side icons + hamburger (mobile) -->
            <div class="d-flex align-items-center gap-3 ms-auto d-lg-none mobile-top-icons">
                <i class="bi bi-search fs-5 text-white" @click="toggleSearch"></i>
                <div class="position-relative cursor-pointer" @click="showCartSidebar = true">
                    <i class="bi bi-bag fs-5 text-white"></i>
                    <span
                        v-if="cartStore.totalItems > 0"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    >
                        {{ cartStore.totalItems }}
                    </span>
                </div>
                <router-link to="/register">
                    <i class="bi bi-person fs-5 text-white"></i>
                </router-link>
                <!-- Hamburger -->
                <button class="hamburger-btn" @click="toggleDrawer" aria-label="Open menu">
                    <span :class="{ open: drawerOpen }"></span>
                    <span :class="{ open: drawerOpen }"></span>
                    <span :class="{ open: drawerOpen }"></span>
                </button>
            </div>

            <!-- ===== DESKTOP MENU ===== -->
<div class="collapse navbar-collapse d-flex align-items-center" id="mainNavbar">
                    <ul class="navbar-nav menu-right gap-4 ">
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
                        >
                            {{ nav.title }}
                        </router-link>

                        <a
                            v-else
                            class="nav-link dropdown-toggle"
                            href="#"
                            @click.prevent="goToMenu(nav.slug)"
                        >
                            {{ nav.title }}
                        </a>

                        <ul v-if="nav.has_dropdown" class="dropdown-menu border-0 shadow-lg">
                            <li v-for="sub in nav.sub_items" :key="sub.id">
                                <router-link class="dropdown-item" :to="sub.route">
                                    {{ sub.title }}
                                </router-link>
                            </li>

                            <li
                                v-for="cat in categoriesByNav[nav.id] || []"
                                :key="cat.id"
                                class="dropdown-submenu position-relative"
                            >
                                <a
                                    href="#"
                                    class="dropdown-item d-flex justify-content-between align-items-center"
                                    @click.prevent="handleCategoryClickInNav(cat)"
                                >
                                    {{ cat.name }}
                                    <i v-if="cat.subcategories?.length" class="bi bi-chevron-right ms-2"></i>
                                </a>

                                <ul v-if="cat.subcategories?.length" class="sub-dropdown dropdown-menu border-0 shadow-lg">
                                    <li v-for="sub in cat.subcategories" :key="sub.id">
                                        <a
                                            href="#"
                                            class="dropdown-item"
                                            @click.prevent="handleCategoryClickInNav(sub)"
                                        >
                                            {{ sub.name }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Desktop Icons -->
                <div class="d-flex align-items-center gap-4 nav-icons position-relative ms-4">
                    <i class="bi bi-search fs-5" @click="toggleSearch"></i>
                    <input
                        v-if="showSearch"
                        type="text"
                        placeholder="Search..."
                        class="search-input form-control form-control-sm"
                    />
                    <div class="position-relative cursor-pointer" @click="showCartSidebar = true">
                        <i class="bi bi-bag fs-5"></i>
                        <span
                            v-if="cartStore.totalItems > 0"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        >
                            {{ cartStore.totalItems }}
                        </span>
                    </div>
                    <router-link to="/register">
                        <i class="bi bi-person fs-5"></i>
                    </router-link>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== MOBILE SIDE DRAWER OVERLAY ===== -->
    <transition name="fade-overlay">
        <div
            v-if="drawerOpen"
            class="drawer-overlay"
            @click="closeDrawer"
        ></div>
    </transition>

    <!-- ===== MOBILE SIDE DRAWER ===== -->
    <transition name="slide-drawer">
        <div v-if="drawerOpen" class="mobile-drawer">
            <!-- Drawer Header -->
            <div class="drawer-header d-flex align-items-center justify-content-between px-4 py-3">
                <img
                    src="/public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png"
                    alt="Prosix"
                    style="height: 28px;"
                />
                <button class="drawer-close-btn" @click="closeDrawer">
                    <i class="bi bi-x-lg text-white fs-5"></i>
                </button>
            </div>

            <div class="drawer-divider"></div>

            <!-- Drawer Nav Items -->
            <ul class="drawer-nav list-unstyled m-0 p-0">
                <li
                    v-for="nav in navigations"
                    :key="nav.id"
                    class="drawer-nav-item"
                >
                    <!-- Simple link (no dropdown) -->
                    <router-link
                        v-if="!nav.has_dropdown && nav.route"
                        class="drawer-link d-flex align-items-center justify-content-between px-4 py-3"
                        :to="nav.route"
                        @click="closeDrawer"
                    >
                        <span>{{ nav.title }}</span>
                        <i class="bi bi-arrow-right text-white opacity-50"></i>
                    </router-link>

                    <!-- ✅ FIXED: Accordion item (has dropdown) - Text navigates, Icon toggles -->
                    <div v-else class="d-flex align-items-center justify-content-between drawer-link-row">
                        <!-- Text: navigates to menu page -->
                        <span
                            class="drawer-link drawer-link-text px-4 py-3 flex-grow-1"
                            @click="goToMenu(nav.slug); closeDrawer()"
                        >
                            {{ nav.title }}
                        </span>
                        <!-- Icon: toggles accordion -->
                        <button
                            class="drawer-toggle-icon px-3 py-3"
                            @click="toggleAccordion(nav.id)"
                            aria-label="Toggle dropdown"
                        >
                            <i
                                class="bi fs-6 transition-icon"
                                :class="openAccordion === nav.id ? 'bi-chevron-up' : 'bi-chevron-down'"
                            ></i>
                        </button>
                    </div>

                    <!-- Accordion Body -->
                    <transition name="accordion">
                        <div v-if="nav.has_dropdown && openAccordion === nav.id" class="drawer-accordion-body">
                            <!-- sub_items -->
                            <router-link
                                v-for="sub in nav.sub_items"
                                :key="sub.id"
                                class="drawer-sub-link d-flex align-items-center px-5 py-2"
                                :to="sub.route"
                                @click="closeDrawer"
                            >
                                <i class="bi bi-dash me-2 opacity-50"></i>
                                {{ sub.title }}
                            </router-link>

                            <!-- categories -->
                            <div
                                v-for="cat in categoriesByNav[nav.id] || []"
                                :key="cat.id"
                            >
                                <!-- Category with subcategories: text navigates, icon toggles -->
                                <div v-if="cat.subcategories?.length" class="d-flex align-items-center drawer-sub-row">
                                    <span
                                        class="drawer-sub-link flex-grow-1 d-flex align-items-center px-5 py-2"
                                        @click="handleCategoryClickInNav(cat); closeDrawer()"
                                        style="cursor:pointer;"
                                    >
                                        <i class="bi bi-dash me-2 opacity-50"></i>{{ cat.name }}
                                    </span>
                                    <button
                                        class="drawer-sub-toggle-icon px-3 py-2"
                                        @click="toggleSubAccordion(cat.id)"
                                        aria-label="Toggle subcategory"
                                    >
                                        <i
                                            class="bi fs-6"
                                            :class="openSubAccordion === cat.id ? 'bi-chevron-up' : 'bi-chevron-down'"
                                        ></i>
                                    </button>
                                </div>

                                <transition name="accordion">
                                    <div v-if="cat.subcategories?.length && openSubAccordion === cat.id" class="drawer-sub-sub-body">
                                        <a
                                            v-for="sub in cat.subcategories"
                                            :key="sub.id"
                                            href="#"
                                            class="drawer-subsub-link d-flex align-items-center py-2"
                                            style="padding-left: 60px !important;"
                                            @click.prevent="handleCategoryClickInNav(sub); closeDrawer()"
                                        >
                                            <i class="bi bi-dot me-1 opacity-40"></i>
                                            {{ sub.name }}
                                        </a>
                                    </div>
                                </transition>

                                <!-- Category without subcategories -->
                                <a
                                    v-if="!cat.subcategories?.length"
                                    href="#"
                                    class="drawer-sub-link d-flex align-items-center px-5 py-2"
                                    @click.prevent="handleCategoryClickInNav(cat); closeDrawer()"
                                >
                                    <i class="bi bi-dash me-2 opacity-50"></i>
                                    {{ cat.name }}
                                </a>
                            </div>
                        </div>
                    </transition>

                    <div class="drawer-item-divider"></div>
                </li>
            </ul>

            <!-- Drawer Footer -->
            <div class="drawer-footer px-4 py-4">
                <router-link to="/register" class="drawer-footer-link d-flex align-items-center gap-3" @click="closeDrawer">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span>My Account</span>
                </router-link>
            </div>
        </div>
    </transition>

    <!-- ===== CART SIDEBAR ===== -->
    <div
        v-if="showCartSidebar"
        class="cart-sidebar position-fixed top-0 end-0 h-100 bg-white shadow-lg p-4"
        style="width: 400px; z-index: 1050"
    >
        <h4 class="mb-4">Your Cart</h4>
        <button
            class="btn-close position-absolute top-0 end-0 m-3"
            @click="showCartSidebar = false"
        ></button>

        <div v-if="cartStore.items.length === 0">
            <p>Cart is empty</p>
        </div>

        <div v-else>
            <div
                v-for="item in cartStore.items"
                :key="`${item.id}-${item.size}`"
                class="d-flex align-items-center mb-3 border-bottom pb-3"
            >
                <img
                    :src="item.image"
                    alt="Product"
                    class="me-3"
                    style="width: 60px; height: 60px; object-fit: cover;"
                />
                <div class="flex-grow-1">
                    <h6>{{ item.name }} ({{ item.size }})</h6>
                    <p>
                        {{ item.price }} × {{ item.quantity }} = ${{
                            parseFloat(item.price.replace("$", "")) * item.quantity
                        }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary" @click="cartStore.updateQuantity(item.id, item.size, item.quantity - 1)">-</button>
                    <span>{{ item.quantity }}</span>
                    <button class="btn btn-sm btn-outline-secondary" @click="cartStore.updateQuantity(item.id, item.size, item.quantity + 1)">+</button>
                    <button class="btn btn-sm btn-danger" @click="cartStore.removeItem(item.id, item.size)">Remove</button>
                </div>
            </div>
            <div class="mt-4">
                <h5>Total: ${{ cartStore.totalPrice }}</h5>
                <button class="btn btn-dark w-100">Checkout</button>
            </div>
        </div>
    </div>

    <!-- ===== PASSWORD MODAL ===== -->
    <div v-if="showPasswordModal" class="password-modal-backdrop">
        <div class="password-modal-box">
            <button class="close-btn" @click="closePasswordModal">×</button>
            <h5 class="mb-3 text-center">Enter Password</h5>
            <input
                type="password"
                v-model="enteredPassword"
                class="form-control mb-3"
                placeholder="Password"
            />
            <p v-if="passwordError" class="text-danger small text-center">{{ passwordError }}</p>
            <button class="btn btn-dark w-100" @click="submitPassword">Unlock</button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useCartStore } from "@/store/cart";

// ── Cart & Router ──────────────────────────────────────
const cartStore = useCartStore();
const router = useRouter();

// ── Navbar scroll ──────────────────────────────────────
const isScrolled = ref(false);
const handleScroll = () => { isScrolled.value = window.scrollY > 80; };
onMounted(() => window.addEventListener("scroll", handleScroll));
onUnmounted(() => window.removeEventListener("scroll", handleScroll));

// ── Search ─────────────────────────────────────────────
const showSearch = ref(false);
const toggleSearch = () => { showSearch.value = !showSearch.value; };

// ── Cart sidebar ───────────────────────────────────────
const showCartSidebar = ref(false);

// ── Mobile drawer ──────────────────────────────────────
const drawerOpen = ref(false);
const openAccordion = ref(null);
const openSubAccordion = ref(null);

const toggleDrawer = () => { drawerOpen.value = !drawerOpen.value; };
const closeDrawer = () => {
    drawerOpen.value = false;
    openAccordion.value = null;
    openSubAccordion.value = null;
};

const toggleAccordion = (id) => {
    openAccordion.value = openAccordion.value === id ? null : id;
    openSubAccordion.value = null;
};

const toggleSubAccordion = (id) => {
    openSubAccordion.value = openSubAccordion.value === id ? null : id;
};

// Prevent body scroll when drawer open
watch(drawerOpen, (val) => {
    document.body.style.overflow = val ? "hidden" : "";
});

// ── Password modal ─────────────────────────────────────
const showPasswordModal = ref(false);
const enteredPassword = ref('');
const passwordError = ref('');
const pendingRoute = ref(null);
const selectedCategoryId = ref(null);

const handleCategoryClickInNav = (item) => {
    if (item?.password) {
        selectedCategoryId.value = item.id;
        pendingRoute.value = item.subcategories?.length > 0
            ? { name: 'Subcategories', params: { id: item.id } }
            : { name: 'CategoryProducts', params: { id: item.id } };
        showPasswordModal.value = true;
    } else {
        router.push(
            item.subcategories?.length > 0
                ? { name: 'Subcategories', params: { id: item.id } }
                : { name: 'CategoryProducts', params: { id: item.id } }
        );
    }
};

const submitPassword = async () => {
    try {
        await axios.post(`/api/categories/${selectedCategoryId.value}/verify-password`, {
            password: enteredPassword.value
        });
        showPasswordModal.value = false;
        enteredPassword.value = '';
        passwordError.value = '';
        router.push(pendingRoute.value);
    } catch (err) {
        passwordError.value = 'Incorrect password. Contact admin.';
    }
};

const closePasswordModal = () => {
    showPasswordModal.value = false;
    enteredPassword.value = '';
    passwordError.value = '';
    pendingRoute.value = null;
    selectedCategoryId.value = null;
};

// ── Navigation data ────────────────────────────────────
const navigations = ref([]);
const categoriesByNav = ref({});

const goToMenu = (slug) => {
    if (slug) router.push({ name: "MenuCategories", params: { slug } });
};

onMounted(async () => {
    try {
        const navRes = await axios.get("/api/navigations");
        navigations.value = navRes.data;

        const catRes = await axios.get("/api/categories-by-navigation");
        categoriesByNav.value = {};
        Object.keys(catRes.data).forEach((key) => {
            categoriesByNav.value[Number(key)] = catRes.data[key] || [];
        });
    } catch (err) {
        console.error("Fetch error:", err);
    }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Smooch+Sans:wght@300;400;600;700&display=swap");

* { font-family: "Smooch Sans", sans-serif; }

/* =========================================
   NAVBAR BASE
   ========================================= */
.custom-navbar {
    height: 50px;
    background: transparent;
    transition: all 0.35s ease;
    z-index: 1000;
    display: flex;
    align-items: center;
}

.custom-navbar::before {
    content: "";
    position: absolute;
    top: 0; right: 0;
    width: 65%; height: 100%;
    background: #000;
    clip-path: polygon(0% 0, 100% 0, 100% 100%, 4% 100%);
    transition: all 0.35s ease;
    z-index: -1;
}

.navbar-scrolled {
    height: 50px;
    background: #000;
}
.navbar-scrolled::before {
    width: 100%;
    clip-path: none;
}

/* =========================================
   LOGO
   ========================================= */
.navbar-logo {
    height: 80px;
    margin-top: 70px;
    transition: all 0.35s ease;
}
.navbar-logo-secondary {
    height: 24px;
    margin-left: 30px;
}
.logo-small {
    height: 24px;
    margin-bottom: 70px;
    filter: brightness(0) invert(1);
}

/* =========================================
   DESKTOP NAV LINKS
   ========================================= */
.nav-link {
    color: #fff !important;
    font-weight: 600;
}
.nav-link:hover { color: #ddd !important; }

.nav-icons i { color: #fff; cursor: pointer; }

.search-input {
    position: absolute;
    top: 100%; right: 0;
    width: 200px;
    padding: 5px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background: #fff;
    color: #000;
    z-index: 10;
}

/* =========================================
   DESKTOP DROPDOWN
   ========================================= */
.dropdown-menu {
    display: none;
    margin-top: 0;
    border-radius: 6px;
    min-width: 220px;
    background: rgb(0, 0, 0)
}
.nav-item.dropdown:hover > .dropdown-menu { display: block; }

.dropdown-item {
    padding: 8px 16px;
    color: #fff !important;
    transition: all 0.15s;
}
.dropdown-item:hover {
    background-color: #000 !important;
    color: #fff !important;
}

.dropdown-submenu { position: relative; }
.dropdown-submenu:hover > .sub-dropdown { display: block !important; }

.sub-dropdown {
    display: none;
    position: absolute !important;
    top: 0; left: 100%;
    min-width: 220px;
    margin-left: 4px;
    background: #111;
}

/* =========================================
   MOBILE TOP ICONS
   ========================================= */
.mobile-top-icons a { color: inherit; text-decoration: none; }

/* =========================================
   HAMBURGER BUTTON
   ========================================= */
.hamburger-btn {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 4px;
}
.hamburger-btn span {
    display: block;
    width: 24px;
    height: 2px;
    background: #fff;
    border-radius: 2px;
    transition: all 0.3s ease;
    transform-origin: center;
}
.hamburger-btn span:nth-child(1).open { transform: translateY(7px) rotate(45deg); }
.hamburger-btn span:nth-child(2).open { opacity: 0; transform: scaleX(0); }
.hamburger-btn span:nth-child(3).open { transform: translateY(-7px) rotate(-45deg); }

/* =========================================
   DRAWER OVERLAY
   ========================================= */
.drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.65);
    z-index: 1100;
    backdrop-filter: blur(2px);
}

/* =========================================
   MOBILE SIDE DRAWER
   ========================================= */
.mobile-drawer {
    position: fixed;
    top: 0; left: 0;
    width: 300px;
    height: 100dvh;
    background: #000;
    z-index: 1200;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
}

.drawer-header {
    background: #0a0a0a;
    min-height: 58px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.drawer-close-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    line-height: 1;
}

.drawer-divider {
    height: 1px;
    background: rgba(255,255,255,0.06);
}

/* ─── Drawer Nav ─── */
.drawer-nav { flex: 1; }
.drawer-nav-item { border: none; }

/* ✅ NEW: Split row for text + icon */
.drawer-link-row {
    width: 100%;
}

.drawer-link {
    color: #fff !important;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
    width: 100%;
}
.drawer-link:hover { background: rgba(255,255,255,0.05); }

/* ✅ Text part of split row */
.drawer-link-text {
    color: #fff !important;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.2s;
    display: block;
}
.drawer-link-text:hover { background: rgba(255,255,255,0.05); }

/* ✅ Icon toggle button */
.drawer-toggle-icon {
    background: transparent;
    border: none;
    border-left: 1px solid rgba(255,255,255,0.08);
    cursor: pointer;
    color: #fff;
    min-width: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    flex-shrink: 0;
}
.drawer-toggle-icon:hover { background: rgba(255,255,255,0.08); }

.transition-icon { transition: transform 0.25s ease; color: #fff; }

/* ─── Accordion body ─── */
.drawer-accordion-body { background: #0d0d0d; }

.drawer-sub-link {
    color: rgba(255,255,255,0.75) !important;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    letter-spacing: 0.02em;
    background: transparent;
    border: none;
    cursor: pointer;
    width: 100%;
    transition: background 0.2s, color 0.2s;
    text-align: left;
}
.drawer-sub-link:hover {
    background: rgba(255,255,255,0.07);
    color: #fff !important;
}

/* ✅ Sub-category split row */
.drawer-sub-row {
    width: 100%;
}

/* ✅ Sub-category icon toggle */
.drawer-sub-toggle-icon {
    background: transparent;
    border: none;
    border-left: 1px solid rgba(255,255,255,0.06);
    cursor: pointer;
    color: rgba(255,255,255,0.6);
    min-width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    flex-shrink: 0;
}
.drawer-sub-toggle-icon:hover { background: rgba(255,255,255,0.05); }

/* sub-sub level */
.drawer-sub-sub-body { background: #111; }

.drawer-subsub-link {
    color: rgba(255,255,255,0.55) !important;
    text-decoration: none;
    font-size: 0.85rem;
    display: flex;
    padding: 8px 16px 8px 60px !important;
    background: transparent;
    border: none;
    width: 100%;
    cursor: pointer;
    transition: background 0.2s;
}
.drawer-subsub-link:hover {
    background: rgba(255,255,255,0.05);
    color: #fff !important;
}

.drawer-item-divider {
    height: 1px;
    background: rgba(255,255,255,0.06);
    margin: 0;
}

/* ─── Drawer Footer ─── */
.drawer-footer {
    border-top: 1px solid rgba(255,255,255,0.08);
    margin-top: auto;
}
.drawer-footer-link {
    color: rgba(255,255,255,0.7) !important;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    transition: color 0.2s;
}
.drawer-footer-link:hover { color: #fff !important; }

/* =========================================
   DRAWER TRANSITIONS
   ========================================= */
.slide-drawer-enter-active,
.slide-drawer-leave-active {
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-drawer-enter-from,
.slide-drawer-leave-to {
    transform: translateX(-100%);
}

.fade-overlay-enter-active,
.fade-overlay-leave-active {
    transition: opacity 0.3s ease;
}
.fade-overlay-enter-from,
.fade-overlay-leave-to {
    opacity: 0;
}

/* Accordion expand/collapse */
.accordion-enter-active,
.accordion-leave-active {
    transition: max-height 0.3s ease, opacity 0.25s ease;
    overflow: hidden;
    max-height: 600px;
    opacity: 1;
}
.accordion-enter-from,
.accordion-leave-to {
    max-height: 0;
    opacity: 0;
}

/* =========================================
   CART SIDEBAR
   ========================================= */
.cart-sidebar { overflow-y: auto; transition: transform 0.3s ease; }

/* =========================================
   PASSWORD MODAL
   ========================================= */
.password-modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
}
.password-modal-box {
    background: #fff;
    padding: 30px 25px;
    border-radius: 12px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
    position: relative;
    animation: fadeInModal 0.3s ease;
}
.close-btn {
    position: absolute; top: 12px; right: 12px;
    background: none; border: none;
    font-size: 1.3rem; cursor: pointer;
}
@keyframes fadeInModal {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* =========================================
   MENU RIGHT (desktop)
   ========================================= */
.menu-right{
    flex:1;
    display:flex;
    justify-content: center;   /* instead of space-evenly */
    gap:40px;
    margin-left:0;
    padding-right:0;
}
/* =========================================
   MOBILE OVERRIDES (≤ 991px)
   ========================================= */
@media (max-width: 991px) {
    .custom-navbar {
        height: 56px;
        background: #000 !important;
    }
    .custom-navbar::before { display: none; }

    .navbar-logo {
        height: 28px;
        margin-top: 0;
        filter: brightness(0) invert(1);
    }
    .logo-small { margin-bottom: 0; }

    /* Hide desktop collapse on mobile */
    #mainNavbar { display: none !important; }

    /* Cart sidebar full width on small screens */
    .cart-sidebar { width: 100% !important; }
}

@media (max-width: 400px) {
    .mobile-drawer { width: 85vw; }
}
</style>
