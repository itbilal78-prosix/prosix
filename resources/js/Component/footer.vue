<template>
  <footer
    class="prosix-footer bg-black text-white"
    :style="footerBackgroundStyle"
  >

    <!--
      DYNAMIC TEXTURE DARKNESS OVERLAY
      Admin: 0 - 100
    -->
    <div
      class="footer-texture-overlay"
      :style="footerOverlayStyle"
    ></div>


    <div
      class="container-fluid px-4 px-md-5"
      style="position: relative; z-index: 1;"
    >
      <div class="row py-4 py-md-5">

        <!-- =========================
             LOGOS
        ========================== -->
        <div class="col-12 mb-4">

          <div
            class="footer-logo d-flex justify-content-center justify-content-md-start align-items-center gap-4 gap-md-5 flex-wrap"
          >

            <img
              v-if="settings.footer_logo_one"
              :src="settings.footer_logo_one"
              alt="Prosix Sports Logo"
              class="footer-logo-img"
            >

            <img
              v-if="settings.footer_logo_two"
              :src="settings.footer_logo_two"
              alt="Prosix Sports Logo"
              class="footer-logo-img"
            >

          </div>

        </div>


        <!-- =========================
             MAIN CONTENT
        ========================== -->
        <div class="col-12">

          <div class="row align-items-start">


            <!-- =========================
                 OPENING SCHEDULE
            ========================== -->
            <div
              class="col-6 col-md-3 mb-4 mb-md-0 text-start order-1"
            >

              <h3 class="footer-title mb-3">
                OPENING SCHEDULE
              </h3>


              <div class="schedule-info">


                <!-- DAYS -->
                <div
                  v-if="settings.opening_days"
                  class="schedule-row"
                >

                  <span class="schedule-label">
                    {{ settings.opening_days }}
                  </span>

                  <span class="schedule-value">
                    {{ settings.opening_status || 'Open' }}
                  </span>

                </div>


                <!-- TIME -->
                <div
                  v-if="settings.opening_time"
                  class="schedule-row"
                >

                  <span class="schedule-label">
                    Time
                  </span>

                  <span class="schedule-value">
                    {{ settings.opening_time }}
                  </span>

                </div>


                <!-- SUNDAY -->
                <div
                  v-if="
                    settings.sunday_label ||
                    settings.sunday_status
                  "
                  class="schedule-row"
                >

                  <span class="schedule-label">
                    {{ settings.sunday_label || 'Sunday' }}
                  </span>

                  <span
                    class="schedule-value"
                    :class="{
                      closed:
                        isClosedStatus(
                          settings.sunday_status
                        )
                    }"
                  >
                    {{ settings.sunday_status || 'Closed' }}
                  </span>

                </div>


              </div>

            </div>


            <!-- =========================
                 DIVIDER
            ========================== -->
            <div
              class="d-none d-md-flex col-md-1 justify-content-center order-2"
            >
              <div class="footer-divider"></div>
            </div>


            <!-- =========================
                 SUBSCRIBE
            ========================== -->
            <div
              class="col-12 col-md-4 mb-4 mb-md-0 text-center d-flex flex-column align-items-center justify-content-center order-3 order-md-2"
            >


              <router-link
                to="/membership"
                class="subscribe-link"
              >

                <h1 class="subscribe-title mb-2">
                  {{
                    settings.subscribe_title ||
                    'SUBSCRIBE'
                  }}
                </h1>

              </router-link>


              <!-- =========================
                   WEBSITE BADGE
              ========================== -->
              <component
                :is="websiteBadgeComponent"
                v-if="
                  settings.show_website_badge &&
                  settings.website_badge_text
                "
                v-bind="websiteBadgeProps"
                class="website-badge"
              >

                <i class="bi bi-code-slash"></i>

                {{ settings.website_badge_text }}

              </component>


              <!-- SUBTITLE -->
              <p
                v-if="settings.subscribe_subtitle"
                class="subscribe-subtitle mb-3"
              >
                {{ settings.subscribe_subtitle }}
              </p>


              <!-- =========================
                   SOCIAL ICONS
              ========================== -->
              <div
                v-if="hasAnySocial"
                class="social-icons d-flex justify-content-center gap-3"
              >


                <!-- FACEBOOK -->
                <a
                  v-if="
                    settings.show_facebook &&
                    settings.facebook_url
                  "
                  :href="settings.facebook_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-link"
                  aria-label="Facebook"
                >

                  <i class="bi bi-facebook"></i>

                </a>


                <!-- INSTAGRAM -->
                <a
                  v-if="
                    settings.show_instagram &&
                    settings.instagram_url
                  "
                  :href="settings.instagram_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-link"
                  aria-label="Instagram"
                >

                  <i class="bi bi-instagram"></i>

                </a>


                <!-- YOUTUBE -->
                <a
                  v-if="
                    settings.show_youtube &&
                    settings.youtube_url
                  "
                  :href="settings.youtube_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-link"
                  aria-label="YouTube"
                >

                  <i class="bi bi-youtube"></i>

                </a>


                <!-- X / TWITTER -->
                <a
                  v-if="
                    settings.show_twitter &&
                    settings.twitter_url
                  "
                  :href="settings.twitter_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-link"
                  aria-label="X / Twitter"
                >

                  <i class="bi bi-twitter-x"></i>

                </a>


                <!-- PINTEREST -->
                <a
                  v-if="
                    settings.show_pinterest &&
                    settings.pinterest_url
                  "
                  :href="settings.pinterest_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-link"
                  aria-label="Pinterest"
                >

                  <i class="bi bi-pinterest"></i>

                </a>


              </div>

            </div>


            <!-- =========================
                 DIVIDER
            ========================== -->
            <div
              class="d-none d-md-flex col-md-1 justify-content-center order-3"
            >
              <div class="footer-divider"></div>
            </div>


            <!-- =========================
                 CONTACT INFO
            ========================== -->
            <div
              class="col-6 col-md-3 mb-4 mb-md-0 order-2 order-md-4 text-md-end"
            >

              <div
                class="contact-info text-start d-inline-block"
              >

                <h3 class="footer-title mb-3">
                  CONTACT INFO
                </h3>


                <!-- =========================
                     PHONE
                ========================== -->
                <div
                  v-if="settings.phone"
                  class="contact-item mb-2"
                >

                  <i class="bi bi-telephone-fill"></i>

                  <a
                    :href="phoneHref"
                    class="contact-link"
                  >
                    {{ settings.phone }}
                  </a>

                </div>


                <!-- =========================
                     WHATSAPP
                ========================== -->
                <div
                  v-if="settings.whatsapp"
                  class="contact-item mb-2"
                >

                  <i class="bi bi-whatsapp"></i>

                  <a
                    :href="whatsappHref"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="contact-link"
                  >
                    {{ settings.whatsapp }}
                  </a>

                </div>


                <!-- =========================
                     ADDRESS ONE
                ========================== -->
                <div
                  v-if="settings.address_one"
                  class="contact-item mb-2"
                >

                  <i class="bi bi-geo-alt-fill"></i>

                  <span class="contact-address">
                    {{ settings.address_one }}
                  </span>

                </div>


                <!-- =========================
                     ADDRESS TWO
                ========================== -->
                <div
                  v-if="settings.address_two"
                  class="contact-item mb-2"
                >

                  <i class="bi bi-geo-alt-fill"></i>

                  <span class="contact-address">
                    {{ settings.address_two }}
                  </span>

                </div>


                <!-- =========================
                     EMAIL
                ========================== -->
                <div
                  v-if="settings.email"
                  class="contact-item"
                >

                  <i class="bi bi-envelope-fill"></i>

                  <a
                    :href="`mailto:${settings.email}`"
                    class="contact-link"
                  >
                    {{ settings.email }}
                  </a>

                </div>


              </div>

            </div>


          </div>

        </div>

      </div>
    </div>

  </footer>
