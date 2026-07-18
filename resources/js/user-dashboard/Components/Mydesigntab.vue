<template>
  <div class="my-design-wrapper">

    <div class="page-header">
      <div>
        <h2 class="title">My Designs</h2>
        <p class="subtitle">
          View, customize, download and manage your saved designs.
        </p>
      </div>

      <div v-if="!loading && designs.length" class="design-count">
        {{ designs.length }} {{ designs.length === 1 ? 'Design' : 'Designs' }}
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="state-box">
      <div class="loader"></div>
      <h3>Loading designs...</h3>
      <p>Please wait while we load your saved designs.</p>
    </div>

    <!-- Empty -->
    <div v-else-if="designs.length === 0" class="state-box">
      <div class="empty-icon">
        <svg
          width="40"
          height="40"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.7"
        >
          <rect x="3" y="3" width="18" height="18" rx="3" />
          <circle cx="8.5" cy="8.5" r="1.5" />
          <path d="M21 15l-5-5L5 21" />
        </svg>
      </div>

      <h3>No designs yet</h3>
      <p>Your saved designs will appear here.</p>
    </div>

    <!-- Designs -->
    <div v-else class="design-grid">
      <div
        v-for="(design, index) in designs"
        :key="design.id"
        class="design-card"
        :class="{ 'new-design-card': index === 0 }"
        draggable="true"
        @dragstart="handleDragStart($event, design)"
      >
        <!-- NEW label only on latest design -->
        <div v-if="index === 0" class="new-badge">
          <span class="new-dot"></span>
          NEW
        </div>

        <!-- Download -->
        <button
          type="button"
          class="download-icon-btn"
          :disabled="downloadingId === design.id"
          title="Download design"
          @click.stop="downloadWithWatermark(design)"
        >
          <svg
            v-if="downloadingId !== design.id"
            viewBox="0 0 24 24"
            width="18"
            height="18"
            aria-hidden="true"
          >
            <path
              d="M12 3v10m0 0l4-4m-4 4l-4-4M5 17v2h14v-2"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>

          <span v-else class="small-spinner"></span>
        </button>

        <!-- Preview -->
        <div
          class="thumb-wrap"
          title="Open design preview"
          @click="openWatermarkedPreview(design)"
        >
          <img
            :src="getImageUrl(design)"
            :alt="design.name || design.model_name || 'Design preview'"
            class="thumb"
            draggable="false"
            @error="handleImageError"
          />

          <div class="preview-overlay">
            <svg
              width="22"
              height="22"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
              <circle cx="12" cy="12" r="3" />
            </svg>

            <span>Preview</span>
          </div>
        </div>

        <!-- Details -->
        <div class="design-info">
          <h4>
            {{ design.name || design.model_name || 'Untitled Design' }}
          </h4>

          <div class="design-meta">
            <span>
              <svg
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <rect x="3" y="4" width="18" height="17" rx="2" />
                <path d="M16 2v4M8 2v4M3 10h18" />
              </svg>

              {{ formatDate(design.created_at) }}
            </span>

            <span v-if="design.model?.model_name">
              {{ design.model.model_name }}
            </span>
          </div>
        </div>

        <!-- Buttons -->
        <div class="btn-row">
          <a
            :href="getCustomizeUrl(design)"
            class="edit-btn"
          >
            <svg
              width="15"
              height="15"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M12 20h9" />
              <path d="M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4z" />
            </svg>

            Customize
          </a>

          <button
            type="button"
            class="delete-btn"
            :disabled="deletingId === design.id"
            @click="deleteDesign(design.id)"
          >
            <svg
              v-if="deletingId !== design.id"
              width="15"
              height="15"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <polyline points="3 6 5 6 21 6" />
              <path d="M19 6l-1 14H6L5 6" />
              <path d="M10 11v6M14 11v6" />
              <path d="M9 6V4h6v2" />
            </svg>

            <span
              v-if="deletingId === design.id"
              class="small-spinner"
            ></span>

            {{ deletingId === design.id ? 'Removing...' : 'Remove' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const designs = ref([])
const loading = ref(true)
const deletingId = ref(null)
const downloadingId = ref(null)

const watermarkBlobCache = new Map()

onMounted(async () => {
  // My Designs tab ko active rakhne ke liye
  localStorage.setItem('dashboard_active_tab', 'my-designs')

  await fetchDesigns()
})

onBeforeUnmount(() => {
  watermarkBlobCache.forEach(blobUrl => {
    if (blobUrl?.startsWith('blob:')) {
      URL.revokeObjectURL(blobUrl)
    }
  })

  watermarkBlobCache.clear()
})

const fetchDesigns = async () => {
  loading.value = true

  try {
    const token = localStorage.getItem('auth_token')

    if (!token) {
      designs.value = []
      return
    }

    const response = await fetch('/api/user/designs', {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`
      }
    })

    if (!response.ok) {
      throw new Error('Unable to load designs.')
    }

    const responseData = await response.json()
    const designList = Array.isArray(responseData)
      ? responseData
      : responseData.data || []

    /*
     * Latest design always first.
     * Isi wajah se sirf sab se naye design par NEW label lagega.
     */
    designs.value = [...designList].sort((first, second) => {
      const firstDate = new Date(
        first.created_at || first.updated_at || 0
      ).getTime()

      const secondDate = new Date(
        second.created_at || second.updated_at || 0
      ).getTime()

      if (secondDate !== firstDate) {
        return secondDate - firstDate
      }

      return Number(second.id || 0) - Number(first.id || 0)
    })
  } catch (error) {
    console.error('Fetch designs error:', error)
    designs.value = []
  } finally {
    loading.value = false
  }
}

const getCsrfToken = () => {
  const match = document.cookie
    .split('; ')
    .find(row => row.startsWith('XSRF-TOKEN='))

  return match ? decodeURIComponent(match.split('=')[1]) : ''
}

const getImageUrl = design => {
  return design?.thumbnail || '/assets/images/placeholder.png'
}

const handleImageError = event => {
  event.target.onerror = null
  event.target.src = '/assets/images/placeholder.png'
}

const getCustomizeUrl = design => {
  const modelId =
    design?.model?.id ||
    design?.model_id ||
    design?.customizable_id ||
    ''

  return `/customize/${modelId}?design_id=${design.id}`
}

const formatDate = dateValue => {
  if (!dateValue) {
    return 'Date not available'
  }

  const date = new Date(dateValue)

  if (Number.isNaN(date.getTime())) {
    return 'Date not available'
  }

  return date.toLocaleDateString('en-US', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const loadImage = src => {
  return new Promise((resolve, reject) => {
    const image = new Image()
    image.crossOrigin = 'anonymous'

    image.onload = () => resolve(image)
    image.onerror = reject
    image.src = src
  })
}

const drawWatermark = (ctx, width, height) => {
  ctx.save()
  ctx.translate(width / 2, height / 2)
  ctx.rotate(-Math.PI / 5)

  const bigFontSize = Math.floor(width * 0.3)
  const smallFontSize = Math.floor(width * 0.1)

  ctx.textAlign = 'center'
  ctx.textBaseline = 'middle'

  ctx.font = `bold ${bigFontSize}px Arial`
  ctx.fillStyle = 'rgba(255,255,255,0.18)'
  ctx.fillText('DRAFT', 0, 0)

  ctx.font = `bold ${smallFontSize}px Arial`
  ctx.fillStyle = 'rgba(255,255,255,0.25)'
  ctx.fillText('NOT PURCHASED', 0, bigFontSize * 0.6)

  ctx.restore()
}

const generateWatermarkedBlobUrl = async design => {
  const cacheKey = `${design.id}-${design.thumbnail || 'placeholder'}`

  if (watermarkBlobCache.has(cacheKey)) {
    return watermarkBlobCache.get(cacheKey)
  }

  const imageUrl = getImageUrl(design)
  const image = await loadImage(imageUrl)

  const canvas = document.createElement('canvas')
  const context = canvas.getContext('2d')

  canvas.width = image.naturalWidth || image.width
  canvas.height = image.naturalHeight || image.height

  context.drawImage(
    image,
    0,
    0,
    canvas.width,
    canvas.height
  )

  // Watermark chahiye ho to is line ko uncomment kar dena
  // drawWatermark(context, canvas.width, canvas.height)

  const blob = await new Promise(resolve => {
    canvas.toBlob(resolve, 'image/png')
  })

  if (!blob) {
    throw new Error('Unable to prepare image.')
  }

  const blobUrl = URL.createObjectURL(blob)
  watermarkBlobCache.set(cacheKey, blobUrl)

  return blobUrl
}

const safeFileName = design => {
  return (
    design.name ||
    design.model_name ||
    `design-${design.id}`
  )
    .replace(/[^a-z0-9]/gi, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '')
    .toLowerCase()
}

const openWatermarkedPreview = async design => {
  try {
    downloadingId.value = design.id

    /*
     * Popup pehle open kar rahe hain.
     * Is se browser popup block nahi karega.
     */
    const previewWindow = window.open('', '_blank')

    if (!previewWindow) {
      alert('Popup blocked. Please allow popups for preview.')
      return
    }

    previewWindow.document.write(`
      <!DOCTYPE html>
      <html>
        <head>
          <title>Design Preview</title>

          <style>
            * {
              box-sizing: border-box;
            }

            body {
              margin: 0;
              padding: 30px;
              background: #111827;
              display: flex;
              justify-content: center;
              align-items: center;
              min-height: 100vh;
              font-family: Arial, sans-serif;
            }

            .preview-loading {
              color: #ffffff;
              font-size: 15px;
            }

            img {
              display: none;
              max-width: 100%;
              max-height: calc(100vh - 60px);
              object-fit: contain;
              border-radius: 12px;
              background: #ffffff;
              box-shadow: 0 20px 50px rgba(0,0,0,.35);
              user-select: none;
              -webkit-user-drag: none;
            }
          </style>
        </head>

        <body>
          <div class="preview-loading">Preparing preview...</div>
        </body>
      </html>
    `)

    previewWindow.document.close()

    const blobUrl = await generateWatermarkedBlobUrl(design)

    const loadingElement =
      previewWindow.document.querySelector('.preview-loading')

    const imageElement =
      previewWindow.document.createElement('img')

    imageElement.src = blobUrl
    imageElement.alt = 'Design Preview'

    imageElement.onload = () => {
      if (loadingElement) {
        loadingElement.remove()
      }

      imageElement.style.display = 'block'
    }

    previewWindow.document.body.appendChild(imageElement)
  } catch (error) {
    console.error('Preview error:', error)
    alert('Could not open design preview.')
  } finally {
    downloadingId.value = null
  }
}

const downloadWithWatermark = async design => {
  try {
    downloadingId.value = design.id

    const imageUrl = getImageUrl(design)

    const response = await fetch(imageUrl)

    if (!response.ok) {
      throw new Error('Download failed.')
    }

    const blob = await response.blob()
    const blobUrl = URL.createObjectURL(blob)

    const link = document.createElement('a')
    link.href = blobUrl
    link.download = `${safeFileName(design)}.png`

    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)

    URL.revokeObjectURL(blobUrl)
  } catch (error) {
    console.error('Download error:', error)

    /*
     * Agar CORS ki wajah se fetch fail ho to normal link open hoga.
     */
    const link = document.createElement('a')
    link.href = getImageUrl(design)
    link.download = `${safeFileName(design)}.png`
    link.target = '_blank'

    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } finally {
    downloadingId.value = null
  }
}

const handleDragStart = (event, design) => {
  const imageUrl = getImageUrl(design)

  event.dataTransfer.effectAllowed = 'copy'
  event.dataTransfer.setData('text/uri-list', imageUrl)
  event.dataTransfer.setData('text/plain', imageUrl)

  const dragImage = new Image()
  dragImage.src = imageUrl

  event.dataTransfer.setDragImage(dragImage, 60, 40)
}

const deleteDesign = async id => {
  const confirmed = window.confirm(
    'Are you sure you want to delete this design?'
  )

  if (!confirmed) {
    return
  }

  deletingId.value = id

  try {
    const token = localStorage.getItem('auth_token')

    if (!token) {
      alert('Please login again.')
      return
    }

    const response = await fetch(`/api/user/designs/${id}`, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
        'X-XSRF-TOKEN': getCsrfToken()
      }
    })

    if (!response.ok) {
      const responseBody = await response
        .json()
        .catch(() => ({}))

      throw new Error(
        responseBody.message ||
        'Delete failed. Please try again.'
      )
    }

    designs.value = designs.value.filter(
      design => Number(design.id) !== Number(id)
    )

    /*
     * Delete ke baad array ka pehla design automatically
     * latest hoga aur NEW label us par aa jayega.
     */
  } catch (error) {
    console.error('Delete error:', error)

    alert(
      error.message ||
      'Something went wrong. Please try again.'
    )
  } finally {
    deletingId.value = null
  }
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.my-design-wrapper {
  min-height: 100%;
  padding: 24px;
  background: #f8fafc;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 24px;
}

.title {
  margin: 0;
  color: #111827;
  font-size: 1.65rem;
  font-weight: 800;
  letter-spacing: -0.03em;
}

.subtitle {
  margin: 6px 0 0;
  color: #6b7280;
  font-size: 0.9rem;
}

.design-count {
  flex-shrink: 0;
  padding: 8px 13px;
  border: 1px solid #e5e7eb;
  border-radius: 999px;
  background: #ffffff;
  color: #374151;
  font-size: 0.8rem;
  font-weight: 700;
}

.state-box {
  min-height: 360px;
  padding: 60px 20px;
  border: 1px dashed #d1d5db;
  border-radius: 16px;
  background: #ffffff;
  color: #6b7280;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.state-box h3 {
  margin: 14px 0 6px;
  color: #111827;
  font-size: 1.05rem;
}

.state-box p {
  margin: 0;
  font-size: 0.88rem;
}

.empty-icon {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: #f3f4f6;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
}

.loader {
  width: 34px;
  height: 34px;
  border: 3px solid #e5e7eb;
  border-top-color: #111827;
  border-radius: 50%;
  animation: spin 0.75s linear infinite;
}

.design-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(235px, 1fr));
  gap: 20px;
}

.design-card {
  position: relative;
  overflow: hidden;
  padding: 14px;
  border: 1px solid #e5e7eb;
  border-radius: 15px;
  background: #ffffff;
  box-shadow: 0 5px 18px rgba(15, 23, 42, 0.06);
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease,
    border-color 0.2s ease;
}

.design-card:hover {
  transform: translateY(-4px);
  border-color: #cbd5e1;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.1);
}

.new-design-card {
  border-color: #111827;
  box-shadow:
    0 0 0 1px #111827,
    0 12px 28px rgba(15, 23, 42, 0.1);
}

.new-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 5;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 10px;
  border-radius: 999px;
  background: #111827;
  color: #ffffff;
  font-size: 0.65rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
}

.new-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #ffffff;
  animation: pulse 1.5s infinite;
}

.download-icon-btn {
  position: absolute;
  top: 11px;
  right: 11px;
  z-index: 5;
  width: 35px;
  height: 35px;
  padding: 0;
  border: none;
  border-radius: 50%;
  background: #ffffff;
  color: #111827;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
  transition:
    background 0.2s,
    color 0.2s,
    transform 0.2s;
}

.download-icon-btn:hover {
  color: #ffffff;
  background: #111827;
  transform: scale(1.06);
}

.download-icon-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.thumb-wrap {
  position: relative;
  height: 170px;
  overflow: hidden;
  margin-bottom: 13px;
  border-radius: 10px;
  background: #f3f4f6;
  cursor: pointer;
}

.thumb {
  width: 100%;
  height: 100%;
  padding: 5px;
  object-fit: contain;
  display: block;
  user-select: none;
  -webkit-user-drag: none;
  transition: transform 0.25s ease;
}

.thumb-wrap:hover .thumb {
  transform: scale(1.035);
}

.preview-overlay {
  position: absolute;
  inset: 0;
  opacity: 0;
  background: rgba(17, 24, 39, 0.62);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  font-size: 0.8rem;
  font-weight: 700;
  transition: opacity 0.2s ease;
}

.thumb-wrap:hover .preview-overlay {
  opacity: 1;
}

.design-info {
  padding: 0 2px;
}

.design-info h4 {
  overflow: hidden;
  margin: 0 0 7px;
  color: #111827;
  font-size: 0.95rem;
  font-weight: 750;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.design-meta {
  min-height: 20px;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
  color: #6b7280;
  font-size: 0.72rem;
}

.design-meta span {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.design-meta span + span::before {
  margin-right: 3px;
  content: "•";
  color: #d1d5db;
}

.btn-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  margin-top: 14px;
}

.edit-btn,
.delete-btn {
  min-height: 38px;
  padding: 9px 10px;
  border: 1px solid #111827;
  border-radius: 8px;
  font-family: inherit;
  font-size: 0.78rem;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition:
    background 0.2s,
    color 0.2s,
    border-color 0.2s;
}

.edit-btn {
  background: #111827;
  color: #ffffff;
}

.edit-btn:hover {
  background: #000000;
  color: #ffffff;
}

.delete-btn {
  background: #ffffff;
  color: #111827;
}

.delete-btn:hover {
  background: #f3f4f6;
}

.delete-btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.small-spinner {
  width: 15px;
  height: 15px;
  border: 2px solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  display: inline-block;
  animation: spin 0.65s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.35;
  }
}

@media (max-width: 767px) {
  .my-design-wrapper {
    padding: 16px;
  }

  .page-header {
    align-items: flex-start;
  }

  .title {
    font-size: 1.4rem;
  }

  .subtitle {
    font-size: 0.8rem;
  }

  .design-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 14px;
  }
}

@media (max-width: 480px) {
  .page-header {
    flex-direction: column;
  }

  .design-grid {
    grid-template-columns: 1fr;
  }

  .thumb-wrap {
    height: 200px;
  }
}
</style>
