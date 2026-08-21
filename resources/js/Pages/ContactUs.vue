<template>
  <div class="contact-page">

    <!-- ===============================
         GLOBAL HEADER / NAVBAR
    ================================ -->
    <nav-component />

    <breadcrumb-component />

    <!-- ===============================
         CONTACT HERO
    ================================ -->
    <section class="contact-hero">
      <div class="contact-container">

        <div class="hero-grid">

          <div class="hero-content">

            <div class="hero-tag">
              <span class="hero-dot"></span>
              WE'RE HERE TO HELP
            </div>

            <h1>
              Let's Start a
              <span>Conversation.</span>
            </h1>

            <p>
              Have a question about custom uniforms, team orders,
              artwork or anything else? Send us a message and our
              team will get back to you.
            </p>

          </div>


          <div class="hero-side">

            <div class="hero-number">
              01
            </div>

            <div class="hero-side-text">
              CONTACT
              <br>
              PROSIX SPORTS
            </div>

          </div>

        </div>

      </div>
    </section>


    <!-- ===============================
         MAIN CONTACT SECTION
    ================================ -->
    <section class="contact-section">

      <div class="contact-container">

        <div class="contact-grid">

          <!-- ===========================
               LEFT CONTACT INFO
          ============================ -->
          <div class="contact-info">

            <div class="section-kicker">
              CONTACT INFORMATION
            </div>

            <h2 class="info-heading">
              Need more information?
              <br>
              Get in touch with us.
            </h2>

            <p class="info-description">
              Whether you need help with an existing order, want to
              discuss custom team uniforms, or simply have a question,
              our team is ready to help.
            </p>


            <div class="contact-items">

              <!-- PHONE -->
              <div class="info-item">

                <div class="info-icon">

                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M22 16.92v3a2 2 0 0 1-2.18 2
                      19.79 19.79 0 0 1-8.63-3.07
                      19.5 19.5 0 0 1-6-6
                      19.79 19.79 0 0 1-3.07-8.67
                      A2 2 0 0 1 4.11 2h3
                      a2 2 0 0 1 2 1.72
                      c.12.9.33 1.78.62 2.63
                      a2 2 0 0 1-.45 2.11L8 9.73
                      a16 16 0 0 0 6 6l1.27-1.27
                      a2 2 0 0 1 2.11-.45
                      c.85.29 1.73.5 2.63.62
                      A2 2 0 0 1 22 16.92z"
                    />
                  </svg>

                </div>


                <div class="info-content">

                  <span class="info-label">
                    Phone Number
                  </span>

                  <a
                    v-if="contact.phone"
                    :href="`tel:${contact.phone}`"
                    class="info-value"
                  >
                    {{ contact.phone }}
                  </a>

                  <span
                    v-else-if="contactLoading"
                    class="info-loading"
                  >
                    Loading...
                  </span>

                  <span
                    v-else
                    class="info-empty"
                  >
                    Not available
                  </span>

                </div>

              </div>


              <!-- EMAIL -->
              <div class="info-item">

                <div class="info-icon">

                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <rect
                      x="3"
                      y="5"
                      width="18"
                      height="14"
                      rx="2"
                    />

                    <path d="m3 7 9 6 9-6" />
                  </svg>

                </div>


                <div class="info-content">

                  <span class="info-label">
                    Email Address
                  </span>

                  <a
                    v-if="contact.email"
                    :href="`mailto:${contact.email}`"
                    class="info-value"
                  >
                    {{ contact.email }}
                  </a>

                  <span
                    v-else-if="contactLoading"
                    class="info-loading"
                  >
                    Loading...
                  </span>

                  <span
                    v-else
                    class="info-empty"
                  >
                    Not available
                  </span>

                </div>

              </div>


              <!-- ADDRESS -->
              <div class="info-item">

                <div class="info-icon">

                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M21 10c0 7-9 12-9 12S3 17 3 10
                      a9 9 0 1 1 18 0z"
                    />

                    <circle
                      cx="12"
                      cy="10"
                      r="3"
                    />
                  </svg>

                </div>


                <div class="info-content">

                  <span class="info-label">
                    Our Location
                  </span>

                  <span
                    v-if="contact.address"
                    class="info-value address-value"
                  >
                    {{ contact.address }}
                  </span>

                  <span
                    v-else-if="contactLoading"
                    class="info-loading"
                  >
                    Loading...
                  </span>

                  <span
                    v-else
                    class="info-empty"
                  >
                    Not available
                  </span>

                </div>

              </div>

            </div>


            <!-- EXTRA INFO BOX -->
            <div class="response-box">

              <div class="response-icon">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                >
                  <circle cx="12" cy="12" r="9" />
                  <path d="M12 7v5l3 2" />
                </svg>
              </div>

              <div>
                <strong>Quick Response</strong>

                <p>
                  Our team usually responds to inquiries as soon as possible.
                </p>
              </div>

            </div>

          </div>


          <!-- ===========================
               RIGHT FORM
          ============================ -->
          <div class="contact-form-card">

            <div class="form-top">

              <div>

                <div class="section-kicker">
                  SEND A MESSAGE
                </div>

                <h2>
                  How can we help?
                </h2>

                <p>
                  Fill out the form and tell us what you need.
                  We'll get back to you shortly.
                </p>

              </div>


              <div class="form-mail-icon">

                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                >
                  <rect
                    x="3"
                    y="5"
                    width="18"
                    height="14"
                    rx="2"
                  />

                  <path d="m3 7 9 6 9-6" />
                </svg>

              </div>

            </div>


            <form
              class="contact-form"
              @submit.prevent="submitForm"
            >

              <!-- NAME -->
              <div class="form-row">

                <div class="field-group">

                  <label>
                    First Name
                    <span>*</span>
                  </label>

                  <input
                    v-model="form.first_name"
                    type="text"
                    placeholder="Enter your first name"
                    :class="{ 'input-error': errors.first_name }"
                  >

                  <div
                    v-if="errors.first_name"
                    class="field-error"
                  >
                    {{ errors.first_name[0] }}
                  </div>

                </div>


                <div class="field-group">

                  <label>
                    Last Name
                  </label>

                  <input
                    v-model="form.last_name"
                    type="text"
                    placeholder="Enter your last name"
                  >

                </div>

              </div>


              <!-- CONTACT -->
              <div class="form-row">

                <div class="field-group">

                  <label>
                    Email Address
                  </label>

                  <input
                    v-model="form.email"
                    type="email"
                    placeholder="example@email.com"
                    :class="{ 'input-error': errors.email }"
                  >

                  <div
                    v-if="errors.email"
                    class="field-error"
                  >
                    {{ errors.email[0] }}
                  </div>

                </div>


                <div class="field-group">

                  <label>
                    Phone Number
                  </label>

                  <input
                    v-model="form.phone"
                    type="text"
                    placeholder="+1 000 000 0000"
                    :class="{ 'input-error': errors.phone }"
                  >

                  <div
                    v-if="errors.phone"
                    class="field-error"
                  >
                    {{ errors.phone[0] }}
                  </div>

                </div>

              </div>


              <!-- MESSAGE -->
              <div class="field-group">

                <label>
                  Your Message
                  <span>*</span>
                </label>

                <textarea
                  v-model="form.message"
                  rows="7"
                  placeholder="Tell us how we can help you..."
                  :class="{ 'input-error': errors.message }"
                ></textarea>

                <div class="textarea-footer">

                  <div
                    v-if="errors.message"
                    class="field-error"
                  >
                    {{ errors.message[0] }}
                  </div>

                  <span class="char-count">
                    {{ form.message.length }} / 5000
                  </span>

                </div>

              </div>


              <!-- SUCCESS -->
              <div
                v-if="successMessage"
                class="alert-box success-box"
              >

                <div class="alert-icon">

                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <circle cx="12" cy="12" r="9" />
                    <path d="m8 12 2.5 2.5L16 9" />
                  </svg>

                </div>

                <span>
                  {{ successMessage }}
                </span>

              </div>


              <!-- ERROR -->
              <div
                v-if="errorMessage"
                class="alert-box error-box"
              >

                <div class="alert-icon">

                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 8v5" />
                    <path d="M12 16h.01" />
                  </svg>

                </div>

                <span>
                  {{ errorMessage }}
                </span>

              </div>


              <!-- BUTTON -->
              <div class="form-action">

                <button
                  type="submit"
                  class="submit-btn"
                  :disabled="submitting"
                >

                  <span
                    v-if="submitting"
                    class="button-loader"
                  ></span>


                  <span>
                    {{ submitting ? 'Sending Message...' : 'Send Message' }}
                  </span>


                  <svg
                    v-if="!submitting"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path d="M5 12h14" />
                    <path d="m13 6 6 6-6 6" />
                  </svg>

                </button>


                <p>
                  By submitting this form you agree to be contacted
                  regarding your inquiry.
                </p>

              </div>

            </form>

          </div>

        </div>

      </div>

    </section>


    <!-- ===============================
         BOTTOM CTA
    ================================ -->
    <section class="contact-bottom">

      <div class="contact-container">

        <div class="bottom-inner">

          <div>

            <span class="bottom-small">
              READY TO GET STARTED?
            </span>

            <h2>
              Let's create something
              <span>great together.</span>
            </h2>

          </div>


          <router-link
            to="/placeorder"
            class="bottom-button"
          >
            Place an Order

            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M5 12h14" />
              <path d="m13 6 6 6-6 6" />
            </svg>

          </router-link>

        </div>

      </div>

    </section>


    <!-- ===============================
         GLOBAL FOOTER
    ================================ -->
    <footer-component />

  </div>
