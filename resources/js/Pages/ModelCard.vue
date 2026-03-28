<template>
  <div class="model-card">
    <div class="card-image-wrapper">
      <!-- Color overlay (applied when color is selected) -->
      <div
        v-if="appliedColor"
        class="color-overlay"
        :style="{ background: appliedColor.code }"
      ></div>

      <!-- Cart button -->
      <button
        class="model-cart-btn"
        @click.stop="$emit('cart', model.id)"
        title="View Product"
      >
        <i class="bi bi-cart" style="transform: scaleX(-1); display:inline-block;"></i>
      </button>

      <!-- Image layers -->
      <img
        v-if="model.thumbnail"
        :src="model.thumbnail"
        class="model-thumb"
        draggable="false"
      />
      <template v-else>
        <img v-if="model.front_black" :src="model.front_black" class="img-layer black" />
        <img v-if="model.front_white" :src="model.front_white" class="img-layer white" />
        <img v-if="model.front_svg"   :src="model.front_svg"   class="img-layer svg" draggable="false" />
        <img
          v-if="!model.front_black && !model.front_white && !model.front_svg"
          src="https://via.placeholder.com/300x180?text=No+Image"
          class="img-layer svg"
        />
      </template>
    </div>

    <div class="card-body py-2 px-2">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <h5 class="card-title mb-0">{{ model.title }}</h5>
        <span class="card-price">${{ model.price || '0.00' }}</span>
      </div>
    </div>

    <div class="card-footer model-footer">
      <button
        class="btn btn-custom btn-sm w-100"
        @click="$emit('customize', model.id)"
      >
        Customize
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  model: {
    type: Object,
    required: true
  },
  appliedColor: {
    type: Object,
    default: null
  }
})

defineEmits(['customize', 'cart'])
</script>

<style scoped>
.model-card {
  border-radius: 14px;
  background: #fff;
  overflow: hidden;
  transition: .35s ease;
  border: 1px solid #ebebeb;
  height: 100%;
}
.model-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 14px 28px rgba(0,0,0,.18);
}

.card-image-wrapper {
  height: 200px;
  background: #fff;
  position: relative;
  overflow: hidden;
}

/* Color overlay */
.color-overlay {
  position: absolute;
  inset: 0;
  z-index: 2;
  opacity: 0.4;
  pointer-events: none;
  mix-blend-mode: multiply;
  transition: background .3s ease;
}

.model-cart-btn {
  position: absolute;
  top: 8px; right: 8px;
  width: 32px; height: 32px;
  background: rgba(255,255,255,.9);
  border: 1px solid #e0e0e0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  font-size: 14px;
  transition: .2s;
  color: #333;
}
.model-cart-btn:hover { background: #000; color: #fff; border-color: #000; }

.model-thumb {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  height: 95%; width: 90%;
  object-fit: contain;
  z-index: 1;
}
.img-layer {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  height: 95%; width: 90%;
  object-fit: contain;
}
.black { z-index: 5; mix-blend-mode: screen; }
.white { z-index: 4; mix-blend-mode: multiply; }
.svg   { z-index: 3; }

.card-title  { font-size: 14px; font-weight: 700; }
.card-price  { font-size: 14px; font-weight: 700; white-space: nowrap; }

.model-footer {
  padding: 10px;
  background: #fff;
  border-top: 1px solid #eee;
}
.btn-custom {
  background: #000;
  color: #fff;
  height: 36px;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: .15s;
}
.btn-custom:hover { background: #222; }
</style>
