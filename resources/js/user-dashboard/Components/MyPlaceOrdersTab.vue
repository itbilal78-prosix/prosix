<template>
  <div class="po-tab-root">
    <!-- Page Header -->
    <div class="po-tab-header">
      <div>
        <span class="po-eyebrow">Order History</span>
        <h2 class="po-tab-title">Active Place Orders</h2>
        <p class="po-tab-subtitle">
          View pending and in-progress orders below or place a new order.
        </p>
      </div>

  <span class="po-tab-count">
    {{ activeOrders.length }} {{ activeOrders.length === 1 ? 'order' : 'orders' }}
  </span>
</div>

<!-- Compact New Order Card -->
<div class="po-new-order-wrap">
  <div class="po-new-order-card">
    <div class="po-new-order-card-top">
      <div class="po-new-order-icon">
        <svg
          width="21"
          height="21"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
        >
          <path d="M12 5v14" />
          <path d="M5 12h14" />
        </svg>
      </div>

      <div>
        <span class="po-new-order-label">New Order</span>
        <h3>Place a New Order</h3>
      </div>
    </div>

    <p>
      Start a fresh custom order and upload your mockups, roster and quote files.
    </p>

    <a
      href="https://prosix.com/placeorder"
      class="po-new-order-btn"
    >
      Place New Order
      <svg
        width="17"
        height="17"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M5 12h14" />
        <path d="m13 6 6 6-6 6" />
      </svg>
    </a>
  </div>
</div>

<!-- Loading -->
<div v-if="isLoading" class="po-loading">
  <div class="po-spinner-wrap">
    <div class="po-spin"></div>
  </div>

  <p>Loading your orders...</p>
</div>

<!-- Empty State -->
<div v-else-if="activeOrders.length === 0" class="po-empty">
  <div class="po-empty-icon">
    <svg
      viewBox="0 0 64 64"
      fill="none"
      width="64"
      height="64"
    >
      <rect
        x="10"
        y="8"
        width="44"
        height="50"
        rx="4"
        stroke="#d1d5db"
        stroke-width="2.5"
      />

      <path
        d="M20 22h24M20 31h24M20 40h14"
        stroke="#d1d5db"
        stroke-width="2.5"
        stroke-linecap="round"
      />
    </svg>
  </div>

  <h4>No Active Orders</h4>

  <p>
    Completed orders are moved automatically to My Orders.
  </p>

  <a href="https://prosix.com/placeorder" class="po-place-btn">
    Place an Order
  </a>
</div>