</template>


<script setup>

import {
  onMounted,
  reactive,
  ref
} from 'vue'

import axios from 'axios'


/* =========================================================
   CONTACT INFO
========================================================= */

const contact = reactive({
  phone: '',
  email: '',
  address: ''
})

const contactLoading = ref(true)


/* =========================================================
   FORM
========================================================= */

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  message: ''
})

const errors = ref({})

const submitting = ref(false)

const successMessage = ref('')

const errorMessage = ref('')


/* =========================================================
   LOAD CONTACT INFORMATION
========================================================= */

const getContactInfo = async () => {

  contactLoading.value = true

  try {

    const response =
      await axios.get('/api/contact-info')

    if (response.data?.success) {

      contact.phone =
        response.data.contact?.phone || ''

      contact.email =
        response.data.contact?.email || ''

      contact.address =
        response.data.contact?.address || ''

    }

  } catch (error) {

    console.error(
      'Contact information error:',
      error
    )

  } finally {

    contactLoading.value = false

  }

}


/* =========================================================
   SUBMIT CONTACT FORM
========================================================= */

const submitForm = async () => {

  if (submitting.value) {
    return
  }

  submitting.value = true

  errors.value = {}

  successMessage.value = ''

  errorMessage.value = ''


  try {

    const response =
      await axios.post(
        '/api/contact-message',
        {
          first_name:
            form.first_name,

          last_name:
            form.last_name,

          email:
            form.email,

          phone:
            form.phone,

          message:
            form.message
        }
      )


    if (response.data?.success) {

      successMessage.value =
        response.data.message ||
        'Your message has been sent successfully.'


      form.first_name = ''

      form.last_name = ''

      form.email = ''

      form.phone = ''

      form.message = ''

    }

  } catch (error) {

    console.error(
      'Contact form error:',
      error
    )


    if (
      error.response?.status === 422
    ) {

      errors.value =
        error.response?.data?.errors || {}


      errorMessage.value =
        error.response?.data?.message ||
        'Please check the information entered above.'

    } else {

      errorMessage.value =
        error.response?.data?.message ||
        'Something went wrong. Please try again.'

    }

  } finally {

    submitting.value = false

  }

}


