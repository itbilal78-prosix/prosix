<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('assets/images/P LOGO BLACK.png') }}">
    <title>Prosix Sports</title>

    <!-- ✅ Smooch Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Smooch+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Square Peg Font (agar use ho raha hai) -->
    <link href="https://fonts.googleapis.com/css2?family=Square+Peg&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Global CSS -->
    <link rel="stylesheet" href="/assets/global.css">

    @vite('resources/css/app.css')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dynamic Title Script -->
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
        const animateTitle = () => { document.title = titles[index]; index = (index + 1) % titles.length; };
        let titleInterval = setInterval(animateTitle, 2000);
        document.addEventListener("visibilitychange", function() {
          if (!document.hidden) { clearInterval(titleInterval); document.title = originalTitle; }
          else { titleInterval = setInterval(animateTitle, 2000); }
        });
        const favicon = document.querySelector("link[rel='icon']");
        let faviconToggle = false;
        setInterval(() => {
          if (document.hidden) {
            favicon.href = faviconToggle ? "{{ asset('assets/images/P LOGO BLACK.png') }}" : "{{ asset('assets/images/P LOGO WHITE.png') }}";
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

<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
Tawk_API.onLoad = function(){ Tawk_API.hideWidget(); };
(function(){
  var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
  s1.async=true;
  s1.src='https://embed.tawk.to/69a01feb650cdf1c39fca48e/default';
  s1.charset='UTF-8';
  s1.setAttribute('crossorigin','*');
  s0.parentNode.insertBefore(s1,s0);
})();
</script>

</body>
</html>
