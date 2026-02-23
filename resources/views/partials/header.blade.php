<nav class="navbar navbar-dark bg-header border-bottom fixed-top px-3" id="header">
    <div class="d-flex w-100 align-items-center" id="header-content">
        <!-- Left Side: Sidebar toggle + Search -->
        <div id="left-side" class="d-flex align-items-center gap-2">
            <!-- Sidebar toggle button -->
            <button class="btn btn-outline-light" id="sidebarToggleTop">
                <i class="bi bi-list"></i>
            </button>

            <!-- Search -->
            <div id="left-search" class="d-flex align-items-center position-relative">
                <form id="searchForm" class="search-form">
                    <input type="text" id="productSearch" class="search-input" placeholder="Search products...">
                    <button type="button" class="search-btn"><i class="bi bi-search"></i></button>
                </form>
                <ul id="searchResults" class="list-group position-absolute mt-1" style="z-index: 1000; display:none;"></ul>
            </div>
        </div>

        <!-- Center Logo -->
        <div class="logo-container">
            <a href="{{ route('products.index') }}" class="logo-link">
                <img src="{{ asset('assets/images/P LOGO WHITE.png') }}" alt="Logo" class="logo-main" height="35">
                <img src="{{ asset('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}" alt="Logo Hover" class="logo-hover" height="35">
            </a>
        </div>

        <!-- Right Side: Mode toggle + User -->
        <div id="right-side" class="ms-auto d-flex align-items-center gap-2">
            <!-- Dark Mode Toggle -->
            <button class="btn btn-outline-light" id="modeToggle">
                <i class="bi bi-moon-fill fs-5" id="modeIcon"></i>
            </button>

            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" 
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form id="logoutForm" action="{{ route('admin.logout') }}" method="POST">
    @csrf
    <button type="submit" class="dropdown-item">Logout</button>
</form>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
/* Make body start below fixed navbar */
body {
    padding-top: 60px; /* Adjust according to navbar height */
}

/* Header */
#header {
    box-shadow: 0 2px 8px rgba(0,0,0,0.2); /* subtle shadow */
}

/* Left Side width */
#left-side {
    width: 200px; 
}

/* Center Logo */
.logo-container {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: 5px;
}

.logo-link {
    position: relative;
    display: inline-block;
    height: 35px;
    width: auto;
}

.logo-link img {
    position: absolute;
    top: 0;
    left: 0;
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.logo-main {
    opacity: 1;
    transform: rotateY(0deg);
    z-index: 2;
}

.logo-hover {
    opacity: 0;
    transform: rotateY(180deg);
    z-index: 1;
}

.logo-link:hover .logo-main {
    opacity: 0;
    transform: rotateY(180deg);
}

.logo-link:hover .logo-hover {
    opacity: 1;
    transform: rotateY(0deg);
}

/* Search Form */
.search-form {
    display: flex;
    align-items: center;
    width: 40px;           
    height: 40px;           
    transition: width 0.4s ease;
    overflow: hidden;
    background-color: #222;
    border: 1px solid #fff;
    cursor: pointer;
}

.search-form.expanded {
    width: 300px;          
}

.search-input {
    border: none;
    outline: none;
    padding: 0.5rem 1rem;
    width: 100%;
    background: transparent;
    color: #fff;
    font-size: 0.9rem;
    display: none;         
}

.search-form.expanded .search-input {
    display: block;        
}

.search-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 0 0.5rem;
    cursor: pointer;
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.search-btn:hover {
    transform: scale(1.2);
}

/* Search Results */
#searchResults {
    background: #222;
    color: #fff;
    list-style: none;
    padding: 0;
    margin: 0;
    border-radius: 0.25rem;
    max-height: 250px;
    overflow-y: auto;
}

#searchResults li {
    padding: 0.5rem;
    cursor: pointer;
}

#searchResults li:hover {
    background-color: #555;
}

/* Right Side & Dropdown */
#right-side .dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 0.2rem;
}

/* Dark Mode Styles */
.dark-mode #right-side .dropdown-menu {
    background-color: #222;
    border-color: #333;
}

.dark-mode #right-side .dropdown-menu .dropdown-item {
    color: #fff;
}

.dark-mode #right-side .dropdown-menu .dropdown-item:hover {
    background-color: #555;
    color: #fff;
}

#header button {
    color: #fff;
    border-radius: 0px;
    border-color: #fff;
    transition: all 0.3s ease;
}

#header button:hover {
    background-color: #fff;
    color: #000;
}

#modeIcon {
    transition: transform 0.5s ease, color 0.5s ease;
}

.dark-mode #modeIcon {
    transform: rotate(180deg);
    color: #ffd700;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modeToggle = document.getElementById('modeToggle');
    const body = document.body;

    // Dark Mode Toggle
    modeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
    });

    // Search expand/collapse
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('productSearch');
    const resultsBox = document.getElementById('searchResults');

    searchForm.addEventListener('click', (e) => {
        searchForm.classList.add('expanded');
        searchInput.focus();
        e.stopPropagation();
    });

    document.addEventListener('click', () => {
        searchForm.classList.remove('expanded');
        resultsBox.style.display = 'none';
    });

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        if (!query) {
            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';
            return;
        }

        fetch(`/products/search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                resultsBox.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(product => {
                        const li = document.createElement('li');
                        li.textContent = product.name;
                        li.addEventListener('click', () => {
                            window.location.href = `/products/${product.id}`;
                        });
                        resultsBox.appendChild(li);
                    });
                    resultsBox.style.display = 'block';
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'No products found';
                    resultsBox.appendChild(li);
                    resultsBox.style.display = 'block';
                }
            });
    });

    resultsBox.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});
</script>
<style>
    .bg-header{
        background-color: #000;
    }
</style>