/* =========================================================
   INIT
========================================================= */

onMounted(() => {

  getContactInfo()

})

</script>


<style scoped>

@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap');


/* =========================================================
   RESET
========================================================= */

* {
  box-sizing: border-box;
}


.contact-page {

  width: 100%;

  overflow-x: hidden;

  background: #ffffff;

  color: #0a0a0a;

  font-family:
    'DM Sans',
    Arial,
    sans-serif;

}


.contact-container {

  width:
    min(
      1240px,
      calc(100% - 60px)
    );

  margin: 0 auto;

}


/* =========================================================
   HERO
========================================================= */

.contact-hero {

  position: relative;

  overflow: hidden;

  background: #080808;

  padding:
    90px
    0
    100px;

  color: #ffffff;

}


.contact-hero::before {

  content: '';

  position: absolute;

  width: 500px;

  height: 500px;

  right: -180px;

  top: -270px;

  border:

    1px solid
    rgba(
      255,
      255,
      255,
      0.07
    );

  border-radius: 50%;

}


.contact-hero::after {

  content: '';

  position: absolute;

  width: 340px;

  height: 340px;

  right: -40px;

  top: -190px;

  border:

    1px solid
    rgba(
      255,
      255,
      255,
      0.05
    );

  border-radius: 50%;

}


