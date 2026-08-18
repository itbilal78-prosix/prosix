<template>
  <div class="web-service-page" ref="pageRoot">
    <div class="scroll-progress"></div>
    <nav-component />

    <!-- =====================================================
         HERO
    ====================================================== -->
    <section class="hero-section">
      <div class="hero-grid-pattern"></div>
      <div class="hero-glow hero-glow-one"></div>
      <div class="hero-glow hero-glow-two"></div>

      <div class="site-container hero-layout">
        <div class="hero-copy reveal reveal-left">
          <div class="hero-badge">
            <span class="badge-dot"></span>
            CUSTOM WEBSITE DEVELOPMENT
          </div>

          <h1>
            We Build Websites.
            <span>You Grow Your Business.</span>
          </h1>

          <p class="hero-description">
            Tell us your idea and let Prosix handle the rest. We design and
            develop modern, responsive and conversion-focused websites built
            around your business, your audience and your goals.
          </p>

          <div class="hero-benefits">
            <div class="hero-benefit">
              <span class="benefit-icon">
                <i class="bi bi-lightning-charge-fill"></i>
              </span>
              <span>
                <strong>Fast & Optimized</strong>
                <small>Speed that converts</small>
              </span>
            </div>

            <div class="hero-benefit">
              <span class="benefit-icon">
                <i class="bi bi-phone"></i>
              </span>
              <span>
                <strong>Fully Responsive</strong>
                <small>Perfect on every device</small>
              </span>
            </div>

            <div class="hero-benefit">
              <span class="benefit-icon">
                <i class="bi bi-shield-check"></i>
              </span>
              <span>
                <strong>Secure & Reliable</strong>
                <small>Built with care</small>
              </span>
            </div>
          </div>

          <div class="hero-actions">
            <a href="#project" class="primary-button">
              Start Your Project
              <i class="bi bi-arrow-up-right"></i>
            </a>

            <a href="#services" class="outline-button">
              See Our Services
              <i class="bi bi-arrow-down"></i>
            </a>
          </div>

          <div class="hero-note">
            <i class="bi bi-stars"></i>
            <span>
              <strong>Your idea. Our expertise.</strong>
              <small>Let's turn it into something people remember.</small>
            </span>
          </div>
        </div>

        <div class="hero-visual">
          <div
            class="hero-slider"
            @mouseenter="pauseSlider"
            @mouseleave="resumeSlider"
            @pointerdown="startDrag"
            @pointermove="onDrag"
            @pointerup="endDrag"
            @pointercancel="endDrag"
          >
            <div class="slider-stage">
              <article
                v-for="(slide, index) in heroSlides"
                :key="slide.id"
                class="hero-slide"
                :class="{ active: currentSlide === index }"
              >
                <div class="slide-card">
                  <img
                    :src="slide.image"
                    :alt="slide.alt"
                    draggable="false"
                  />

                  <div class="slide-overlay"></div>

                  <div class="slide-meta">
                    <span>{{ String(index + 1).padStart(2, "0") }}</span>
                    <div>
                      <strong>{{ slide.title }}</strong>
                      <small>{{ slide.subtitle }}</small>
                    </div>
                  </div>
                </div>
              </article>
            </div>

            <!-- <div class="slider-side-card">
              <span>WE BUILD</span>
              <strong>Web Experiences</strong>
              <small>Business • Ecommerce • Portals • Custom Apps</small>
            </div> -->

            <button
              type="button"
              class="slider-arrow slider-prev"
              aria-label="Previous website"
              @click.stop="prevSlide"
            >
              <i class="bi bi-arrow-left"></i>
            </button>

            <button
              type="button"
              class="slider-arrow slider-next"
              aria-label="Next website"
              @click.stop="nextSlide"
            >
              <i class="bi bi-arrow-right"></i>
            </button>

            <div class="slider-bottom">
              <div class="slider-dots">
                <button
                  v-for="(slide, index) in heroSlides"
                  :key="`dot-${slide.id}`"
                  type="button"
                  :class="{ active: currentSlide === index }"
                  :aria-label="`Show slide ${index + 1}`"
                  @click.stop="goToSlide(index)"
                ></button>
              </div>

              <div class="drag-note">
                <i class="bi bi-arrows"></i>
                Drag to explore
              </div>
            </div>
          </div>
        </div>
      </div>

      <a href="#services" class="scroll-down">
        <span>SCROLL TO EXPLORE</span>
        <i class="bi bi-arrow-down"></i>
      </a>
    </section>

    <!-- =====================================================
         MOTIVATION STRIP
    ====================================================== -->
    <section class="motivation-strip">
      <div class="site-container motivation-layout reveal">
        <span class="section-no">01</span>

        <div>
          <span class="mini-label">WE BUILD FOR RESULTS</span>
          <h2>
            Your website is often your
            <span>first conversation with a customer.</span>
          </h2>
        </div>

        <p>
          We make that first impression count. Clear messaging, strong visuals
          and smooth user experience help your visitors understand your business
          and feel confident enough to take the next step.
        </p>
      </div>
    </section>

    <!-- =====================================================
         SERVICES
    ====================================================== -->
    <section id="services" class="services-section">
      <div class="site-container">
        <div class="section-heading reveal">
          <div>
            <div class="section-label">
              <span></span>
              WHAT WE BUILD
            </div>

            <h2>
              Websites for every
              <span>business need.</span>
            </h2>
          </div>

          <p>
            Select what you need. Every project is custom-built to match your
            brand, your workflow and your growth goals.
          </p>
        </div>

        <div class="services-grid">
          <button
            v-for="(service, index) in services"
            :key="service.id"
            class="service-card reveal-card"
            :class="{ selected: selectedService === service.id }"
            :style="{ '--delay': `${index * 45}ms` }"
            @click="selectService(service.id)"
          >
            <div class="service-card-top">
              <span>0{{ index + 1 }}</span>
              <i
                :class="
                  selectedService === service.id
                    ? 'bi bi-check-circle-fill'
                    : 'bi bi-plus-circle'
                "
              ></i>
            </div>

            <div class="service-icon">
              <i :class="service.icon"></i>
            </div>

            <h3>{{ service.title }}</h3>
            <p>{{ service.description }}</p>

            <div class="service-arrow">
              <span>Select Service</span>
              <i class="bi bi-arrow-up-right"></i>
            </div>
          </button>
        </div>
      </div>
    </section>

    <!-- =====================================================
         MOVING SERVICES MARQUEE
    ====================================================== -->
    <section class="marquee-section" aria-hidden="true">
      <div class="marquee-track">
        <div class="marquee-group">
          <span>WEB DESIGN</span><i></i>
          <span>DEVELOPMENT</span><i></i>
          <span>ECOMMERCE</span><i></i>
          <span>WEB APPS</span><i></i>
          <span>SEO READY</span><i></i>
          <span>RESPONSIVE</span><i></i>
        </div>

        <div class="marquee-group">
          <span>WEB DESIGN</span><i></i>
          <span>DEVELOPMENT</span><i></i>
          <span>ECOMMERCE</span><i></i>
          <span>WEB APPS</span><i></i>
          <span>SEO READY</span><i></i>
          <span>RESPONSIVE</span><i></i>
        </div>
      </div>
    </section>

    <!-- =====================================================
         DEVICE EXPERIENCE
    ====================================================== -->
    <section class="device-section">
      <div class="site-container device-layout">
        <div class="device-copy reveal reveal-left">
          <div class="section-label light-label">
            <span></span>
            PERFECT ON EVERY SCREEN
          </div>

          <h2>
            One website.
            <span>Every device.</span>
          </h2>

          <p>
            Your customer could discover you from a laptop, tablet or phone.
            Your website should feel premium on all three.
          </p>

          <div class="device-points">
            <div>
              <i class="bi bi-check2"></i>
              <span>Responsive layouts</span>
            </div>
            <div>
              <i class="bi bi-check2"></i>
              <span>Mobile-friendly navigation</span>
            </div>
            <div>
              <i class="bi bi-check2"></i>
              <span>Clear calls to action</span>
            </div>
            <div>
              <i class="bi bi-check2"></i>
              <span>Optimized user experience</span>
            </div>
          </div>
        </div>

        <div class="devices-preview reveal reveal-right">
          <div class="desktop-frame">
            <div class="desktop-top">
              <span></span>
              <span></span>
              <span></span>
            </div>

            <div class="desktop-screen">
              <div class="screen-nav">
                <strong>YOUR<span>BRAND</span></strong>
                <div>
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </div>

              <div class="screen-content">
                <div class="screen-copy">
                  <small>WELCOME TO YOUR BRAND</small>
                  <div class="screen-title"></div>
                  <div class="screen-title short"></div>
                  <div class="screen-text"></div>
                  <div class="screen-text short"></div>
                  <button></button>
                </div>

                <div class="screen-art">
                  <i class="bi bi-image"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="tablet-frame">
            <div class="tablet-content">
              <strong>YOUR BRAND</strong>
              <span></span>
              <span></span>
              <span class="short"></span>
              <button></button>
            </div>
          </div>

          <div class="phone-frame">
            <div class="phone-notch"></div>

            <div class="phone-content">
              <strong>YOUR BRAND</strong>
              <span></span>
              <span></span>
              <span class="short"></span>
              <button></button>
            </div>
          </div>

          <div class="responsive-label">
            <i class="bi bi-arrows-angle-expand"></i>
            <span>
              <strong>100% Responsive</strong>
              <small>Desktop • Tablet • Mobile</small>
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         BUSINESS VALUE - NEW SECTION 1
    ====================================================== -->
    <section class="value-section">
      <div class="site-container">
        <div class="section-heading reveal">
          <div>
            <div class="section-label">
              <span></span>
              BUILT FOR YOUR BUSINESS
            </div>

            <h2>
              A website should do more
              <span>than simply exist.</span>
            </h2>
          </div>

          <p>
            We combine design, development and business thinking so your website
            becomes a useful part of your sales and brand experience.
          </p>
        </div>

        <div class="value-grid">
          <article
            v-for="(item, index) in values"
            :key="item.title"
            class="value-card reveal-card"
            :style="{ '--delay': `${index * 45}ms` }"
          >
            <div class="value-icon">
              <i :class="item.icon"></i>
            </div>

            <span class="value-number">0{{ index + 1 }}</span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.text }}</p>
          </article>
        </div>
      </div>
    </section>

    <!-- =====================================================
         SHOWCASE IMAGE - NEW SECTION 2
    ====================================================== -->
    <section class="showcase-section">
      <div class="site-container showcase-layout">
        <div class="showcase-image reveal reveal-left">
          <img
            src="/public/assets/images/prosix-web-development-banner.png"
            alt="Prosix website development service page concept"
          />
          <div class="showcase-fade"></div>
        </div>

        <div class="showcase-copy reveal reveal-right">
          <div class="section-label">
            <span></span>
            FROM IDEA TO EXPERIENCE
          </div>

          <h2>
            We think about the
            <span>whole customer journey.</span>
          </h2>

          <p>
            A beautiful banner is only the beginning. We shape the sections,
            calls-to-action, content flow and responsive behavior so the page
            feels connected from top to bottom.
          </p>

          <div class="showcase-list">
            <div>
              <strong>01</strong>
              <span>
                <b>Clear first impression</b>
                <small>Your visitor understands what you offer quickly.</small>
              </span>
            </div>

            <div>
              <strong>02</strong>
              <span>
                <b>Natural content flow</b>
                <small>Each section leads smoothly into the next.</small>
              </span>
            </div>

            <div>
              <strong>03</strong>
              <span>
                <b>Strong action points</b>
                <small>Visitors always know what they can do next.</small>
              </span>
            </div>
          </div>

          <a href="#project" class="showcase-button">
            Build Something Like This
            <i class="bi bi-arrow-up-right"></i>
          </a>
        </div>
      </div>
    </section>

    <!-- =====================================================
         PROJECT BUILDER
    ====================================================== -->
    <section id="project" class="project-section">
      <div class="project-grid-bg"></div>

      <div class="site-container project-layout">
        <div class="project-copy reveal reveal-left">
          <div class="section-label light-label">
            <span></span>
            START YOUR PROJECT
          </div>

          <h2>
            Tell us a little.
            <span>We'll take it from there.</span>
          </h2>

          <p>
            You do not need technical knowledge. Answer three simple questions
            and create a ready project brief for the Prosix team.
          </p>

          <div class="project-benefits">
            <div>
              <i class="bi bi-chat-dots"></i>
              <span>
                <strong>Simple conversation</strong>
                <small>No complicated technical language.</small>
              </span>
            </div>

            <div>
              <i class="bi bi-shield-check"></i>
              <span>
                <strong>Your idea stays private</strong>
                <small>We treat project information with care.</small>
              </span>
            </div>

            <div>
              <i class="bi bi-stars"></i>
              <span>
                <strong>Built around your goal</strong>
                <small>Not around a forced template.</small>
              </span>
            </div>
          </div>
        </div>

        <div class="project-card reveal reveal-right">
          <div class="form-progress">
            <div
              v-for="n in 3"
              :key="n"
              class="form-progress-step"
              :class="{ active: step >= n }"
            >
              <span>{{ n }}</span>
              <div></div>
            </div>
          </div>

          <!-- STEP ONE -->
          <div v-if="step === 1" class="form-step">
            <span class="step-label">STEP 1 OF 3</span>
            <h3>What do you want us to build?</h3>
            <p>Select the option closest to your project.</p>

            <div class="form-service-grid">
              <button
                v-for="service in services"
                :key="service.id"
                type="button"
                :class="{ active: selectedService === service.id }"
                @click="selectedService = service.id"
              >
                <i :class="service.icon"></i>
                <span>{{ service.shortTitle }}</span>
                <i
                  :class="
                    selectedService === service.id
                      ? 'bi bi-check-circle-fill form-check'
                      : 'bi bi-circle form-check'
                  "
                ></i>
              </button>
            </div>

            <button
              type="button"
              class="next-button"
              :disabled="!selectedService"
              @click="step = 2"
            >
              Continue
              <i class="bi bi-arrow-right"></i>
            </button>
          </div>

          <!-- STEP TWO -->
          <div v-else-if="step === 2" class="form-step">
            <span class="step-label">STEP 2 OF 3</span>
            <h3>What should the website achieve?</h3>
            <p>Choose the main result you want from your website.</p>

            <div class="goal-list">
              <button
                v-for="goal in goals"
                :key="goal.id"
                type="button"
                :class="{ active: selectedGoal === goal.id }"
                @click="selectedGoal = goal.id"
              >
                <span class="goal-icon">
                  <i :class="goal.icon"></i>
                </span>

                <span>
                  <strong>{{ goal.title }}</strong>
                  <small>{{ goal.description }}</small>
                </span>

                <i
                  :class="
                    selectedGoal === goal.id
                      ? 'bi bi-check-circle-fill'
                      : 'bi bi-circle'
                  "
                ></i>
              </button>
            </div>

            <div class="form-actions">
              <button type="button" class="back-button" @click="step = 1">
                <i class="bi bi-arrow-left"></i>
                Back
              </button>

              <button
                type="button"
                class="next-button"
                :disabled="!selectedGoal"
                @click="step = 3"
              >
                Continue
                <i class="bi bi-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- STEP THREE -->
          <div v-else class="form-step">
            <span class="step-label">STEP 3 OF 3</span>
            <h3>Tell us about your idea.</h3>
            <p>A few lines are enough. We can discuss everything else with you.</p>

            <textarea
              v-model="projectNote"
              rows="5"
              placeholder="Example: I need a modern business website with Home, About, Services, Portfolio and Contact pages..."
            ></textarea>

            <div class="project-summary">
              <div>
                <small>PROJECT TYPE</small>
                <strong>{{ selectedServiceTitle }}</strong>
              </div>

              <div>
                <small>MAIN GOAL</small>
                <strong>{{ selectedGoalTitle }}</strong>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="back-button" @click="step = 2">
                <i class="bi bi-arrow-left"></i>
                Back
              </button>

              <a :href="emailLink" class="send-button">
                Send To Prosix
                <i class="bi bi-send"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
         PROCESS
    ====================================================== -->
    <section class="process-section">
      <div class="site-container">
        <div class="section-heading reveal">
          <div>
            <div class="section-label">
              <span></span>
              OUR PROCESS
            </div>

            <h2>
              Simple process.
              <span>Powerful result.</span>
            </h2>
          </div>

          <p>
            You focus on your business. We guide the project from first idea to
            final launch.
          </p>
        </div>

        <div class="process-grid">
          <article
            v-for="(item, index) in process"
            :key="item.title"
            class="process-card reveal-card"
            :style="{ '--delay': `${index * 55}ms` }"
          >
            <div class="process-icon">
              <i :class="item.icon"></i>
            </div>

            <span class="process-number">0{{ index + 1 }}</span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.text }}</p>

            <i
              v-if="index < process.length - 1"
              class="bi bi-arrow-right process-arrow"
            ></i>
          </article>
        </div>
      </div>
    </section>

    <!-- =====================================================
         FINAL CTA
    ====================================================== -->
    <section class="final-cta-section">
      <div class="site-container">
        <div class="final-cta reveal">
          <div class="final-grid"></div>

          <div class="final-copy">
            <span class="final-kicker">READY TO GROW YOUR BUSINESS?</span>

            <h2>
              Your next website
              <span>can start today.</span>
            </h2>

            <p>
              You bring the idea. Prosix brings the design, development and
              experience to make it real.
            </p>
          </div>

          <a href="#project" class="final-button">
            <span>
              Let's Build Your Website
              <small>Tell us what you have in mind</small>
            </span>
            <i class="bi bi-arrow-up-right"></i>
          </a>
        </div>
      </div>
    </section>

    <footer-component />
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from "vue";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

