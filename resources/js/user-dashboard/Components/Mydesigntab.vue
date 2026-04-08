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

        <div class="btn-group">
          <!--
            Customize button:
            - Opens the model's customizer page
            - Passes design_id as query param so the customizer
              can load the user's saved colors/patterns for that design
          -->
          <a
            :href="`/customize/${design.model?.id}?design_id=${design.id}`"
            class="edit-btn"
          >
            Customize
          </a>

          <button
            class="delete-btn"
            :disabled="deletingId === design.id"
            @click="deleteDesign(design.id)"
          >
            {{ deletingId === design.id ? 'Removing...' : 'Remove' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const designs    = ref([])
const loading    = ref(true)
const deletingId = ref(null)   // tracks which card is being deleted

// ── Fetch user designs ──────────────────────────────────────────────────────
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
    console.error('Fetch designs error:', err)
  } finally {
    loading.value = false
  }
})

// ── CSRF token helper ───────────────────────────────────────────────────────
// Laravel stores XSRF-TOKEN in a cookie; we read it and send it as a header
const getCsrfToken = () => {
  const match = document.cookie
    .split('; ')
    .find(row => row.startsWith('XSRF-TOKEN='))
  return match ? decodeURIComponent(match.split('=')[1]) : ''
}

// ── Delete a design ─────────────────────────────────────────────────────────
const deleteDesign = async (id) => {
  if (!confirm('Are you sure you want to delete this design?')) return

  deletingId.value = id   // show loading state on button

  try {
    const token = localStorage.getItem('auth_token')

    const res = await fetch(`/api/user/designs/${id}`, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
        'X-XSRF-TOKEN': getCsrfToken()   // ✅ fixes CSRF token mismatch
      }
    })

    if (res.ok) {
      // Remove from local list — no page reload needed
      designs.value = designs.value.filter(d => d.id !== id)
    } else {
      const body = await res.json().catch(() => ({}))
      alert(body.message || 'Delete failed. Please try again.')
    }
  } catch (err) {
    console.error('Delete error:', err)
    alert('Something went wrong. Please try again.')
  } finally {
    deletingId.value = null
  }
}
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

/* ── Grid ── */
.design-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
}

/* ── Card ── */
.design-card {
  background: white;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
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

/* ── Buttons ── */
.btn-group {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin-top: 10px;
}

.edit-btn,
.delete-btn {
  display: inline-block;
  padding: 8px 16px;
  background: #000;
  color: white;
  border: none;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.edit-btn:hover   { background: #161616; }
.delete-btn:hover { background: #252525; }

/* Disabled state while deleting */
.delete-btn:disabled {
  background: #6b7280;
  cursor: not-allowed;
  opacity: 0.7;
}
</style>
