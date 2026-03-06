<template>
  <div>
    <nav-component />
    <breadcrumb-component />

    <div class="container py-4 mt-5 form-section">
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

              <!-- Team Colors - Click to open popup -->
              <div class="col-md-8">
                <label class="form-label">Team Colors</label>
                <div class="color-selector-box" @click="openColorPicker">
                  <span v-if="form.selectedColors.length === 0" class="color-placeholder">Click to select colors...</span>
                  <div v-else class="selected-colors-preview">
                    <div v-for="c in form.selectedColors" :key="c.id" class="selected-color-chip">
                      <span class="chip-swatch" :style="{ background: c.code }"></span>
                      <span class="chip-name">{{ c.name }}</span>
                      <button type="button" class="chip-remove" @click.stop="removeColor(c)">×</button>
                    </div>
                  </div>
                  <i class="bi bi-chevron-down color-arrow"></i>
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label">Home Mockup?</label>
                <select v-model="form.homeAway" class="form-select">
                  <option>Yes</option>
                  <option>No</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Design Style</label>
                <select v-model="form.designStyle" class="form-select">
                  <option>Traditional</option>
                  <option>Non-Traditional</option>
                  <option>Combo</option>
                </select>
              </div>
              <div class="col-md-4">
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
            <input type="file" multiple accept="image/*" class="form-control" @change="handleImages" />
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

        <div class="text-center mt-4">
          <button class="btn-submit" type="submit" :disabled="isSubmitting">
            <span v-if="isSubmitting"><i class="spinner-border spinner-border-sm me-2"></i>Submitting...</span>
            <span v-else>Submit Artwork Request</span>
          </button>
        </div>
      </form>
    </div>

    <!-- ===== COLOR PICKER POPUP ===== -->
    <div v-if="colorPickerOpen" class="color-popup-backdrop" @click.self="closeColorPicker">
      <div class="color-popup">

        <div class="color-popup-header">
          <h6>Select Team Colors</h6>
          <button type="button" @click="closeColorPicker" class="popup-close">×</button>
        </div>

        <div v-if="colorsLoading" class="text-center py-4">
          <div class="spinner-border spinner-border-sm"></div>
        </div>

        <div v-else class="color-popup-grid">
          <div
            v-for="color in allColors"
            :key="color.id"
            class="color-popup-item"
            :class="{ selected: isSelected(color) }"
            @click="toggleColor(color)"
          >
            <div class="popup-swatch" :style="{ background: color.code }">
              <i v-if="isSelected(color)" class="bi bi-check-lg check-icon"></i>
            </div>
            <span class="popup-color-name">{{ color.name }}</span>
          </div>
        </div>

        <div class="color-popup-footer">
          <span class="selected-count">{{ form.selectedColors.length }} color(s) selected</span>
          <button type="button" class="btn btn-dark btn-sm px-4" @click="closeColorPicker">Done</button>
        </div>

      </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dark-modal animate-success">
          <div class="modal-body text-center p-4">
            <div class="success-icon mb-3">✓</div>
            <h4 class="mb-2 text-white">Success</h4>
            <p class="text-white mb-4">Artwork Request Submitted Successfully</p>
            <button type="button" class="btn btn-outline-light px-4" data-bs-dismiss="modal" @click="resetForm">OK</button>
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
        teamName: "", role: "Player", quantity: "",
        selectedColors: [],
        homeAway: "Yes", designStyle: "Traditional", material: "Don't Know",
        products: [], additional: "", source: ""
      },
      isSubmitting: false,
      colorPickerOpen: false,
      allColors: [],
      colorsLoading: false,
    };
  },
  methods: {
    async openColorPicker() {
      this.colorPickerOpen = true;
      if (this.allColors.length === 0) {
        this.colorsLoading = true;
        try {
          const res = await axios.get('/api/colors');
          this.allColors = res.data;
        } catch (e) {
          console.error(e);
        } finally {
          this.colorsLoading = false;
        }
      }
    },
    closeColorPicker() { this.colorPickerOpen = false; },
    isSelected(color) { return this.form.selectedColors.some(c => c.id === color.id); },
    toggleColor(color) {
      if (this.isSelected(color)) {
        this.form.selectedColors = this.form.selectedColors.filter(c => c.id !== color.id);
      } else {
        this.form.selectedColors.push(color);
      }
    },
    removeColor(color) {
      this.form.selectedColors = this.form.selectedColors.filter(c => c.id !== color.id);
    },
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
          if (key === 'selectedColors') return;
          if (Array.isArray(this.form[key])) {
            this.form[key].forEach(val => formData.append(`${key}[]`, val));
          } else {
            formData.append(key, this.form[key]);
          }
        });
        // Send color names as teamColor
        formData.append('teamColor', this.form.selectedColors.map(c => c.name).join(', '));
        this.images.forEach(img => formData.append("images[]", img));

        await axios.post('/api/artwork-request', formData);
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
        teamName: "", role: "Player", quantity: "", selectedColors: [],
        homeAway: "Yes", designStyle: "Traditional", material: "Don't Know",
        products: [], additional: "", source: ""
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

