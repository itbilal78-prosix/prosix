<template>
  <nav-component />
        <breadcrumb-component />

  <div class="cs-page">
    <div class="cs-wrap">

      <!-- Icon -->
      <div class="cs-icon">
        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="8" y="8" width="48" height="48" rx="6" stroke="#000" stroke-width="2"/>
          <path d="M20 24h24M20 32h24M20 40h16" stroke="#000" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>

      <p class="cs-eyebrow">Stay Tuned</p>
      <h2 class="cs-title">Coming Soon</h2>
      <div class="cs-rule"></div>
      <p class="cs-sub">
        Our order placement system is being set up.<br/>
        Check back soon to place your orders.
      </p>

      <!-- Animated dots -->
      <div class="cs-dots">
        <span></span><span></span><span></span>
      </div>

    </div>
  </div>

  <footer-component />
</template>

<script setup>
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

.cs-page {
  min-height: 80vh;
  background: #f5f3ef;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  font-family: 'DM Sans', sans-serif;
}

.cs-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  text-align: center;
}

.cs-icon {
  width: 72px;
  height: 72px;
  background: #fff;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  margin-bottom: 8px;
}
.cs-icon svg { width: 36px; height: 36px; }

.cs-eyebrow {
  font-size: 11px;
  letter-spacing: 4px;
  text-transform: uppercase;
  color: #555;
  margin: 0;
  font-weight: 500;
}

.cs-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(32px, 5vw, 52px);
  font-weight: 700;
  color: #000;
  margin: 0;
  line-height: 1.1;
}

.cs-rule {
  width: 48px;
  height: 2px;
  background: linear-gradient(90deg, transparent, #000, transparent);
  margin: 4px auto;
}

.cs-sub {
  font-size: 14px;
  color: #777;
  line-height: 1.8;
  margin: 0;
}

.cs-dots {
  display: flex;
  gap: 8px;
  margin-top: 10px;
  align-items: center;
  justify-content: center;
}
.cs-dots span {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #000;
  animation: csDot 1.4s ease-in-out infinite;
}
.cs-dots span:nth-child(1) { animation-delay: 0s; }
.cs-dots span:nth-child(2) { animation-delay: 0.2s; }
.cs-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes csDot {
  0%, 80%, 100% { transform: scale(0.6); opacity: 0.25; }
  40%            { transform: scale(1.2); opacity: 1; }
}
</style>
