<template>
  <div class="empty-page">
    <nav-component />

    <main class="empty-section">
      <!-- Decorative Background -->
      <div class="background-shape shape-one"></div>
      <div class="background-shape shape-two"></div>
      <div class="background-dots"></div>

      <div class="empty-container">
        <!-- Illustration -->
        <div class="empty-illustration">
          <div class="illustration-circle">
            <div class="file-card">
              <div class="file-top">
                <span></span>
                <span></span>
                <span></span>
              </div>

              <div class="file-icon">
                <i class="bi bi-file-earmark"></i>
              </div>

              <div class="file-lines">
                <span class="line-one"></span>
                <span class="line-two"></span>
                <span class="line-three"></span>
              </div>
            </div>

            <div class="small-icon icon-one">
              <i class="bi bi-stars"></i>
            </div>

            <div class="small-icon icon-two">
              <i class="bi bi-plus-lg"></i>
            </div>

            <div class="small-icon icon-three">
              <i class="bi bi-circle-fill"></i>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="empty-content">
          <div class="status-label">
            <span></span>
            PAGE COMING SOON
          </div>

          <h1>This page is currently empty</h1>

          <p>
            We are preparing something useful and exciting for you.
            New content and features will be available here soon.
          </p>

          <div class="actions">
            <RouterLink to="/" class="home-button">
              <i class="bi bi-house-door"></i>
              Back to Home
            </RouterLink>

            <a href="mailto:sales@prosix.com" class="contact-button">
              <i class="bi bi-envelope"></i>
              Contact Us
            </a>
          </div>

          <div class="help-text">
            <i class="bi bi-info-circle"></i>
            Need help? Our team is always available to assist you.
          </div>
        </div>
      </div>
    </main>

    <footer-component />
  </div>
</template>

<script setup>
</script>

<style scoped>
.empty-page {
  min-height: 100vh;
  background: #ffffff;
  color: #111111;
}

.empty-section {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 150px);
  padding: 90px 24px;
  overflow: hidden;
  background:
    radial-gradient(circle at 10% 20%, rgba(0, 0, 0, 0.035), transparent 28%),
    radial-gradient(circle at 90% 80%, rgba(0, 0, 0, 0.03), transparent 30%),
    #ffffff;
}

/* Background Decoration */

.background-dots {
  position: absolute;
  inset: 0;
  opacity: 0.28;
  background-image: radial-gradient(
    rgba(0, 0, 0, 0.16) 1px,
    transparent 1px
  );
  background-size: 28px 28px;
  mask-image: linear-gradient(
    to bottom,
    transparent,
    #000 20%,
    #000 80%,
    transparent
  );
}

.background-shape {
  position: absolute;
  border-radius: 50%;
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.shape-one {
  width: 420px;
  height: 420px;
  top: -210px;
  right: -130px;
  box-shadow:
    0 0 0 50px rgba(0, 0, 0, 0.012),
    0 0 0 100px rgba(0, 0, 0, 0.008);
}

.shape-two {
  width: 340px;
  height: 340px;
  bottom: -190px;
  left: -120px;
  box-shadow:
    0 0 0 45px rgba(0, 0, 0, 0.012),
    0 0 0 90px rgba(0, 0, 0, 0.008);
}

/* Main Container */

.empty-container {
  position: relative;
  z-index: 2;
  display: grid;
  grid-template-columns: 420px 1fr;
  align-items: center;
  gap: 75px;
  width: min(1100px, 100%);
}

/* Illustration */

.empty-illustration {
  display: flex;
  justify-content: center;
  align-items: center;
}

.illustration-circle {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 360px;
  height: 360px;
  border-radius: 50%;
  background: #f7f7f7;
  border: 1px solid #ececec;
}

.illustration-circle::before {
  content: "";
  position: absolute;
  width: 285px;
  height: 285px;
  border-radius: 50%;
  border: 1px dashed rgba(0, 0, 0, 0.12);
  animation: rotateCircle 18s linear infinite;
}

.file-card {
  position: relative;
  z-index: 2;
  width: 210px;
  min-height: 245px;
  padding: 18px;
  border-radius: 22px;
  background: #ffffff;
  border: 1px solid #e8e8e8;
  box-shadow:
    0 30px 70px rgba(0, 0, 0, 0.1),
    0 8px 25px rgba(0, 0, 0, 0.05);
  transform: rotate(-3deg);
  animation: floatCard 4s ease-in-out infinite;
}

.file-top {
  display: flex;
  gap: 6px;
}

.file-top span {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #d8d8d8;
}

.file-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 72px;
  height: 72px;
  margin: 38px auto 26px;
  border-radius: 20px;
  background: #111111;
  color: #ffffff;
  font-size: 34px;
  box-shadow: 0 14px 30px rgba(0, 0, 0, 0.18);
}

.file-lines {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 11px;
}

.file-lines span {
  display: block;
  height: 7px;
  border-radius: 20px;
  background: #e9e9e9;
}

