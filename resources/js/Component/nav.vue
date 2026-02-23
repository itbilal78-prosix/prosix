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

            <!-- Mobile Toggle -->
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav menu-right gap-4 ms-auto">
                    <li
                        v-for="nav in navigations"
                        :key="nav.id"
                        class="nav-item"
                        :class="{ dropdown: nav.has_dropdown }"
                    >
                        <!-- Normal link -->
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

<!-- یہ والا بلاک مکمل تبدیل کر دو -->
<ul v-if="nav.has_dropdown" class="dropdown-menu border-0 shadow-lg">
  <!-- اگر nav.sub_items ہیں تو انہیں رکھ لو، ورنہ ہٹا دو -->
  <li v-for="sub in nav.sub_items" :key="sub.id">
    <router-link class="dropdown-item" :to="sub.route">
      {{ sub.title }}
    </router-link>
  </li>

 <!-- یہ والا بلاک بالکل ایسا ہونا چاہیے – router-link بالکل نہیں ہونا چاہیے -->
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

                <!-- Icons -->
                <div
                    class="d-flex align-items-center gap-4 nav-icons position-relative ms-4"
                >
                    <i class="bi bi-search fs-5" @click="toggleSearch"></i>
                    <input
                        v-if="showSearch"
                        type="text"
                        placeholder="Search..."
                        class="search-input form-control form-control-sm"
                    />

                    <!-- Cart Icon with Badge -->
                    <div
                        class="position-relative cursor-pointer"
                        @click="showCartSidebar = true"
                    >
                        <!-- Click pe sidebar open -->
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

                <!-- Sidebar for Cart (simple version, yahan se items edit kar sako) -->
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
                                style="
                                    width: 60px;
                                    height: 60px;
                                    object-fit: cover;
                                "
                            />
                            <div class="flex-grow-1">
                                <h6>{{ item.name }} ({{ item.size }})</h6>
                                <p>
                                    {{ item.price }} × {{ item.quantity }} = ${{
                                        parseFloat(
                                            item.price.replace("$", "")
                                        ) * item.quantity
                                    }}
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="
                                        cartStore.updateQuantity(
                                            item.id,
                                            item.size,
                                            item.quantity - 1
                                        )
                                    "
                                >
                                    -
                                </button>
                                <span>{{ item.quantity }}</span>
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="
                                        cartStore.updateQuantity(
                                            item.id,
                                            item.size,
                                            item.quantity + 1
                                        )
                                    "
                                >
                                    +
                                </button>
                                <button
                                    class="btn btn-sm btn-danger"
                                    @click="
                                        cartStore.removeItem(item.id, item.size)
                                    "
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h5>Total: ${{ cartStore.totalPrice }}</h5>
                            <button class="btn btn-dark w-100">Checkout</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
<!-- Password Modal -->
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
import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useCartStore } from "@/store/cart"; // ← yeh sab se zaroori line



const showPasswordModal = ref(false)
const enteredPassword = ref('')
const passwordError = ref('')
const pendingRoute = ref(null)
const selectedCategoryId = ref(null)

const handleCategoryClickInNav = (item) => {
  console.log('───────────────────────────────')
  console.log('Navbar Click →', item?.name || 'Name not found')
  console.log('Full Item Object:', item)
  console.log('Password flag exists? →', item?.password, typeof item?.password)
  console.log('───────────────────────────────')

  if (item?.password) {  // or !!item.password
    console.log('→ PASSWORD DETECTED → Opening Modal')
    
    selectedCategoryId.value = item.id

    pendingRoute.value = item.subcategories?.length > 0
      ? { name: 'Subcategories', params: { id: item.id } }
      : { name: 'CategoryProducts', params: { id: item.id } }

    showPasswordModal.value = true
  } else {
    console.log('→ No password → Direct route push')
    router.push(
      item.subcategories?.length > 0
        ? { name: 'Subcategories', params: { id: item.id } }
        : { name: 'CategoryProducts', params: { id: item.id } }
    )
  }
}

// Password submit
const submitPassword = async () => {
  try {
    await axios.post(`/api/categories/${selectedCategoryId.value}/verify-password`, {
      password: enteredPassword.value
    })
    showPasswordModal.value = false
    enteredPassword.value = ''
    passwordError.value = ''
    router.push(pendingRoute.value)
  } catch (err) {
    passwordError.value = 'Incorrect password help admin'
    console.error('Password verify error:', err)
  }
}

const closePasswordModal = () => {
  showPasswordModal.value = false
  enteredPassword.value = ''
  passwordError.value = ''
  pendingRoute.value = null
  selectedCategoryId.value = null
}
const cartStore = useCartStore();
const showCartSidebar = ref(false); // Sidebar control
const isScrolled = ref(false);
const showSearch = ref(false);
const router = useRouter();

const handleScroll = () => {
    isScrolled.value = window.scrollY > 80;
};

onMounted(() => window.addEventListener("scroll", handleScroll));
onUnmounted(() => window.removeEventListener("scroll", handleScroll));

const toggleSearch = () => {
    showSearch.value = !showSearch.value;
};