</template>


<script setup>

import {
  computed,
  onMounted,
  reactive,
  ref
} from 'vue'


/* =========================================================
   LOADING
   ========================================================= */

const loading = ref(false)


/* =========================================================
   DEFAULT SETTINGS

   Agar API temporarily fail ho jaye to footer
   default Prosix information show karega.
   ========================================================= */

const settings = reactive({

  /* CONTACT */

  phone:
    '+1 929 210 4402',

  whatsapp:
    '+1 929 210 4402',

  email:
    'sales@prosix.com',


  address_one:
    '2604 Whittier Place Wilmington, Delaware 19808',

  address_two:
    '5556 E Kings Canyon Rd, Fresno, CA 93727',


  /* OPENING */

  opening_days:
    'Mon – Sat',

  opening_status:
    'Open',

  opening_time:
    '08:00 – 18:00',

  sunday_label:
    'Sunday',

  sunday_status:
    'Closed',


  /* SUBSCRIBE */

  subscribe_title:
    'SUBSCRIBE',

  subscribe_subtitle:
    'To our newsletter for latest updates',


  /* WEBSITE BADGE */

  website_badge_text:
    'Need a Professional Website?',

  website_badge_link:
    '/website-request',

  show_website_badge:
    true,


  /* SOCIAL LINKS */

  facebook_url:
    'https://www.facebook.com/prosixsports/',

  instagram_url:
    'https://www.instagram.com/prosixsports',

  youtube_url:
    'https://www.youtube.com/@prosixsports',

  twitter_url:
    'https://x.com/ProsixSports',

  pinterest_url:
    'https://www.pinterest.com/prosixsports/',


  /* SOCIAL VISIBILITY */

  show_facebook:
    true,

  show_instagram:
    true,

  show_youtube:
    true,

  show_twitter:
    true,

  show_pinterest:
    true,


  /* FOOTER MEDIA */

  footer_logo_one:
    '/public/assets/images/P LOGO WHITE.png',

  footer_logo_two:
    '/public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png',

  footer_background:
    '/public/assets/images/footer texture.svg',


  /*
  |--------------------------------------------------------------------------
  | FOOTER TEXTURE DARKNESS
  |--------------------------------------------------------------------------
  | 0   = Texture fully visible
  | 48  = Recommended
  | 100 = Texture hidden
  |--------------------------------------------------------------------------
  */

  footer_texture_opacity:
    48,

})