.line-one {
  width: 125px;
}

.line-two {
  width: 95px;
}

.line-three {
  width: 65px;
}

.small-icon {
  position: absolute;
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 14px;
  background: #ffffff;
  border: 1px solid #e6e6e6;
  color: #111111;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.09);
}

.icon-one {
  top: 48px;
  right: 22px;
  width: 50px;
  height: 50px;
  font-size: 20px;
  animation: floatIcon 3.5s ease-in-out infinite;
}

.icon-two {
  left: 25px;
  bottom: 75px;
  width: 44px;
  height: 44px;
  font-size: 16px;
  animation: floatIcon 4s ease-in-out infinite reverse;
}

.icon-three {
  right: 42px;
  bottom: 45px;
  width: 34px;
  height: 34px;
  font-size: 7px;
  animation: floatIcon 3s ease-in-out infinite;
}

/* Content */

.empty-content {
  max-width: 590px;
}

.status-label {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 22px;
  padding: 9px 14px;
  border-radius: 50px;
  background: #f5f5f5;
  border: 1px solid #e9e9e9;
  color: #606060;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 1.5px;
}

.status-label span {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #111111;
  box-shadow: 0 0 0 5px rgba(0, 0, 0, 0.07);
  animation: pulseDot 1.8s ease-in-out infinite;
}

h1 {
  max-width: 570px;
  margin: 0;
  color: #111111;
  font-size: clamp(48px, 6vw, 78px);
  font-weight: 900;
  line-height: 1.02;
  letter-spacing: -3.5px;
}

.empty-content > p {
  max-width: 540px;
  margin: 25px 0 0;
  color: #717171;
  font-size: 16px;
  line-height: 1.8;
}

.actions {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 13px;
  margin-top: 34px;
}

.home-button,
.contact-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  min-height: 54px;
  padding: 0 24px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 800;
  text-decoration: none;
  transition: all 0.3s ease;
}

.home-button {
  border: 1px solid #111111;
  background: #111111;
  color: #ffffff;
  box-shadow: 0 14px 30px rgba(0, 0, 0, 0.14);
}

.home-button:hover {
  background: #ffffff;
  color: #111111;
  transform: translateY(-3px);
  box-shadow: 0 18px 35px rgba(0, 0, 0, 0.11);
}

.contact-button {
  border: 1px solid #dddddd;
  background: #ffffff;
  color: #111111;
}

.contact-button:hover {
  border-color: #111111;
  background: #f7f7f7;
  transform: translateY(-3px);
}

.help-text {
  display: flex;
  align-items: center;
  gap: 9px;
  margin-top: 31px;
  color: #929292;
  font-size: 12px;
}

.help-text i {
  color: #111111;
  font-size: 15px;
}

/* Animations */

@keyframes floatCard {
  0%,
  100% {
    transform: translateY(0) rotate(-3deg);
  }

  50% {
    transform: translateY(-12px) rotate(-1deg);
  }
}

@keyframes floatIcon {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-9px);
  }
}

@keyframes rotateCircle {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

@keyframes pulseDot {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }

  50% {
    opacity: 0.45;
    transform: scale(0.8);
  }
}

/* Tablet */

@media (max-width: 991px) {
  .empty-section {
    padding: 80px 22px;
  }

  .empty-container {
    grid-template-columns: 330px 1fr;
    gap: 45px;
  }

  .illustration-circle {
    width: 310px;
    height: 310px;
  }

  .illustration-circle::before {
    width: 245px;
    height: 245px;
  }

  .file-card {
    width: 180px;
    min-height: 215px;
  }

  .file-icon {
    width: 62px;
    height: 62px;
    margin-top: 28px;
    font-size: 28px;
  }

  h1 {
    font-size: clamp(44px, 6vw, 65px);
  }
}

/* Mobile */

@media (max-width: 767px) {
  .empty-section {
    min-height: calc(100vh - 120px);
    padding: 65px 16px;
  }

  .empty-container {
    grid-template-columns: 1fr;
    gap: 48px;
    text-align: center;
  }

  .empty-illustration {
    order: 1;
  }

  .empty-content {
    order: 2;
    margin: 0 auto;
  }

  .illustration-circle {
    width: 285px;
    height: 285px;
  }

  .illustration-circle::before {
    width: 225px;
    height: 225px;
  }

  .file-card {
    width: 165px;
    min-height: 200px;
    padding: 15px;
  }

  .file-icon {
    width: 58px;
    height: 58px;
    margin: 26px auto 22px;
    border-radius: 17px;
    font-size: 26px;
  }

  h1 {
    font-size: clamp(42px, 13vw, 60px);
    letter-spacing: -2.5px;
  }

  .empty-content > p {
    margin-left: auto;
    margin-right: auto;
    font-size: 14px;
  }

  .actions {
    flex-direction: column;
  }

  .home-button,
  .contact-button {
    width: 100%;
  }

  .help-text {
    justify-content: center;
    line-height: 1.6;
  }
}
</style>


