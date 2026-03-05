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

        <hr class="mb-4 d-md-none" style="border-color: white; opacity: 0.5;" /> <!-- Mobile divider -->

        <!-- Main content row -->
        <div class="col-12">
          <div class="row align-items-start">

            <!-- Opening Schedule - Left on md+, full on mobile -->
            <div class="col-md-4 col-12 mb-5 mb-md-0 text-center text-md-start">
              <h3 class="footer-title mb-3">OPENING SCHEDULE</h3>
              <div class="schedule-info">
                <div class="d-flex justify-content-between justify-content-md-between mb-2">
                  <span class="text-secondary">Days</span>
                  <span>Mon - Sat</span>
                </div>
                <div class="d-flex justify-content-between justify-content-md-between mb-2">
                  <span class="text-secondary">Time</span>
                  <span>08:00 - 18:00</span>
                </div>
                <div class="d-flex justify-content-between justify-content-md-between">
                  <span class="text-secondary">Sunday:</span>
                  <span>Closed</span>
                </div>
              </div>
            </div>

            <!-- Vertical divider (only on md+) -->
            <div class="d-none d-md-block col-md-1 text-center">
              <div style="height: 100%; width: 1px; background: white; margin: 0 auto;"></div>
            </div>

            <!-- Subscribe / Social - Center on md+, after schedule on mobile -->
            <div class="col-md-3 col-12 mb-5 mb-md-0 text-center order-md-0">
              <h1 class="subscribe-title mb-3">SUBSCRIBE</h1>
              <p class="subscribe-subtitle mb-4">To our newsletter for latest updates</p>
              <div class="social-icons d-flex justify-content-center gap-4 gap-md-3">
                <a href="https://www.facebook.com/prosixsports/" class="social-link"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/prosixsports" class="social-link"><i class="bi bi-instagram"></i></a>
                <a href="https://www.youtube.com/@prosixsports" class="social-link"><i class="bi bi-youtube"></i></a>
                <a href="https://x.com/ProsixSports" class="social-link"><i class="bi bi-twitter"></i></a>
                <a href="https://www.pinterest.com/prosixsports/" class="social-link"><i class="bi bi-pinterest"></i></a>
              </div>
            </div>

            <!-- Contact Info - Right on md+, last on mobile -->
            <div class="col-md-4 col-12 text-center text-md-end">
              <h3 class="footer-title mb-3">CONTACT INFO</h3>
              <div class="contact-info">
                <p class="mb-2">sales@prosix.com</p>
                <p class="mb-2">(+1 929) 210 4402</p>
                <p class="mb-0">2604 whittier place Wilmington Delaware 19808</p>
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
    font-size: 28px;
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

  @media (max-width: 767px) {
    .footer-logo-img {
      height: 60px;
    }
    .subscribe-title {
      font-size: 2.2rem;
    }
  }

  .prosix-footer {
    background: url('/public/assets/images/footer texture.svg') no-repeat center center;
    background-size: cover;
    position: relative;
    color: white;
  }

  .footer-title {
    font-size: 1.4rem;
    font-weight: bold;
    text-transform: uppercase;
  }

  .subscribe-title {
    font-size: 3rem;
    font-weight: bold;
    letter-spacing: 2px;
  }
</style>