const pageRoot = ref(null);
const step = ref(1);
const selectedService = ref("");
const selectedGoal = ref("");
const projectNote = ref("");

const currentSlide = ref(0);
let sliderTimer = null;
let sliderPaused = false;
let dragStartX = 0;
let dragging = false;
let dragDistance = 0;

const heroSlides = [
  {
    id: "business",
    image: "/assets/images/website-slide-1.jpg",
    alt: "Business website design by Prosix",
    title: "Business Websites",
    subtitle: "Professional. Clear. Built to convert.",
  },
  {
    id: "ecommerce",
    image: "/assets/images/website-slide-2.jpg",
    alt: "Ecommerce website design by Prosix",
    title: "Ecommerce Stores",
    subtitle: "Smooth shopping experiences that sell.",
  },
  {
    id: "portal",
    image: "/assets/images/website-slide-3.jpg",
    alt: "Web application dashboard by Prosix",
    title: "Dashboards & Portals",
    subtitle: "Custom systems for real business workflows.",
  },
  {
    id: "custom",
    image: "/assets/images/website-slide-4.jpg",
    alt: "Custom web platform by Prosix",
    title: "Custom Platforms",
    subtitle: "Unique ideas turned into digital products.",
  },
  {
    id: "school",
    image: "/assets/images/website-slide-5.jpg",
    alt: "School management system by Prosix",
    title: "School Management",
    subtitle: "Modern systems built for smarter management.",
  },
];