.hero-grid {

  position: relative;

  z-index: 2;

  display: grid;

  grid-template-columns:
    minmax(0, 1fr)
    auto;

  gap: 80px;

  align-items: flex-end;

}


.hero-tag {

  display: inline-flex;

  align-items: center;

  gap: 10px;

  margin-bottom: 24px;

  font-size: 14px;

  font-weight: 700;

  letter-spacing: 2px;

}


.hero-dot {

  width: 8px;

  height: 8px;

  background: #ffffff;

  border-radius: 50%;

}


.hero-content h1 {

  max-width: 800px;

  margin:
    0
    0
    24px;

  font-family:
    'Syne',
    sans-serif;

  font-size:
    clamp(
      52px,
      6vw,
      82px
    );

  line-height: 0.98;

  font-weight: 800;

  letter-spacing: -4px;

}


.hero-content h1 span {

  display: block;

  color:
    rgba(
      255,
      255,
      255,
      0.48
    );

}


.hero-content p {

  max-width: 680px;

  margin: 0;

  color:
    rgba(
      255,
      255,
      255,
      0.65
    );

  font-size: 18px;

  line-height: 1.75;

}


.hero-side {

  display: flex;

  align-items: flex-end;

  gap: 18px;

  padding-bottom: 8px;

}


.hero-number {

  font-family:
    'Syne',
    sans-serif;

  font-size: 64px;

  line-height: 1;

  font-weight: 800;

  color:
    rgba(
      255,
      255,
      255,
      0.14
    );

}


.hero-side-text {

  color:
    rgba(
      255,
      255,
      255,
      0.6
    );

  font-size: 12px;

  font-weight: 700;

  letter-spacing: 2px;

  line-height: 1.7;

}


/* =========================================================
   CONTACT SECTION
========================================================= */

.contact-section {

  padding:
    100px
    0
    110px;

  background: #f7f7f5;

}


.contact-grid {

  display: grid;

  grid-template-columns:
    minmax(0, 0.82fr)
    minmax(0, 1.18fr);

  gap: 90px;

  align-items: start;

}


/* =========================================================
   SECTION TEXT
========================================================= */

.section-kicker {

  margin-bottom: 16px;

  color: #777;

  font-size: 13px;

  font-weight: 700;

  letter-spacing: 2px;

}


.info-heading {

  margin:
    0
    0
    22px;

  font-family:
    'Syne',
    sans-serif;

  font-size: 42px;

  line-height: 1.08;

  font-weight: 800;

  letter-spacing: -1.8px;

}


.info-description {

  max-width: 520px;

  margin:
    0
    0
    45px;

  color: #696969;

  font-size: 17px;

  line-height: 1.8;

}


/* =========================================================
   CONTACT ITEMS
========================================================= */

.contact-items {

  display: flex;

  flex-direction: column;

  gap: 8px;

}


.info-item {

  display: flex;

  align-items: center;

  gap: 20px;

  padding:
    19px
    0;

  border-bottom:
    1px solid
    #deded9;

}


.info-icon {

  width: 60px;

  height: 60px;

  flex-shrink: 0;

  display: flex;

  align-items: center;

  justify-content: center;

  border:

    1px solid
    #deded9;

  border-radius: 50%;

  background: #ffffff;

}


.info-icon svg {

  width: 24px;

  height: 24px;

}


.info-content {

  min-width: 0;

  display: flex;

  flex-direction: column;

  gap: 5px;

}


.info-label {

  color: #777;

  font-size: 13px;

  font-weight: 600;

  text-transform: uppercase;

  letter-spacing: 1px;

}


.info-value {

  color: #111;

  font-size: 18px;

  font-weight: 600;

  line-height: 1.45;

  text-decoration: none;

  transition: opacity 0.2s ease;

}


.info-value:hover {
  opacity: 0.55;
}


.address-value {

  max-width: 360px;

}


.info-loading,
.info-empty {

  color: #999;

  font-size: 16px;

}


/* =========================================================
   RESPONSE BOX
========================================================= */

.response-box {

  display: flex;

  gap: 16px;

  margin-top: 38px;

  padding: 22px;

  border:
    1px solid
    #dfdfdb;

  border-radius: 14px;

  background: #ffffff;

}


