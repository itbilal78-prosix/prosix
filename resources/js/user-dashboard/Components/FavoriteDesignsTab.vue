<template>
  <div class="fav-root">
    <div class="fav-header">
      <h2>Favorite Designs</h2>
      <p>Your saved favorite products will appear here.</p>
    </div>

    <div v-if="favorites.length === 0" class="empty-box">
      <h3>No favorite designs yet</h3>
      <p>Click the heart icon on any product to save it here.</p>
      <router-link to="/" class="shop-btn">Visit Store</router-link>
    </div>

    <div v-else class="fav-grid">
      <div v-for="item in favorites" :key="item.id" class="fav-card">
        <img :src="item.image" alt="Favorite Design" />
        <h3>{{ item.name }}</h3>
        <p>${{ item.price }}</p>
        <router-link :to="`/product/${item.id}`" class="view-btn">View Design</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const favorites = ref([])

const loadFavorites = () => {
  favorites.value = JSON.parse(localStorage.getItem('favorite_designs') || '[]')
}

onMounted(loadFavorites)
</script>

<style scoped>
.fav-root { display:flex; flex-direction:column; gap:20px; }
.fav-header h2 { margin:0; font-size:24px; color:#111827; }
.fav-header p { margin:6px 0 0; color:#6b7280; }

.empty-box {
  background:white; padding:40px; border-radius:16px;
  text-align:center; border:1px solid #eee;
}
.shop-btn, .view-btn {
  display:inline-block; background:#000; color:#fff;
  padding:10px 16px; border-radius:10px; text-decoration:none;
  margin-top:12px;
}

.fav-grid {
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
  gap:18px;
}
.fav-card {
  background:white; border-radius:16px; padding:16px;
  border:1px solid #eee; box-shadow:0 4px 14px rgba(0,0,0,.06);
}
.fav-card img {
  width:100%; height:220px; object-fit:contain;
  background:#f8fafc; border-radius:12px;
}
.fav-card h3 { font-size:16px; margin:12px 0 6px; }
.fav-card p { font-weight:700; margin:0; }
</style>