const services = [
  {
    id: "business",
    title: "Business Website",
    shortTitle: "Business Website",
    icon: "bi bi-globe2",
    description:
      "A professional website that presents your company clearly, builds trust and turns visitors into inquiries.",
  },
  {
    id: "ecommerce",
    title: "Ecommerce Store",
    shortTitle: "Ecommerce Store",
    icon: "bi bi-cart3",
    description:
      "A smooth online store experience designed to showcase products, take orders and support your growth.",
  },
  {
    id: "dashboard",
    title: "Dashboard / Web App",
    shortTitle: "Dashboard / Web App",
    icon: "bi bi-columns-gap",
    description:
      "Secure dashboards, client portals and web applications built around how your business actually works.",
  },
  {
    id: "custom",
    title: "Custom Platform",
    shortTitle: "Custom Platform",
    icon: "bi bi-code-square",
    description:
      "Unique workflows, integrations and custom functionality for ideas that need more than a standard website.",
  },
];

const values = [
  {
    icon: "bi bi-eye",
    title: "Build Trust",
    text: "A polished website makes your business feel established, professional and easier to trust.",
  },
  {
    icon: "bi bi-people",
    title: "Connect With Customers",
    text: "Clear content and thoughtful calls-to-action help visitors move from interest to conversation.",
  },
  {
    icon: "bi bi-graph-up-arrow",
    title: "Support Growth",
    text: "Your website can become a stronger sales, marketing and customer experience tool as you grow.",
  },
  {
    icon: "bi bi-diagram-3",
    title: "Scale Later",
    text: "Start with what you need now and add dashboards, integrations and new features when the time is right.",
  },
];

const goals = [
  {
    id: "leads",
    title: "Get More Leads",
    description: "Turn visitors into inquiries and potential customers.",
    icon: "bi bi-graph-up-arrow",
  },
  {
    id: "sales",
    title: "Sell Online",
    description: "Show products and generate online sales.",
    icon: "bi bi-cart-check",
  },
  {
    id: "trust",
    title: "Build Trust",
    description: "Give your business a stronger professional presence.",
    icon: "bi bi-shield-check",
  },
  {
    id: "manage",
    title: "Manage Operations",
    description: "Use dashboards, portals and custom business workflows.",
    icon: "bi bi-sliders",
  },
];

const process = [
  {
    icon: "bi bi-chat-square-text",
    title: "Discuss",
    text: "We understand your idea, your business, your audience and what you want the website to achieve.",
  },
  {
    icon: "bi bi-bezier2",
    title: "Design & Build",
    text: "We shape the experience and develop a responsive website around the direction you approve.",
  },
  {
    icon: "bi bi-rocket-takeoff",
    title: "Launch & Grow",
    text: "You review the final result, we complete the finishing touches and prepare your website to go live.",
  },
];

const selectedServiceTitle = computed(() => {
  return services.find((item) => item.id === selectedService.value)?.title || "";
});

const selectedGoalTitle = computed(() => {
  return goals.find((item) => item.id === selectedGoal.value)?.title || "";
});

const emailLink = computed(() => {
  const subject = `Website Development Inquiry - ${selectedServiceTitle.value}`;

  const body = [
    "Hi Prosix Team,",
    "",
    "I would like to discuss a website project.",
    "",
    `Project Type: ${selectedServiceTitle.value}`,
    `Main Goal: ${selectedGoalTitle.value}`,
    "",
    projectNote.value ? `Project Details: ${projectNote.value}` : "",
    "",
    "Please contact me to discuss the next steps.",
  ]
    .filter(Boolean)
    .join("\n");

  return `mailto:sales@prosix.com?subject=${encodeURIComponent(
    subject
  )}&body=${encodeURIComponent(body)}`;
});


function animateSlide(direction = 1) {
  nextTick(() => {
    const activeSlide = pageRoot.value?.querySelector(".hero-slide.active .slide-card");
    if (!activeSlide) return;

    gsap.fromTo(
      activeSlide,
      {
        x: direction > 0 ? 70 : -70,
        opacity: 0,
        scale: 0.94,
        rotateY: direction > 0 ? -5 : 5,
      },
      {
        x: 0,
        opacity: 1,
        scale: 1,
        rotateY: 0,
        duration: 0.65,
        ease: "power4.out",
      }
    );

    gsap.fromTo(
      activeSlide.querySelector("img"),
      { scale: 1.08 },
      {
        scale: 1,
        duration: 1.15,
        ease: "power3.out",
      }
    );
  });
}

function goToSlide(index, direction = null) {
  if (index === currentSlide.value) return;

  const old = currentSlide.value;
  currentSlide.value = index;

  const resolvedDirection =
    direction ?? (index > old || (old === heroSlides.length - 1 && index === 0) ? 1 : -1);

  animateSlide(resolvedDirection);
  restartSlider();
}

function nextSlide() {
  goToSlide((currentSlide.value + 1) % heroSlides.length, 1);
}

function prevSlide() {
  goToSlide(
    (currentSlide.value - 1 + heroSlides.length) % heroSlides.length,
    -1
  );
}

function startSlider() {
  clearInterval(sliderTimer);

  sliderTimer = setInterval(() => {
    if (!sliderPaused && !dragging) {
      nextSlide();
    }
  }, 3800);
}

function restartSlider() {
  if (!sliderPaused) {
    startSlider();
  }
}

function pauseSlider() {
  sliderPaused = true;
  clearInterval(sliderTimer);
}

function resumeSlider() {
  sliderPaused = false;
  startSlider();
}

function startDrag(event) {
  if (event.pointerType === "mouse" && event.button !== 0) return;

  dragging = true;
  dragDistance = 0;
  dragStartX = event.clientX;
  event.currentTarget?.setPointerCapture?.(event.pointerId);
  pauseSlider();
}

function onDrag(event) {
  if (!dragging) return;

  dragDistance = event.clientX - dragStartX;

  const activeCard = pageRoot.value?.querySelector(".hero-slide.active .slide-card");

  if (activeCard) {
    gsap.set(activeCard, {
      x: dragDistance * 0.22,
      rotateY: dragDistance * -0.015,
    });
  }
}

function endDrag(event) {
  if (!dragging) return;

  const distance = dragDistance;
  dragging = false;

  const activeCard = pageRoot.value?.querySelector(".hero-slide.active .slide-card");

  if (activeCard) {
    gsap.to(activeCard, {
      x: 0,
      rotateY: 0,
      duration: 0.35,
      ease: "power3.out",
    });
  }

  if (Math.abs(distance) > 55) {
    if (distance < 0) nextSlide();
    else prevSlide();
  } else {
    resumeSlider();
  }

  dragDistance = 0;
  event.currentTarget?.releasePointerCapture?.(event.pointerId);
}

function selectService(id) {
  selectedService.value = id;
  step.value = 1;

  document.querySelector("#project")?.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
}

let ctx;

