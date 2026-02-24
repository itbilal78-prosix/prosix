<template>
  <footer class="prosix-footer bg-black text-white">
    <div class="container-fluid px-5" style="position: relative; z-index: 1;">
      <div class="row py-5 ">

<div class="footer-logo mb-4 d-flex align-items-start  gap-5">
      <img src="/public/assets/images/P LOGO WHITE.png" alt="Prosix Sports Logo" class="footer-logo-img">

  <img src="/public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png" alt="Prosix Sports Logo" class="footer-logo-img">
</div>



                <hr class="mb-3" style="border-color:white" />

        <!-- Left Section - Opening Schedule -->
        <div class="col-md-3 mb-4">
          <h3 class="footer-title mb-4">OPENING SCHEDULE</h3>
          <div class="schedule-info">
            <div class="d-flex justify-content-between mb-3">
              <span class="text-secondary">Days</span>
              <span>Mon - Sat</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
              <span class="text-secondary">Time</span>
              <span>08:00 - 18:00</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-secondary">Sunday:</span>
              <span>Closed</span>
            </div>
          </div>
        </div>

        <!-- Center Section - Subscribe -->
        <div class="col-md-6 mb-4 text-center">
          <h1 class="subscribe-title mb-3 ">SUBSCRIBE</h1>
          <p class="subscribe-subtitle mb-4">To our newsletter for latest updates</p>
          <div class="social-icons d-flex justify-content-center  gap-4">
            <a href="https://www.facebook.com/prosixsports/" class="social-link"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/prosixsports" class="social-link"><i class="bi bi-instagram"></i></a>
            <a href="https://www.youtube.com/@prosixsports" class="social-link"><i class="bi bi-youtube"></i></a>
            <a href="https://x.com/ProsixSports" class="social-link"><i class="bi bi-twitter"></i></a>
            <a href="https://www.pinterest.com/prosixsports/" class="social-link"><i class="bi bi-pinterest"></i></a>
          </div>
        </div>

        <!-- Right Section - Contact Info -->
        <div class="col-md-3 mb-4 text-end">
          <h3 class="footer-title mb-4">CONTACT INFO</h3>
          <div class="contact-info">
            <p class="mb-3">sales@prosix.com</p>
            <p class="mb-3">+ (929) 210 4402</p>
            <p class="mb-0">19 Holly Cove Ln, Dover Delaware 19901</p>
          </div>
        </div>
      </div>

      <!-- Bottom Copyright -->
      <div class="footer-bottom py-3 text-center">
                   <hr class="mb-3" style="border-color:white" />

        <p class="mb-0 copyright-text">
          Copyright © 2009 - 2024, All rights reserved by <strong>Prosix Sports</strong>
        </p>
      </div>
    </div>
  </footer>
</template>
<style>
  .social-icons .social-link i {
  color: white; /* makes icons white */
  font-size: 24px; /* optional: adjust size */
  transition: color 0.3s ease;
}

.social-icons .social-link:hover i {
  color: #ccc; /* optional: change color on hover */
}
.footer-logo-img {
  height: 80px; /* adjust size */
  object-fit: contain;

}
.prosix-footer {
  background: url('/public/assets/images/footer texture.svg') no-repeat center center;
  background-size: cover;
  position: relative;
  color: rgb(255, 255, 255); /* text color on top of image */
}

</style>
