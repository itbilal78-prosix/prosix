<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Dashboard</title>

<link rel="icon" type="image/png" href="{{ asset('assets/images/P LOGO BLACK.png') }}">
    <!-- ✅ Smooch Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Smooch+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/global.css">

    <style>
        body { transition: background-color 0.3s, color 0.3s; }
        #sidebar { min-width: 250px; max-width: 250px; height: 100vh; position: fixed; top:0; left:0; transition: all 0.3s; overflow-y:auto; }
        #sidebar.collapsed { width: 80px; }
        #sidebar .nav-link span { transition: opacity 0.3s; }
        #sidebar.collapsed .nav-link span { opacity:0; }
        #content { margin-left:250px; transition: all 0.3s; }
        #sidebar.collapsed ~ #content { margin-left:80px; }

        @media (max-width:768px){
            #sidebar { left:-250px; z-index:999; }
            #sidebar.show { left:0; }
            #overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:998; }
            #overlay.active { display:block; }
            #content { margin-left:0; }
        }

        [data-bs-theme="dark"] body { background-color:#121212; color:#fff; }
        [data-bs-theme="dark"] #sidebar { background-color:#1f1f1f; border-right:1px solid #333; }
        [data-bs-theme="dark"] .navbar, [data-bs-theme="dark"] .card { background-color:#1f1f1f; color:#fff; }
        [data-bs-theme="dark"] .table th, [data-bs-theme="dark"] .table td { color:#fff; }
    </style>
</head>
<body>
<div id="overlay"></div>

@include('partials.sidebar')
@include('partials.header')

<div id="content">
    <div class="container-fluid p-4">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
const sidebarToggleTop = document.getElementById('sidebarToggleTop');
const sidebarClose = document.getElementById('sidebarClose');
const overlay = document.getElementById('overlay');
const modeToggle = document.getElementById('modeToggle');
const modeIcon = document.getElementById('modeIcon');

sidebarToggleTop.addEventListener('click', ()=>{
    if(window.innerWidth >= 768) sidebar.classList.toggle('collapsed');
    else { sidebar.classList.add('show'); overlay.classList.add('active'); }
});
sidebarClose.addEventListener('click', ()=>{ sidebar.classList.remove('show'); overlay.classList.remove('active'); });
overlay.addEventListener('click', ()=>{ sidebar.classList.remove('show'); overlay.classList.remove('active'); });

const savedTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-bs-theme', savedTheme);
modeIcon.className = savedTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';

modeToggle.addEventListener('click', ()=>{
    let current = document.documentElement.getAttribute('data-bs-theme');
    let newTheme = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    modeIcon.className = newTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
});
</script>
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

  const animateTitle = () => {
    document.title = titles[index];
    index = (index + 1) % titles.length;
  };

  let titleInterval = setInterval(animateTitle, 2000);

  document.addEventListener("visibilitychange", function() {
    if (!document.hidden) {
      clearInterval(titleInterval);
      document.title = originalTitle;
    } else {
      titleInterval = setInterval(animateTitle, 2000);
    }
  });
});
</script>
</body>
</html>
