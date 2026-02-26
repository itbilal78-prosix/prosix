<template>
  <div>
    <nav-component />

    <div class="container my-5 pt-5">
      <!-- Heading -->
      <div class="text-center mb-5">
        <h1 class="fw-bold">{{ category?.name }}</h1>
      </div>

      <!-- Loader -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-dark"></div>
      </div>

      <div v-else>
        <!-- ===== NORMAL PRODUCTS ===== -->
        <div class="row g-4 mb-5" v-if="products.length > 0">
          <div
            v-for="product in products"
            :key="product.id"
class="col-lg-2 col-md-4 col-sm-6 col-6"
          >
            <div class="card product-card h-100 shadow-sm">
              <div class="product-img-wrapper">
                <img :src="product.image" class="product-img" />
              </div>
              <div class="card-body text-center">
                <h6 class="fw-bold mb-1">{{ product.name }}</h6>
                <p class="text-muted mb-2">$ {{ product.price }}</p>
                <router-link
                  :to="`/product/${product.id}`"
                  class="btn btn-dark btn-sm w-100"
                >
                  View & Buy
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== CUSTOMIZER MODELS - GROUPED BY model_name ===== -->
        <div v-if="Object.keys(groupedModels).length > 0">
          <!-- Loop each model_name group -->
          <div
            v-for="(groupModels, modelName) in groupedModels"
            :key="modelName"
            class="mb-5"
          >
            <!-- Group Heading (like "Model-Vent Top") -->
            <div class="model-group-heading">
              <span>{{ modelName }}</span>
            </div>

            <!-- Cards inside this group -->
            <div class="row g-4">
              <div
                v-for="model in groupModels"
                :key="model.id"
class="col-lg-2 col-md-4 col-sm-6 col-6"
        >
                <div class="card model-card">
                  <!-- Image -->
                  <div class="card-image-wrapper">
                  <!-- Cart Icon -->
<button
  class="model-cart-btn"
  @click.stop="goToProduct(model.id)"
>
<i class="bi bi-cart" style="transform: scaleX(-1); display:inline-block;"></i>
</button>
                    <img
                      v-if="model.thumbnail"
                      :src="model.thumbnail"
                      class="model-thumb"
                    />
                    <template v-else>
                      <img v-if="model.front_black" :src="model.front_black" class="img-layer black" />
                      <img v-if="model.front_white" :src="model.front_white" class="img-layer white" />
                      <img v-if="model.front_svg"   :src="model.front_svg"   class="img-layer svg"   />
                      <img
                        v-if="!model.front_black && !model.front_white && !model.front_svg"
                        src="https://via.placeholder.com/300x180?text=No+Image"
                        class="img-layer svg"
                      />
                    </template>
                  </div>

                  <!-- Body -->
                  <div class="card-body py-2">

  <div class="d-flex justify-content-between align-items-center mb-1">
    <h5 class="card-title mb-0">{{ model.title }}</h5>
    <span class="card-price">${{ model.price || '0.00' }}</span>
  </div>

</div>

                  <!-- Footer -->
                  <div class="card-footer model-footer">
                    <button
                      class="btn btn-custom btn-sm w-100"
                      @click="handleCustomizerClick(model.id)"
                    >
                      Customize
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No models -->
        <div v-else-if="!loading" class="text-center py-5 text-muted">
          <p>No customizer models in this subcategory yet.</p>
        </div>
      </div>
    </div>

    <!-- ===== LOGIN REQUIRED MODAL ===== -->
    <div
      v-if="showLoginModal"
      class="modal fade show"
      style="display: block"
      @click.self="showLoginModal = false"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
          <div class="text-center pt-4 pb-2">
            <div class="login-icon-wrap mx-auto">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
          </div>
          <div class="modal-body text-center px-5 pb-2">
            <h5 class="fw-bold mb-2">Login Required</h5>
            <p class="text-muted mb-0">
              Customizer use karne ke liye pehle apne account mein login karna hoga.
            </p>
          </div>
          <div class="modal-footer justify-content-center border-0 px-5 pb-4 gap-3">
            <button
              class="btn btn-outline-secondary rounded-pill px-4"
              @click="showLoginModal = false"
            >
              Cancel
            </button>
            <router-link
              :to="{ path: '/user-login', query: { redirect: `/models/${pendingModelId}` } }"
              class="btn btn-dark rounded-pill px-4"
              @click="showLoginModal = false"
            >
              Login
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div
      v-if="showLoginModal"
      class="modal-backdrop fade show"
      @click="showLoginModal = false"
    ></div>

    <footer-component />
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route  = useRoute()
const router = useRouter()

