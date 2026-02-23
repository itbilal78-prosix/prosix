<template>
  <section class="blog-detail py-5">
    <div class="container" v-if="blog">

      <!-- Title -->
      <h1 class="blog-title mb-2">{{ blog.title }}</h1>

      <!-- Meta -->
      <p class="blog-meta mb-4">
        {{ formatDate(blog.created_at) }} · 4 min read
      </p>

      <!-- Featured Image (SMALL) -->
      <div class="text-center mb-5">
        <img
          v-if="blog.image"
          :src="imageUrl(blog.image)"
          class="blog-image-small"
          alt="blog image"
        />
      </div>

      <!-- Description -->
      <p class="blog-description mb-5">
        {{ blog.description }}
      </p>

      <!-- BODY -->
<div class="blog-body mb-5" v-html="formatBlogBody(blog.body)"></div>

      <!-- VIDEO SECTION -->
      <div v-if="blog.video" class="video-section">
        <div class="row align-items-center">

          <!-- LEFT CONTENT -->
          <div class="col-lg-6 mb-4 mb-lg-0">
            <h3 class="mb-3">Why This Matters</h3>
            <p>
              In today’s competitive construction industry, understanding customer
              behavior is critical. Video insights allow businesses to see real-world
              implementation, improving engagement and trust.
            </p>
            <p>
              By combining written content with video demonstrations, companies
              can communicate value more effectively and convert leads faster.
            </p>
            <ul>
              <li>Better engagement</li>
              <li>Higher trust level</li>
              <li>Clear communication</li>
            </ul>
          </div>

          <!-- RIGHT VIDEO BOX -->
          <div class="col-lg-6">
            <div class="video-box">
              <video controls>
                <source :src="imageUrl(blog.video)" type="video/mp4" />
              </video>
            </div>
          </div>

        </div>
      </div>

      <!-- Back Button -->
      <div class="text-center mt-5">
        <button class="btn-back" @click="goBlogs">
          ← Back to Blogs
        </button>
      </div>

    </div>

    <!-- Loader -->
    <div v-else class="text-center py-5">
      <div class="loader"></div>
      <p class="mt-3">Loading blog...</p>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const blog = ref(null)

const fetchBlog = async () => {
  try {
    const res = await axios.get(
      `http://127.0.0.1:8000/api/blogs/${route.params.slug}`
    )
    blog.value = res.data
  } catch (error) {
    console.error('Blog not found')
  }
}

const formatDate = (date) => {
  return new Date(date).toDateString()
}
const formatBlogBody = (body) => {
  if (!body) return ''

  // Replace all <strong>...</strong> with <h3>...</h3>
  let formatted = body.replace(/<strong>(.*?)<\/strong>/g, '<h3>$1</h3>')

  // Optional: ensure h3 text is on one line
  formatted = formatted.replace(/(\r\n|\n|\r)/gm, '')

  return formatted
}

const imageUrl = (path) => {
  return `http://127.0.0.1:8000/storage/${path}`
}

const goBlogs = () => {
  router.push('/blogs')
}

onMounted(() => {
  fetchBlog()
})
</script>

<style scoped>
.blog-detail {
  background: #f8f9fa;
}

/* Title */
.blog-title {
  font-size: 2.2rem;
  font-weight: 800;
}

/* Meta */
.blog-meta {
  color: #777;
  font-size: 0.9rem;
}

/* Small Image */
.blog-image-small {
  width: 65%;
  height: 280px;        /* ✅ max-height HATAO */
  object-fit: cover;   /* ✅ cover ab kaam karega */
  border-radius: 14px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}


/* Description */
.blog-description {
  font-size: 1.1rem;
  color: #444;
  text-align: center;
}

/* Body */ 
.blog-body {
  font-size: 1.05rem;
  line-height: 1.9;
  color: #333;
}

/* Video Section */
.video-section {
  background: #fff;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}

/* Video Box */
.video-box {
  background: #000;
  padding: 12px;
  border-radius: 16px;
}

.video-box video {
  width: 100%;
  border-radius: 12px;
}

/* Button */
.btn-back {
  background: #111;
  color: #fff;
  padding: 12px 32px;
  border-radius: 30px;
  border: none;
  font-weight: 600;
  transition: 0.3s;
}

.btn-back:hover {
  background: #ff3b3b;
}

/* Loader */
.loader {
  width: 45px;
  height: 45px;
  border: 5px solid #ddd;
  border-top: 5px solid #ff3b3b;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: auto;
}
.blog-body h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 20px 0 10px;
  line-height: 1.2; /* single line style */
}

@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}
</style>