.response-icon {

  width: 42px;

  height: 42px;

  flex-shrink: 0;

  display: flex;

  align-items: center;

  justify-content: center;

  background: #111;

  color: #fff;

  border-radius: 10px;

}


.response-icon svg {

  width: 20px;

  height: 20px;

}


.response-box strong {

  display: block;

  margin-bottom: 4px;

  font-size: 16px;

}


.response-box p {

  margin: 0;

  color: #777;

  font-size: 14px;

  line-height: 1.6;

}


/* =========================================================
   FORM CARD
========================================================= */

.contact-form-card {

  padding: 42px;

  border:

    1px solid
    #deded9;

  border-radius: 20px;

  background: #ffffff;

}


.form-top {

  display: flex;

  justify-content: space-between;

  align-items: flex-start;

  gap: 30px;

  padding-bottom: 30px;

  margin-bottom: 32px;

  border-bottom:
    1px solid
    #ebebe7;

}


.form-top h2 {

  margin:
    0
    0
    10px;

  font-family:
    'Syne',
    sans-serif;

  font-size: 38px;

  font-weight: 800;

  letter-spacing: -1.5px;

}


.form-top p {

  max-width: 500px;

  margin: 0;

  color: #777;

  font-size: 16px;

  line-height: 1.7;

}


.form-mail-icon {

  width: 58px;

  height: 58px;

  flex-shrink: 0;

  display: flex;

  align-items: center;

  justify-content: center;

  border-radius: 50%;

  background: #111;

  color: #ffffff;

}


.form-mail-icon svg {

  width: 24px;

  height: 24px;

}


/* =========================================================
   FORM
========================================================= */

.contact-form {

  width: 100%;

}


.form-row {

  display: grid;

  grid-template-columns:
    repeat(
      2,
      minmax(0, 1fr)
    );

  gap: 18px;

}


.field-group {

  margin-bottom: 22px;

}


.field-group label {

  display: block;

  margin-bottom: 9px;

  color: #222;

  font-size: 15px;

  font-weight: 600;

}


.field-group label span {

  color: #c90000;

}


.field-group input,
.field-group textarea {

  width: 100%;

  border:
    1px solid
    #d8d8d3;

  border-radius: 10px;

  outline: none;

  background: #fafaf8;

  color: #111;

  font-family:
    'DM Sans',
    sans-serif;

  font-size: 16px;

  transition:
    border-color 0.2s ease,
    background 0.2s ease,
    box-shadow 0.2s ease;

}


.field-group input {

  height: 58px;

  padding:
    0
    17px;

}


.field-group textarea {

  min-height: 190px;

  padding: 17px;

  resize: vertical;

  line-height: 1.6;

}


.field-group input::placeholder,
.field-group textarea::placeholder {

  color: #aaa;

}


.field-group input:focus,
.field-group textarea:focus {

  border-color: #111;

  background: #fff;

  box-shadow:
    0
    0
    0
    3px
    rgba(
      0,
      0,
      0,
      0.04
    );

}


.field-group .input-error {

  border-color: #d94242;

}


.field-error {

  margin-top: 7px;

  color: #cf2424;

  font-size: 13px;

}


.textarea-footer {

  display: flex;

  align-items: flex-start;

  justify-content: space-between;

  gap: 15px;

}


.char-count {

  margin-left: auto;

  margin-top: 7px;

  color: #aaa;

  font-size: 12px;

}


/* =========================================================
   ALERTS
========================================================= */

.alert-box {

  display: flex;

  align-items: center;

  gap: 12px;

  margin-bottom: 22px;

  padding:
    15px
    17px;

  border-radius: 10px;

  font-size: 15px;

  line-height: 1.5;

}


.alert-icon {

  width: 22px;

  height: 22px;

  flex-shrink: 0;

}


.alert-icon svg {

  width: 100%;

  height: 100%;

}


.success-box {

  border:
    1px solid
    #b9dfc2;

  background: #f3fbf5;

  color: #247337;

}


.error-box {

  border:
    1px solid
    #efbcbc;

  background: #fff5f5;

  color: #c72929;

}


/* =========================================================
   BUTTON
========================================================= */

.form-action {

  display: flex;

  align-items: center;

  gap: 22px;

}