/* =========================================================
   LOAD WEBSITE INFO FROM DATABASE
   ========================================================= */

const loadWebsiteInfo = async () => {

  loading.value = true


  try {

    const response = await fetch(
      '/api/website-info',
      {

        method:
          'GET',

        headers: {

          Accept:
            'application/json',

          'X-Requested-With':
            'XMLHttpRequest',

        },

      }
    )


    if (!response.ok) {

      console.error(
        'Website Info API failed:',
        response.status
      )

      return
    }


    const result =
      await response.json()


    if (result?.data) {

      Object.assign(
        settings,
        result.data
      )

    }


  } catch (error) {

    console.error(
      'Website information loading failed:',
      error
    )

  } finally {

    loading.value =
      false

  }

}


/* =========================================================
   PHONE LINK
   ========================================================= */

const phoneHref = computed(() => {

  const number =
    String(
      settings.phone || ''
    )
      .replace(
        /[^\d+]/g,
        ''
      )


  return number
    ? `tel:${number}`
    : '#'

})


/* =========================================================
   WHATSAPP LINK
   ========================================================= */

const whatsappHref = computed(() => {

  const number =
    String(
      settings.whatsapp || ''
    )
      .replace(
        /\D/g,
        ''
      )


  return number
    ? `https://wa.me/${number}`
    : '#'

})


/* =========================================================
   FOOTER BACKGROUND IMAGE
   ========================================================= */

const footerBackgroundStyle = computed(() => {

  if (
    !settings.footer_background
  ) {

    return {

      backgroundColor:
        '#000000'

    }

  }


  return {

    backgroundImage:
      `url("${settings.footer_background}")`,

    backgroundRepeat:
      'no-repeat',

    backgroundPosition:
      'center center',

    backgroundSize:
      'cover',

    backgroundColor:
      '#000000',

  }

})


/* =========================================================
   FOOTER TEXTURE DARKNESS

   Admin gives:
   0 - 100

   CSS needs:
   0 - 1
   ========================================================= */

const footerOverlayStyle = computed(() => {

  let opacity =
    Number(
      settings.footer_texture_opacity
      ?? 48
    )


  /*
   * If invalid value comes from API,
   * fall back to 48.
   */
  if (
    Number.isNaN(opacity)
  ) {

    opacity =
      48

  }


  /*
   * Keep value between
   * 0 and 100.
   */
  opacity =
    Math.min(
      100,
      Math.max(
        0,
        opacity
      )
    )


  return {

    backgroundColor:
      `rgba(0, 0, 0, ${opacity / 100})`

  }

})


