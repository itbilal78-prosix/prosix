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
        draggable="true"
        @dragstart="handleDragStart($event, design)"
      >
        <!-- top-right download icon -->
        <button
          class="download-icon-btn"
          :disabled="downloadingId === design.id"
          @click.stop="downloadWithWatermark(design)"
          title="Download watermarked image"
        >
          <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
            <path
              d="M12 3v10m0 0l4-4m-4 4l-4-4M5 17v2h14v-2"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </button>

        <!-- preview -->
        <div
          class="thumb-wrap"
          @click="openWatermarkedPreview(design)"
          title="Open watermarked preview"
        >
          <img
            :src="design.thumbnail || '/assets/images/placeholder.png'"
            class="thumb"
            draggable="false"
            @error="e => e.target.src = '/assets/images/placeholder.png'"
          />
        </div>

        <h4>{{ design.name || design.model_name || 'Design' }}</h4>
        <small>{{ new Date(design.created_at).toLocaleDateString() }}</small>

        <!-- one row -->
        <div class="btn-row">
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

const designs = ref([])
const loading = ref(true)
const deletingId = ref(null)
const downloadingId = ref(null)

// blob url cache for drag/open/download
const watermarkBlobCache = new Map()

onMounted(async () => {
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      loading.value = false
      return
    }

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

const getCsrfToken = () => {
  const match = document.cookie
    .split('; ')
    .find(row => row.startsWith('XSRF-TOKEN='))

  return match ? decodeURIComponent(match.split('=')[1]) : ''
}

const getImageUrl = (design) => {
  return design.thumbnail || '/assets/images/placeholder.png'
}

const loadImage = (src) => {
  return new Promise((resolve, reject) => {
    const img = new Image()
    img.crossOrigin = 'anonymous'
    img.onload = () => resolve(img)
    img.onerror = reject
    img.src = src
  })
}

const drawWatermark = (ctx, width, height) => {
  ctx.save()
  ctx.translate(width / 2, height / 2)
  ctx.rotate(-Math.PI / 5)

  const bigFontSize = Math.floor(width * 0.30)
  const smallFontSize = Math.floor(width * 0.10)

  ctx.textAlign = 'center'
  ctx.textBaseline = 'middle'

  ctx.font = `bold ${bigFontSize}px Arial`
  ctx.fillStyle = 'rgba(255,255,255,0.18)'
  ctx.fillText('DRAFT', 0, 0)

  ctx.font = `bold ${smallFontSize}px Arial`
  ctx.fillStyle = 'rgba(255,255,255,0.25)'
  ctx.fillText('NOT PURCHASED', 0, bigFontSize * 0.60)

  ctx.restore()
}

const generateWatermarkedBlobUrl = async (design) => {
  const cacheKey = `${design.id}-${design.thumbnail || 'placeholder'}`

  if (watermarkBlobCache.has(cacheKey)) {
    return watermarkBlobCache.get(cacheKey)
  }

  const imageUrl = getImageUrl(design)
  const img = await loadImage(imageUrl)

  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')

  canvas.width = img.naturalWidth || img.width
  canvas.height = img.naturalHeight || img.height

  ctx.drawImage(img, 0, 0, canvas.width, canvas.height)
  drawWatermark(ctx, canvas.width, canvas.height)

  const blob = await new Promise((resolve) => {
    canvas.toBlob(resolve, 'image/png')
  })

  if (!blob) {
    throw new Error('Failed to create watermarked blob.')
  }

  const blobUrl = URL.createObjectURL(blob)
  watermarkBlobCache.set(cacheKey, blobUrl)

  return blobUrl
}

const safeFileName = (design) => {
  return (design.name || design.model_name || `design-${design.id}`)
    .replace(/[^a-z0-9]/gi, '-')
    .toLowerCase()
}

const openWatermarkedPreview = async (design) => {
  try {
    downloadingId.value = design.id

    const blobUrl = await generateWatermarkedBlobUrl(design)

    const win = window.open('', '_blank')
    if (!win) {
      alert('Popup blocked. Please allow popups for preview.')
      downloadingId.value = null
      return
    }

    win.document.write(`
      <html>
        <head>
          <title>Watermarked Preview</title>
          <style>
            body {
              margin: 0;
              background: #111;
              display: flex;
              justify-content: center;
              align-items: center;
              min-height: 100vh;
            }
            img {
              max-width: 100%;
              height: auto;
              user-select: none;
              -webkit-user-drag: none;
            }
          </style>
        </head>
        <body>
          <img src="${blobUrl}" alt="Watermarked Preview" />
        </body>
      </html>
    `)
    win.document.close()
  } catch (err) {
    console.error('Preview error:', err)
    alert('Could not open watermarked preview.')
  } finally {
    downloadingId.value = null
  }
}

const downloadWithWatermark = async (design) => {
  try {
    downloadingId.value = design.id

    const blobUrl = await generateWatermarkedBlobUrl(design)

    const link = document.createElement('a')
    link.href = blobUrl
    link.download = `${safeFileName(design)}-draft.png`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } catch (err) {
    console.error('Download error:', err)
    alert('Something went wrong while downloading.')
  } finally {
    downloadingId.value = null
  }
}

// drag card -> pass watermarked image url to browser
const handleDragStart = async (event, design) => {
  try {
    const blobUrl = await generateWatermarkedBlobUrl(design)

    event.dataTransfer.effectAllowed = 'copy'
    event.dataTransfer.setData('text/uri-list', blobUrl)
    event.dataTransfer.setData('text/plain', blobUrl)

    // some browsers ignore custom drag image if not image element, but still okay
    const dragImg = new Image()
    dragImg.src = blobUrl
    event.dataTransfer.setDragImage(dragImg, 60, 40)
  } catch (err) {
    console.error('Drag watermark error:', err)
  }
}

const deleteDesign = async (id) => {
  if (!confirm('Are you sure you want to delete this design?')) return

  deletingId.value = id

  try {
    const token = localStorage.getItem('auth_token')

    const res = await fetch(`/api/user/designs/${id}`, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
        'X-XSRF-TOKEN': getCsrfToken()
      }
    })

    if (res.ok) {
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
.my-design-wrapper {
  padding: 20px;
}

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
  position: relative;
  background: white;
  padding: 15px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
  text-align: center;
  transition: transform 0.2s;
}

.design-card:hover {
  transform: translateY(-3px);
}

.download-icon-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 3;
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 50%;
  background: #111827;
  color: #fff;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
  transition: background 0.2s, transform 0.2s;
}

.download-icon-btn:hover {
  background: #000;
  transform: scale(1.05);
}

.download-icon-btn:disabled {
  background: #6b7280;
  cursor: not-allowed;
  opacity: 0.7;
}

.thumb-wrap {
  cursor: pointer;
  margin-bottom: 10px;
}

.thumb {
  width: 100%;
  height: 140px;
  object-fit: contain;
  background: #f9fafb;
  border-radius: 6px;
  user-select: none;
  -webkit-user-drag: none;
}

h4 {
  margin: 8px 0 4px;
}

.btn-row {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin-top: 12px;
}

.edit-btn,
.delete-btn {
  display: inline-block;
  min-width: 96px;
  padding: 8px 16px;
  background: #000;
  color: #fff;
  border: none;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.edit-btn:hover,
.delete-btn:hover {
  background: #161616;
}

.delete-btn:disabled {
  background: #6b7280;
  cursor: not-allowed;
  opacity: 0.7;
}
</style>