onMounted(async () => {
  await nextTick();
  startSlider();

  ctx = gsap.context(() => {
    // -------------------------------------------------
    // Scroll progress bar
    // -------------------------------------------------
    gsap.to(".scroll-progress", {
      scaleX: 1,
      ease: "none",
      scrollTrigger: {
        trigger: document.documentElement,
        start: "top top",
        end: "bottom bottom",
        scrub: 0.15,
      },
    });

    // -------------------------------------------------
    // HERO: fast, premium entrance
    // -------------------------------------------------
    const heroTl = gsap.timeline({
      defaults: {
        ease: "power4.out",
      },
    });

    heroTl
      .from(".hero-badge", {
        y: 25,
        opacity: 0,
        duration: 0.45,
      })
      .from(
        ".hero-copy h1",
        {
          y: 75,
          opacity: 0,
          scale: 0.94,
          duration: 0.75,
        },
        "-=0.2"
      )
      .from(
        ".hero-description",
        {
          y: 35,
          opacity: 0,
          duration: 0.5,
        },
        "-=0.4"
      )
      .from(
        ".hero-benefit",
        {
          y: 30,
          opacity: 0,
          stagger: 0.08,
          duration: 0.45,
        },
        "-=0.3"
      )
      .from(
        ".hero-actions > *",
        {
          y: 25,
          opacity: 0,
          stagger: 0.08,
          duration: 0.4,
        },
        "-=0.3"
      )
      .from(
        ".hero-slider",
        {
          x: 110,
          opacity: 0,
          scale: 0.92,
          duration: 0.9,
        },
        "-=0.85"
      )
      .from(
        ".slider-side-card",
        {
          x: 55,
          opacity: 0,
          duration: 0.5,
        },
        "-=0.35"
      );

    // -------------------------------------------------
    // HERO parallax / cinematic movement
    // -------------------------------------------------
    gsap.to(".hero-slider", {
      yPercent: 8,
      scale: 1.025,
      ease: "none",
      scrollTrigger: {
        trigger: ".hero-section",
        start: "top top",
        end: "bottom top",
        scrub: 0.55,
      },
    });

    gsap.to(".hero-copy", {
      yPercent: 18,
      opacity: 0.38,
      ease: "none",
      scrollTrigger: {
        trigger: ".hero-section",
        start: "30% top",
        end: "bottom top",
        scrub: 0.5,
      },
    });

    // -------------------------------------------------
    // Generic section headings: dramatic rise
    // -------------------------------------------------
    gsap.utils.toArray(".section-heading").forEach((heading) => {
      gsap.from(heading, {
        y: 80,
        opacity: 0,
        scale: 0.96,
        duration: 0.65,
        ease: "power4.out",
        scrollTrigger: {
          trigger: heading,
          start: "top 88%",
          toggleActions: "play none none reverse",
        },
      });
    });

    // -------------------------------------------------
    // Motivation strip - slides apart and together
    // -------------------------------------------------
    gsap.from(".motivation-layout > *", {
      y: 65,
      opacity: 0,
      stagger: 0.08,
      duration: 0.55,
      ease: "power3.out",
      scrollTrigger: {
        trigger: ".motivation-strip",
        start: "top 85%",
        toggleActions: "play none none reverse",
      },
    });

    // -------------------------------------------------
    // Service cards pop in quickly + scrub motion
    // -------------------------------------------------
    gsap.from(".service-card", {
      y: 95,
      opacity: 0,
      scale: 0.9,
      rotate: (index) => (index % 2 ? 2.5 : -2.5),
      stagger: 0.07,
      duration: 0.58,
      ease: "back.out(1.3)",
      scrollTrigger: {
        trigger: ".services-grid",
        start: "top 82%",
        toggleActions: "play none none reverse",
      },
    });

    gsap.to(".service-card", {
      y: (index) => (index % 2 ? -22 : 22),
      ease: "none",
      scrollTrigger: {
        trigger: ".services-section",
        start: "top bottom",
        end: "bottom top",
        scrub: 1,
      },
    });

    // -------------------------------------------------
    // Infinite marquee via GSAP
    // -------------------------------------------------
    gsap.to(".marquee-track", {
      xPercent: -50,
      duration: 18,
      repeat: -1,
      ease: "none",
    });

    // -------------------------------------------------
    // Devices: staged entrance then scrub movement
    // -------------------------------------------------
    const deviceTl = gsap.timeline({
      scrollTrigger: {
        trigger: ".device-section",
        start: "top 72%",
        toggleActions: "play none none reverse",
      },
    });

    deviceTl
      .from(".device-copy", {
        x: -80,
        opacity: 0,
        duration: 0.6,
        ease: "power4.out",
      })
      .from(
        ".desktop-frame",
        {
          x: 120,
          opacity: 0,
          scale: 0.82,
          rotationY: -12,
          duration: 0.75,
          ease: "power4.out",
        },
        "-=0.35"
      )
      .from(
        ".tablet-frame",
        {
          x: 110,
          y: 80,
          opacity: 0,
          scale: 0.72,
          duration: 0.55,
          ease: "back.out(1.5)",
        },
        "-=0.35"
      )
      .from(
        ".phone-frame",
        {
          x: -100,
          y: 90,
          opacity: 0,
          scale: 0.68,
          duration: 0.55,
          ease: "back.out(1.5)",
        },
        "-=0.45"
      )
      .from(
        ".responsive-label",
        {
          y: 35,
          opacity: 0,
          scale: 0.85,
          duration: 0.4,
        },
        "-=0.25"
      );

    gsap.to(".desktop-frame", {
      y: -70,
      rotateY: 4,
      ease: "none",
      scrollTrigger: {
        trigger: ".device-section",
        start: "top bottom",
        end: "bottom top",
        scrub: 0.8,
      },
    });

    gsap.to(".tablet-frame", {
      y: -120,
      rotate: -4,
      ease: "none",
      scrollTrigger: {
        trigger: ".device-section",
        start: "top bottom",
        end: "bottom top",
        scrub: 1,
      },
    });

    gsap.to(".phone-frame", {
      y: -90,
      rotate: 5,
      ease: "none",
      scrollTrigger: {
        trigger: ".device-section",
        start: "top bottom",
        end: "bottom top",
        scrub: 0.9,
      },
    });

    // -------------------------------------------------
    // Business value cards
    // -------------------------------------------------
    gsap.from(".value-card", {
      y: 90,
      opacity: 0,
      scale: 0.88,
      stagger: 0.07,
      duration: 0.55,
      ease: "back.out(1.25)",
      scrollTrigger: {
        trigger: ".value-grid",
        start: "top 82%",
        toggleActions: "play none none reverse",
      },
    });

    // -------------------------------------------------
    // Showcase: image/copy counter-parallax
    // -------------------------------------------------
    gsap.from(".showcase-image", {
      x: -100,
      opacity: 0,
      scale: 0.92,
      duration: 0.7,
      ease: "power4.out",
      scrollTrigger: {
        trigger: ".showcase-section",
        start: "top 75%",
        toggleActions: "play none none reverse",
      },
    });

    gsap.from(".showcase-copy", {
      x: 100,
      opacity: 0,
      duration: 0.7,
      ease: "power4.out",
      scrollTrigger: {
        trigger: ".showcase-section",
        start: "top 75%",
        toggleActions: "play none none reverse",
      },
    });

    gsap.to(".showcase-image img", {
      yPercent: -12,
      scale: 1.08,
      ease: "none",
      scrollTrigger: {
        trigger: ".showcase-section",
        start: "top bottom",
        end: "bottom top",
        scrub: 0.7,
      },
    });

    // -------------------------------------------------
    // Project builder: strong reveal
    // -------------------------------------------------
    gsap.from(".project-copy", {
      x: -90,
      opacity: 0,
      duration: 0.65,
      ease: "power4.out",
      scrollTrigger: {
        trigger: ".project-section",
        start: "top 72%",
        toggleActions: "play none none reverse",
      },
    });

    gsap.from(".project-card", {
      x: 100,
      opacity: 0,
      scale: 0.91,
      rotate: 2,
      duration: 0.75,
      ease: "power4.out",
      scrollTrigger: {
        trigger: ".project-section",
        start: "top 72%",
        toggleActions: "play none none reverse",
      },
    });

    // -------------------------------------------------
    // Process cards - left / center / right
    // -------------------------------------------------
    gsap.from(".process-card", {
      x: (index) => (index === 0 ? -100 : index === 2 ? 100 : 0),
      y: (index) => (index === 1 ? 85 : 0),
      opacity: 0,
      scale: 0.9,
      stagger: 0.08,
      duration: 0.6,
      ease: "power4.out",
      scrollTrigger: {
        trigger: ".process-grid",
        start: "top 82%",
        toggleActions: "play none none reverse",
      },
    });

    // -------------------------------------------------
    // Final CTA zoom-in
    // -------------------------------------------------
    gsap.from(".final-cta", {
      y: 100,
      opacity: 0,
      scale: 0.88,
      duration: 0.7,
      ease: "power4.out",
      scrollTrigger: {
        trigger: ".final-cta-section",
        start: "top 82%",
        toggleActions: "play none none reverse",
      },
    });

    // Refresh after fonts/images settle.
    setTimeout(() => ScrollTrigger.refresh(), 250);
  }, pageRoot.value);
});

