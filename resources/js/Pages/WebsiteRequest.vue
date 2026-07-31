<template>
  <div class="website-request-page">
    <nav-component />

    <!-- ================= HERO ================= -->
    <section class="request-hero">
      <div class="request-container">
        <div class="hero-content">
          <span class="hero-eyebrow">
            PROFESSIONAL WEB DEVELOPMENT
          </span>

          <h1>
            Get Your Custom Website
            <span>Built by Our Professional Team</span>
          </h1>

          <p>
            We create modern, secure, responsive, and high-performing
            websites for businesses, brands, startups, and entrepreneurs.
          </p>

          <a href="#project-form" class="hero-button">
            START YOUR PROJECT
            <i class="bi bi-arrow-down"></i>
          </a>
        </div>

        <div class="hero-visual">
          <div class="browser-mockup">
            <div class="browser-top">
              <span></span>
              <span></span>
              <span></span>
            </div>

            <div class="browser-content">
              <div class="mock-sidebar"></div>

              <div class="mock-main">
                <div class="mock-heading"></div>
                <div class="mock-line large"></div>
                <div class="mock-line"></div>

                <div class="mock-cards">
                  <div></div>
                  <div></div>
                  <div></div>
                </div>

                <div class="mock-button"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= MAIN CONTENT ================= -->
    <section class="request-section">
      <div class="request-container request-layout">

        <!-- LEFT INFORMATION -->
        <div class="information-side">

          <div class="section-heading">
            <span>YOUR DIGITAL PARTNER</span>

            <h2>
              Everything You Need to Build a Powerful Online Presence
            </h2>

            <p>
              From planning and design to development and launch,
              our team handles the complete website creation process.
            </p>
          </div>

          <!-- WHAT WE DO -->
          <div class="info-card">
            <div class="info-icon">
              <i class="bi bi-display"></i>
            </div>

            <div>
              <h3>What We Do</h3>

              <p>
                We build custom websites tailored to your business,
                audience, goals, and brand identity.
              </p>
            </div>
          </div>

          <!-- PROCESS -->
          <div class="info-card">
            <div class="info-icon">
              <i class="bi bi-gear"></i>
            </div>

            <div class="info-content-full">
              <h3>How It Works</h3>

              <ul class="check-list">
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  You complete the project request form.
                </li>

                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Our team contacts you to discuss your requirements.
                </li>

                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  We plan, design, and develop your website.
                </li>

                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  You review the website and request changes.
                </li>

                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  We launch your website after final approval.
                </li>
              </ul>
            </div>
          </div>

          <!-- BENEFITS -->
          <div class="info-card">
            <div class="info-icon">
              <i class="bi bi-gift"></i>
            </div>

            <div class="info-content-full">
              <h3>What You Get</h3>

              <ul class="check-list">
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Modern and fully responsive design
                </li>

                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Fast loading and SEO-friendly structure
                </li>

                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Secure, reliable, and scalable development
                </li>

                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Easy-to-manage website content
                </li>

                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Support after website launch
                </li>
              </ul>
            </div>
          </div>

          <!-- SERVICES -->
          <div class="services-block">
            <h3>Website Services We Provide</h3>

            <div class="service-tags">
              <span>Business Website</span>
              <span>E-Commerce Store</span>
              <span>Sports Website</span>
              <span>Portfolio Website</span>
              <span>Booking Website</span>
              <span>Custom Web Application</span>
              <span>Landing Page</span>
              <span>Website Redesign</span>
            </div>
          </div>

          <!-- CONTACT -->
          <div class="contact-card">
            <div>
              <span class="contact-small">NEED HELP FIRST?</span>
              <h3>Contact Our Team</h3>
            </div>

            <div class="contact-options">
              <a href="mailto:sales@prosix.com">
                <i class="bi bi-envelope"></i>
                sales@prosix.com
              </a>

              <a href="tel:+19292104402">
                <i class="bi bi-telephone"></i>
                +1 929 210 4402
              </a>
            </div>
          </div>

        </div>

        <!-- RIGHT FORM -->
        <div class="form-side" id="project-form">
          <div class="form-card">

            <div class="form-heading">
              <span>PROJECT REQUEST</span>
              <h2>Tell Us About Your Project</h2>

              <p>
                Complete the form below. Our team will contact you
                to discuss your website requirements.
              </p>
            </div>

            <form @submit.prevent="submitRequest">

              <div class="form-grid">

                <div class="form-group">
                  <label for="full_name">
                    Full Name <span>*</span>
                  </label>

                  <input
                    id="full_name"
                    v-model.trim="form.full_name"
                    type="text"
                    placeholder="Enter your full name"
                    required
                  />
                </div>

                <div class="form-group">
                  <label for="email">
                    Email Address <span>*</span>
                  </label>

                  <input
                    id="email"
                    v-model.trim="form.email"
                    type="email"
                    placeholder="Enter your email address"
                    required
                  />
                </div>

                <div class="form-group">
                  <label for="phone">
                    Phone Number <span>*</span>
                  </label>

                  <input
                    id="phone"
                    v-model.trim="form.phone"
                    type="tel"
                    placeholder="Enter your phone number"
                    required
                  />
                </div>

                <div class="form-group">
                  <label for="company">
                    Business / Company Name
                  </label>

                  <input
                    id="company"
                    v-model.trim="form.company"
                    type="text"
                    placeholder="Enter company name"
                  />
                </div>

                <div class="form-group form-full">
                  <label for="website_type">
                    Website Type <span>*</span>
                  </label>

                  <select
                    id="website_type"
                    v-model="form.website_type"
                    required
                  >
                    <option value="" disabled>
                      Select the website you need
                    </option>

                    <option value="Business Website">
                      Business Website
                    </option>

                    <option value="E-Commerce Store">
                      E-Commerce Store
                    </option>

                    <option value="Sports Website">
                      Sports Website
                    </option>

                    <option value="Portfolio Website">
                      Portfolio Website
                    </option>

                    <option value="Booking Website">
                      Booking Website
                    </option>

                    <option value="Landing Page">
                      Landing Page
                    </option>

                    <option value="Custom Web Application">
                      Custom Web Application
                    </option>

                    <option value="Website Redesign">
                      Website Redesign
                    </option>

                    <option value="Other">
                      Other
                    </option>
                  </select>
                </div>

                <div class="form-group form-full">
                  <label for="project_details">
                    Project Details <span>*</span>
                  </label>

                  <textarea
                    id="project_details"
                    v-model.trim="form.project_details"
                    rows="6"
                    maxlength="2000"
                    placeholder="Tell us about your business, required pages, features, preferred design, and any other project details..."
                    required
                  ></textarea>

                  <small class="character-count">
                    {{ form.project_details.length }}/2000
                  </small>
                </div>

                <div class="form-group">
                  <label for="budget">
                    Budget Range
                  </label>

                  <select
                    id="budget"
                    v-model="form.budget"
                  >
                    <option value="">
                      Select your estimated budget
                    </option>

                    <option value="Under $500">
                      Under $500
                    </option>

                    <option value="$500 - $1,000">
                      $500 – $1,000
                    </option>

                    <option value="$1,000 - $2,500">
                      $1,000 – $2,500
                    </option>

                    <option value="$2,500 - $5,000">
                      $2,500 – $5,000
                    </option>

                    <option value="$5,000+">
                      $5,000+
                    </option>

                    <option value="Need Consultation">
                      Need Consultation
                    </option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="timeline">
                    When Do You Want to Start?
                  </label>

                  <select
                    id="timeline"
                    v-model="form.timeline"
                  >
                    <option value="">
                      Select project timeline
                    </option>

                    <option value="Immediately">
                      Immediately
                    </option>

                    <option value="Within 1 Month">
                      Within 1 Month
                    </option>

                    <option value="Within 2-3 Months">
                      Within 2–3 Months
                    </option>

                    <option value="Flexible">
                      Flexible
                    </option>

                    <option value="Not Sure">
                      Not Sure
                    </option>
                  </select>
                </div>

                <div class="form-group form-full">
                  <label for="current_website">
                    Current Website URL
                  </label>

                  <input
                    id="current_website"
                    v-model.trim="form.current_website"
                    type="url"
                    placeholder="https://yourwebsite.com"
                  />
                </div>

              </div>

              <label class="agreement-box">
                <input
                  v-model="form.agreed"
                  type="checkbox"
                  required
                />

                <span>
                  I agree to be contacted about my website project.
                </span>
              </label>

              <button
                type="submit"
                class="submit-button"
                :disabled="submitting"
              >
                <span v-if="submitting">
                  <span class="button-spinner"></span>
                  SUBMITTING...
                </span>

                <span v-else>
                  SUBMIT PROJECT REQUEST
                  <i class="bi bi-arrow-right"></i>
                </span>
              </button>

              <p class="response-time">
                <i class="bi bi-clock"></i>
                Our team will contact you within 24 business hours.
              </p>

            </form>

            <!-- SUCCESS MESSAGE -->
            <transition name="success-pop">
              <div
                v-if="successMessage"
                class="success-message"
              >
                <div class="success-icon">
                  <i class="bi bi-check-lg"></i>
                </div>

                <div>
                  <h3>Request Submitted Successfully!</h3>

                  <p>
                    Thank you for contacting us. Our team will
                    review your information and contact you soon.
                  </p>
                </div>
              </div>
            </transition>

          </div>
        </div>

      </div>
    </section>

    <footer-component />
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'