const categoryId     = route.params.id
const category       = ref(null)
const products       = ref([])
const models         = ref([])
const loading        = ref(true)
const showLoginModal = ref(false)
const pendingModelId = ref(null)
const goToProduct = (id) => {
  router.push(`/product/${id}`)
}
// ===== GROUP MODELS BY model_name =====
// Result: { "Model-Vent Top": [...], "Model-Blitz Top": [...] }
const groupedModels = computed(() => {
  const groups = {}
  models.value.forEach(m => {
    const key = m.model_name || 'Other'
    if (!groups[key]) groups[key] = []
    groups[key].push(m)
  })
  return groups
})

// ===== CUSTOMIZER CLICK =====
const handleCustomizerClick = (modelId) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    router.push(`/models/${modelId}`)
  } else {
    pendingModelId.value = modelId
    showLoginModal.value = true
  }
}

// ===== LOAD MODELS =====
const loadModels = async () => {
  try {
    // First try subcategory models
    const subRes = await axios.get(`/api/subcategories/${categoryId}/models`)
    let loadedModels = subRes.data.models || []

    if (loadedModels.length > 0) {
      models.value = loadedModels
      return
    }

    // Fallback: category models
    const catRes = await axios.get(`/api/categories/${categoryId}/models`)
    models.value = catRes.data.models || []
  } catch (err) {
    console.error('Models error:', err)
  }
}

// ===== LOAD PRODUCTS =====
const fetchProducts = async () => {
  try {
    const res = await axios.get(`/api/category/${route.params.id}/products`)
    products.value = res.data   // sirf ye line rakho
    console.log("Loaded:", products.value)
  } catch (e) {
    console.error('Products error:', e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProducts()
  loadModels()
})
</script>

<style scoped>
/* ===== PRODUCT CARDS ===== */
.model-cart-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  background: transparent;
  border: none;
  padding: 0;
  cursor: pointer;
  z-index: 10;
  color: #000;
}

.model-cart-btn i {
  font-size: 18px;
}

.model-cart-btn:hover {
  color: #333;
}
.product-card {
  border-radius: 14px;
  overflow: hidden;

  transition: .35s ease;
}
.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 22px rgba(0,0,0,.15);
}
.product-img-wrapper {
  height: 150px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.product-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

/* ===== GROUP HEADING (like admin "Model-Vent Top") ===== */
.model-group-heading {
  text-align: center;
  position: relative;
  margin-bottom: 20px;
  margin-top: 10px;
}
.model-group-heading::before,
.model-group-heading::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 38%;
  height: 1px;
  background: #ccc;
}
.model-group-heading::before { left: 0; }
.model-group-heading::after  { right: 0; }
.model-group-heading span {
  font-size: 15px;
  font-weight: 700;
  background: #fff;
  padding: 0 14px;
  position: relative;
  z-index: 1;
  letter-spacing: .5px;
}

/* ===== MODEL CARDS ===== */
.model-card {
  border-radius: 14px;
  background: #fff;
  overflow: hidden;
  height:90%;
  transition: .35s ease;
}
.model-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 14px 28px rgba(0,0,0,.18);
}
.card-image-wrapper {
  height: 250px;
  background: #fff;
  position: relative;
  overflow: hidden;
}
.model-thumb {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 90%;
  object-fit: contain;
}
.img-layer {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 90%;
  object-fit: contain;
}
.black { z-index: 3; mix-blend-mode: screen; }
.white { z-index: 2; mix-blend-mode: multiply; }
.svg   { z-index: 1; }

.card-title  { font-size: 15px; font-weight: 600; }
.card-text   { font-size: 13px; color: #555; }
.card-price  { font-size: 14px; font-weight: 700; }

.model-footer {
  padding: 10px;
  background: #fff;
  border-top: 1px solid #eee;
}

/* ===== BUTTON ===== */
.btn-custom {
  background: #000;
  color: #fff;
  height: 38px;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
}
.btn-custom:hover {
  background: #222;
  color: #fff;
}

/* ===== LOGIN MODAL ===== */
.login-icon-wrap {
  width: 72px;
  height: 72px;
  background: #f0f0f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