onBeforeUnmount(() => {
  clearInterval(sliderTimer);
  ctx?.revert();
  ScrollTrigger.getAll().forEach((trigger) => trigger.kill());
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700;800&display=swap");
.web-service-page {
  --black: #080808;
  --black-2: #111111;
  --dark: #171717;
  --gray-900: #232323;
  --gray-800: #383838;
  --gray-700: #5f5f5f;
  --gray-600: #737373;
  --gray-500: #909090;
  --gray-400: #b1b1b1;
  --gray-300: #d5d5d5;
  --gray-200: #e9e9e9;
  --gray-100: #f5f5f5;
  --gray-50: #fafafa;
  --white: #ffffff;

  overflow: hidden;
  background: #ffffff;
  color: var(--black);
  font-family: "Outfit", "Segoe UI", Arial, sans-serif;
}

.web-service-page *,
.web-service-page *::before,
.web-service-page *::after {
  box-sizing: border-box;
}

.site-container {
  width: min(1240px, calc(100% - 48px));
  margin: 0 auto;
}

/* =====================================================
   GSAP ANIMATION BASE
===================================================== */

.reveal,
.reveal-card,
.reveal-left,
.reveal-right {
  opacity: 1;
  transform: none;
  filter: none;
}

.scroll-progress {
  position: fixed;
  z-index: 99999;
  top: 0;
  left: 0;
  width: 100%;
  height: 5px;
  transform: scaleX(0);
  transform-origin: left center;
  background: #111111;
  pointer-events: none;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.16);
}

.hero-copy h1,
.motivation-layout h2,
.section-heading h2,
.device-copy h2,
.showcase-copy h2,
.project-copy h2,
.final-copy h2,
.form-step h3,
.service-card h3,
.value-card h3,
.process-card h3 {
  font-family: "Sora", "Segoe UI", Arial, sans-serif;
  text-wrap: balance;
}

/* =====================================================
   HERO
===================================================== */

.hero-section {
  position: relative;
  min-height: calc(100vh - 76px);
  display: flex;
  align-items: center;
  padding: 105px 0 95px;
  overflow: hidden;
  background:
    radial-gradient(circle at 78% 18%, rgba(24, 55, 96, 0.10), transparent 28%),
    radial-gradient(circle at 8% 78%, rgba(0, 0, 0, 0.045), transparent 28%),
    linear-gradient(180deg, #ffffff 0%, #f7f9fc 100%);
  color: #111111;
}

.hero-grid-pattern,
.project-grid-bg,
.final-grid {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background-image:
    linear-gradient(rgba(0, 0, 0, 0.035) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0, 0, 0, 0.035) 1px, transparent 1px);
  background-size: 52px 52px;
  mask-image: linear-gradient(to bottom, #000, transparent 92%);
}

.hero-glow {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(2px);
}

.hero-glow-one {
  width: 570px;
  height: 570px;
  top: -300px;
  right: -180px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow:
    0 0 0 70px rgba(255, 255, 255, 0.012),
    0 0 0 140px rgba(255, 255, 255, 0.007);
}

.hero-glow-two {
  width: 390px;
  height: 390px;
  left: -230px;
  bottom: -260px;
  border: 1px solid rgba(255, 255, 255, 0.05);
  box-shadow:
    0 0 0 55px rgba(255, 255, 255, 0.008),
    0 0 0 110px rgba(255, 255, 255, 0.005);
}

.hero-layout {
  position: relative;
  z-index: 2;
  display: grid;
  grid-template-columns: 0.88fr 1.12fr;
  gap: 64px;
  align-items: center;
}

.hero-badge,
.section-label {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 800;
  letter-spacing: 1.6px;
}

.hero-badge {
  padding: 9px 13px;
  border: 1px solid #dfe3e8;
  border-radius: 100px;
  background: rgba(255, 255, 255, 0.92);
  color: #555b63;
  backdrop-filter: blur(8px);
}

.badge-dot,
.section-label > span {
  width: 7px;
  height: 7px;
  flex: 0 0 7px;
  border-radius: 50%;
  background: currentColor;
}

.badge-dot {
  color: #111111;
  box-shadow: 0 0 0 5px rgba(0, 0, 0, 0.07);
  animation: pulseDot 1.8s ease-in-out infinite;
}

.hero-copy h1 {
  max-width: 820px;
  margin: 30px 0 0;
  font-size: clamp(72px, 6.8vw, 108px);
  font-weight: 800;
  line-height: 1.04;
  letter-spacing: -2.2px;
}

.hero-copy h1 span,
.section-heading h2 span,
.device-copy h2 span,
.value-section h2 span,
.showcase-copy h2 span,
.project-copy h2 span,
.final-copy h2 span {
  display: block;
  color: #6f7782;
}

.hero-description {
  max-width: 690px;
  margin: 30px 0 0;
  color: #555e68;
  font-size: 20px;
  line-height: 1.72;
  font-weight: 500;
}

.hero-benefits {
  display: flex;
  flex-wrap: wrap;
  gap: 17px;
  margin-top: 31px;
}

.hero-benefit {
  display: flex;
  align-items: center;
  gap: 9px;
}

.benefit-icon {
  width: 34px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e0e4e8;
  border-radius: 9px;
  background: #ffffff;
  color: #111111;
  font-size: 13px;
}

.hero-benefit strong,
.hero-benefit small,
.hero-note strong,
.hero-note small {
  display: block;
}

.hero-benefit strong {
  font-size: 13px;
  line-height: 1.35;
}

.hero-benefit small {
  margin-top: 3px;
  color: #777f88;
  font-size: 11px;
  line-height: 1.4;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 11px;
  margin-top: 34px;
}

.primary-button,
.outline-button,
.showcase-button,
.next-button,
.back-button,
.send-button,
.final-button {
  text-decoration: none;
}

.primary-button,
.outline-button {
  min-height: 56px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 11px;
  padding: 0 24px;
  border-radius: 9px;
  font-size: 14px;
  font-weight: 800;
  transition: 0.22s ease;
}

.primary-button {
  border: 1px solid #111111;
  background: #111111;
  color: #ffffff;
  box-shadow: 0 18px 40px rgba(0, 0, 0, 0.16);
}

.outline-button {
  border: 1px solid #d8dde3;
  background: rgba(255, 255, 255, 0.88);
  color: #111111;
}

.primary-button:hover,
.outline-button:hover {
  transform: translateY(-3px);
}

.primary-button i {
  transition: 0.25s ease;
}

.primary-button:hover i {
  transform: translate(3px, -3px);
}

.hero-note {
  display: flex;
  align-items: center;
  gap: 10px;
  max-width: 390px;
  margin-top: 28px;
  padding-top: 22px;
  border-top: 1px solid #e1e5e9;
}

.hero-note > i {
  color: #111111;
}

.hero-note strong {
  font-size: 13px;
}

.hero-note small {
  margin-top: 3px;
  color: #737b84;
  font-size: 11px;
}

/* HERO SLIDER - FULL IMAGE / NO CROP */

.hero-visual {
  position: relative;
  width: 100%;
  min-width: 0;
  min-height: 610px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-slider {
  position: relative;
  width: 100%;
  height: 590px;
  min-width: 0;
  overflow: hidden;

  border: 1px solid #dfe3e8;
  border-radius: 28px;

  background:
    radial-gradient(
      circle at 90% 10%,
      rgba(43, 77, 122, 0.11),
      transparent 30%
    ),
    linear-gradient(
      145deg,
      #f7f9fc 0%,
      #ffffff 100%
    );

  box-shadow:
    0 45px 100px rgba(21, 34, 50, 0.14),
    0 12px 30px rgba(21, 34, 50, 0.06);

  cursor: grab;
  user-select: none;
  touch-action: pan-y;
}

.hero-slider:active {
  cursor: grabbing;
}

.slider-stage {
  position: relative;
  width: 100%;
  height: 100%;
  perspective: 1300px;
}

.hero-slide {
  position: absolute;
  inset: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  padding: 35px 55px 72px;

  opacity: 0;
  visibility: hidden;
  pointer-events: none;
}

.hero-slide.active {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}

.slide-card {
  position: relative;

  width: 94%;
  height: 475px;

  display: flex;
  align-items: center;
  justify-content: center;

  overflow: hidden;

  border: 1px solid #d7dce2;
  border-radius: 18px;

  background: #ffffff;

  box-shadow:
    0 35px 75px rgba(0, 0, 0, 0.15),
    0 9px 22px rgba(0, 0, 0, 0.07);

  transform-origin: center center;
  will-change: transform, opacity;
}

/*
  IMPORTANT:
  contain = complete screenshot visible.
  cover   = screenshot gets cropped.
*/
.slide-card img {
  width: 100%;
  height: 100%;

  display: block;

  object-fit: contain;
  object-position: center center;

  background: #ffffff;

  pointer-events: none;
  user-select: none;
  -webkit-user-drag: none;
}

.slide-overlay {
  position: absolute;
  inset: 0;

  pointer-events: none;

  background:
    linear-gradient(
      180deg,
      transparent 68%,
      rgba(4, 8, 14, 0.08) 78%,
      rgba(4, 8, 14, 0.55) 100%
    );
}

.slide-meta {
  position: absolute;

  z-index: 3;

  right: 20px;
  bottom: 18px;
  left: 20px;

  display: flex;
  align-items: center;

  gap: 12px;

  color: #ffffff;

  pointer-events: none;
}

.slide-meta > span {
  width: 40px;
  height: 40px;

  flex: 0 0 40px;

  display: flex;
  align-items: center;
  justify-content: center;

  border: 1px solid rgba(255, 255, 255, 0.35);
  border-radius: 50%;

  background: rgba(0, 0, 0, 0.42);

  color: #ffffff;

  font-size: 11px;
  font-weight: 700;

  backdrop-filter: blur(8px);
}

.slide-meta div {
  min-width: 0;
}

.slide-meta strong,
.slide-meta small {
  display: block;
}

.slide-meta strong {
  font-family: "Sora", "Segoe UI", Arial, sans-serif;

  color: #ffffff;

  font-size: 17px;
  font-weight: 700;

  line-height: 1.3;
}

.slide-meta small {
  margin-top: 3px;

  color: rgba(255, 255, 255, 0.82);

  font-size: 11px;

  line-height: 1.4;
}

.slider-side-card {
  position: absolute;

  z-index: 15;

  top: 48px;
  right: 18px;

  width: 175px;

  padding: 19px 17px;

  border: 1px solid #d9dee4;
  border-radius: 14px;

  background: rgba(255, 255, 255, 0.95);

  box-shadow:
    0 18px 42px rgba(23, 38, 56, 0.12);

  backdrop-filter: blur(13px);

  pointer-events: none;
}

.slider-side-card span,
.slider-side-card strong,
.slider-side-card small {
  display: block;
}

.slider-side-card > span {
  color: #8a929c;

  font-size: 10px;
  font-weight: 800;

  letter-spacing: 1.4px;
}

.slider-side-card strong {
  margin-top: 7px;

  color: #111111;

  font-family: "Sora", "Segoe UI", Arial, sans-serif;

  font-size: 18px;
  font-weight: 700;

  line-height: 1.25;
}

.slider-side-card small {
  margin-top: 9px;

  color: #777f88;

  font-size: 11px;

  line-height: 1.55;
}

.slider-arrow {
  position: absolute;

  z-index: 20;

  top: 50%;

  width: 46px;
  height: 46px;

  display: flex;
  align-items: center;
  justify-content: center;

  transform: translateY(-50%);

  padding: 0;

  border: 1px solid #d9dee4;
  border-radius: 50%;

  outline: none;

  background: rgba(255, 255, 255, 0.96);

  color: #111111;

  font-size: 17px;

  cursor: pointer;

  box-shadow:
    0 12px 28px rgba(0, 0, 0, 0.09);

  transition:
    transform 0.22s ease,
    background 0.22s ease,
    color 0.22s ease,
    box-shadow 0.22s ease;
}

.slider-arrow:hover {
  background: #111111;

  color: #ffffff;

  transform:
    translateY(-50%)
    scale(1.08);

  box-shadow:
    0 15px 34px rgba(0, 0, 0, 0.16);
}

.slider-arrow:active {
  transform:
    translateY(-50%)
    scale(0.95);
}

.slider-prev {
  left: 15px;
}

.slider-next {
  right: 15px;
}

.slider-bottom {
  position: absolute;

  z-index: 20;

  right: 28px;
  bottom: 20px;
  left: 28px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  gap: 20px;
}

.slider-dots {
  display: flex;
  align-items: center;

  gap: 8px;
}

.slider-dots button {
  width: 9px;
  height: 9px;

  padding: 0;

  border: 0;
  border-radius: 50%;

  outline: none;

  background: #c8cdd3;

  cursor: pointer;

  transition:
    width 0.25s ease,
    transform 0.25s ease,
    border-radius 0.25s ease,
    background 0.25s ease;
}

.slider-dots button:hover {
  background: #7d858e;

  transform: scale(1.15);
}

.slider-dots button.active {
  width: 30px;

  border-radius: 20px;

  background: #111111;

  transform: none;
}

.drag-note {
  display: flex;
  align-items: center;

  gap: 7px;

  color: #7e8791;

  font-size: 11px;
  font-weight: 600;

  pointer-events: none;
}

.drag-note i {
  color: #111111;

  font-size: 14px;
}

.scroll-down {
  position: absolute;
  z-index: 3;
  left: 50%;
  bottom: 18px;
  display: flex;
  align-items: center;
  gap: 8px;
  transform: translateX(-50%);
  color: #626b75;
  font-size: 7px;
  font-weight: 800;
  letter-spacing: 1.4px;
  text-decoration: none;
}

.scroll-down i {
  animation: arrowBounce 0.8s ease-in-out infinite;
}

/* =====================================================
   MOTIVATION
===================================================== */

.motivation-strip {
  padding: 66px 0;
  border-top: 1px solid #eeeeee;
  border-bottom: 1px solid #eeeeee;
  background: #ffffff;
}

.motivation-layout {
  display: grid;
  grid-template-columns: 45px 1.05fr 0.95fr;
  gap: 35px;
  align-items: center;
}

.section-no {
  color: #a6a6a6;
  font-size: 9px;
  font-weight: 900;
}

.mini-label {
  color: #777777;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 1.5px;
}

.motivation-layout h2 {
  margin: 16px 0 0;
  font-size: clamp(46px, 4.7vw, 66px);
  line-height: 1.06;
  letter-spacing: -1.7px;
}

.motivation-layout h2 span {
  display: block;
  color: #9a9a9a;
}

.motivation-layout p {
  max-width: 590px;
  margin: 0;
  color: #747474;
  font-size: 17px;
  line-height: 1.8;
}

/* =====================================================
   COMMON
===================================================== */

.services-section,
.value-section,
.process-section {
  padding: 105px 0;
}

.section-heading {
  display: grid;
  grid-template-columns: 1fr 0.72fr;
  gap: 70px;
  align-items: end;
  margin-bottom: 50px;
}

.section-label {
  color: #737373;
}

.section-label > span {
  color: #111111;
}

.section-heading h2,
.device-copy h2,
.showcase-copy h2,
.project-copy h2,
.final-copy h2 {
  margin: 18px 0 0;
  font-size: clamp(58px, 5.8vw, 82px);
  font-weight: 800;
  line-height: 1.08;
  letter-spacing: -1.6px;
}

.section-heading > p {
  max-width: 430px;
  margin: 0 0 5px auto;
  color: #6f6f6f;
  font-size: 17px;
  line-height: 1.8;
}

/* =====================================================
   SERVICES
===================================================== */

.services-section {
  position: relative;
  background:
    radial-gradient(circle at 88% 12%, rgba(25, 54, 90, 0.08), transparent 24%),
    #f7f8fa;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.service-card {
  position: relative;
  min-height: 390px;
  display: flex;
  flex-direction: column;
  padding: 26px;
  border: 1px solid #e2e2e2;
  border-radius: 16px;
  background: #ffffff;
  color: #111111;
  text-align: left;
  cursor: pointer;
  overflow: hidden;
  transition:
    opacity 0.8s ease,
    transform 0.8s ease,
    border-color 0.3s ease,
    box-shadow 0.3s ease,
    color 0.3s ease;
}

.service-card::after {
  content: "";
  position: absolute;
  inset: auto 0 0 0;
  height: 0;
  background: #0d1014;
  transition: height 0.32s ease;
}

.service-card > * {
  position: relative;
  z-index: 2;
}

.service-card:hover::after,
.service-card.selected::after {
  height: 100%;
}

.service-card:hover,
.service-card.selected {
  border-color: #171717;
  color: #ffffff;
  transform: translateY(-12px) scale(1.015);
  box-shadow: 0 32px 70px rgba(0, 0, 0, 0.15);
}

.service-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.service-card-top span {
  color: #aaaaaa;
  font-size: 8px;
  font-weight: 900;
}

.service-card-top i {
  font-size: 17px;
}

.service-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 43px;
  border-radius: 12px;
  background: #f1f1f1;
  font-size: 18px;
  transition: 0.3s ease;
}

.service-card:hover .service-icon,
.service-card.selected .service-icon {
  background: #20242a;
}

.service-card h3 {
  margin: 25px 0 0;
  font-size: 29px;
  line-height: 1.15;
  letter-spacing: -0.8px;
}

.service-card p {
  margin: 13px 0 0;
  color: #686868;
  font-size: 16px;
  line-height: 1.72;
  transition: color 0.3s ease;
}

.service-card:hover p,
.service-card.selected p {
  color: #7f8790;
}

.service-arrow {
  margin-top: auto;
  padding-top: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #8d8d8d;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.5px;
}

/* =====================================================
   MOVING MARQUEE
===================================================== */

.marquee-section {
  overflow: hidden;
  padding: 24px 0;
  border-top: 1px solid #20242a;
  border-bottom: 1px solid #20242a;
  background: #0a0d11;
  color: #ffffff;
}

.marquee-track {
  width: max-content;
  display: flex;
  will-change: transform;
}

.marquee-group {
  display: flex;
  align-items: center;
  flex-shrink: 0;
  gap: 34px;
  padding-right: 34px;
}

.marquee-group span {
  font-family: "Sora", "Segoe UI", Arial, sans-serif;
  font-size: clamp(27px, 2.5vw, 40px);
  letter-spacing: -1px;
  white-space: nowrap;
}

.marquee-group i {
  width: 8px;
  height: 8px;
  flex: 0 0 8px;
  border-radius: 50%;
  background: #717a84;
}

/* =====================================================
   DEVICES
===================================================== */

.device-section {
  padding: 115px 0;
  background: #080a0d;
  color: #ffffff;
}

.device-layout {
  display: grid;
  grid-template-columns: 0.77fr 1.23fr;
  gap: 80px;
  align-items: center;
}

.light-label {
  color: #747d87;
}

.light-label > span {
  color: #ffffff;
}

.device-copy > p,
.project-copy > p {
  max-width: 520px;
  margin: 25px 0 0;
  color: #a4abb4;
  font-size: 17px;
  line-height: 1.78;
}

.device-points {
  margin-top: 33px;
}

.device-points > div {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 0;
  border-bottom: 1px solid #252b32;
  color: #a2a8af;
  font-size: 14px;
}

.device-points i {
  color: #ffffff;
}

.devices-preview {
  position: relative;
  min-height: 520px;
}

.desktop-frame {
  position: absolute;
  width: 82%;
  height: 365px;
  top: 25px;
  right: 2%;
  padding: 12px 12px 17px;
  border: 1px solid #343b43;
  border-radius: 19px;
  background: #14181d;
  box-shadow: 0 42px 95px rgba(0, 0, 0, 0.48);
  transform: perspective(1100px) rotateY(-4deg);
}

.desktop-top {
  height: 18px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 4px;
}

.desktop-top span {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: #555e67;
}

.desktop-screen {
  height: calc(100% - 18px);
  overflow: hidden;
  border-radius: 10px;
  background: #ffffff;
  color: #111111;
}

.screen-nav {
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 18px;
  border-bottom: 1px solid #eeeeee;
}

.screen-nav strong {
  font-size: 9px;
}

.screen-nav strong span {
  color: #999999;
}

.screen-nav > div {
  display: flex;
  gap: 7px;
}

.screen-nav > div span {
  width: 28px;
  height: 4px;
  border-radius: 20px;
  background: #dedede;
}

.screen-content {
  min-height: 260px;
  display: grid;
  grid-template-columns: 1fr 0.82fr;
  gap: 20px;
  align-items: center;
  padding: 24px;
  background: #f4f4f4;
}

.screen-copy small {
  color: #aaaaaa;
  font-size: 6px;
  font-weight: 900;
}

.screen-title,
.screen-text {
  width: 88%;
  height: 14px;
  margin-top: 15px;
  border-radius: 20px;
  background: #111111;
}

.screen-title.short {
  width: 62%;
  margin-top: 6px;
}

.screen-text {
  height: 5px;
  margin-top: 16px;
  background: #cccccc;
}

.screen-text.short {
  width: 60%;
  margin-top: 6px;
}

.screen-copy button {
  width: 72px;
  height: 26px;
  margin-top: 18px;
  border: 0;
  border-radius: 5px;
  background: #111111;
}

.screen-art {
  height: 170px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #dddddd;
  border-radius: 12px;
  background: #ffffff;
  color: #c1c1c1;
  font-size: 31px;
}

.tablet-frame {
  position: absolute;
  z-index: 3;
  width: 218px;
  height: 295px;
  right: 1%;
  bottom: 2px;
  padding: 9px;
  border: 1px solid #3c434b;
  border-radius: 20px;
  background: #14181d;
  box-shadow: 0 30px 70px rgba(0, 0, 0, 0.5);
  transform: rotate(5deg);
}

.tablet-content,
.phone-content {
  height: 100%;
  padding: 35px 16px;
  border-radius: 12px;
  background: #f6f6f6;
  color: #111111;
}

.tablet-content strong,
.phone-content strong {
  font-size: 8px;
}

.tablet-content > span,
.phone-content > span {
  display: block;
  width: 92%;
  height: 11px;
  margin-top: 25px;
  border-radius: 10px;
  background: #111111;
}

.tablet-content > span + span,
.phone-content > span + span {
  margin-top: 6px;
}

.tablet-content > span.short,
.phone-content > span.short {
  width: 58%;
  height: 5px;
  background: #cccccc;
}

.tablet-content button,
.phone-content button {
  width: 64px;
  height: 24px;
  margin-top: 22px;
  border: 0;
  border-radius: 5px;
  background: #111111;
}

.phone-frame {
  position: absolute;
  z-index: 4;
  width: 143px;
  height: 278px;
  left: 3%;
  bottom: 0;
  padding: 8px;
  border: 1px solid #3b4249;
  border-radius: 27px;
  background: #13171c;
  box-shadow: 0 30px 70px rgba(0, 0, 0, 0.5);
  transform: rotate(-4deg);
}

.phone-notch {
  position: absolute;
  z-index: 3;
  width: 54px;
  height: 13px;
  top: 8px;
  left: 50%;
  transform: translateX(-50%);
  border-radius: 0 0 9px 9px;
  background: #111111;
}

.phone-content {
  padding: 34px 12px;
  border-radius: 20px;
}

.phone-content > span {
  height: 9px;
}

.phone-content button {
  width: 52px;
  height: 22px;
}

.responsive-label {
  position: absolute;
  z-index: 6;
  right: 128px;
  bottom: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border: 1px solid #343b43;
  border-radius: 11px;
  background: rgba(18, 22, 27, 0.94);
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(10px);
}

.responsive-label > i {
  width: 31px;
  height: 31px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: #ffffff;
  color: #111111;
}

.responsive-label strong,
.responsive-label small {
  display: block;
}

.responsive-label strong {
  font-size: 9px;
}

.responsive-label small {
  margin-top: 2px;
  color: #777f88;
  font-size: 7px;
}

/* =====================================================
   VALUE
===================================================== */

.value-section {
  background: #ffffff;
}

.value-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.value-card {
  position: relative;
  min-height: 270px;
  padding: 27px;
  border: 1px solid #e4e4e4;
  border-radius: 16px;
  background: #fafafa;
  transition:
    opacity 0.8s ease,
    transform 0.8s ease,
    border-color 0.3s ease,
    box-shadow 0.3s ease;
}

.value-card:hover {
  border-color: #cccccc;
  box-shadow: 0 20px 48px rgba(0, 0, 0, 0.07);
  transform: translateY(-6px);
}

.value-icon {
  width: 46px;
  height: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: #111111;
  color: #ffffff;
  font-size: 17px;
}

.value-number {
  position: absolute;
  top: 28px;
  right: 28px;
  color: #ababab;
  font-size: 8px;
  font-weight: 900;
}

.value-card h3 {
  margin: 42px 0 0;
  font-size: 24px;
  line-height: 1.25;
}

.value-card p {
  margin: 11px 0 0;
  color: #6f6f6f;
  font-size: 15px;
  line-height: 1.7;
}

/* =====================================================
   SHOWCASE
===================================================== */

.showcase-section {
  padding: 110px 0;
  background: #f7f7f7;
  border-top: 1px solid #eeeeee;
  border-bottom: 1px solid #eeeeee;
}

.showcase-layout {
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 75px;
  align-items: center;
}

.showcase-image {
  position: relative;
  height: 590px;
  overflow: hidden;
  border: 1px solid #dddddd;
  border-radius: 20px;
  background: #111111;
  box-shadow: 0 35px 80px rgba(0, 0, 0, 0.14);
}

.showcase-image img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
  object-position: top center;
}

.showcase-fade {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.2), transparent 30%);
}