<!-- Orders -->
<div v-else class="po-orders-grid">
  <div
    v-for="order in activeOrders"
    :key="order.id"
    class="po-order-card"
  >
    <!-- Order Header -->
    <div class="po-card-header">
      <div class="po-order-num">
        <span class="po-label-sm">Order #</span>

        <span class="po-num-val">
          {{ order.order_number }}
        </span>
      </div>

      <span
        class="po-status-badge"
        :class="'status-' + normalizeStatus(order.status)"
      >
        {{ capitalize(order.status) }}
      </span>
    </div>

    <!-- Order Body -->
    <div class="po-card-body">
      <!-- Information -->
      <div class="po-info-grid">
        <div class="po-info-item">
          <span class="po-info-label">Name</span>
          <span class="po-info-val">
            {{ order.full_name || '—' }}
          </span>
        </div>

        <div class="po-info-item">
          <span class="po-info-label">Email</span>
          <span class="po-info-val po-break-text">
            {{ order.email || '—' }}
          </span>
        </div>

        <div class="po-info-item">
          <span class="po-info-label">Order Date</span>
          <span class="po-info-val">
            {{ order.order_date || '—' }}
          </span>
        </div>

        <div class="po-info-item">
          <span class="po-info-label">Delivery Date</span>
          <span class="po-info-val">
            {{ order.delivery_date || '—' }}
          </span>
        </div>

        <div class="po-info-item">
          <span class="po-info-label">Sales Rep</span>
          <span class="po-info-val">
            {{ order.sales_rep || '—' }}
          </span>
        </div>

        <div class="po-info-item">
          <span class="po-info-label">Team Colors</span>
          <span class="po-info-val">
            {{ order.team_colors || '—' }}
          </span>
        </div>

        <div class="po-info-item">
          <span class="po-info-label">Submitted</span>
          <span class="po-info-val">
            {{ order.created_at || '—' }}
          </span>
        </div>
      </div>

      <!-- Notes -->
      <div v-if="order.notes" class="po-notes-section">
        <span class="po-info-label">Notes</span>

        <div
          class="po-notes-text"
          v-html="order.notes"
        ></div>
      </div>

      <!-- Files Section -->
      <div
        v-if="hasAnyFiles(order)"
        class="po-files-section"
      >
        <div class="po-files-heading">
          <div>
            <h4>Order Files</h4>
            <p>Click an image to open the full-size file.</p>
          </div>
        </div>

        <!-- Mockup Files -->
        <div
          v-if="hasFiles(order.mockup_files)"
          class="po-file-group"
        >
          <div class="po-file-label-row">
            <span class="po-file-icon">
              <svg
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"
                />
                <polyline points="13 2 13 9 20 9" />
              </svg>
            </span>

            <span class="po-file-label">
              Mockups
            </span>

            <span class="po-file-count">
              {{ order.mockup_files.length }}
            </span>
          </div>

          <div class="po-file-gallery">
            <a
              v-for="(file, index) in order.mockup_files"
              :key="'mockup-' + order.id + '-' + index"
              :href="getFileUrl(file, 'mockup')"
              target="_blank"
              rel="noopener noreferrer"
              class="po-file-card"
              :title="getOriginalName(file)"
            >
              <!-- Image Thumbnail -->
              <template v-if="isImage(file)">
                <div class="po-thumbnail">
                  <img
                    :src="getFileUrl(file, 'mockup')"
                    :alt="getOriginalName(file)"
                    loading="lazy"
                    @error="handleImageError"
                  />

                  <div class="po-thumbnail-overlay">
                    <svg
                      width="23"
                      height="23"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <circle cx="11" cy="11" r="8" />
                      <path d="m21 21-4.35-4.35" />
                      <path d="M11 8v6M8 11h6" />
                    </svg>

                    <span>Open Image</span>
                  </div>
                </div>

                <div class="po-file-name">
                  {{ getDisplayName(file) }}
                </div>
              </template>

              <!-- Non Image File -->
              <template v-else>
                <div class="po-document-card">
                  <div class="po-document-icon">
                    <svg
                      width="30"
                      height="30"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <path
                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                      />
                      <polyline points="14 2 14 8 20 8" />
                    </svg>
                  </div>

                  <span class="po-document-extension">
                    {{ getExt(file) }}
                  </span>
                </div>

                <div class="po-file-name">
                  {{ getDisplayName(file) }}
                </div>
              </template>
            </a>
          </div>
        </div>

        <!-- Roster Files -->
        <div
          v-if="hasFiles(order.roster_files)"
          class="po-file-group"
        >
          <div class="po-file-label-row">
            <span class="po-file-icon">
              <svg
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                />
                <circle cx="9" cy="7" r="4" />
                <path
                  d="M23 21v-2a4 4 0 0 0-3-3.87"
                />
                <path
                  d="M16 3.13a4 4 0 0 1 0 7.75"
                />
              </svg>
            </span>

            <span class="po-file-label">
              Roster
            </span>

            <span class="po-file-count">
              {{ order.roster_files.length }}
            </span>
          </div>

          <div class="po-file-gallery">
            <a
              v-for="(file, index) in order.roster_files"
              :key="'roster-' + order.id + '-' + index"
              :href="getFileUrl(file, 'roster')"
              target="_blank"
              rel="noopener noreferrer"
              class="po-file-card"
              :title="getOriginalName(file)"
            >
              <!-- Image Thumbnail -->
              <template v-if="isImage(file)">
                <div class="po-thumbnail">
                  <img
                    :src="getFileUrl(file, 'roster')"
                    :alt="getOriginalName(file)"
                    loading="lazy"
                    @error="handleImageError"
                  />

                  <div class="po-thumbnail-overlay">
                    <svg
                      width="23"
                      height="23"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <circle cx="11" cy="11" r="8" />
                      <path d="m21 21-4.35-4.35" />
                      <path d="M11 8v6M8 11h6" />
                    </svg>

                    <span>Open Image</span>
                  </div>
                </div>

                <div class="po-file-name">
                  {{ getDisplayName(file) }}
                </div>
              </template>

              <!-- Non Image File -->
              <template v-else>
                <div class="po-document-card">
                  <div class="po-document-icon">
                    <svg
                      width="30"
                      height="30"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <path
                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                      />
                      <polyline points="14 2 14 8 20 8" />
                    </svg>
                  </div>

                  <span class="po-document-extension">
                    {{ getExt(file) }}
                  </span>
                </div>

                <div class="po-file-name">
                  {{ getDisplayName(file) }}
                </div>
              </template>
            </a>
          </div>
        </div>

        <!-- Quote Files -->
        <div
          v-if="hasFiles(order.quote_files)"
          class="po-file-group"
        >
          <div class="po-file-label-row">
            <span class="po-file-icon">
              <svg
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
              </svg>
            </span>

            <span class="po-file-label">
              Quote / Invoice
            </span>

            <span class="po-file-count">
              {{ order.quote_files.length }}
            </span>
          </div>

          <div class="po-file-gallery">
            <a
              v-for="(file, index) in order.quote_files"
              :key="'quote-' + order.id + '-' + index"
              :href="getFileUrl(file, 'quote')"
              target="_blank"
              rel="noopener noreferrer"
              class="po-file-card"
              :title="getOriginalName(file)"
            >
              <!-- Image Thumbnail -->
              <template v-if="isImage(file)">
                <div class="po-thumbnail">
                  <img
                    :src="getFileUrl(file, 'quote')"
                    :alt="getOriginalName(file)"
                    loading="lazy"
                    @error="handleImageError"
                  />

                  <div class="po-thumbnail-overlay">
                    <svg
                      width="23"
                      height="23"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <circle cx="11" cy="11" r="8" />
                      <path d="m21 21-4.35-4.35" />
                      <path d="M11 8v6M8 11h6" />
                    </svg>

                    <span>Open Image</span>
                  </div>
                </div>

                <div class="po-file-name">
                  {{ getDisplayName(file) }}
                </div>
              </template>

              <!-- Non Image File -->
              <template v-else>
                <div class="po-document-card">
                  <div class="po-document-icon">
                    <svg
                      width="30"
                      height="30"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <path
                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                      />
                      <polyline points="14 2 14 8 20 8" />
                    </svg>
                  </div>

                  <span class="po-document-extension">
                    {{ getExt(file) }}
                  </span>
                </div>

                <div class="po-file-name">
                  {{ getDisplayName(file) }}
                </div>
              </template>
            </a>
          </div>
        </div>
      </div>

      <!-- No Files -->
      <div v-else class="po-no-files">
        No files attached with this order.
      </div>
    </div>
  </div>