const submitting = ref(false)
const successMessage = ref(false)

const initialForm = {
  full_name: '',
  email: '',
  phone: '',
  company: '',
  website_type: '',
  project_details: '',
  budget: '',
  timeline: '',
  current_website: '',
  agreed: false
}

const form = reactive({
  ...initialForm
})

const resetForm = () => {
  Object.assign(form, initialForm)
}

const submitRequest = async () => {
  if (!form.agreed) {
    return
  }

  submitting.value = true
  successMessage.value = false

  try {
    /*
     * Abhi frontend demo hai.
     * Baad mein yahan Laravel API request add hogi:
     *
     * await axios.post('/api/website-requests', form)
     */

    await new Promise(resolve => setTimeout(resolve, 900))

    successMessage.value = true
    resetForm()

    setTimeout(() => {
      successMessage.value = false
    }, 8000)
  } catch (error) {
    console.error('Website request submission failed:', error)
    alert('Request could not be submitted. Please try again.')
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.website-request-page {
  min-height: 100vh;
  background: #f5f5f5;
  color: #111;
}

.request-container {
  width: min(1240px, calc(100% - 40px));
  margin: 0 auto;
}

/* ================= HERO ================= */

.request-hero {
  position: relative;
  overflow: hidden;
  padding: 100px 0;
  background: #050505;
  color: #fff;
}

.request-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  opacity: 0.2;
  background:
    linear-gradient(
      135deg,
      transparent 0 45%,
      rgba(255, 255, 255, 0.06) 45% 50%,
      transparent 50% 100%
    );
  background-size: 38px 38px;
}

.request-hero .request-container {
  position: relative;
  z-index: 2;
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  align-items: center;
  gap: 80px;
}

.hero-eyebrow {
  display: inline-block;
  margin-bottom: 20px;
  color: rgba(255, 255, 255, 0.62);
  font-size: 13px;
  font-weight: 800;
  letter-spacing: 2px;
}

.hero-content h1 {
  max-width: 680px;
  margin: 0;
  color: #fff;
  font-size: clamp(44px, 5.2vw, 76px);
  font-weight: 900;
  line-height: 1.03;
  letter-spacing: -2px;
}

.hero-content h1 span {
  display: block;
  margin-top: 8px;
  color: rgba(255, 255, 255, 0.55);
}

.hero-content p {
  max-width: 650px;
  margin: 28px 0;
  color: rgba(255, 255, 255, 0.67);
  font-size: 17px;
  line-height: 1.8;
}

.hero-button {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  min-height: 52px;
  padding: 0 25px;
  border: 1px solid #fff;
  background: #fff;
  color: #000;
  font-size: 13px;
  font-weight: 900;
  letter-spacing: 0.8px;
  text-decoration: none;
  transition:
    background 0.25s ease,
    color 0.25s ease,
    transform 0.25s ease;
}

.hero-button:hover {
  background: transparent;
  color: #fff;
  transform: translateY(-3px);
}

/* ================= BROWSER MOCKUP ================= */

.browser-mockup {
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.17);
  border-radius: 13px;
  background: #fff;
  box-shadow: 0 35px 80px rgba(0, 0, 0, 0.55);
  transform: rotate(2deg);
}

