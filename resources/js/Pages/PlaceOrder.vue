<template>
  <nav-component />
  <breadcrumb-component />

  <div class="po-page">

    <div class="po-header">
      <h1 class="po-main-title">THANKS FOR CHOOSING US!</h1>
      <p class="po-subtitle">WE REALLY APPRECIATE &amp; VALUE YOUR BUSINESS</p>
    </div>

    <div class="po-layout">

      <!-- LEFT SIDEBAR: How to Place Order -->
      <div class="how-to-sidebar">
        <div class="hts-title">How to Place Order</div>

        <div class="hts-step" :class="stepClass(1)">
          <div class="hts-num">{{ stepDone(1) ? '✓' : '1' }}</div>
          <div>
            <div class="hts-label">Enter your details</div>
            <div class="hts-desc">Fill name, email, phone & delivery date</div>
          </div>
        </div>

        <div class="hts-step" :class="stepClass(2)">
          <div class="hts-num">{{ stepDone(2) ? '✓' : '2' }}</div>
          <div>
            <div class="hts-label">Select team colors</div>
            <div class="hts-desc">Pick from the color picker</div>
          </div>
        </div>

        <div class="hts-step" :class="stepClass(3)">
          <div class="hts-num">{{ stepDone(3) ? '✓' : '3' }}</div>
          <div>
            <div class="hts-label">Upload final mockup</div>
            <div class="hts-desc">Drag & drop or browse file</div>
          </div>
        </div>

        <div class="hts-step" :class="stepClass(4)">
          <div class="hts-num">{{ stepDone(4) ? '✓' : '4' }}</div>
          <div>
            <div class="hts-label">Upload team roster</div>
            <div class="hts-desc">Player names & numbers</div>
          </div>
        </div>

        <div class="hts-step" :class="stepClass(5)">
          <div class="hts-num">{{ stepDone(5) ? '✓' : '5' }}</div>
          <div>
            <div class="hts-label">Add notes</div>
            <div class="hts-desc">Optional special instructions</div>
          </div>
        </div>

        <div class="hts-step" :class="stepClass(6)">
          <div class="hts-num">{{ stepDone(6) ? '✓' : '6' }}</div>
          <div>
            <div class="hts-label">Click Place Order</div>
            <div class="hts-desc">Review & submit your order</div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="hts-progress-wrap">
          <div class="hts-progress-label">
            <span>Progress</span>
            <span>{{ completedSteps }}/6</span>
          </div>
          <div class="hts-progress-track">
            <div class="hts-progress-fill" :style="{ width: (completedSteps / 6 * 100) + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- MAIN FORM CONTAINER -->
      <div class="po-container">

        <!-- Row 1: Name, Email, Phone, Date, Delivery, Sales Rep, Colors, Order# -->
        <div class="po-top-row">

          <div class="po-field-group">
            <label class="po-label">Full Name *</label>
            <input type="text" class="po-input" v-model="form.fullName" placeholder="Your Name" required />
          </div>

          <div class="po-field-group">
            <label class="po-label">Email *</label>
            <input type="email" class="po-input" v-model="form.email" placeholder="your@email.com" required />
          </div>

          <!-- ✅ NEW PHONE FIELD -->
          <div class="po-field-group">
            <label class="po-label">Phone Number</label>
            <input type="tel" class="po-input" v-model="form.phone" placeholder="+1 234 567 8900" />
          </div>

          <div class="po-field-group">
            <label class="po-label">Order Place Date</label>
            <input type="text" class="po-input" :value="todayDate" readonly />
          </div>

          <div class="po-field-group">
            <label class="po-label">Mention Delivery Date</label>
            <input type="date" class="po-input" v-model="form.deliveryDate" :min="minDeliveryDate" />
          </div>

          <div class="po-field-group">
            <label class="po-label">Mention Your Sales Rep</label>
            <input type="text" class="po-input" v-model="form.salesRep" placeholder="Sales Rep Name" />
          </div>

          <div class="po-field-group">
            <label class="po-label">Your Team Colors</label>
            <div class="po-color-selector" @click="openColorPicker">
              <div v-if="form.selectedColors.length === 0" class="po-color-placeholder">
                Please select color
              </div>
              <div v-else class="po-selected-chips">
                <div v-for="c in form.selectedColors" :key="c.id" class="po-chip">
                  <span class="po-chip-dot" :style="{ background: c.code }"></span>
                  <span class="po-chip-name">{{ c.name }}</span>
                  <button type="button" class="po-chip-remove" @click.stop="removeColor(c)">×</button>
                </div>
              </div>
              <i class="bi bi-chevron-down po-chevron"></i>
            </div>
          </div>

          <div class="po-field-group">
            <label class="po-label">Order #</label>
            <input type="text" class="po-input po-order-num" :value="orderNumber" readonly />
          </div>

        </div>

        <div class="po-divider"></div>

        <!-- Upload + Notes Row -->
        <div class="po-mid-row">

          <!-- Final Mockup -->
          <div class="po-upload-card">
            <h6 class="po-upload-title">Upload Your Final Mockup</h6>
            <div class="po-dropzone" @click="$refs.mockupInput.click()" @dragover.prevent @drop.prevent="handleDrop($event, 'mockup')">
              <div class="po-cloud-icon">
                <svg viewBox="0 0 64 48" fill="none"><path d="M32 36V16M32 16l-8 8M32 16l8 8" stroke="#555" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 36a12 12 0 1 1 3.5-23.4A14 14 0 1 1 46 28H16" stroke="#555" stroke-width="2.5" stroke-linecap="round"/></svg>
              </div>
              <p class="po-browse-text">Browse file to upload</p>
            </div>
            <input ref="mockupInput" type="file" multiple accept="image/*,application/pdf,.xlsx,.xls,.doc,.docx" hidden @change="handleFiles($event, 'mockup')" />
            <div class="po-file-list">
              <div v-for="(f, i) in mockupFiles" :key="i" class="po-file-item">
                <div class="po-file-thumb">
                  <img v-if="f.preview" :src="f.preview" class="po-thumb-img" />
                  <div v-else class="po-file-type-icon" :class="getFileTypeClass(f.name)">{{ getFileExt(f.name) }}</div>
                </div>
                <div class="po-file-info">
                  <span class="po-file-name">{{ f.name }}</span>
                  <span class="po-file-size">{{ formatSize(f.size) }}</span>
                  <div v-if="f.progress < 100" class="po-progress-bar"><div class="po-progress-fill" :style="{ width: f.progress + '%' }"></div></div>
                </div>
                <div class="po-file-status">
                  <span v-if="f.progress < 100" class="po-pct">{{ f.progress }}%</span>
                  <svg v-else viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2.5" class="po-check"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <button type="button" class="po-file-remove" @click="removeFile('mockup', i)">×</button>
              </div>
            </div>
          </div>

          <!-- Team Roster -->
          <div class="po-upload-card">
            <h6 class="po-upload-title">Upload Your Team Roster</h6>
            <div class="po-dropzone" @click="$refs.rosterInput.click()" @dragover.prevent @drop.prevent="handleDrop($event, 'roster')">
              <div class="po-cloud-icon">
                <svg viewBox="0 0 64 48" fill="none"><path d="M32 36V16M32 16l-8 8M32 16l8 8" stroke="#555" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 36a12 12 0 1 1 3.5-23.4A14 14 0 1 1 46 28H16" stroke="#555" stroke-width="2.5" stroke-linecap="round"/></svg>
              </div>
              <p class="po-browse-text">Browse file to upload</p>
            </div>
            <input ref="rosterInput" type="file" multiple accept="image/*,application/pdf,.xlsx,.xls,.doc,.docx" hidden @change="handleFiles($event, 'roster')" />
            <div class="po-file-list">
              <div v-for="(f, i) in rosterFiles" :key="i" class="po-file-item">
                <div class="po-file-thumb">
                  <img v-if="f.preview" :src="f.preview" class="po-thumb-img" />
                  <div v-else class="po-file-type-icon" :class="getFileTypeClass(f.name)">{{ getFileExt(f.name) }}</div>
                </div>
                <div class="po-file-info">
                  <span class="po-file-name">{{ f.name }}</span>
                  <span class="po-file-size">{{ formatSize(f.size) }}</span>
                  <div v-if="f.progress < 100" class="po-progress-bar"><div class="po-progress-fill" :style="{ width: f.progress + '%' }"></div></div>
                </div>
                <div class="po-file-status">
                  <span v-if="f.progress < 100" class="po-pct">{{ f.progress }}%</span>
                  <svg v-else viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2.5" class="po-check"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <button type="button" class="po-file-remove" @click="removeFile('roster', i)">×</button>
              </div>
            </div>
          </div>

          <!-- Quote/Invoice -->
          <div class="po-upload-card">
            <h6 class="po-upload-title">Your Quot / Inv (Optional)</h6>
            <div class="po-dropzone" @click="$refs.quoteInput.click()" @dragover.prevent @drop.prevent="handleDrop($event, 'quote')">
              <div class="po-cloud-icon">
                <svg viewBox="0 0 64 48" fill="none"><path d="M32 36V16M32 16l-8 8M32 16l8 8" stroke="#555" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 36a12 12 0 1 1 3.5-23.4A14 14 0 1 1 46 28H16" stroke="#555" stroke-width="2.5" stroke-linecap="round"/></svg>
              </div>
              <p class="po-browse-text">Browse file to upload</p>
            </div>
            <input ref="quoteInput" type="file" multiple accept="image/*,application/pdf,.xlsx,.xls,.doc,.docx" hidden @change="handleFiles($event, 'quote')" />
            <div class="po-file-list">
              <div v-for="(f, i) in quoteFiles" :key="i" class="po-file-item">
                <div class="po-file-thumb">
                  <img v-if="f.preview" :src="f.preview" class="po-thumb-img" />
                  <div v-else class="po-file-type-icon" :class="getFileTypeClass(f.name)">{{ getFileExt(f.name) }}</div>
                </div>
                <div class="po-file-info">
                  <span class="po-file-name">{{ f.name }}</span>
                  <span class="po-file-size">{{ formatSize(f.size) }}</span>
                  <div v-if="f.progress < 100" class="po-progress-bar"><div class="po-progress-fill" :style="{ width: f.progress + '%' }"></div></div>
                </div>
                <div class="po-file-status">
                  <span v-if="f.progress < 100" class="po-pct">{{ f.progress }}%</span>
                  <svg v-else viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2.5" class="po-check"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <button type="button" class="po-file-remove" @click="removeFile('quote', i)">×</button>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="po-notes-card">
            <h6 class="po-upload-title">Notes :</h6>
            <div class="po-notes-toolbar">
              <button type="button" class="po-tb-btn" :class="{ active: noteFormat.bold }" @click="toggleFormat('bold')" title="Bold"><b>B</b></button>
              <button type="button" class="po-tb-btn" :class="{ active: noteFormat.italic }" @click="toggleFormat('italic')" title="Italic"><i>I</i></button>
              <button type="button" class="po-tb-btn" :class="{ active: noteFormat.underline }" @click="toggleFormat('underline')" title="Underline"><u>U</u></button>
              <div class="po-tb-sep"></div>
              <button type="button" class="po-tb-btn" @click="toggleFormat('insertUnorderedList')" title="Bullet List">
                <svg viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><line x1="6" y1="5" x2="16" y2="5"/><line x1="6" y1="9" x2="16" y2="9"/><line x1="6" y1="13" x2="16" y2="13"/><circle cx="2.5" cy="5" r="1.2" fill="currentColor" stroke="none"/><circle cx="2.5" cy="9" r="1.2" fill="currentColor" stroke="none"/><circle cx="2.5" cy="13" r="1.2" fill="currentColor" stroke="none"/></svg>
              </button>
              <button type="button" class="po-tb-btn" @click="toggleFormat('insertOrderedList')" title="Numbered List">
                <svg viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><line x1="7" y1="5" x2="16" y2="5"/><line x1="7" y1="9" x2="16" y2="9"/><line x1="7" y1="13" x2="16" y2="13"/><text x="1" y="6" font-size="5" fill="currentColor" stroke="none">1</text><text x="1" y="10" font-size="5" fill="currentColor" stroke="none">2</text><text x="1" y="14" font-size="5" fill="currentColor" stroke="none">3</text></svg>
              </button>
            </div>
            <div ref="notesEditor" class="po-notes-editor" contenteditable="true" @input="onNotesInput" @mouseup="updateFormatState" @keyup="updateFormatState"></div>
          </div>

        </div>

        <div class="po-divider"></div>

        <div class="po-submit-row">
          <button class="po-submit-btn" @click="submitOrder" :disabled="isSubmitting">
            <span v-if="isSubmitting"><span class="po-spinner"></span> Submitting...</span>
            <span v-else>Place Order</span>
          </button>
        </div>

      </div>
      <!-- END po-container -->

    </div>
    <!-- END po-layout -->

  </div>
  <!-- END po-page -->

  <!-- Color Picker Popup -->
  <div v-if="colorPickerOpen" class="po-color-backdrop" @click.self="closeColorPicker">
    <div class="po-color-popup">
      <div class="po-color-popup-header">
        <h6>Select Team Colors</h6>
        <button type="button" @click="closeColorPicker" class="po-popup-close">×</button>
      </div>
      <div v-if="colorsLoading" class="po-color-loading"><div class="po-spinner-dark"></div></div>
      <div v-else class="po-color-grid">
        <div v-for="color in allColors" :key="color.id" class="po-color-item" :class="{ selected: isSelected(color) }" @click="toggleColor(color)">
          <div class="po-color-swatch" :style="{ background: color.code }">
            <svg v-if="isSelected(color)" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" class="po-swatch-check"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <span class="po-color-name">{{ color.name }}</span>
        </div>
        <div class="po-color-item" @click="openCustomColor">
          <div class="po-color-swatch" style="background:#fff; border:2px dashed #000;">+</div>
          <span class="po-color-name">Other</span>
        </div>
      </div>

      <div v-if="showCustomPicker" class="custom-picker-box">
        <div class="custom-picker-inner">
          <input type="color" v-model="tempCustomColor" class="custom-color-input" />
          <div class="custom-picker-actions">
            <button class="po-cancel-btn" @click="showCustomPicker = false">Cancel</button>
            <button class="po-done-btn" @click="applyCustomColor">Apply</button>
          </div>
        </div>
      </div>

      <div class="po-color-popup-footer">
        <span>{{ form.selectedColors.length }} selected</span>
        <button type="button" class="po-done-btn" @click="closeColorPicker">Done</button>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div v-if="showSuccess" class="po-modal-backdrop">
    <div class="po-modal">
      <div class="po-modal-icon">✓</div>
      <h4>Order Placed!</h4>
      <p>Your order has been submitted successfully.</p>
      <p class="po-modal-order">Order # {{ orderNumber }}</p>
      <button class="po-done-btn" @click="resetForm">OK</button>
    </div>
  </div>

  <footer-component />