/* =========================================================
   SOCIAL CHECK
   ========================================================= */

const hasAnySocial = computed(() => {

  return Boolean(

    (
      settings.show_facebook &&
      settings.facebook_url
    )

    ||

    (
      settings.show_instagram &&
      settings.instagram_url
    )

    ||

    (
      settings.show_youtube &&
      settings.youtube_url
    )

    ||

    (
      settings.show_twitter &&
      settings.twitter_url
    )

    ||

    (
      settings.show_pinterest &&
      settings.pinterest_url
    )

  )

})


/* =========================================================
   WEBSITE BADGE
   ========================================================= */

const isExternalWebsiteBadge = computed(() => {

  const link =
    String(
      settings.website_badge_link
      || ''
    )
      .trim()


  return (

    link.startsWith(
      'http://'
    )

    ||

    link.startsWith(
      'https://'
    )

  )

})


/* =========================================================
   WEBSITE BADGE COMPONENT

   Internal:
       RouterLink

   External:
       <a>
   ========================================================= */

const websiteBadgeComponent = computed(() => {

  return isExternalWebsiteBadge.value
    ? 'a'
    : 'router-link'

})


/* =========================================================
   WEBSITE BADGE PROPS
   ========================================================= */

const websiteBadgeProps = computed(() => {

  const link =
    settings.website_badge_link
    ||
    '/website-request'


  if (
    isExternalWebsiteBadge.value
  ) {

    return {

      href:
        link,

      target:
        '_blank',

      rel:
        'noopener noreferrer',

    }

  }


  return {

    to:
      link,

  }

})


/* =========================================================
   CLOSED STATUS HELPER
   ========================================================= */

const isClosedStatus = (status) => {

  return String(
    status || ''
  )
    .trim()
    .toLowerCase()
    ===
    'closed'

}


/* =========================================================
   MOUNT
   ========================================================= */

onMounted(() => {

  loadWebsiteInfo()

})

</script>


<style>

/* =========================================================
   FOOTER
   ========================================================= */

.prosix-footer {

  position:
    relative;

  color:
    #ffffff;

  background-color:
    #000000;

  background-repeat:
    no-repeat;

  background-position:
    center center;

  background-size:
    cover;

  overflow:
    hidden;

}


/* =========================================================
   DYNAMIC TEXTURE DARKNESS OVERLAY

   IMPORTANT:
   opacity is NOT hard-coded here anymore.

   Admin controls it from Website Info.
   ========================================================= */

.footer-texture-overlay {

  position:
    absolute;

  inset:
    0;

  z-index:
    0;

  pointer-events:
    none;

  transition:
    background-color
    0.3s ease;

}


/* =========================================================
   CONTENT ABOVE OVERLAY
   ========================================================= */

.prosix-footer
> .container-fluid {

  position:
    relative;

  z-index:
    1;

}


/* =========================================================
   LOGOS
   ========================================================= */

.footer-logo-img {

  height:
    60px;

  max-height:
    60px;

  max-width:
    240px;

  object-fit:
    contain;

}


/* =========================================================
   DIVIDERS
   ========================================================= */

.footer-divider {

  width:
    1px;

  min-height:
    120px;

  background:
    rgba(
      255,
      255,
      255,
      0.22
    );

}


/* =========================================================
   TITLES
   ========================================================= */

.footer-title {

  font-size:
    1.1rem;

  font-weight:
    bold;

  text-transform:
    uppercase;

  letter-spacing:
    0.05em;

}


/* =========================================================
   OPENING SCHEDULE
   ========================================================= */

.schedule-info {

  display:
    flex;

  flex-direction:
    column;

  gap:
    10px;

}


.schedule-row {

  display:
    flex;

  align-items:
    baseline;

  gap:
    0;

}


.schedule-label {

  min-width:
    80px;

  font-size:
    0.88rem;

  color:
    rgba(
      255,
      255,
      255,
      0.55
    );

  white-space:
    nowrap;

}


.schedule-value {

  margin-left:
    8px;

  padding-left:
    12px;

  border-left:
    1px solid
    rgba(
      255,
      255,
      255,
      0.2
    );

  font-size:
    0.88rem;

  font-weight:
    600;

  color:
    #ffffff;

}


