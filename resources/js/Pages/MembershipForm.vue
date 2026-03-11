<template>
  <div class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav-component />
        <breadcrumb-component />

    <!-- Page Content -->
    <main class="flex-grow-1 form-page d-flex justify-content-center align-items-center">
      <div class="form-card">
        <!-- Header -->
        <div class="text-center mb-3">
          <h2 class="fw-bold mb-1">Special Deals & Promo Package</h2>
          <p class="text-muted small mb-0">
            Fill out the form to get exclusive advantages, pricing, and custom packages.
          </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="form-content">
          <!-- Row 1: Name & Email -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold small">Your Name *</label>
              <input
                v-model="form.name"
                type="text"
                class="form-control form-control-sm"
                placeholder="Full Name"
                required
              />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold small">Email *</label>
              <input
                v-model="form.email"
                type="email"
                class="form-control form-control-sm"
                placeholder="example@email.com"
                required
              />
            </div>
          </div>

          <!-- Row 2: Address & Organization -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold small">Mailing Address *</label>
              <input
                v-model="form.address"
                type="text"
                class="form-control form-control-sm"
                placeholder="Street address"
                required
              />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold small">Organization / School *</label>
              <input
                v-model="form.organization"
                type="text"
                class="form-control form-control-sm"
                placeholder="School / Club / Academy"
                required
              />
            </div>
          </div>

          <!-- Row 3: State, Zip, Phone, Role -->
          <div class="row g-3 mb-3">
            <div class="col-md-3 position-relative">
              <label class="form-label fw-bold small">State / Province *</label>
              <input
                v-model="form.state"
                type="text"
                class="form-control form-control-sm"
                placeholder="Start typing e.g. Pun..."
                @focus="showStateDropdown = true"
                @blur="delayHideDropdown"
                @input="filterStates"
                required
              />

              <!-- State Suggestions Dropdown -->
              <div
                v-if="showStateDropdown && filteredStates.length"
                class="position-absolute w-100 bg-white border shadow-sm rounded mt-1 list-group"
                style="max-height: 240px; overflow-y: auto; z-index: 10;"
              >
                <button
                  v-for="item in filteredStates"
                  :key="item.value"
                  type="button"
                  class="list-group-item list-group-item-action text-start py-2 px-3 small"
                  @mousedown="selectState(item.value)"
                >
                  {{ item.label }}
                </button>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label fw-bold small">Zip / Postal Code</label>
              <input
                v-model="form.zip"
                type="text"
                class="form-control form-control-sm"
                placeholder="54000"
              />
            </div>
            <div class="col-md-3">
              <label class="form-label fw-bold small">Phone Number *</label>
              <input
                v-model="form.phone"
                type="tel"
                class="form-control form-control-sm"
                placeholder="+92 300 1234567"
                required
              />
            </div>
            <div class="col-md-3">
              <label class="form-label fw-bold small">You are a... *</label>
              <button
                type="button"
                class="btn btn-outline-dark btn-sm w-100 text-start"
                @click="showRoleModal = true"
              >
                {{ form.role || 'Select Role' }}
              </button>
            </div>
          </div>

          <!-- Row 4: Sports & Level -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold small">Sport(s) (up to 2) *</label>
              <button
                type="button"
                class="btn btn-outline-dark btn-sm w-100 text-start d-flex justify-content-between align-items-center"
                @click="showSportsModal = true"
              >
                <span>
                  {{ form.sports.length > 0 ? `${form.sports.length} sport(s) selected` : 'Select Sports' }}
                </span>
                <i class="bi bi-chevron-down"></i>
              </button>

              <div v-if="form.sports.length > 0" class="mt-2">
                <span
                  v-for="sport in form.sports"
                  :key="sport"
                  class="badge bg-dark me-1 mb-1"
                >
                  {{ sport }}
                  <button
                    type="button"
                    class="btn-close btn-close-white ms-1"
                    style="font-size: 0.6rem;"
                    @click="removeSport(sport)"
                  ></button>
                </span>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold small">Apparel Level *</label>
              <select v-model="form.level" class="form-select form-select-sm" required>
                <option value="">Select Level</option>
                <option value="Youth">Youth</option>
                <option value="Semi Pro">Semi Pro</option>
                <option value="High School">High School</option>
                <option value="Mockup">Mockup</option>
                <option value="Team Apparel">Team Apparel</option>
              </select>
            </div>
          </div>

          <!-- Submit -->
          <div class="text-center mt-4">
            <button
              type="submit"
              class="btn btn-dark w-100 py-2 fw-bold"
              :disabled="loading"
            >
              <span v-if="loading">
                <span class="spinner-border spinner-border-sm me-2"></span>
                Submitting...
              </span>
              <span v-else>GET MY SPECIAL DEAL →</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Role Modal -->
      <div v-if="showRoleModal" class="modal-backdrop" @click.self="showRoleModal = false">
        <div class="modal-card">
          <h5 class="mb-3 fw-bold">Select Your Role</h5>
          <div class="d-flex flex-column gap-2">
            <button
              v-for="role in roles"
              :key="role"
              type="button"
              class="btn btn-outline-dark text-start"
              @click="selectRole(role)"
            >
              {{ role }}
            </button>
          </div>
          <button class="btn btn-secondary mt-3 w-100" @click="showRoleModal = false">
            Cancel
          </button>
        </div>
      </div>

      <!-- Sports Modal -->
      <div v-if="showSportsModal" class="modal-backdrop" @click.self="showSportsModal = false">
        <div class="modal-card">
          <h5 class="mb-3 fw-bold">Select Sports (up to 2)</h5>
          <div class="sports-grid">
            <button
              v-for="sport in sportsList"
              :key="sport"
              type="button"
              class="btn sport-btn"
              :class="{
                'btn-dark text-white': form.sports.includes(sport),
                'btn-outline-dark': !form.sports.includes(sport)
              }"
              @click="toggleSport(sport)"
              :disabled="form.sports.length >= 2 && !form.sports.includes(sport)"
            >
              {{ sport }}
            </button>
          </div>
          <button class="btn btn-secondary mt-3 w-100" @click="showSportsModal = false">
            Done
          </button>
        </div>
      </div>

      <!-- Success Modal (using Bootstrap) -->
      <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content dark-modal animate-success">
            <div class="modal-body text-center p-4">
              <div class="success-icon mb-3">✓</div>
              <h4 class="mb-2 text-dark">Success</h4>
              <p class="text-dark mb-4">
                Your Special Deal Request has been submitted successfully!
              </p>
              <button
                type="button"
                class="btn btn-outline-dark px-4"
                data-bs-dismiss="modal"
                @click="resetForm"
              >
                OK
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer-component />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const form = ref({
  name: '',
  email: '',
  address: '',
  organization: '',
  state: '',
  zip: '',
  phone: '',
  role: '',
  sports: [],
  level: ''
})