</template>

<script>
import axios from 'axios';

export default {
  name: 'PlaceOrder',
  data() {
    const today = new Date();
    const year  = today.getFullYear();
    const minDate = new Date();
    minDate.setDate(today.getDate() + 7);
    const orderNum = `P6S: ${year}-${Math.floor(1000 + Math.random() * 9000)}`;

    return {
      showCustomPicker: false,
      tempCustomColor: "#000000",
      todayDate: today.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }),
      minDeliveryDate: minDate.toISOString().split('T')[0],
      orderNumber: orderNum,
      form: {
        fullName: '',
        email: '',
        phone: '',        // ✅ NEW
        deliveryDate: '',
        salesRep: '',
        selectedColors: [],
        notes: '',
      },
      mockupFiles: [], rosterFiles: [], quoteFiles: [],
      colorPickerOpen: false, allColors: [], colorsLoading: false,
      isSubmitting: false, showSuccess: false,
      noteFormat: { bold: false, italic: false, underline: false },
    };
  },

  computed: {
    completedSteps() {
      return [1, 2, 3, 4, 5, 6].filter(n => this.stepDone(n)).length;
    },
  },

  methods: {
    applyCustomColor() {
      this.form.selectedColors.push({ id: Date.now(), name: "Custom", code: this.tempCustomColor });
      this.showCustomPicker = false;
    },

    stepDone(n) {
      if (n === 1) return !!(this.form.fullName && this.form.email && this.form.deliveryDate);
      if (n === 2) return this.form.selectedColors.length > 0;
      if (n === 3) return this.mockupFiles.length > 0;
      if (n === 4) return this.rosterFiles.length > 0;
      if (n === 5) return this.form.notes.trim().length > 0;
      if (n === 6) return [1, 2, 3, 4].every(i => this.stepDone(i));
      return false;
    },
    stepClass(n) {
      if (this.stepDone(n)) return 'done';
      for (let i = 1; i < n; i++) { if (!this.stepDone(i)) return ''; }
      return 'active';
    },

    async openColorPicker() {
      this.colorPickerOpen = true;
      if (this.allColors.length === 0) {
        this.colorsLoading = true;
        try {
          const res = await axios.get('/api/colors');
          this.allColors = res.data;
        } catch {
          this.allColors = [
            { id:1, name:'Black', code:'#000000' }, { id:2, name:'White', code:'#ffffff' },
            { id:3, name:'Red', code:'#e63946' }, { id:4, name:'Navy', code:'#023e8a' },
            { id:5, name:'Royal Blue', code:'#4895ef' }, { id:6, name:'Orange', code:'#f4a261' },
            { id:7, name:'Green', code:'#2dc653' }, { id:8, name:'Gold', code:'#ffd60a' },
            { id:9, name:'Purple', code:'#7b2d8b' }, { id:10, name:'Maroon', code:'#800000' },
            { id:11, name:'Grey', code:'#888888' }, { id:12, name:'Pink', code:'#f72585' },
          ];
        } finally { this.colorsLoading = false; }
      }
    },
    closeColorPicker() { this.colorPickerOpen = false; },
    isSelected(c)      { return this.form.selectedColors.some(x => x.id === c.id); },
    toggleColor(c) {
      if (this.isSelected(c)) this.form.selectedColors = this.form.selectedColors.filter(x => x.id !== c.id);
      else this.form.selectedColors.push(c);
    },
    removeColor(c) { this.form.selectedColors = this.form.selectedColors.filter(x => x.id !== c.id); },
    openCustomColor() { this.showCustomPicker = true; },

    handleFiles(e, type) { this.addFiles(Array.from(e.target.files), type); e.target.value = ''; },
    handleDrop(e, type)  { this.addFiles(Array.from(e.dataTransfer.files), type); },
    addFiles(files, type) {
      files.forEach(file => {
        const entry = { name: file.name, size: file.size, progress: 0, file, preview: null };
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = ev => { entry.preview = ev.target.result; };
          reader.readAsDataURL(file);
        }
        this[type + 'Files'].push(entry);
        const idx = this[type + 'Files'].length - 1;
        let prog = 0;
        const timer = setInterval(() => {
          prog += Math.floor(Math.random() * 25) + 10;
          if (prog >= 100) { prog = 100; clearInterval(timer); }
          this[type + 'Files'][idx].progress = prog;
        }, 180);
      });
    },
    removeFile(type, i) { this[type + 'Files'].splice(i, 1); },
    formatSize(b) {
      if (b < 1024)    return b + ' B';
      if (b < 1048576) return (b / 1024).toFixed(2) + ' KB';
      return (b / 1048576).toFixed(2) + ' MB';
    },
    getFileExt(name)  { return name.split('.').pop().toUpperCase().slice(0, 4); },
    getFileTypeClass(name) {
      const ext = name.split('.').pop().toLowerCase();
      if (ext === 'pdf') return 'type-pdf';
      if (['xlsx','xls'].includes(ext)) return 'type-xls';
      if (['doc','docx'].includes(ext)) return 'type-doc';
      return 'type-other';
    },

    toggleFormat(cmd) { document.execCommand(cmd, false, null); this.$refs.notesEditor.focus(); this.updateFormatState(); },
    updateFormatState() {
      this.noteFormat.bold      = document.queryCommandState('bold');
      this.noteFormat.italic    = document.queryCommandState('italic');
      this.noteFormat.underline = document.queryCommandState('underline');
    },
    onNotesInput() { this.form.notes = this.$refs.notesEditor.innerHTML; },

    async submitOrder() {
      if (this.isSubmitting) return;
      if (!this.form.fullName || !this.form.email) {
        alert('Please fill in your Full Name and Email.');
        return;
      }
      this.isSubmitting = true;
      try {
        const fd = new FormData();
        fd.append('full_name',     this.form.fullName);
        fd.append('email',         this.form.email);
        fd.append('phone',         this.form.phone);   // ✅ NEW
        fd.append('order_number',  this.orderNumber);
        fd.append('order_date',    this.todayDate);
        fd.append('delivery_date', this.form.deliveryDate);
        fd.append('sales_rep',     this.form.salesRep);
        fd.append('notes',         this.form.notes);
        fd.append('team_colors',   this.form.selectedColors.map(c => c.name).join(', '));
        this.mockupFiles.forEach(f => fd.append('mockup_files[]', f.file));
        this.rosterFiles.forEach(f => fd.append('roster_files[]', f.file));
        this.quoteFiles.forEach(f  => fd.append('quote_files[]',  f.file));

        const token = localStorage.getItem('auth_token');
        const cfg   = token ? { headers: { Authorization: `Bearer ${token}` } } : {};
        await axios.post('/api/place-order', fd, cfg);
        this.showSuccess = true;
      } catch (err) {
        alert(err.response?.data?.message || 'Server Error. Please try again.');
      } finally { this.isSubmitting = false; }
    },

    resetForm() {
      this.showSuccess = false;
      this.form = { fullName: '', email: '', phone: '', deliveryDate: '', salesRep: '', selectedColors: [], notes: '' };
      this.mockupFiles = []; this.rosterFiles = []; this.quoteFiles = [];
      if (this.$refs.notesEditor) this.$refs.notesEditor.innerHTML = '';
      const today = new Date();
      this.orderNumber = `P6S: ${today.getFullYear()}-${Math.floor(1000 + Math.random() * 9000)}`;
    },
  },
};
</script>

