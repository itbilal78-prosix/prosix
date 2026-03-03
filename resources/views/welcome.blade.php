<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/P LOGO BLACK.png') }}">

    <!-- Default title -->
    <title>Prosix Sports</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Square+Peg&display=swap" rel="stylesheet">
{{-- <link href="https://fonts.googleapis.com/css2?family=Kaleo&display=swap" rel="stylesheet"> --}}

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">




<!-- JS (sab se neeche, body ke end pe) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons (zaruri hai icons ke liye) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/global.css">

    @vite('resources/css/app.css')

    <!-- Dynamic & Animated Title Script -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const titles = [
          "🚀 hurry up hurry up!",
          "🔥 selling out fast!",
          "⏰ come back come back!",
          "💥 prosix brand!",
          "⚡ prosix brand!"
        ];
        let index = 0;
        let originalTitle = document.title;

        // Animate titles in loop
        const animateTitle = () => {
          document.title = titles[index];
          index = (index + 1) % titles.length;
        };

        let titleInterval = setInterval(animateTitle, 2000); // 2 second per change

        // Optional: Stop animation when tab is active
        document.addEventListener("visibilitychange", function() {
          if (!document.hidden) {
            clearInterval(titleInterval);
            document.title = originalTitle; // restore original title
          } else {
            titleInterval = setInterval(animateTitle, 2000);
          }
        });

        // Optional: Flash favicon to attract attention
        const favicon = document.querySelector("link[rel='icon']");
        let faviconToggle = false;
        setInterval(() => {
          if (document.hidden) {
            favicon.href = faviconToggle
              ? "{{ asset('assets/images/P LOGO BLACK.png') }}"
              : "{{ asset('assets/images/P LOGO WHITE.png') }}"; // alternate image
            faviconToggle = !faviconToggle;
          } else {
            favicon.href = "{{ asset('assets/images/P LOGO BLACK.png') }}";
          }
        }, 1000);
      });
    </script>
</head>
<body class="antialiased">

<div id="app"></div>

@vite(['resources/js/app.js'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/P LOGO BLACK.png') }}">

    <!-- Default title -->
    <title>Prosix Sports</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Square+Peg&display=swap" rel="stylesheet">
{{-- <link href="https://fonts.googleapis.com/css2?family=Kaleo&display=swap" rel="stylesheet"> --}}

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">




<!-- JS (sab se neeche, body ke end pe) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons (zaruri hai icons ke liye) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/global.css">

    @vite('resources/css/app.css')

    <!-- Dynamic & Animated Title Script -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const titles = [
          "🚀 hurry up hurry up!",
          "🔥 selling out fast!",
          "⏰ come back come back!",
          "💥 prosix brand!",
          "⚡ prosix brand!"
        ];
        let index = 0;
        let originalTitle = document.title;

        // Animate titles in loop
        const animateTitle = () => {
          document.title = titles[index];
          index = (index + 1) % titles.length;
        };

        let titleInterval = setInterval(animateTitle, 2000); // 2 second per change

        // Optional: Stop animation when tab is active
        document.addEventListener("visibilitychange", function() {
          if (!document.hidden) {
            clearInterval(titleInterval);
            document.title = originalTitle; // restore original title
          } else {
            titleInterval = setInterval(animateTitle, 2000);
          }
        });

        // Optional: Flash favicon to attract attention
        const favicon = document.querySelector("link[rel='icon']");
        let faviconToggle = false;
        setInterval(() => {
          if (document.hidden) {
            favicon.href = faviconToggle
              ? "{{ asset('assets/images/P LOGO BLACK.png') }}"
              : "{{ asset('assets/images/P LOGO WHITE.png') }}"; // alternate image
            faviconToggle = !faviconToggle;
          } else {
            favicon.href = "{{ asset('assets/images/P LOGO BLACK.png') }}";
          }
        }, 1000);
      });
    </script>
</head>
<body class="antialiased">

<div id="app"></div>

@vite(['resources/js/app.js'])
</body>

<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();

// Tawk.to ka apna button/widget HIDE kar do
Tawk_API.onLoad = function(){
  Tawk_API.hideWidget();
};

(function(){
  var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
  s1.async=true;
  s1.src='https://embed.tawk.to/69a01feb650cdf1c39fca48e/default';
  s1.charset='UTF-8';
  s1.setAttribute('crossorigin','*');
  s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</html>

</body>

</html>