.showcase-copy > p {
  max-width: 540px;
  margin: 24px 0 0;
  color: #6f6f6f;
  font-size: 17px;
  line-height: 1.8;
}

.showcase-list {
  margin-top: 31px;
}

.showcase-list > div {
  display: grid;
  grid-template-columns: 34px 1fr;
  gap: 13px;
  padding: 15px 0;
  border-bottom: 1px solid #dddddd;
}

.showcase-list > div > strong {
  color: #999999;
  font-size: 8px;
}

.showcase-list b,
.showcase-list small {
  display: block;
}

.showcase-list b {
  font-size: 15px;
}

.showcase-list small {
  margin-top: 3px;
  color: #8b8b8b;
  font-size: 13px;
  line-height: 1.6;
}

.showcase-button {
  min-height: 54px;
  display: inline-flex;
  align-items: center;
  gap: 11px;
  margin-top: 30px;
  padding: 0 22px;
  border-radius: 9px;
  background: #111111;
  color: #ffffff;
  font-size: 10px;
  font-weight: 800;
  transition: 0.28s ease;
}

.showcase-button:hover {
  transform: translateY(-3px);
}

/* =====================================================
   PROJECT
===================================================== */

.project-section {
  position: relative;
  padding: 115px 0;
  overflow: hidden;
  background: #080a0d;
  color: #ffffff;
}