</div>

<!-- Bottom New Order Button -->
<div v-if="!isLoading && activeOrders.length > 0" class="po-bottom-order-action">
  <a href="https://prosix.com/placeorder" class="po-bottom-order-btn">
    <svg
      width="18"
      height="18"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
    >
      <path d="M12 5v14" />
      <path d="M5 12h14" />
    </svg>
    Place Another Order
  </a>
</div>

  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  },

  isLoading: {
    type: Boolean,
    default: false
  }
})

const activeOrders = computed(() => {
  return props.orders.filter(order => {
    return String(order.status || '')
      .trim()
      .toLowerCase() !== 'completed'
  })
})

/*
|--------------------------------------------------------------------------
| File Helpers
|--------------------------------------------------------------------------
*/

const getFileName = (file) => {
  if (!file) return ''

  if (typeof file === 'object') {
    return (
      file.filename ||
      file.file_name ||
      file.name ||
      file.path ||
      ''
    )
  }

  return String(file)
}

const getOriginalName = (file) => {
  if (!file) return 'File'

  if (typeof file === 'object') {
    return (
      file.original ||
      file.original_name ||
      file.name ||
      file.filename ||
      file.file_name ||
      'File'
    )
  }

  return String(file).split('/').pop() || 'File'
}

const getDisplayName = (file) => {
  const name = getOriginalName(file)

  if (name.length <= 24) {
    return name
  }

  const extension = name.includes('.')
    ? '.' + name.split('.').pop()
    : ''

  const cleanName = extension
    ? name.slice(0, -extension.length)
    : name

  return cleanName.slice(0, 17) + '...' + extension
}

