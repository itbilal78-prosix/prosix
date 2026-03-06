<template>
  <div class="d-flex flex-column min-vh-100">
    <nav-component />
    <breadcrumb-component />

    <main class="flex-grow-1">
      <div v-if="loading" class="text-center py-5 mt-5">
        <div class="spinner-border text-dark"></div>
      </div>

      <div v-else class="page-wrap">

        <!-- ── TOP: Heading ── -->
        <div class="top-panel">
          <h1 class="hero-title">{{ navigation?.title || "Explore" }} Categories</h1>
          <p class="hero-desc">
            Discover our exclusive range of high-quality {{ navigation?.title?.toLowerCase() || "products" }}.
            Handpicked for style, durability and performance.
          </p>
        </div>

        <!-- ── BOTTOM: Categories 5 per row ── -->
        <div class="bottom-panel">
          <div v-if="categories.length === 0" class="text-center py-5">
            <h3>No categories found</h3>
            <router-link to="/" class="btn btn-outline-dark mt-3">Back to Home</router-link>
          </div>

          <div v-else class="cat-grid">
            <div
              v-for="cat in categories"
              :key="cat.id"
              class="cat-card"
              @click="handleCategoryClick(cat)"
            >
              <div class="cat-img-wrap">
                <img
                  :src="cat.icon_image || defaultImage"
                  class="cat-img"
                  :alt="cat.name"
                  @error="handleImageError"
                />
              </div>
              <h6 class="cat-name">{{ cat.name }}</h6>
            </div>
          </div>
        </div>

      </div>
    </main>

    <!-- Password Modal -->
    <div v-if="showPasswordModal" class="pw-overlay">
      <div class="pw-box">
        <button class="pw-close" @click="closePasswordModal">&times;</button>
        <h5 class="fw-bold mb-3">Enter Password</h5>
        <input type="password" v-model="enteredPassword" class="form-control mb-2" placeholder="Password" />
        <p v-if="passwordError" class="text-danger small mb-2">{{ passwordError }}</p>
        <button class="btn btn-dark w-100" @click="submitPassword">Unlock</button>
      </div>
    </div>

    <footer-component />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue"
import { useRoute, useRouter } from "vue-router"
import axios from "axios"

const route  = useRoute()
const router = useRouter()

const loading            = ref(true)
const navigation         = ref(null)
const categories         = ref([])
const defaultImage       = "https://via.placeholder.com/400x300/f8f9fa/6c757d?text=Category"
const showPasswordModal  = ref(false)
const enteredPassword    = ref("")
const passwordError      = ref("")
const pendingRoute       = ref(null)
const selectedCategoryId = ref(null)

const handleCategoryClick = (cat) => {
  if (cat.password) {
    selectedCategoryId.value = cat.id
    pendingRoute.value = cat.subcategories?.length
      ? { name: "Subcategories",    params: { id: cat.id } }
      : { name: "CategoryProducts", params: { id: cat.id } }
    showPasswordModal.value = true
  } else {
    router.push(
      cat.subcategories?.length
        ? { name: "Subcategories",    params: { id: cat.id } }
        : { name: "CategoryProducts", params: { id: cat.id } }
    )
  }
}

const submitPassword = async () => {
  try {
    await axios.post(`/api/categories/${selectedCategoryId.value}/verify-password`, {
      password: enteredPassword.value
    })
    showPasswordModal.value = false
    enteredPassword.value   = ""
    passwordError.value     = ""
    router.push(pendingRoute.value)
  } catch {
    passwordError.value = "Wrong password"
  }
}

const handleImageError = (e) => { e.target.src = defaultImage }

const closePasswordModal = () => {
  showPasswordModal.value  = false
  enteredPassword.value    = ""
  passwordError.value      = ""
  pendingRoute.value       = null
  selectedCategoryId.value = null
}

const loadData = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/menu-categories/${route.params.slug}`)
    navigation.value = data.navigation
    categories.value = data.categories || []
  } catch {
    router.push("/")
  } finally {
    loading.value = false
  }
}

onMounted(loadData)
watch(() => route.params.slug, loadData)
</script>

<style scoped>
.page-wrap {
  width: 100%;
  padding: 40px 40px 60px;
}

/* ── TOP: Heading ── */
.top-panel {
  text-align: center;
  margin-bottom: 40px;
}
.hero-title {
  font-size: 38px;
  font-weight: 800;
  color: #000;
  font-family: 'Montserrat', sans-serif;
  margin-bottom: 10px;
}
.hero-desc {
  font-size: 15px;
  color: #777;
  max-width: 500px;
  margin: 0 auto;
  line-height: 1.7;
}

/* ── BOTTOM: Categories Grid ── */
.bottom-panel { width: 100%; }

.cat-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 24px;
}

@media (max-width: 1200px) { .cat-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 900px)  { .cat-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px)  { .cat-grid { grid-template-columns: repeat(2, 1fr); } }

/* ── Category Card - FIXED UNIFORM SIZE ── */
.cat-card {
  cursor: pointer;
  text-align: center;
  transition: transform 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.cat-card:hover { transform: translateY(-6px); }

/* THIS is the key - fixed square box, image always same size */
.cat-img-wrap {
  width: 100%;
  aspect-ratio: 1 / 1;   /* perfect square */
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border-radius: 12px;
  border: 1px solid #eee;
}

.cat-img {
  width: 100%;
  height: 100%;
  object-fit: contain;   /* never crops, always fits inside */
  padding: 16px;
  filter: grayscale(20%);
  transition: filter 0.3s, transform 0.3s;
}
.cat-card:hover .cat-img {
  filter: grayscale(0%);
  transform: scale(1.08);
}

.cat-name {
  font-size: 13px;
  font-weight: 700;
  color: #222;
  margin-top: 10px;
  margin-bottom: 0;
  font-family: 'Montserrat', sans-serif;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

/* ── Password Modal ── */
.pw-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.pw-box {
  position: relative;
  background: #fff;
  padding: 28px;
  width: 320px;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.pw-close {
  position: absolute;
  top: 10px; right: 14px;
  background: transparent;
  border: none;
  font-size: 1.5rem;
  font-weight: bold;
  cursor: pointer;
  color: #333;
}
.pw-close:hover { color: #000; }
</style>