.project-grid-bg {
  opacity: 0.3;
}

.project-layout {
  position: relative;
  z-index: 2;
  display: grid;
  grid-template-columns: 0.74fr 1.26fr;
  gap: 85px;
  align-items: center;
}

.project-benefits {
  margin-top: 33px;
}

.project-benefits > div {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 13px 0;
  border-bottom: 1px solid #252b32;
}

.project-benefits > div > i {
  width: 36px;
  height: 36px;
  flex: 0 0 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 9px;
  background: #171b20;
  color: #ffffff;
}

.project-benefits strong,
.project-benefits small {
  display: block;
}

.project-benefits strong {
  font-size: 14px;
}

.project-benefits small {
  margin-top: 3px;
  color: #707985;
  font-size: 11px;
}

.project-card {
  min-height: 585px;
  padding: 34px;
  border: 1px solid #2d343c;
  border-radius: 20px;
  background: #13171c;
  box-shadow: 0 35px 85px rgba(0, 0, 0, 0.35);
}

.form-progress {
  display: flex;
  align-items: center;
  margin-bottom: 42px;
}

.form-progress-step {
  flex: 1;
  display: flex;
  align-items: center;
}

.form-progress-step > span {
  width: 28px;
  height: 28px;
  flex: 0 0 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #343b43;
  border-radius: 50%;
  color: #626b75;
  font-size: 8px;
  font-weight: 900;
}

.form-progress-step > div {
  flex: 1;
  height: 1px;
  background: #2a3037;
}

.form-progress-step:last-child > div {
  display: none;
}

.form-progress-step.active > span {
  border-color: #ffffff;
  background: #ffffff;
  color: #111111;
}

.form-progress-step.active > div {
  background: #626b75;
}

.step-label {
  color: #8a939d;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: 1.3px;
}

.form-step h3 {
  margin: 14px 0 0;
  font-size: 44px;
  line-height: 1.08;
  letter-spacing: -1.2px;
}

.form-step > p {
  margin: 8px 0 0;
  color: #9aa3ad;
  font-size: 15px;
  line-height: 1.7;
}

.form-service-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
  margin-top: 24px;
}

.form-service-grid > button {
  min-height: 84px;
  display: grid;
  grid-template-columns: 26px 1fr auto;
  gap: 10px;
  align-items: center;
  padding: 14px;
  border: 1px solid #2d343c;
  border-radius: 10px;
  background: #191e24;
  color: #858e98;
  text-align: left;
  cursor: pointer;
  transition: 0.25s ease;
}

.form-service-grid > button:hover,
.form-service-grid > button.active {
  border-color: #68717b;
  background: #20262d;
  color: #ffffff;
}

.form-service-grid > button > i:first-child {
  font-size: 17px;
}

.form-service-grid > button > span {
  font-size: 13px;
  font-weight: 800;
}

.form-check {
  font-size: 13px;
}

.goal-list {
  margin-top: 23px;
}

.goal-list > button {
  width: 100%;
  min-height: 74px;
  display: grid;
  grid-template-columns: 40px 1fr auto;
  gap: 12px;
  align-items: center;
  padding: 12px;
  margin-bottom: 7px;
  border: 1px solid #2d343c;
  border-radius: 10px;
  background: #191e24;
  color: #848d97;
  text-align: left;
  cursor: pointer;
}

.goal-list > button:hover,
.goal-list > button.active {
  border-color: #68717b;
  background: #20262d;
}

.goal-icon {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 9px;
  background: #242a31;
  color: #ffffff;
}

.goal-list strong,
.goal-list small {
  display: block;
}

.goal-list strong {
  color: #eeeeee;
  font-size: 10px;
}

.goal-list small {
  margin-top: 3px;
  color: #6c7580;
  font-size: 7px;
}

.next-button,
.back-button,
.send-button {
  min-height: 50px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  padding: 0 19px;
  border-radius: 9px;
  font-size: 9px;
  font-weight: 800;
  cursor: pointer;
  transition: 0.25s ease;
}

.next-button,
.send-button {
  margin-top: 24px;
  border: 1px solid #ffffff;
  background: #ffffff;
  color: #111111;
}

.back-button {
  margin-top: 24px;
  border: 1px solid #343b43;
  background: transparent;
  color: #818a94;
}

.next-button:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.form-step textarea {
  width: 100%;
  margin-top: 22px;
  padding: 14px;
  resize: vertical;
  outline: none;
  border: 1px solid #303740;
  border-radius: 10px;
  background: #191e24;
  color: #ffffff;
  font: inherit;
  font-size: 10px;
  line-height: 1.6;
}

.form-step textarea:focus {
  border-color: #68717b;
}

.project-summary {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
  margin-top: 12px;
}

.project-summary > div {
  padding: 12px;
  border: 1px solid #2a3037;
  border-radius: 9px;
  background: #171c21;
}

.project-summary small,
.project-summary strong {
  display: block;
}

.project-summary small {
  color: #626b75;
  font-size: 6px;
  letter-spacing: 0.8px;
}

.project-summary strong {
  margin-top: 4px;
  color: #dddddd;
  font-size: 9px;
}

/* =====================================================
   PROCESS
===================================================== */

.process-section {
  background: #fafafa;
}

.process-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.process-card {
  position: relative;
  min-height: 270px;
  padding: 27px;
  border: 1px solid #dfdfdf;
  border-radius: 16px;
  background: #ffffff;
}