const getFileUrl = (file, folder) => {
  if (!file) return '#'

  /*
   * Agar backend complete URL bhej raha ho:
   * https://example.com/uploads/file.png
   */
  if (typeof file === 'object') {
    const directUrl =
      file.url ||
      file.file_url ||
      file.full_url ||
      file.path_url

    if (directUrl) {
      return directUrl
    }
  }

  const filename = getFileName(file)

  if (!filename) return '#'

  /*
   * Agar filename ke andar already full URL ho
   */
  if (
    filename.startsWith('http://') ||
    filename.startsWith('https://')
  ) {
    return filename
  }

  /*
   * Agar path already /uploads se start ho raha ho
   */
  if (filename.startsWith('/uploads/')) {
    return filename
  }

  /*
   * Agar uploads/ se start ho magar slash missing ho
   */
  if (filename.startsWith('uploads/')) {
    return '/' + filename
  }

  /*
   * Normal stored filename
   */
  const cleanFilename = filename.split('/').pop()

  return `/uploads/orders/${folder}/${cleanFilename}`
}

const isImage = (file) => {
  const fileName = getOriginalName(file)

  const extension = fileName
    .split('?')[0]
    .split('.')
    .pop()
    .toLowerCase()

  return [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'webp',
    'bmp',
    'svg'
  ].includes(extension)
}

const getExt = (file) => {
  if (!file) return 'FILE'

  if (
    typeof file === 'object' &&
    file.ext
  ) {
    return String(file.ext)
      .replace('.', '')
      .toUpperCase()
      .slice(0, 5)
  }

  const name = getOriginalName(file)

  if (!name.includes('.')) {
    return 'FILE'
  }

  return name
    .split('?')[0]
    .split('.')
    .pop()
    .toUpperCase()
    .slice(0, 5)
}

const hasFiles = (files) => {
  return Array.isArray(files) && files.length > 0
}

const hasAnyFiles = (order) => {
  return (
    hasFiles(order.mockup_files) ||
    hasFiles(order.roster_files) ||
    hasFiles(order.quote_files)
  )
}

const capitalize = (value) => {
  if (!value) return ''

  const stringValue = String(value)

  return (
    stringValue.charAt(0).toUpperCase() +
    stringValue.slice(1).replaceAll('_', ' ')
  )
}

const normalizeStatus = (status) => {
  return String(status || 'pending')
    .toLowerCase()
    .replaceAll(' ', '-')
    .replaceAll('_', '-')
}

const handleImageError = (event) => {
  event.target.classList.add('po-image-error')
}
</script>

<style scoped>
.po-tab-root {
  padding: 0;
  font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont,
    sans-serif;
}

/* Header */
.po-tab-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 24px;
}

.po-tab-title {
  margin: 0;
  color: #111827;
  font-size: 1.4rem;
  font-weight: 750;
}

.po-tab-count {
  flex-shrink: 0;
  padding: 5px 13px;
  border: 1px solid #e5e7eb;
  border-radius: 99px;
  background: #f9fafb;
  color: #6b7280;
  font-size: 0.8rem;
  font-weight: 650;
}

/* Header supporting text */
.po-eyebrow {
  display: inline-block;
  margin-bottom: 5px;
  color: #6b7280;
  font-size: 0.7rem;
  font-weight: 750;
  letter-spacing: 0.8px;
  text-transform: uppercase;
}

.po-tab-subtitle {
  margin: 6px 0 0;
  color: #6b7280;
  font-size: 0.86rem;
  line-height: 1.5;
}

/* Compact New Order Card */
.po-new-order-wrap {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 22px;
}

.po-new-order-card {
  width: 100%;
  max-width: 390px;
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  background: #ffffff;
  box-shadow: 0 4px 16px rgba(17, 24, 39, 0.06);
}

.po-new-order-card-top {
  display: flex;
  align-items: center;
  gap: 11px;
}

.po-new-order-icon {
  display: inline-flex;
  flex: 0 0 42px;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: #111827;
  color: #ffffff;
}

.po-new-order-label {
  display: block;
  margin-bottom: 2px;
  color: #9ca3af;
  font-size: 0.64rem;
  font-weight: 800;
  letter-spacing: 0.65px;
  text-transform: uppercase;
}

.po-new-order-card h3 {
  margin: 0;
  color: #111827;
  font-size: 0.96rem;
  font-weight: 800;
}

.po-new-order-card > p {
  margin: 13px 0 14px;
  color: #6b7280;
  font-size: 0.78rem;
  line-height: 1.55;
}

