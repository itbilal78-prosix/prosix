<template>
  <div class="my-design-wrapper">

    <h2 class="title">My Designs</h2>

    <div v-if="loading" class="coming-soon-box">
      Loading designs...
    </div>

    <div v-else-if="designs.length === 0" class="coming-soon-box">
      <h3>No designs yet</h3>
      <p>Your saved designs will appear here.</p>
    </div>

    <div v-else class="design-grid">
      <div
        v-for="design in designs"
        :key="design.id"
        class="design-card"
      >
        <img
          :src="design.thumbnail || '/assets/images/placeholder.png'"
          class="thumb"
          @error="e => e.target.src = '/assets/images/placeholder.png'"
        />

        <h4>{{ design.name }}</h4>

        <small>{{ new Date(design.created_at).toLocaleDateString() }}</small>

        <!-- ✅ Edit button -->
<a
:href="`/customize/${design.model?.id}`"class="edit-btn">Edit Design
        </a>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"

const designs = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) { loading.value = false; return }

    const res = await fetch('/api/user/designs', {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`
      }
    })

    if (res.ok) {
      const data = await res.json()
      designs.value = data.data || []
    }

  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.my-design-wrapper { padding: 20px; }

.title {
  font-size: 1.4rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: #1f2937;
}

.coming-soon-box {
  text-align: center;
  padding: 60px 20px;
  color: #6b7280;
}

.design-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
}

.design-card {
  background: white;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.06);
  text-align: center;
  transition: transform 0.2s;
}

.design-card:hover { transform: translateY(-3px); }

.thumb {
  width: 100%;
  height: 140px;
  object-fit: contain;
  margin-bottom: 10px;
  background: #f9fafb;
  border-radius: 6px;
}

.edit-btn {
  display: inline-block;
  margin-top: 10px;
  padding: 8px 16px;
  background: #000000;
  color: white;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 600;
  transition: background 0.2s;
}

.edit-btn:hover { background: #161616; }
</style>