.browser-top {
  display: flex;
  align-items: center;
  gap: 7px;
  height: 42px;
  padding: 0 16px;
  background: #e8e8e8;
}

.browser-top span {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #111;
  opacity: 0.75;
}

.browser-content {
  display: grid;
  grid-template-columns: 22% 78%;
  min-height: 390px;
}

.mock-sidebar {
  background: #0e0e0e;
}

.mock-main {
  padding: 46px 34px;
}

.mock-heading {
  width: 62%;
  height: 24px;
  margin-bottom: 20px;
  background: #111;
}

.mock-line {
  width: 68%;
  height: 9px;
  margin-bottom: 10px;
  background: #cacaca;
}

.mock-line.large {
  width: 85%;
}

.mock-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 13px;
  margin-top: 42px;
}

.mock-cards div {
  height: 110px;
  border: 1px solid #d7d7d7;
  background: #f2f2f2;
}

.mock-button {
  width: 140px;
  height: 38px;
  margin-top: 30px;
  background: #111;
}

/* ================= MAIN ================= */

.request-section {
  padding: 95px 0;
}

.request-layout {
  display: grid;
  grid-template-columns: 0.92fr 1.08fr;
  align-items: start;
  gap: 70px;
}

.section-heading span,
.form-heading > span {
  display: block;
  margin-bottom: 13px;
  color: #6b6b6b;
  font-size: 12px;
  font-weight: 900;
  letter-spacing: 1.7px;
}