.po-new-order-btn,
.po-bottom-order-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 40px;
  padding: 10px 15px;
  border-radius: 9px;
  background: #111827;
  color: #ffffff;
  font-size: 0.79rem;
  font-weight: 750;
  text-decoration: none;
  transition: 0.2s ease;
}

.po-new-order-btn {
  width: 100%;
}

.po-new-order-btn:hover,
.po-bottom-order-btn:hover {
  transform: translateY(-1px);
  background: #000000;
}

.po-bottom-order-action {
  display: flex;
  justify-content: center;
  margin-top: 24px;
}

.po-bottom-order-btn {
  border: 1px solid #111827;
}

/* Loading */
.po-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  min-height: 300px;
  padding: 60px 20px;
  color: #9ca3af;
  font-size: 0.9rem;
}

.po-loading p {
  margin: 0;
}

.po-spinner-wrap {
  display: flex;
  justify-content: center;
}

.po-spin {
  width: 36px;
  height: 36px;
  border: 3px solid #e5e7eb;
  border-top-color: #111827;
  border-radius: 50%;
  animation: po-spin-animation 0.75s linear infinite;
}

@keyframes po-spin-animation {
  to {
    transform: rotate(360deg);
  }
}

/* Empty State */
.po-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  min-height: 350px;
  padding: 60px 20px;
  text-align: center;
}

.po-empty-icon {
  margin-bottom: 8px;
}

.po-empty h4 {
  margin: 0;
  color: #374151;
  font-size: 1.1rem;
  font-weight: 700;
}

.po-empty p {
  max-width: 310px;
  margin: 0;
  color: #9ca3af;
  font-size: 0.88rem;
  line-height: 1.6;
}

.po-place-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-top: 8px;
  padding: 10px 28px;
  border-radius: 8px;
  background: #111827;
  color: #ffffff;
  font-size: 0.875rem;
  font-weight: 650;
  text-decoration: none;
  transition: 0.2s ease;
}

.po-place-btn:hover {
  background: #000000;
  transform: translateY(-1px);
}

/* Orders */
.po-orders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 390px));
  align-items: start;
  justify-content: start;
  gap: 18px;
}

.po-order-card {
  width: 100%;
  overflow: hidden;
  border: 1px solid #e5e7eb;
  border-radius: 15px;
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(17, 24, 39, 0.04);
  transition:
    box-shadow 0.2s ease,
    transform 0.2s ease;
}

.po-order-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 26px rgba(17, 24, 39, 0.08);
}

/* Card Header */
.po-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 16px;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.po-order-num {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.po-label-sm {
  color: #9ca3af;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.55px;
  text-transform: uppercase;
}

.po-num-val {
  color: #111827;
  font-size: 0.97rem;
  font-weight: 750;
}

/* Status */
.po-status-badge {
  flex-shrink: 0;
  padding: 5px 12px;
  border-radius: 99px;
  background: #f3f4f6;
  color: #374151;
  font-size: 0.73rem;
  font-weight: 750;
  text-transform: capitalize;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-processing,
.status-in-progress,
.status-production {
  background: #dbeafe;
  color: #1e40af;
}

.status-completed {
  background: #d1fae5;
  color: #065f46;
}

.status-shipped {
  background: #ede9fe;
  color: #5b21b6;
}

.status-cancelled,
.status-canceled {
  background: #fee2e2;
  color: #991b1b;
}

/* Card Body */
.po-card-body {
  padding: 16px;
}

.po-info-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px 12px;
  margin-bottom: 16px;
}

.po-info-item {
  display: flex;
  min-width: 0;
  flex-direction: column;
  gap: 4px;
}

.po-info-label {
  color: #9ca3af;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.45px;
  text-transform: uppercase;
}

.po-info-val {
  color: #111827;
  font-size: 0.875rem;
  font-weight: 550;
  line-height: 1.45;
}

.po-break-text {
  overflow-wrap: anywhere;
}

/* Notes */
.po-notes-section {
  margin-bottom: 20px;
  padding: 13px 15px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #f9fafb;
}

.po-notes-text {
  margin-top: 6px;
  color: #374151;
  font-size: 0.85rem;
  line-height: 1.65;
  overflow-wrap: anywhere;
}

.po-notes-text :deep(p) {
  margin-top: 0;
  margin-bottom: 8px;
}

.po-notes-text :deep(p:last-child) {
  margin-bottom: 0;
}

/* Main Files Box */
.po-files-section {
  overflow: hidden;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  background: #ffffff;
}

.po-files-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 13px;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.po-files-heading h4 {
  margin: 0;
  color: #111827;
  font-size: 0.9rem;
  font-weight: 750;
}

.po-files-heading p {
  margin: 3px 0 0;
  color: #9ca3af;
  font-size: 0.72rem;
}

/* File Group */
.po-file-group {
  padding: 13px;
  border-bottom: 1px solid #e5e7eb;
}

.po-file-group:last-child {
  border-bottom: 0;
}

.po-file-label-row {
  display: flex;
  align-items: center;
  gap: 7px;
  margin-bottom: 11px;
}

.po-file-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #4b5563;
}