const loading = ref(false)
const showRoleModal = ref(false)
const showSportsModal = ref(false)

// ── State Search ────────────────────────────────────────
const showStateDropdown = ref(false)
const searchQuery = ref('')

const allStatesData = {
  Pakistan: ["Punjab", "Sindh", "Khyber Pakhtunkhwa", "Balochistan", "Islamabad Capital Territory", "Gilgit-Baltistan", "Azad Jammu & Kashmir"],
  India: [
    "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat",
    "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh",
    "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab",
    "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh",
    "Uttarakhand", "West Bengal", "Delhi", "Jammu & Kashmir", "Ladakh"
  ],
  USA: [
    "Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut",
    "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa",
    "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan",
    "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire",
    "New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio",
    "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota",
    "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia",
    "Wisconsin", "Wyoming"
  ]
}

const filteredStates = computed(() => {
  const q = (form.value.state || '').toLowerCase().trim()
  if (q.length < 1) return []

  const results = []

  Object.entries(allStatesData).forEach(([country, states]) => {
    states.forEach(state => {
      if (state.toLowerCase().includes(q)) {
        results.push({
          value: state,
          label: `${state} (${country})`
        })
      }
    })
  })

  return results.slice(0, 12) // limit displayed suggestions
})

const filterStates = () => {
  showStateDropdown.value = true
}

const selectState = (value) => {
  form.value.state = value
  showStateDropdown.value = false
}

const delayHideDropdown = () => {
  setTimeout(() => {
    showStateDropdown.value = false
  }, 180)
}

// ── Role & Sports Logic ─────────────────────────────────
const roles = ['Coach', 'Athletic Director', 'Manager', 'Player', 'Parent']

const sportsList = [
  'Cricket', 'Football', 'Basketball', 'Hockey', 'Volleyball',
  'Badminton', 'Tennis', 'Squash', 'Table Tennis', 'Others'
]

const selectRole = (role) => {
  form.value.role = role
  showRoleModal.value = false
}

const toggleSport = (sport) => {
  const index = form.value.sports.indexOf(sport)
  if (index > -1) {
    form.value.sports.splice(index, 1)
  } else if (form.value.sports.length < 2) {
    form.value.sports.push(sport)
  }
}

const removeSport = (sport) => {
  const index = form.value.sports.indexOf(sport)
  if (index > -1) form.value.sports.splice(index, 1)
}

// ── Form Submit ─────────────────────────────────────────
const submitForm = async () => {
  if (form.value.sports.length === 0) {
    alert('Please select at least one sport')
    return
  }

  loading.value = true

  try {
const token = localStorage.getItem('auth_token');
const config = token ? { headers: { Authorization: `Bearer ${token}` } } : {};
await axios.post('/api/membership-request', form.value, config);
    const modal = new bootstrap.Modal(document.getElementById('successModal'))
    modal.show()
  } catch (err) {
    console.error(err)
    alert(err.response?.data?.message || 'Something went wrong. Please try again.')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    address: '',
    organization: '',
    state: '',
    zip: '',
    phone: '',
    role: '',
    sports: [],
    level: ''
  }
}
</script>

<style scoped>
.form-page {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: calc(100vh - 120px);
  padding: 1.5rem 0;
}
.form-card {
  max-width: 950px;
  width: 95%;
  background: #fff;
  padding: 2rem;
  margin-top: 50px;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}
.form-control-sm,
.form-select-sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.9rem;
}

/* State dropdown enhancements */
.list-group-item-action:hover {
  background-color: #f1f3f5;
}

/* Modal styles remain same */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}
.modal-card {
  background: #fff;
  padding: 2rem;
  border-radius: 16px;
  width: 400px;
  max-width: 90%;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.sports-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}
.sport-btn {
  padding: 0.75rem;
  font-size: 0.9rem;
  transition: all 0.2s;
}
.sport-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.sport-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.badge {
  padding: 0.4rem 0.6rem;
  font-size: 0.85rem;
  font-weight: 500;
}

.dark-modal {
  background: #f8f9fa;
  border-radius: 16px;
  border: 1px solid #ccc;
}
.success-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: black;
  border: 2px solid #28a745;
  color: #28a745;
  font-size: 32px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: auto;
}
.animate-success {
  animation: popCenter 0.35s ease-out;
}
@keyframes popCenter {
  0% { transform: scale(0.7); opacity: 0 }
  100% { transform: scale(1); opacity: 1 }
}

@media (max-width: 768px) {
  .form-card { padding: 1.5rem; }
  .sports-grid { grid-template-columns: 1fr; }
}
</style>