.section-heading h2,
.form-heading h2 {
  margin: 0;
  color: #111;
  font-size: clamp(30px, 3.3vw, 48px);
  font-weight: 900;
  line-height: 1.12;
  letter-spacing: -1px;
}

.section-heading p,
.form-heading p {
  margin: 20px 0 0;
  color: #6c6c6c;
  font-size: 15px;
  line-height: 1.8;
}

/* ================= INFORMATION ================= */

.information-side {
  display: flex;
  flex-direction: column;
  gap: 23px;
}

.info-card {
  display: flex;
  gap: 20px;
  padding: 25px;
  border: 1px solid #dedede;
  background: #fff;
}

.info-icon {
  display: inline-flex;
  flex: 0 0 52px;
  align-items: center;
  justify-content: center;
  width: 52px;
  height: 52px;
  background: #111;
  color: #fff;
}

.info-icon i {
  font-size: 22px;
}

.info-card h3,
.services-block h3,
.contact-card h3 {
  margin: 0 0 9px;
  color: #111;
  font-size: 19px;
  font-weight: 900;
}

.info-card p {
  margin: 0;
  color: #656565;
  font-size: 14px;
  line-height: 1.75;
}

.info-content-full {
  flex: 1;
}

.check-list {
  display: flex;
  flex-direction: column;
  gap: 11px;
  margin: 14px 0 0;
  padding: 0;
  list-style: none;
}

.check-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  color: #444;
  font-size: 14px;
  line-height: 1.5;
}

.check-list i {
  margin-top: 2px;
  color: #111;
}

/* ================= SERVICES ================= */

.services-block {
  padding: 28px;
  background: #111;
  color: #fff;
}

.services-block h3 {
  color: #fff;
}

.service-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 20px;
}

.service-tags span {
  padding: 10px 13px;
  border: 1px solid rgba(255, 255, 255, 0.26);
  color: rgba(255, 255, 255, 0.82);
  font-size: 12px;
}

/* ================= CONTACT CARD ================= */

.contact-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 25px;
  padding: 27px;
  border: 1px solid #d9d9d9;
  background: #fff;
}

.contact-small {
  display: block;
  margin-bottom: 6px;
  color: #777;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: 1.4px;
}

.contact-card h3 {
  margin-bottom: 0;
}

.contact-options {
  display: flex;
  flex-direction: column;
  gap: 9px;
}