.po-file-label {
  color: #374151;
  font-size: 0.76rem;
  font-weight: 750;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.po-file-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 21px;
  height: 21px;
  padding: 0 6px;
  border-radius: 99px;
  background: #f3f4f6;
  color: #6b7280;
  font-size: 0.66rem;
  font-weight: 750;
}

/* File Gallery */
.po-file-gallery {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.po-file-card {
  min-width: 0;
  color: inherit;
  text-decoration: none;
}

/* Image Thumbnail */
.po-thumbnail {
  position: relative;
  width: 100%;
  height: 112px;
  overflow: hidden;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #f3f4f6;
}

.po-thumbnail img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition:
    transform 0.25s ease,
    opacity 0.25s ease;
}

.po-file-card:hover .po-thumbnail img {
  transform: scale(1.055);
}

.po-thumbnail-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  visibility: hidden;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 5px;
  background: rgba(17, 24, 39, 0.7);
  color: #ffffff;
  opacity: 0;
  transition: 0.2s ease;
}

.po-thumbnail-overlay span {
  font-size: 0.68rem;
  font-weight: 700;
}

.po-file-card:hover .po-thumbnail-overlay {
  visibility: visible;
  opacity: 1;
}

.po-image-error {
  opacity: 0;
}

/* File Name */
.po-file-name {
  overflow: hidden;
  margin-top: 7px;
  color: #4b5563;
  font-size: 0.7rem;
  font-weight: 600;
  line-height: 1.35;
  text-align: center;
  text-overflow: ellipsis;
  white-space: nowrap;
  transition: color 0.2s ease;
}

.po-file-card:hover .po-file-name {
  color: #111827;
}

/* Other File Card */
.po-document-card {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 7px;
  width: 100%;
  height: 112px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #f9fafb;
  color: #6b7280;
  transition:
    border-color 0.2s ease,
    background 0.2s ease,
    color 0.2s ease,
    transform 0.2s ease;
}

.po-file-card:hover .po-document-card {
  transform: translateY(-2px);
  border-color: #111827;
  background: #111827;
  color: #ffffff;
}

.po-document-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.po-document-extension {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 43px;
  padding: 3px 8px;
  border-radius: 5px;
  background: #e5e7eb;
  color: #374151;
  font-size: 0.66rem;
  font-weight: 800;
  letter-spacing: 0.4px;
}

.po-file-card:hover .po-document-extension {
  background: #ffffff;
  color: #111827;
}

/* No Files */
.po-no-files {
  padding: 13px 15px;
  border: 1px dashed #d1d5db;
  border-radius: 9px;
  background: #f9fafb;
  color: #9ca3af;
  font-size: 0.8rem;
  text-align: center;
}

/* Tablet */
@media (max-width: 900px) {
  .po-orders-grid {
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
  }

  .po-file-gallery {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

/* Mobile */
@media (max-width: 560px) {
  .po-tab-header {
    align-items: flex-start;
  }

  .po-tab-title {
    font-size: 1.2rem;
  }

  .po-new-order-card {
    max-width: none;
  }

  .po-orders-grid {
    grid-template-columns: 1fr;
  }

  .po-card-header {
    padding: 13px 15px;
  }

  .po-card-body {
    padding: 15px;
  }

  .po-info-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 15px 12px;
  }

  .po-files-heading {
    padding: 12px 13px;
  }

  .po-file-group {
    padding: 13px;
  }

  .po-file-gallery {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .po-thumbnail,
  .po-document-card {
    height: 105px;
  }
}

/* Very Small Mobile */
@media (max-width: 360px) {
  .po-info-grid {
    grid-template-columns: 1fr;
  }

  .po-file-gallery {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