.schedule-value.closed {

  color:
    #ff5555;

}


/* =========================================================
   SUBSCRIBE
   ========================================================= */

.subscribe-link {

  text-decoration:
    none;

}


.subscribe-link:hover {

  text-decoration:
    none;

}


.subscribe-title {

  margin:
    0;

  font-size:
    2.4rem;

  color:
    #ffffff;

  font-weight:
    bold;

  letter-spacing:
    2px;

  transition:
    opacity
    0.25s ease;

}


.subscribe-link:hover
.subscribe-title {

  opacity:
    0.82;

}


.subscribe-subtitle {

  font-size:
    0.85rem;

  color:
    rgba(
      255,
      255,
      255,
      0.55
    );

}


/* =========================================================
   WEBSITE BADGE
   ========================================================= */

.website-badge {

  display:
    inline-flex;

  align-items:
    center;

  justify-content:
    center;

  gap:
    8px;


  margin:
    8px 0 18px;


  padding:
    7px 16px;


  border:
    1px solid
    rgba(
      255,
      255,
      255,
      0.35
    );


  border-radius:
    50px;


  background:
    rgba(
      255,
      255,
      255,
      0.05
    );


  color:
    #ffffff;


  text-decoration:
    none;


  font-size:
    13px;


  font-weight:
    600;


  letter-spacing:
    0.4px;


  transition:
    background
    0.3s ease,

    color
    0.3s ease,

    border-color
    0.3s ease,

    transform
    0.3s ease;

}


.website-badge i {

  font-size:
    15px;

}


.website-badge:hover {

  background:
    #ffffff;

  color:
    #000000;

  border-color:
    #ffffff;

  transform:
    translateY(-2px);

  text-decoration:
    none;

}


/* =========================================================
   SOCIAL ICONS
   ========================================================= */

.social-icons {

  flex-wrap:
    wrap;

}


.social-icons
.social-link {

  display:
    inline-flex;

  align-items:
    center;

  justify-content:
    center;

  text-decoration:
    none;

}


.social-icons
.social-link i {

  color:
    #ffffff;

  font-size:
    26px;

  transition:
    color
    0.3s ease,

    transform
    0.3s ease,

    opacity
    0.3s ease;

}


.social-icons
.social-link:hover i {

  color:
    #cccccc;

  transform:
    translateY(-2px);

}


/* =========================================================
   CONTACT
   ========================================================= */

.contact-info {

  max-width:
    340px;

}


.contact-item {

  display:
    flex;

  align-items:
    flex-start;

  gap:
    10px;

}


.contact-item i {

  flex:
    0 0 auto;

  min-width:
    18px;

  margin-top:
    4px;

  font-size:
    16px;

}


.contact-item span,
.contact-item a {

  font-size:
    0.9rem;

  line-height:
    1.5;

}


.contact-link {

  color:
    #ffffff;

  text-decoration:
    none;

  font-weight:
    500;

  overflow-wrap:
    anywhere;

}


.contact-link:hover {

  color:
    #cccccc;

  text-decoration:
    none;

}


.contact-address {

  white-space:
    normal;

  overflow-wrap:
    anywhere;

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (
  max-width: 767px
) {

  .footer-logo-img {

    height:
      50px;

    max-height:
      50px;

    max-width:
      180px;

  }


  .subscribe-title {

    font-size:
      2rem;

  }


  .footer-title {

    font-size:
      1rem;

  }


  .schedule-label {

    min-width:
      65px;

    font-size:
      0.8rem;

  }


  .schedule-value {

    font-size:
      0.8rem;

    padding-left:
      8px;

    margin-left:
      5px;

  }


  .contact-item span,
  .contact-item a {

    font-size:
      0.8rem;

  }


  .social-icons
  .social-link i {

    font-size:
      23px;

  }


  .website-badge {

    font-size:
      12px;

    padding:
      6px 13px;

  }

}


/* =========================================================
   VERY SMALL MOBILE
   ========================================================= */

@media (
  max-width: 480px
) {

  .footer-logo {

    gap:
      18px
      !important;

  }


  .footer-logo-img {

    height:
      42px;

    max-height:
      42px;

  }


  .schedule-label {

    min-width:
      58px;

  }


  .contact-info {

    width:
      100%;

    max-width:
      none;

  }

}

</style>
