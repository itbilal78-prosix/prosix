<template>
  <div class="d-flex flex-column min-vh-100">
    <nav-component />

    <main class="flex-grow-1 py-5">
      <div class="container my-5 pt-4">
        <h1 class="display-5 fw-bold text-center mb-5">
          {{ parentCategory?.name || "Subcategories" }}
        </h1>

        <!-- Loading Spinner -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary"></div>
        </div>

        <!-- No Subcategories -->
        <div v-else-if="subcategories.length === 0" class="text-center py-5">
          <h4>No subcategories found</h4>
          <p class="text-muted">This category has no subcategories yet.</p>
          <router-link to="/" class="btn btn-outline-dark mt-3">Back to Home</router-link>
        </div>

        <!-- Subcategories Grid -->
        <div v-else class="row g-3">
          <div v-for="sub in subcategories" :key="sub.id" class="col-6 col-md-4 col-lg-3 text-center">
            <img
              :src="sub.icon_image || defaultImage"
              class="deal-image mb-2 clickable-image"
              :alt="sub.name"
              @error="handleImageError"
              @click="handleSubcategoryClick(sub)"
            />
            <h5 class="fw-bold">{{ sub.name }}</h5>
          </div>
        </div>
      </div>
    </main>

    <!-- Password Modal -->
    <div v-if="showPasswordModal" class="password-modal">
      <div class="password-box">
        <button class="close-btn" @click="closePasswordModal">×</button>
        <h5>Protected Category</h5>
        <input
          type="password"
          v-model="enteredPassword"
          class="form-control mb-3"
          placeholder="Enter password"
        />
        <p v-if="passwordError" class="text-danger small">{{ passwordError }}</p>
        <button class="btn btn-dark w-100" @click="submitPassword">Unlock</button>
      </div>
    </div>

    <footer-component />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const parentCategory = ref(null);
const subcategories = ref([]);
const defaultImage = "https://via.placeholder.com/400x300/f8f9fa/6c757d?text=Subcategory";

// Password modal
const showPasswordModal = ref(false);
const enteredPassword = ref('');
const passwordError = ref('');
const pendingRoute = ref(null);
const selectedCategoryId = ref(null);

// Image fallback
const handleImageError = (e) => { e.target.src = defaultImage };

// Handle click on subcategory
const handleSubcategoryClick = (sub) => {
  if (sub.password) {
    selectedCategoryId.value = sub.id;
    pendingRoute.value = { name: "CategoryProducts", params: { id: sub.id } };
    showPasswordModal.value = true;
  } else {
    router.push({ name: "CategoryProducts", params: { id: sub.id } });
  }
};

// Submit password
const submitPassword = async () => {
  try {
    await axios.post(`/api/categories/${selectedCategoryId.value}/verify-password`, {
      password: enteredPassword.value
    });
    showPasswordModal.value = false;
    enteredPassword.value = '';
    passwordError.value = '';
    router.push(pendingRoute.value);
  } catch {
    passwordError.value = 'Incorrect password';
  }
};

// Close modal
const closePasswordModal = () => {
  showPasswordModal.value = false;
  enteredPassword.value = '';
  passwordError.value = '';
  selectedCategoryId.value = null;
  pendingRoute.value = null;
};

// Fetch subcategories
onMounted(async () => {
  const param = route.params.id || route.params.slug;
  if (!param) return (loading.value = false);

  try {
    const { data } = await axios.get(`/api/categories/${param}/subcategories`);
    parentCategory.value = data.parent;
    subcategories.value = data.subcategories || [];
  } catch {
    router.push("/");
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
/* Image only hover effect */
.deal-image {
  width: 100%;
  height: 220px;
  object-fit: contain;
  padding: 20px;
  filter: grayscale(100%) brightness(0.85);
  transition: all 0.4s ease;
  border-radius: 12px;
  cursor: pointer;
}
.deal-image:hover {
  filter: grayscale(0%) brightness(1);
  transform: scale(1.05);
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