<style scoped>
/* All your existing styles remain exactly the same */
@import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap');

.po-page { min-height: 80vh; background: #fff; padding-bottom: 60px; position: relative; }
.po-header { text-align: center; padding: 40px 20px 20px; border-bottom: 1px solid #e5e5e5; }
.po-main-title { font-family: 'Barlow Condensed', sans-serif; font-size: clamp(22px,4vw,38px); font-weight: 800; font-style: italic; color: #000; margin: 0 0 6px; }
.po-subtitle { font-size: 12px; letter-spacing: 3px; color: #666; text-transform: uppercase; margin: 0; }
.po-layout { display: flex; gap: 0; align-items: flex-start; }
.custom-picker-box { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 99999; }
.custom-picker-inner { background: white; padding: 25px; border-radius: 14px; box-shadow: 0 20px 59px rgba(0,0,0,0.25); display: flex; flex-direction: column; align-items: center; gap: 18px; }
.custom-color-input { width: 120px; height: 120px; border: none; cursor: pointer; }
.custom-picker-actions { display: flex; gap: 12px; }
.po-cancel-btn { background: #eee; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; }
.po-cancel-btn:hover { background: #ddd; }
.how-to-sidebar { width: 230px; flex-shrink: 0; position: sticky; top: 20px; align-self: flex-start; background: #fff; border-right: 1px solid #e5e5e5; padding: 28px 18px 28px 24px; min-height: calc(100vh - 160px); }
.hts-title { font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #888; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
.hts-step { display: flex; align-items: flex-start; gap: 10px; padding: 10px 10px; border-radius: 8px; margin-bottom: 4px; border: 1.5px solid transparent; transition: background 0.25s, border-color 0.25s; }
.hts-step.active { background: #000; border-color: #000; }
.hts-step.active .hts-num { background: #fff; color: #000; }
.hts-step.active .hts-label { color: #fff; }
.hts-step.active .hts-desc { color: rgba(255,255,255,0.6); }
.hts-step.done { background: #eaf3de; border-color: #c0dd97; }
.hts-step.done .hts-num { background: #639922; color: #fff; }
.hts-num { width: 24px; height: 24px; border-radius: 50%; background: #f0f0f0; color: #666; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; transition: background 0.25s, color 0.25s; }
.hts-label { font-size: 12px; font-weight: 700; color: #222; line-height: 1.3; transition: color 0.25s; }
.hts-desc { font-size: 11px; color: #888; margin-top: 2px; line-height: 1.4; transition: color 0.25s; }
.hts-progress-wrap { margin-top: 20px; padding-top: 16px; border-top: 1px solid #eee; }
.hts-progress-label { display: flex; justify-content: space-between; font-size: 11px; color: #888; margin-bottom: 6px; }
.hts-progress-track { height: 4px; background: #eee; border-radius: 99px; overflow: hidden; }
.hts-progress-fill { height: 100%; background: #000; border-radius: 99px; transition: width 0.4s ease; }
.po-container { flex: 1; min-width: 0; padding: 30px 40px; }
.po-top-row { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1.2fr 1.8fr 0.9fr; gap: 16px; align-items: end; }
.po-field-group { display: flex; flex-direction: column; gap: 6px; }
.po-label { font-size: 17px; font-weight: 600; color: #333; }
.po-input { height: 38px; border: 1px solid #d0d0d0; border-radius: 6px; padding: 0 12px; font-size: 17px; font-family: 'DM Sans', sans-serif; color: #000; background: #fafafa; outline: none; transition: border-color 0.2s; }
.po-input:focus { border-color: #000; background: #fff; }
.po-input[readonly] { background: #f0f0f0; color: #555; cursor: default; }
.po-order-num { font-weight: 700; }
.po-color-selector { min-height: 38px; border: 1px solid #d0d0d0; border-radius: 6px; padding: 4px 10px; background: #fafafa; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: border-color 0.2s; }
.po-color-selector:hover { border-color: #000; }
.po-color-placeholder { color: #888; font-size: 14px; font-weight: 500; }
.po-selected-chips { display: flex; flex-wrap: wrap; gap: 4px; flex: 1; }
.po-chip { display: flex; align-items: center; gap: 4px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 20px; padding: 2px 7px 2px 4px; font-size: 11px; }
.po-chip-dot { width: 11px; height: 11px; border-radius: 50%; border: 1px solid rgba(0,0,0,.1); flex-shrink: 0; }
.po-chip-name { font-weight: 600; }
.po-chip-remove { background: none; border: none; cursor: pointer; font-size: 14px; color: #999; padding: 0; line-height: 1; }
.po-chip-remove:hover { color: #000; }
.po-chevron { font-size: 11px; color: #888; flex-shrink: 0; }
.po-divider { height: 1px; background: #e5e5e5; margin: 28px 0; }
.po-mid-row { display: grid; grid-template-columns: repeat(3,1fr) 1.1fr; gap: 20px; align-items: start; }
.po-upload-card { border: 1px solid #d0d0d0; border-radius: 10px; padding: 16px; background: #fff; }
.po-upload-title { font-size: 17px; font-weight: 700; margin: 0 0 12px; color: #000; }
.po-dropzone { border: 2px dashed #ccc; border-radius: 8px; padding: 22px 12px; display: flex; flex-direction: column; align-items: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; background: #fafafa; margin-bottom: 12px; }
.po-dropzone:hover { border-color: #000; background: #f5f5f5; }
.po-cloud-icon svg { width: 46px; height: 46px; }
.po-browse-text { font-size: 12px; color: #888; margin: 6px 0 0; }
.po-file-list { display: flex; flex-direction: column; gap: 8px; }
.po-file-item { display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: #f8f8f8; border-radius: 7px; border: 1px solid #ececec; }
.po-file-thumb { width: 36px; height: 36px; border-radius: 6px; overflow: hidden; flex-shrink: 0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #fff; }
.po-thumb-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.po-file-type-icon { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; border-radius: 5px; }
.type-pdf { background: #fff0f0; color: #d32f2f; }
.type-xls { background: #f0fff4; color: #2e7d32; }
.type-doc { background: #e8f0fe; color: #1565c0; }
.type-other { background: #f5f5f5; color: #555; }
.po-file-info { flex: 1; min-width: 0; }
.po-file-name { font-size: 11px; font-weight: 600; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.po-file-size { font-size: 10px; color: #888; display: block; }
.po-progress-bar { height: 3px; background: #e0e0e0; border-radius: 99px; margin-top: 4px; }
.po-progress-fill { height: 100%; background: #000; border-radius: 99px; transition: width 0.2s; }
.po-file-status { flex-shrink: 0; }
.po-pct { font-size: 11px; color: #555; font-weight: 600; }
.po-check { width: 16px; height: 16px; }
.po-file-remove { background: none; border: none; cursor: pointer; font-size: 16px; color: #bbb; line-height: 1; padding: 0; flex-shrink: 0; }
.po-file-remove:hover { color: #000; }
.po-notes-card { border: 1px solid #d0d0d0; border-radius: 10px; padding: 16px; background: #fff; display: flex; flex-direction: column; }
.po-notes-toolbar { display: flex; align-items: center; gap: 2px; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #ececec; }
.po-tb-btn { width: 30px; height: 28px; border: 1px solid transparent; background: none; border-radius: 5px; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; color: #444; transition: all 0.15s; }
.po-tb-btn:hover { background: #f0f0f0; border-color: #ccc; }
.po-tb-btn.active { background: #000; color: #fff; border-color: #000; }
.po-tb-sep { width: 1px; height: 18px; background: #ddd; margin: 0 4px; }
.po-notes-editor { flex: 1; min-height: 230px; border: 1px dashed #ccc; border-radius: 6px; padding: 10px 12px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: #333; outline: none; line-height: 1.7; overflow-y: auto; }
.po-notes-editor:focus { border-color: #000; }
.po-notes-editor:empty::before { content: 'Type your notes here...'; color: #aaa; pointer-events: none; }
.po-submit-row { display: flex; justify-content: center; padding-top: 8px; }
.po-submit-btn { background: #000; color: #fff; padding: 12px 60px; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif; display: flex; align-items: center; gap: 10px; transition: background 0.2s, transform 0.15s; }
.po-submit-btn:hover:not(:disabled) { background: #222; transform: translateY(-1px); }
.po-submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.po-spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.35); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; display: inline-block; }
.po-spinner-dark { width: 28px; height: 28px; border: 3px solid #eee; border-top-color: #000; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.po-color-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.po-color-popup { background: #fff; border-radius: 16px; width: 900px; max-width: 95vw; max-height: 80vh; display: flex; flex-direction: column; box-shadow: 0 24px 64px rgba(0,0,0,.25); overflow: hidden; }
.po-color-popup-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #eee; }
.po-color-popup-header h6 { margin: 0; font-weight: 700; }
.po-popup-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #999; }
.po-popup-close:hover { color: #000; }
.po-color-loading { display: flex; justify-content: center; align-items: center; padding: 40px; }
.po-color-grid { display: grid; grid-template-columns: repeat(8,1fr); gap: 14px; padding: 20px; overflow-y: auto; }
.po-color-item { display: flex; flex-direction: column; align-items: center; cursor: pointer; padding: 7px; border-radius: 10px; border: 2px solid transparent; transition: border-color 0.15s; }
.po-color-item.selected { border-color: #000; }
.po-color-swatch { width: 48px; height: 48px; border-radius: 6px; border: 2px solid rgba(0,0,0,.1); display: flex; align-items: center; justify-content: center; }
.po-swatch-check { width: 20px; height: 20px; filter: drop-shadow(0 0 4px rgba(0,0,0,.5)); }
.po-color-name { font-size: 10px; font-weight: 600; text-align: center; color: #333; margin-top: 5px; }
.po-color-popup-footer { padding: 14px 20px; border-top: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; font-size: 13px; color: #777; }
.po-done-btn { background: #000; color: #fff; border: none; padding: 8px 24px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.po-done-btn:hover { background: #222; }
.po-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.6); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.po-modal { background: #0f0f0f; border-radius: 16px; border: 1px solid #222; padding: 40px 48px; text-align: center; animation: popUp 0.3s ease-out; color: #fff; }
.po-modal-icon { width: 60px; height: 60px; border-radius: 50%; background: #000; border: 2px solid #28a745; color: #28a745; font-size: 32px; font-weight: bold; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.po-modal h4 { color: #fff; margin: 0 0 8px; }
.po-modal p { color: rgba(255,255,255,.7); margin: 0 0 6px; }
.po-modal-order { font-weight: 700; color: #fff !important; font-size: 16px; margin-bottom: 20px !important; }
@keyframes popUp { from { transform: scale(.7); opacity: 0; } to { transform: scale(1); opacity: 1; } }
@media (max-width: 1200px) { .po-top-row { grid-template-columns: repeat(4,1fr); } .po-mid-row { grid-template-columns: 1fr 1fr; } }
@media (max-width: 900px) { .how-to-sidebar { display: none; } .po-container { padding: 20px; } }
@media (max-width: 768px) { .po-top-row { grid-template-columns: 1fr 1fr; } .po-mid-row { grid-template-columns: 1fr; } .po-color-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width: 480px) { .po-top-row { grid-template-columns: 1fr; } }
</style>