.submit-btn {

  min-width: 190px;

  height: 58px;

  padding:
    0
    25px;

  border: none;

  border-radius: 10px;

  background: #0a0a0a;

  color: #ffffff;

  cursor: pointer;

  display: inline-flex;

  align-items: center;

  justify-content: center;

  gap: 11px;

  font-family:
    'DM Sans',
    sans-serif;

  font-size: 15px;

  font-weight: 700;

  transition:
    background 0.2s ease,
    transform 0.2s ease;

}


.submit-btn svg {

  width: 19px;

  height: 19px;

}


.submit-btn:hover:not(:disabled) {

  background: #292929;

  transform:
    translateY(-1px);

}


.submit-btn:disabled {

  opacity: 0.65;

  cursor: not-allowed;

}


.form-action p {

  max-width: 280px;

  margin: 0;

  color: #999;

  font-size: 12px;

  line-height: 1.6;

}


.button-loader {

  width: 18px;

  height: 18px;

  border:

    2px solid
    rgba(
      255,
      255,
      255,
      0.3
    );

  border-top-color: #ffffff;

  border-radius: 50%;

  animation:
    spin
    0.7s
    linear
    infinite;

}


@keyframes spin {

  to {
    transform:
      rotate(360deg);
  }

}


/* =========================================================
   BOTTOM CTA
========================================================= */

.contact-bottom {

  padding:
    75px
    0;

  background: #ffffff;

}


.bottom-inner {

  display: flex;

  align-items: center;

  justify-content: space-between;

  gap: 50px;

}


.bottom-small {

  display: block;

  margin-bottom: 13px;

  color: #888;

  font-size: 12px;

  font-weight: 700;

  letter-spacing: 2px;

}


.bottom-inner h2 {

  max-width: 680px;

  margin: 0;

  font-family:
    'Syne',
    sans-serif;

  font-size:
    clamp(
      34px,
      4vw,
      52px
    );

  line-height: 1.08;

  font-weight: 800;

  letter-spacing: -2px;

}


.bottom-inner h2 span {

  color: #999;

}


.bottom-button {

  min-width: 190px;

  height: 58px;

  padding:
    0
    24px;

  display: inline-flex;

  align-items: center;

  justify-content: center;

  gap: 12px;

  flex-shrink: 0;

  border-radius: 10px;

  background: #111;

  color: #fff;

  text-decoration: none;

  font-size: 15px;

  font-weight: 700;

  transition:
    background 0.2s ease,
    transform 0.2s ease;

}


.bottom-button svg {

  width: 18px;

  height: 18px;

}


.bottom-button:hover {

  background: #333;

  color: #fff;

  transform:
    translateY(-2px);

}


/* =========================================================
   TABLET
========================================================= */

@media (
  max-width: 1000px
) {

  .hero-side {
    display: none;
  }


  .contact-grid {

    grid-template-columns: 1fr;

    gap: 65px;

  }


  .contact-info {

    max-width: 700px;

  }

}


/* =========================================================
   MOBILE
========================================================= */

@media (
  max-width: 700px
) {

  .contact-container {

    width:
      min(
        100% - 30px,
        1240px
      );

  }


  .contact-hero {

    padding:
      65px
      0
      70px;

  }


  .hero-content h1 {

    font-size: 48px;

    letter-spacing: -2.5px;

  }


  .hero-content p {

    font-size: 16px;

  }


  .contact-section {

    padding:
      65px
      0
      70px;

  }


  .info-heading {

    font-size: 34px;

  }


  .info-description {

    font-size: 16px;

  }


  .contact-form-card {

    padding:
      28px
      20px;

    border-radius: 14px;

  }


  .form-top {

    gap: 15px;

  }


  .form-top h2 {

    font-size: 32px;

  }


  .form-mail-icon {

    width: 50px;

    height: 50px;

  }


  .form-row {

    grid-template-columns: 1fr;

    gap: 0;

  }


  .field-group input {

    height: 56px;

  }


  .form-action {

    flex-direction: column;

    align-items: stretch;

  }


  .submit-btn {

    width: 100%;

  }


  .form-action p {

    max-width: none;

    text-align: center;

  }


  .bottom-inner {

    flex-direction: column;

    align-items: flex-start;

  }


  .bottom-button {

    width: 100%;

  }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (
  max-width: 420px
) {

  .hero-content h1 {

    font-size: 40px;

  }


  .hero-tag {

    font-size: 11px;

  }


  .info-item {

    align-items: flex-start;

  }


  .info-icon {

    width: 52px;

    height: 52px;

  }


  .info-value {

    font-size: 16px;

  }

}

</style>
