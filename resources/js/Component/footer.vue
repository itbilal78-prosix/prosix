<template>
  <footer class="prosix-footer bg-black text-white">
    <div class="container-fluid px-4 px-md-5" style="position: relative; z-index: 1;">
      <div class="row py-4 py-md-5">

        <!-- Logos at top -->
        <div class="col-12 mb-4">
          <div class="footer-logo d-flex justify-content-center justify-content-md-start align-items-center gap-4 gap-md-5 flex-wrap">
            <img src="/public/assets/images/P LOGO WHITE.png" alt="Prosix Sports Logo" class="footer-logo-img">
            <img src="/public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png" alt="Prosix Sports Logo" class="footer-logo-img">
          </div>
        </div>

        <!-- Main content row -->
        <div class="col-12">
          <div class="row align-items-start">

            <!-- OPENING SCHEDULE — col 1 on desktop, col 1 on mobile (left) -->
<div class="col-6 col-md-3 mb-4 mb-md-0 text-start order-1">
                  <h3 class="footer-title mb-3">OPENING SCHEDULE</h3>
              <div class="schedule-info">
                <div class="schedule-row">
                  <span class="schedule-label">Mon – Sat</span>
                  <span class="schedule-value">Open</span>
                </div>
                <div class="schedule-row">
                  <span class="schedule-label">Time</span>
                  <span class="schedule-value">08:00 – 18:00</span>
                </div>
                <div class="schedule-row">
                  <span class="schedule-label">Sunday</span>
                  <span class="schedule-value closed">Closed</span>
                </div>
              </div>
            </div>

            <!-- Vertical divider (desktop only between schedule and subscribe) -->
            <div class="d-none d-md-flex col-md-1 justify-content-center order-2">
              <div style="width:1px; background:rgba(255,255,255,0.3); min-height:120px;"></div>
            </div>

            <!-- SUBSCRIBE — center desktop, last on mobile -->
<div class="col-12 col-md-4 mb-4 mb-md-0 text-center d-flex flex-column align-items-center justify-content-center order-3 order-md-2">
                  <h1 class="subscribe-title mb-2">SUBSCRIBE</h1>
              <p class="subscribe-subtitle mb-3">To our newsletter for latest updates</p>
              <div class="social-icons d-flex justify-content-center gap-3">
                <a href="https://www.facebook.com/prosixsports/" class="social-link"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/prosixsports" class="social-link"><i class="bi bi-instagram"></i></a>
                <a href="https://www.youtube.com/@prosixsports" class="social-link"><i class="bi bi-youtube"></i></a>
                <a href="https://x.com/ProsixSports" class="social-link"><i class="bi bi-twitter"></i></a>
                <a href="https://www.pinterest.com/prosixsports/" class="social-link"><i class="bi bi-pinterest"></i></a>
              </div>
            </div>

            <!-- Vertical divider (desktop only between subscribe and contact) -->
            <div class="d-none d-md-flex col-md-1 justify-content-center order-3">
              <div style="width:1px; background:rgba(255,255,255,0.3); min-height:120px;"></div>
            </div>

            <!-- CONTACT INFO — col 3 on desktop, col 2 on mobile (right) -->
       <div class="col-6 col-md-3 mb-4 mb-md-0 order-2 order-md-4 text-md-end">

  <div class="contact-info text-start d-inline-block">

    <h3 class="footer-title mb-3">CONTACT INFO</h3>

    <a href="tel:+19292104402" class="contact-link mb-2 d-block">
      +1 929 210 4402
    </a>

    <p class="mb-2">
      2604 Whittier Place Wilmington <br>
      Delaware 19808 U.S.A
    </p>

    <a href="mailto:sales@prosix.com" class="contact-link">
      sales@prosix.com
    </a>

  </div>

</div>

          </div>
        </div>

      </div>

      <!-- Bottom Copyright -->
      <div class="footer-bottom py-3 text-center border-top border-white">
        <p class="mb-0 copyright-text">
          Copyright © 2009 - 2024, All rights reserved by <strong>Prosix Sports</strong>
        </p>
      </div>
    </div>
  </footer>
</template>

<style>
.social-icons .social-link i {
  color: white;
  font-size: 26px;
  transition: color 0.3s ease;
}
.social-icons .social-link:hover i {
  color: #ccc;
}

.footer-logo-img {
  height: 70px;
  max-height: 60px;
  object-fit: contain;
}

.prosix-footer {
  background: url('/public/assets/images/footer texture.svg') no-repeat center center;
  background-size: cover;
  position: relative;
  color: white;
}

.footer-title {
  font-size: 1.1rem;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* ── Schedule key-value rows ── */
.schedule-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.schedule-row {
  display: flex;
  align-items: baseline;
  gap: 0;
}
.schedule-label {
  font-size: 0.88rem;
  color: rgba(255,255,255,0.55);
  white-space: nowrap;
  min-width: 80px;
}
.schedule-value {
  font-size: 0.88rem;
  color: #fff;
  font-weight: 600;
  padding-left: 12px;
  border-left: 1px solid rgba(255,255,255,0.2);
  margin-left: 8px;
}
.schedule-value.closed {
  color: #ff5555;
}

/* ── Subscribe ── */
.subscribe-title {
  font-size: 2.4rem;
  font-weight: bold;
  letter-spacing: 2px;
}
.subscribe-subtitle {
  font-size: 0.85rem;
  color: rgba(255,255,255,0.55);
}

/* ── Contact info ── */
.contact-link{
  color:#fff;
  text-decoration:none;
  font-weight:500;
}

.contact-link:hover{
  color:#ccc;
  text-decoration:none;
}

/* ── Copyright ── */
.copyright-text {
  font-size: 0.82rem;
  color: rgba(255,255,255,0.6);
}

/* ── Responsive ── */
@media (max-width: 767px) {
  .footer-logo-img { height: 50px; }
  .subscribe-title { font-size: 2rem; }
  .footer-title { font-size: 1rem; }
  .schedule-label { min-width: 65px; font-size: 0.8rem; }
  .schedule-value { font-size: 0.8rem; }
  .contact-info p { font-size: 0.8rem; }
}
</style>