.contact-options a {
  display: flex;
  align-items: center;
  gap: 9px;
  color: #111;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
}

/* ================= FORM ================= */

.form-side {
  position: sticky;
  top: 25px;
}

.form-card {
  padding: 42px;
  border: 1px solid #d8d8d8;
  background: #fff;
  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.08);
}

.form-heading {
  margin-bottom: 32px;
}

.form-heading h2 {
  font-size: 36px;
}

.form-heading p {
  margin-top: 13px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 19px;
}

.form-group {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-full {
  grid-column: 1 / -1;
}

.form-group label {
  color: #222;
  font-size: 13px;
  font-weight: 800;
}

.form-group label span {
  color: #d00000;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  border: 1px solid #d6d6d6;
  border-radius: 0;
  outline: none;
  background: #fff;
  color: #111;
  font-family: inherit;
  font-size: 14px;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.form-group input,
.form-group select {
  height: 50px;
  padding: 0 15px;
}

.form-group textarea {
  resize: vertical;
  min-height: 145px;
  padding: 15px;
  line-height: 1.6;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: #111;
  box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.07);
}

.character-count {
  align-self: flex-end;
  color: #929292;
  font-size: 11px;
}

.agreement-box {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin: 25px 0;
  color: #555;
  font-size: 13px;
  line-height: 1.5;
  cursor: pointer;
}

.agreement-box input {
  width: 17px;
  height: 17px;
  margin-top: 1px;
  accent-color: #111;
}

.submit-button {
  width: 100%;
  min-height: 54px;
  border: 1px solid #111;
  background: #111;
  color: #fff;
  font-size: 13px;
  font-weight: 900;
  letter-spacing: 0.7px;
  cursor: pointer;
  transition:
    background 0.25s ease,
    color 0.25s ease,
    transform 0.25s ease;
}

.submit-button > span {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 11px;
}

.submit-button:hover:not(:disabled) {
  background: #fff;
  color: #111;
  transform: translateY(-2px);
}

.submit-button:disabled {
  cursor: not-allowed;
  opacity: 0.65;
}

.button-spinner {
  width: 17px;
  height: 17px;
  border: 2px solid rgba(255, 255, 255, 0.38);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.response-time {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  margin: 15px 0 0;
  color: #858585;
  font-size: 12px;
}

/* ================= SUCCESS ================= */

.success-message {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  margin-top: 25px;
  padding: 20px;
  border: 1px solid #111;
  background: #f5f5f5;
}

.success-icon {
  display: inline-flex;
  flex: 0 0 42px;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  background: #111;
  color: #fff;
}

.success-message h3 {
  margin: 0 0 5px;
  color: #111;
  font-size: 16px;
  font-weight: 900;
}

.success-message p {
  margin: 0;
  color: #666;
  font-size: 13px;
  line-height: 1.6;
}

.success-pop-enter-active,
.success-pop-leave-active {
  transition: all 0.25s ease;
}

.success-pop-enter-from,
.success-pop-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ================= RESPONSIVE ================= */

@media (max-width: 991px) {
  .request-hero {
    padding: 75px 0;
  }

  .request-hero .request-container,
  .request-layout {
    grid-template-columns: 1fr;
  }

  .hero-visual {
    max-width: 720px;
  }

  .form-side {
    position: static;
  }
}

@media (max-width: 767px) {
  .request-container {
    width: min(100% - 28px, 1240px);
  }

  .request-hero {
    padding: 60px 0;
  }

  .hero-content h1 {
    font-size: 43px;
    letter-spacing: -1px;
  }

  .hero-content p {
    font-size: 15px;
  }

  .browser-content {
    min-height: 260px;
  }

  .mock-main {
    padding: 30px 20px;
  }

  .mock-cards {
    gap: 7px;
  }

  .mock-cards div {
    height: 70px;
  }

  .request-section {
    padding: 60px 0;
  }

  .request-layout {
    gap: 45px;
  }

  .form-card {
    padding: 25px 19px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-full {
    grid-column: auto;
  }

  .form-heading h2 {
    font-size: 30px;
  }

  .info-card {
    padding: 20px;
  }

  .contact-card {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>