/* ── Color Selector Box ── */
.color-selector-box {
  min-height: 44px; border: 1px solid #ced4da; border-radius: 8px;
  padding: 6px 12px; cursor: pointer; background: #fff;
  display: flex; align-items: center; justify-content: space-between; gap: 8px;
  transition: border-color 0.2s;
}
.color-selector-box:hover { border-color: #000; }
.color-placeholder { color: #aaa; font-size: 14px; }
.color-arrow { color: #888; font-size: 12px; flex-shrink: 0; }
.selected-colors-preview { display: flex; flex-wrap: wrap; gap: 6px; flex: 1; }
.selected-color-chip {
  display: flex; align-items: center; gap: 5px;
  background: #f0f0f0; border: 1px solid #ddd;
  border-radius: 20px; padding: 3px 8px 3px 5px; font-size: 12px;
}
.chip-swatch { width: 14px; height: 14px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.15); flex-shrink: 0; }
.chip-name { font-weight: 500; }
.chip-remove { background: none; border: none; cursor: pointer; font-size: 15px; color: #999; padding: 0; margin-left: 2px; line-height: 1; }
.chip-remove:hover { color: #000; }

/* ── Color Popup ── */
.color-popup-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.55);
  z-index: 9999; display: flex; align-items: center; justify-content: center;
}
.color-popup {
  background: #fff; border-radius: 16px;
  width: 540px; max-width: 95vw; max-height: 82vh;
  display: flex; flex-direction: column;
  box-shadow: 0 24px 64px rgba(0,0,0,0.25); overflow: hidden;
}
.color-popup-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px; border-bottom: 1px solid #eee;
}
.color-popup-header h6 { margin: 0; font-weight: 700; font-size: 15px; }
.popup-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #999; line-height: 1; }
.popup-close:hover { color: #000; }

.color-popup-grid {
  display: grid; grid-template-columns: repeat(5, 1fr);
  gap: 16px; padding: 20px; overflow-y: auto;
}
.color-popup-item {
  display: flex; flex-direction: column; align-items: center; gap: 7px;
  cursor: pointer; padding: 10px 6px; border-radius: 12px;
  border: 2px solid transparent; transition: all 0.15s;
}
.color-popup-item:hover { background: #f5f5f5; }
.color-popup-item.selected { border-color: #000; background: #f0f0f0; }
.popup-swatch {
  width: 52px; height: 52px; border-radius: 50%;
  border: 2px solid rgba(0,0,0,0.1);
  display: flex; align-items: center; justify-content: center;
}
.check-icon { color: #fff; font-size: 20px; text-shadow: 0 0 6px rgba(0,0,0,0.7); }
.popup-color-name {
  font-size: 11px; font-weight: 600; text-align: center;
  color: #333; line-height: 1.3; max-width: 64px; word-break: break-word;
}
.color-popup-footer {
  padding: 14px 20px; border-top: 1px solid #eee;
  display: flex; align-items: center; justify-content: space-between;
}
.selected-count { font-size: 13px; color: #777; }

/* ── Product Grid ── */
.product-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 14px 20px; padding-top: 12px;
}
.product-checkbox {
  display: flex; align-items: center; gap: 8px; cursor: pointer;
  padding: 6px 10px; border-radius: 8px; border: 1px solid #ddd;
  background: #fff; transition: border-color 0.2s;
}
.product-checkbox:hover { border-color: #000; }
.product-checkbox input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }
.product-label { font-size: 13px; font-weight: 500; }

/* Dark Modal */
.dark-modal { background: #0f0f0f; border-radius: 16px; border: 1px solid #222; }
.success-icon {
  width: 60px; height: 60px; border-radius: 50%; background: #000;
  border: 2px solid #28a745; color: #28a745; font-size: 32px; font-weight: bold;
  display: flex; align-items: center; justify-content: center; margin: auto;
}
.animate-success { animation: popCenter 0.35s ease-out; }
@keyframes popCenter {
  0% { transform: scale(0.7); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
@media (max-width: 768px) {
  .form-grid { grid-template-columns: 1fr; }
  .color-popup-grid { grid-template-columns: repeat(3, 1fr); }
}
</style>