const navigations = ref([]);
const categoriesByNav = ref({});
const goToMenu = (slug) => {
    if (slug) {
        router.push({ name: "MenuCategories", params: { slug } });
    }
};
onMounted(async () => {
    try {
        // Navigations
        const navRes = await axios.get("/api/navigations");
        navigations.value = navRes.data;

        // Categories with subcategories
        const catRes = await axios.get("/api/categories-by-navigation");
        console.log("Categories API raw:", catRes.data);

        categoriesByNav.value = {};
        Object.keys(catRes.data).forEach((key) => {
            const navId = Number(key);
            categoriesByNav.value[navId] = catRes.data[key] || [];
        });

        console.log("Processed categoriesByNav:", categoriesByNav.value);
    } catch (err) {
        console.error("Fetch error:", err);
    }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Smooch+Sans:wght@300;400;600;700&display=swap");

* {
    font-family: "Smooch Sans", sans-serif;
}

/* ================= NAVBAR ================= */

.custom-navbar {
    height: 50px; /* initial navbar height */
    background: transparent;
    transition: all 0.35s ease;
    z-index: 1000;
    display: flex;
    align-items: center;
}

/* BLACK CUT BACKGROUND */
.custom-navbar::before {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 65%;
    height: 100%;
    background: #000;
    clip-path: polygon(0% 0, 100% 0, 100% 100%, 4% 100%);
    transition: all 0.35s ease;
    z-index: -1;
}

/* ================= SCROLL STATE ================= */
.navbar-scrolled {
    height: 50px;
    background: #000;
}

.navbar-scrolled::before {
    width: 100%;
    clip-path: none;
}

/* ================= LOGO ================= */
.navbar-logo {
    height: 80px;
    margin-top: 70px;
    transition: all 0.35s ease;
    filter: none;
}
/* Secondary Logo (only shows when scrolled) */
.navbar-logo-secondary {
    height: 24px;
    margin-left: 30px;
    filter: brightness(1) invert(0);
}

/* SCROLL LOGO */
.logo-small {
    height: 24px;
    margin-bottom: 70px;
    filter: brightness(0) invert(1);
}
/* ================= LINKS ================= */
.nav-link {
    color: #fff !important;
    font-weight: 600;
}

.nav-link:hover {
    color: #ddd !important;
}

/* ================= ICONS ================= */
.nav-icons i {
    color: #fff;
    cursor: pointer;
}

/* ================= SEARCH INPUT ================= */
.nav-icons {
    position: relative; /* for absolute positioning of search */
}

.search-input {
    position: absolute;
    top: 100%; /* below icons */
    right: 0; /* align with search icon */
    width: 200px;
    padding: 5px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background: #fff;
    color: #000;
    z-index: 10;
    transition: all 0.3s ease;
}

/* ================= DROPDOWN ================= */
dropdown-menu {
    margin-top: 8px;
    border-radius: 6px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
    min-width: 220px;
}

.dropdown-item {
    padding: 8px 16px;
    transition: all 0.15s;
}

.dropdown-item:hover {
    background-color: #111 !important;
    color: white !important;
}

/* Submenu (right side flyout) */
.dropdown-submenu {
    position: relative;
}

.dropdown-submenu:hover > .sub-dropdown {
    display: block !important;
}

.sub-dropdown {
    display: none;
    position: absolute !important;
    top: 0;
    left: 100%;
    min-width: 220px;
    margin-left: 4px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.sub-dropdown .dropdown-item {
    font-size: 0.95rem;
}
.nav-item.dropdown:hover > .dropdown-menu {
    display: block;
    margin-top: 0;
}

/* prevent flicker */
.dropdown-menu {
    display: none;
}

/* optional smooth feel */
.dropdown-menu {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.menu-right {
    width: 95%;
    justify-content: end;
}
.cart-sidebar {
    overflow-y: auto;
    transition: transform 0.3s ease;
}
/* ================= PASSWORD MODAL ================= */
.password-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5); /* shadow background */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000; /* navbar se upar */
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
  position: absolute;
  top: 12px;
  right: 12px;
  background: none;
  border: none;
  font-size: 1.3rem;
  cursor: pointer;
}

.password-modal-box input.form-control {
  padding: 10px;
  font-size: 1rem;
}

.password-modal-box button {
  padding: 10px;
  font-size: 1rem;
  margin-top: 10px;
}

/* Optional fade-in */
@keyframes fadeInModal {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ================= MOBILE ================= */
@media (max-width: 991px) {
    .custom-navbar {
        height: 50px;
    }

    .navbar-logo {
        height: 24px;
    }
}
</style>
<!-- Title	Ye navigation ka naam hai, jo user menu me dekhega. Example: Contact Us, Services.
Slug	Ye URL ka short name hai, jo route me use hota hai. Example: /contact ya /services. Laravel me unique hona chahiye.
Route	Ye website ka actual route hai jo link ko point karega. Example: /contact ya /about. Agar empty chhodo, menu click pe kuch nahi hoga.
Position	Ye number decide karta hai navigation ka order. Example: 0 = first, 1 = second, 2 = third, etc.
Has Dropdown	Agar menu ke andar aur sub-items (submenu) hain to check karo. Example: "Services" ke andar "Web Design, SEO, Marketing".
Active	Ye decide karta hai ki navigation visible hai ya hidden. Checked = visible, unchecked = hidden. -->
