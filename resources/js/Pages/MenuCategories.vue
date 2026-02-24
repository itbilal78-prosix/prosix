<template>
  <div class="d-flex flex-column min-vh-100">
    <nav-component />
    <main class="flex-grow-1 pt-5 mt-5">
      <div class="container my-5 pt-4">
        <!-- Loading -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary"></div>
        </div>

        <div v-else class="row g-4 align-items-center">
          <!-- Left Side - Text Content -->
          <div class="col-lg-6 mb-5 mb-lg-0">
            <div v-if="content" class="pe-lg-5">
              <h1 class="display-5 fw-bold mb-4">
                {{ content.title || navigation?.title + " Collection" }}
              </h1>
              <h4 v-if="content.subtitle" class="mb-3 fw-semibold">
                {{ content.subtitle }}
              </h4>
              <div v-if="content.description" class="lead mb-4 text-muted" v-html="content.description"></div>
              <router-link
                v-if="content.button_text"
                :to="{ name: 'ShopAll' }"
                class="btn single-line-btn btn-lg px-5 py-3 mt-2"
              >
                {{ content.button_text }}
              </router-link>
            </div>

            <div v-else class="pe-lg-5">
              <h1 class="display-5 fw-bold mb-4">
                {{ navigation?.title || "Explore" }} Categories
              </h1>
              <h4 class="mb-3 fw-semibold text-black">Premium Quality Selection</h4>
              <p class="lead mb-4 text-black">
                Discover our exclusive range of high-quality {{ navigation?.title.toLowerCase() || "products" }}. Handpicked for style, durability and performance.
              </p>
              <router-link to="/shop" class="btn single-line-btn btn-md mt-2">Shop Now</router-link>
            </div>
          </div>

          <!-- Right Side - Categories -->
          <div class="col-lg-6">
            <div v-if="categories.length === 0" class="text-center py-5">
              <h3>No categories found</h3>
              <p class="text-muted">{{ navigation?.id }}</p>
              <router-link to="/" class="btn btn-outline-dark mt-3">Back to Home</router-link>
            </div>

            <div v-else class="row g-3">
              <div v-for="cat in categories" :key="cat.id" class="col-6 col-md-4">
                <div class="deal-card h-100 text-center" @click="handleCategoryClick(cat)">
                  <!-- Image -->
                  <img
                    :src="cat.icon_image || defaultImage"
                    class="deal-image mb-2"
                    :alt="cat.name"
                    @error="handleImageError"
                  />
                  <!-- Category Name -->
                  <h5 class="mt-2 fw-bold">{{ cat.name }}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Password Modal -->
    <div v-if="showPasswordModal" class="password-modal">
      <div class="password-box">
        <button class="close-btn" @click="closePasswordModal">&times;</button>
        <h5>Enter Password</h5>
        <input type="password" v-model="enteredPassword" class="form-control mb-3" placeholder="Password" />
        <p v-if="passwordError" class="text-danger small">{{ passwordError }}</p>
        <button class="btn btn-dark w-100" @click="submitPassword">Unlock</button>
      </div>
    </div>

    <footer-component />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const navigation = ref(null);
const categories = ref([]);
const content = ref(null);
const defaultImage = "https://via.placeholder.com/400x300/f8f9fa/6c757d?text=Category";
const showPasswordModal = ref(false);
const enteredPassword = ref("");
const passwordError = ref("");
const pendingRoute = ref(null);
const selectedCategoryId = ref(null);

const handleCategoryClick = (cat) => {
  if (cat.password) {
    selectedCategoryId.value = cat.id;
    pendingRoute.value = cat.subcategories?.length
      ? { name: "Subcategories", params: { id: cat.id } }
      : { name: "CategoryProducts", params: { id: cat.id } };
    showPasswordModal.value = true;
  } else {
    router.push(
      cat.subcategories?.length
        ? { name: "Subcategories", params: { id: cat.id } }
        : { name: "CategoryProducts", params: { id: cat.id } }
    );
  }
};

const submitPassword = async () => {
  try {
    await axios.post(`/api/categories/${selectedCategoryId.value}/verify-password`, { password: enteredPassword.value });
    showPasswordModal.value = false;
    enteredPassword.value = "";
    passwordError.value = "";
    router.push(pendingRoute.value);
  } catch {
    passwordError.value = "Wrong password";
  }
};

const handleImageError = (e) => { e.target.src = defaultImage; };

const loadData = async () => {
  loading.value = true;
  try {
    const slug = route.params.slug;
    const { data } = await axios.get(`/api/menu-categories/${slug}`);
    navigation.value = data.navigation;
    categories.value = data.categories || [];
    content.value = data.content;
  } catch {
    router.push("/");
  } finally {
    loading.value = false;
  }
};

const closePasswordModal = () => {
  showPasswordModal.value = false;
  enteredPassword.value = "";
  passwordError.value = "";
  pendingRoute.value = null;
  selectedCategoryId.value = null;
};

onMounted(loadData);
watch(() => route.params.slug, loadData);
</script>

<style scoped>
.deal-image {
  width: 100%;
  height: 220px;
  object-fit: contain;
  padding: 20px;
  filter: grayscale(100%) brightness(0.85);
  transition: all 0.4s ease;
  border-radius: 12px;
}

.deal-card:hover .deal-image {
  filter: grayscale(0%) brightness(1);
  transform: scale(1.05);
}

.deal-card {
  transition: all 0.3s ease;
  cursor: pointer;
}

.deal-card h5 {
  margin-top: 0.5rem;
}

/* Password modal */
.password-modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.password-box {
  position: relative;
  background: #fff;
  padding: 25px;
  width: 320px;
  border-radius: 10px;
}
.password-box .close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: transparent;
  border: none;
  font-size: 1.5rem;
  font-weight: bold;
  cursor: pointer;
  color: #333;
}
.password-box .close-btn:hover { color: #000; }
</style>
