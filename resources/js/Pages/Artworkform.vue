<template>
  <div>
    <nav-component />

    <div class="container py-4 mt-5 form-section">
      <!-- Header -->
      <div class="form-header text-center mb-4">
        <h2 class="form-title">ARTWORK REQUEST FORM</h2>
        <p class="form-subtitle">
          Complete the form to receive your custom uniform mockup • Delivered within 2–3 business days
        </p>
      </div>

      <form @submit.prevent="submitForm" class="modern-form">
        <div class="form-grid">
          <!-- Personal Information -->
          <div class="form-card">
            <h6 class="section-title">Personal Information</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <input v-model="form.fullName" type="text" class="form-control" required />
              </div>
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input v-model="form.email" type="email" class="form-control" required />
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input v-model="form.phone" type="text" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Instagram</label>
                <input v-model="form.instagram" type="text" class="form-control" />
              </div>
            </div>
          </div>

          <!-- Team Details -->
          <div class="form-card">
            <h6 class="section-title">Team Details</h6>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Address</label>
                <input v-model="form.address" type="text" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Team / Organization Name</label>
                <input v-model="form.teamName" type="text" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">You Are</label>
                <select v-model="form.role" class="form-select">
                  <option>Player</option>
                  <option>Parent</option>
                  <option>Coach</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Order Specifications -->
          <div class="form-card">
            <h6 class="section-title">Order Specifications</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Order Quantity</label>
                <input v-model="form.quantity" type="number" class="form-control" min="1" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Team Colors</label>
                <input v-model="form.teamColor" type="text" class="form-control" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Home Mockup?</label>
                <select v-model="form.homeAway" class="form-select">
                  <option>Yes</option>
                  <option>No</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Design Style</label>
                <select v-model="form.designStyle" class="form-select">
                  <option>Traditional</option>
                  <option>Non-Traditional</option>
                  <option>Combo</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Material Type</label>
                <select v-model="form.material" class="form-select">
                  <option>Fully Twill</option>
                  <option>Sub + Twill</option>
                  <option>Silicone + Twill</option>
                  <option>Fully Sublimation</option>
                  <option>Don't Know</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Additional Info -->
          <div class="form-card">
            <h6 class="section-title">Additional Info / Notes</h6>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Additional Details</label>
                <textarea v-model="form.additional" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">How Did You Hear About Us?</label>
                <input v-model="form.source" type="text" class="form-control" />
              </div>
            </div>
          </div>

          <!-- Image Upload -->
          <div class="form-card full-width">
            <h6 class="section-title">Upload Reference Images</h6>
            <input
              type="file"
              multiple
              accept="image/*"
              class="form-control"
              @change="handleImages"
            />
            <div v-if="imagePreviews.length" class="preview-grid mt-3">
              <div v-for="(img, index) in imagePreviews" :key="index" class="preview-card">
                <img :src="img" />
                <button type="button" class="remove-btn" @click="removeImage(index)">✕</button>
              </div>
            </div>
          </div>

          <!-- Products -->
          <div class="form-card full-width">
            <h6 class="section-title">Sport / Product Selection</h6>
            <div class="product-grid">
              <label v-for="item in products" :key="item" class="product-checkbox">
                <input type="checkbox" :value="item" v-model="form.products" />
                <span class="product-label">{{ item }}</span>
              </label>
            </div>
          </div>

        </div>

        <!-- Submit -->
        <div class="text-center mt-4">
          <button class="btn-submit" type="submit" :disabled="isSubmitting">
            <span v-if="isSubmitting">
              <i class="spinner-border spinner-border-sm me-2"></i>
              Submitting...
            </span>
            <span v-else>Submit Artwork Request</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Success Modal -->
   <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content dark-modal animate-success">

      <div class="modal-body text-center p-4">
        <div class="success-icon mb-3">✓</div>

        <h4 class="mb-2 text-white">Success</h4>
        <p class="text-white mb-4">
          Artwork Request Submitted Successfully
        </p>

        <button
          type="button"
          class="btn btn-outline-light px-4"
          data-bs-dismiss="modal"
          @click="resetForm"
        >
          OK
        </button>
      </div>

    </div>
  </div>
</div>


    <footer-component />
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ArtworkRequestForm",
  data() {
    return {
      products: [
        "Football Uniforms", "Basketball Uniforms", "Baseball Uniforms", "Track & Field",
        "Compression Shirt", "Compression Tights", "Warm-Ups", "Hoodie",
        "Custom Duffle Bag", "Custom Backpack", "Letterman Jacket", "Cheer Uniform", "Other"
      ],
      images: [],
      imagePreviews: [],
      form: {
        fullName: "", email: "", phone: "", instagram: "", address: "",
        teamName: "", role: "Player", quantity: "", teamColor: "", homeAway: "Yes",
        designStyle: "Traditional", material: "Don't Know", products: [],
        additional: "", source: ""
      },
      isSubmitting: false
    };
  },
  methods: {
    handleImages(e) {
      const files = Array.from(e.target.files);
      files.forEach(file => {
        this.images.push(file);
        const reader = new FileReader();
        reader.onload = ev => this.imagePreviews.push(ev.target.result);
        reader.readAsDataURL(file);
      });
      e.target.value = "";
    },
    removeImage(index) {
      this.images.splice(index, 1);
      this.imagePreviews.splice(index, 1);
    },
    async submitForm() {
      if (this.isSubmitting) return;

      this.isSubmitting = true;
      try {
        const formData = new FormData();
        Object.keys(this.form).forEach(key => {
          if (Array.isArray(this.form[key])) {
            this.form[key].forEach(val => formData.append(`${key}[]`, val));
          } else {
            formData.append(key, this.form[key]);
          }
        });
        this.images.forEach(img => formData.append("images[]", img));

        await axios.post("http://localhost:8000/api/artwork-request", formData, {
          headers: { "Content-Type": "multipart/form-data" }
        });

        // Show success modal
        const modal = new bootstrap.Modal(document.getElementById("successModal"));
        modal.show();

      } catch (err) {
        alert("Something went wrong ❌");
      } finally {
        this.isSubmitting = false;
      }
    },
    resetForm() {
      this.form = {
        fullName: "", email: "", phone: "", instagram: "", address: "",
        teamName: "", role: "Player", quantity: "", teamColor: "", homeAway: "Yes",
        designStyle: "Traditional", material: "Don't Know", products: [],
        additional: "", source: ""
      };
      this.images = [];
      this.imagePreviews = [];
    }
  }
};
</script>

<style scoped>
.form-section { max-width: 1300px; margin: auto; }
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
.form-card { background: #f8f9fa; padding: 1.5rem; border-radius: 12px; border: 1px solid #000; }
.full-width { grid-column: 1 / -1; }
.preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem; }
.preview-card { position: relative; border: 1px solid #000; border-radius: 10px; overflow: hidden; }
.preview-card img { width: 100%; height: 120px; object-fit: cover; }
.remove-btn { position: absolute; top: 5px; right: 5px; background: black; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; }
.btn-submit { background: black; color: white; padding: 0.8rem 3rem; border-radius: 8px; border: none; font-weight: 600; }
/* Dark Modal Style */
.dark-modal {
  background: #0f0f0f;
  border-radius: 16px;
  border: 1px solid #222;
}

/* Success Check Icon */
.success-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #000;
  border: 2px solid #28a745;
  color: #28a745;
  font-size: 32px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: auto;
}

/* Animation */
.animate-success {
  animation: popCenter 0.35s ease-out;
}

@keyframes popCenter {
  0% {
    transform: scale(0.7);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>