.process-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: #111111;
  color: #ffffff;
  font-size: 17px;
}

.process-number {
  position: absolute;
  top: 27px;
  right: 27px;
  color: #aaaaaa;
  font-size: 8px;
  font-weight: 900;
}

.process-card h3 {
  margin: 42px 0 0;
  font-size: 32px;
  line-height: 1.12;
  letter-spacing: -0.8px;
}

.process-card p {
  margin: 13px 0 0;
  color: #6f6f6f;
  font-size: 15px;
  line-height: 1.7;
}

.process-arrow {
  position: absolute;
  z-index: 4;
  top: 50%;
  right: -22px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transform: translateY(-50%);
  border: 1px solid #dddddd;
  border-radius: 50%;
  background: #ffffff;
  color: #777777;
}

/* =====================================================
   FINAL CTA
===================================================== */

.final-cta-section {
  padding: 28px 0 105px;
  background: #fafafa;
}

.final-cta {
  position: relative;
  overflow: hidden;
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 60px;
  align-items: center;
  padding: 60px;
  border-radius: 22px;
  background:
    radial-gradient(circle at 85% 15%, rgba(43, 79, 125, 0.28), transparent 28%),
    #080a0d;
  color: #ffffff;
}

.final-grid {
  opacity: 0.18;
}

.final-cta > *:not(.final-grid) {
  position: relative;
  z-index: 2;
}

.final-kicker {
  color: #707985;
  font-size: 8px;
  font-weight: 900;
  letter-spacing: 1.25px;
}

.final-copy p {
  max-width: 640px;
  margin: 20px 0 0;
  color: #7c858f;
  font-size: 16px;
  line-height: 1.75;
}

.final-button {
  min-width: 260px;
  min-height: 86px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  padding: 0 24px;
  border-radius: 13px;
  background: #ffffff;
  color: #111111;
  transition: 0.3s ease;
}

.final-button:hover {
  transform: translateY(-4px);
}

.final-button span {
  font-size: 14px;
  font-weight: 800;
}

.final-button small {
  display: block;
  margin-top: 4px;
  color: #8b8b8b;
  font-size: 11px;
  font-weight: 500;
}

.final-button > i {
  font-size: 19px;
}

/* =====================================================
   KEYFRAMES
===================================================== */

@keyframes pulseDot {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }

  50% {
    opacity: 0.4;
    transform: scale(0.75);
  }
}

@keyframes floatingOne {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-8px);
  }
}

@keyframes floatingTwo {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(8px);
  }
}

@keyframes arrowBounce {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(5px);
  }
}

/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 1200px) {
  .hero-layout {
    gap: 42px;
  }

  .services-grid,
  .value-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 991px) {
  .site-container {
    width: min(100% - 40px, 850px);
  }

  .hero-section {
    min-height: auto;
    padding: 85px 0;
  }

  .hero-layout,
  .section-heading,
  .device-layout,
  .showcase-layout,
  .project-layout,
  .final-cta {
    grid-template-columns: 1fr;
  }

  .hero-layout {
    gap: 60px;
  }

  .hero-copy {
    max-width: 760px;
  }

  .hero-visual {
    min-height: 575px;
  }

  .motivation-layout {
    grid-template-columns: 35px 1fr;
  }

  .motivation-layout p {
    grid-column: 2;
  }

  .section-heading {
    gap: 22px;
  }

  .section-heading > p {
    margin-left: 0;
  }

  .device-layout,
  .project-layout {
    gap: 60px;
  }

  .device-copy,
  .project-copy {
    max-width: 700px;
  }

  .devices-preview {
    max-width: 800px;
    width: 100%;
    margin: 0 auto;
  }

  .showcase-image {
    height: 650px;
  }

  .final-cta {
    gap: 34px;
  }

  .final-button {
    max-width: 360px;
  }
}

@media (max-width: 767px) {
  .site-container {
    width: calc(100% - 30px);
  }

  .hero-section {
    padding: 72px 0 70px;
  }

  .hero-copy h1 {
    font-size: clamp(52px, 14vw, 72px);
    line-height: 1.02;
    letter-spacing: -2.4px;
  }

  .hero-description {
    font-size: 16px;
    line-height: 1.72;
  }

  .hero-benefits {
    flex-direction: column;
    gap: 11px;
  }

  .hero-actions {
    flex-direction: column;
  }

  .primary-button,
  .outline-button {
    width: 100%;
  }

  .hero-visual {
    min-height: auto;
    padding-bottom: 65px;
  }
.scroll-down {
    display: none;
  }

  .motivation-strip {
    padding: 54px 0;
  }

  .motivation-layout {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .motivation-layout p {
    grid-column: auto;
  }

  .services-section,
  .value-section,
  .process-section,
  .device-section,
  .showcase-section,
  .project-section {
    padding: 80px 0;
  }

  .section-heading h2,
  .device-copy h2,
  .showcase-copy h2,
  .project-copy h2,
  .final-copy h2 {
    font-size: clamp(40px, 11vw, 56px);
    line-height: 1.1;
    letter-spacing: -1.8px;
  }

  .services-grid,
  .value-grid,
  .process-grid,
  .form-service-grid,
  .project-summary {
    grid-template-columns: 1fr;
  }

  .service-card {
    min-height: 300px;
  }

  .devices-preview {
    min-height: 430px;
  }

  .desktop-frame {
    width: 94%;
    height: 300px;
    right: 0;
    left: 3%;
    transform: none;
  }

  .screen-content {
    min-height: 205px;
    padding: 17px;
  }

  .screen-art {
    height: 125px;
  }

  .tablet-frame {
    width: 150px;
    height: 230px;
    right: 2%;
  }

  .phone-frame {
    width: 110px;
    height: 215px;
  }

  .responsive-label {
    display: none;
  }

  .showcase-image {
    height: 500px;
  }

  .project-card {
    min-height: 0;
    padding: 23px;
  }

  .form-step h3 {
    font-size: 26px;
  }

  .form-actions {
    flex-wrap: wrap;
  }

  .send-button {
    flex: 1;
  }

  .process-arrow {
    display: none;
  }

  .final-cta-section {
    padding-bottom: 75px;
  }

  .final-cta {
    padding: 38px 24px;
  }

  .final-button {
    width: 100%;
    min-width: 0;
  }
}

@media (max-width: 430px) {
.desktop-frame {
    height: 265px;
  }

  .screen-nav > div {
    display: none;
  }

  .screen-content {
    grid-template-columns: 1fr;
  }

  .screen-art {
    display: none;
  }

  .tablet-frame {
    width: 135px;
    height: 210px;
  }

  .phone-frame {
    width: 98px;
    height: 195px;
  }

  .showcase-image {
    height: 420px;
  }
}



/* =====================================================
   HERO SLIDER RESPONSIVE
===================================================== */

@media (max-width: 1200px) {
  .hero-slider {
    height: 550px;
  }

  .hero-visual {
    min-height: 570px;
  }

  .hero-slide {
    padding: 35px 48px 72px;
  }

  .slide-card {
    width: 94%;
    height: 435px;
  }

  .slider-side-card {
    width: 160px;
  }
}

@media (max-width: 991px) {
  .hero-visual {
    width: 100%;
    min-height: 600px;
  }

  .hero-slider {
    width: 100%;
    height: 580px;
  }

  .slider-stage {
    height: 580px;
  }

  .hero-slide {
    padding: 38px 55px 75px;
  }

  .slide-card {
    width: 92%;
    height: 465px;
  }
}

@media (max-width: 767px) {
  .hero-visual {
    width: 100%;
    min-height: auto;
    padding-bottom: 0;
  }

  .hero-slider {
    width: 100%;
    height: 500px;
    min-height: 500px;
    border-radius: 20px;
  }

  .slider-stage {
    height: 500px;
    min-height: 500px;
  }

  .hero-slide {
    padding: 25px 20px 70px;
  }

  .slide-card {
    width: 100%;
    height: 390px;
    border-radius: 15px;
  }

  .slide-card img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center center;
    background: #ffffff;
  }

  .slider-side-card {
    display: none;
  }

  .slider-arrow {
    width: 40px;
    height: 40px;
    font-size: 15px;
  }

  .slider-prev {
    left: 8px;
  }

  .slider-next {
    right: 8px;
  }

  .slider-bottom {
    right: 16px;
    bottom: 16px;
    left: 16px;
  }

  .drag-note {
    font-size: 10px;
  }

  .slide-meta {
    right: 14px;
    bottom: 14px;
    left: 14px;
  }

  .slide-meta > span {
    width: 35px;
    height: 35px;
    flex-basis: 35px;
    font-size: 9px;
  }

  .slide-meta strong {
    font-size: 14px;
  }

  .slide-meta small {
    font-size: 9px;
  }
}

@media (max-width: 430px) {
  .hero-slider {
    height: 440px;
    min-height: 440px;
  }

  .slider-stage {
    height: 440px;
    min-height: 440px;
  }

  .hero-slide {
    padding: 18px 12px 65px;
  }

  .slide-card {
    width: 100%;
    height: 335px;
    border-radius: 14px;
  }

  .slide-card img {
    object-fit: contain;
    object-position: center center;
  }

  .slider-arrow {
    width: 37px;
    height: 37px;
    font-size: 14px;
  }

  .slider-dots {
    gap: 6px;
  }

  .slider-dots button {
    width: 7px;
    height: 7px;
  }

  .slider-dots button.active {
    width: 24px;
  }

  .drag-note {
    display: none;
  }

  .slide-meta {
    right: 12px;
    bottom: 12px;
    left: 12px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .web-service-page *,
  .web-service-page *::before,
  .web-service-page *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    scroll-behavior: auto !important;
  }
}
</style>
