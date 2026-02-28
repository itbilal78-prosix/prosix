<!-- Sidebar -->
<div id="sidebar" class="bg-sidbare text-light vh-100 position-fixed mt-5">
    <div class="sidebar-header  d-flex align-items-center justify-content-between border-bottom">

        <button class="btn btn-sm d-md-none text-light" id="sidebarClose"><i class="bi bi-x"></i></button>
    </div>

    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link text-light d-flex align-items-center px-3 py-2" href="#">
                <i class="bi bi-speedometer2 me-2"></i>
                <span class="flex-grow-1">Dashboard</span>
            </a>
        </li>

        <!-- Website Management -->
        <li class="nav-item">
            <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse" href="#websiteSubmenu" role="button" aria-expanded="false">
                <i class="bi bi-window-stack me-2"></i>
                <span class="flex-grow-1">Website Management</span>
                <i class="bi bi-chevron-right ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled ps-4" id="websiteSubmenu">
                <li><a class="nav-link text-light py-1" href="#">Website Info</a></li>
                <li><a class="nav-link text-light py-1" href="#">Pages</a></li>
<li>
    <a class="nav-link text-light py-1" href="{{ route('banners.index') }}">Banner</a>
</li>

  <li>
            <a class="nav-link text-light py-1"
              href="{{ route('navigations.index') }}">Navigation Menu</a>

        </li>
<li>
    <li>
    <a class="nav-link text-light py-1 d-flex align-items-center gap-2"
       href="{{ route('admin.flipbooks.index') }}">
        <i class="bi bi-book-half"></i>
        <span>Flip Books</span>
    </a>
</li>
    <a class="nav-link text-light py-1" href="{{ route('blogs.index') }}">Blog / News</a>
</li>
<li>
    <a class="nav-link text-light py-1" href="{{ route('testimonials.index') }}">Testimonials</a>
</li>                <li><a class="nav-link text-light py-1" href="#">Media Library</a></li>
<li>
    <a class="nav-link text-light py-1" href="{{ route('admin.memberships') }}">Memberships</a>
</li>
<li>
    <a class="nav-link text-light py-1" href="{{ route('admin.artwork') }}">
        Artwork Requests
    </a>
</li>

            </ul>
        </li>

        <!-- Product Management -->
        <li class="nav-item">
            <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse" href="#productSubmenu" role="button" aria-expanded="false">
                <i class="bi bi-box-seam me-2"></i>
                <span class="flex-grow-1">Product Management</span>
                <i class="bi bi-chevron-right ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled ps-4" id="productSubmenu">
                 <li>
            <a class="nav-link text-light py-1" href="{{ route('categories.index') }}">
                Categories
            </a>
        </li>


        <li>
            <a class="nav-link text-light py-1" href="{{ route('deals.index') }}">
                <i class="bi bi-gift me-2"></i> Deals & Offers
            </a>
        </li>
           <li>
            <a class="nav-link text-light py-1" href="{{ route('videos.index') }}">
                <i class="bi bi-camera-video me-2"></i> Video Management
            </a>
        </li>
                <li><a class="nav-link text-light py-1" href="{{ route('products.index') }}">All Products</a></li>
                <li><a class="nav-link text-light py-1" href="{{ route('products.create') }}">Add Product</a></li>
            </ul>
        </li>

        <!-- Customizer Management -->
        <li class="nav-item">
            <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse" href="#customizerSubmenu" role="button" aria-expanded="false">
                <i class="bi bi-paint-bucket me-2"></i>
                <span class="flex-grow-1">Customizer Management</span>
                <i class="bi bi-chevron-right ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled ps-4" id="customizerSubmenu">
                                <li><a class="nav-link text-light py-1" href="#">Customizer Control</a></li>
<li>
    <a class="nav-link text-light py-1" href="{{ route('models.index') }}">
        Models
    </a>
</li>
<li>
    <a class="nav-link text-light py-1" href="{{ route('patterns.index') }}">Patterns</a>
</li>
 <li>
        <a class="nav-link text-light py-1" href="{{ route('colors.index') }}">
            Color
        </a>
    </li>
  <li>
            <a class="nav-link text-light py-1" href="{{ route('templates.index') }}">
                Templates
            </a>
        </li>
<li>
    <a class="nav-link text-light py-1" href="{{ route('fonts.index') }}">
        Font
    </a>
</li>
            </ul>
        </li>

        <!-- Orders -->
       <li class="nav-item">
   <a class="nav-link text-light d-flex align-items-center px-3 py-2"
   href="{{ route('admin.orders.index') }}">
    <i class="bi bi-bag me-2"></i>
    <span class="flex-grow-1">Orders</span>
</a>

</li>

        <!-- Customers -->
        <li class="nav-item">
            <a class="nav-link text-light d-flex align-items-center px-3 py-2" href="#">
                <i class="bi bi-people-fill me-2"></i>
                <span class="flex-grow-1">Customers</span>
            </a>
        </li>

        <!-- Reports -->
        <li class="nav-item">
            <a class="nav-link text-light d-flex align-items-center px-3 py-2" href="#">
                <i class="bi bi-bar-chart-line me-2"></i>
                <span class="flex-grow-1">Reports</span>
            </a>
        </li>
{{-- User Management --}}
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center px-3 py-2"
               data-bs-toggle="collapse" href="#userSubmenu">
                <i class="bi bi-people-fill me-2"></i>
                <span class="flex-grow-1">User Management</span>
                <i class="bi bi-chevron-right ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled ps-4" id="userSubmenu">
<li>
    <a class="nav-link py-1" href="{{ route('admin.users.index') }}">
        All Users
    </a>
</li>
                <li><a class="nav-link py-1" href="#">Add User</a></li>
                <li><a class="nav-link py-1" href="#">Roles</a></li>
                <li><a class="nav-link py-1" href="#">Permissions</a></li>
            </ul>
        </li>
{{-- SEO & Marketing --}}
 <li class="nav-item">
            <a class="nav-link d-flex align-items-center px-3 py-2"
               data-bs-toggle="collapse" href="#seoSubmenu">
                <i class="bi bi-graph-up-arrow me-2"></i>
                <span class="flex-grow-1">SEO & Marketing</span>
                <i class="bi bi-chevron-right ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled ps-4" id="seoSubmenu">
                <li><a class="nav-link py-1" href="#">SEO Settings</a></li>
                <li><a class="nav-link py-1" href="#">Meta Tags</a></li>
                <li><a class="nav-link py-1" href="#">Campaigns</a></li>
                <li><a class="nav-link py-1" href="#">Analytics</a></li>
            </ul>
        </li>


         <!-- System & Security -->
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center px-3 py-2"
               data-bs-toggle="collapse" href="#systemSubmenu">
                <i class="bi bi-shield-lock-fill me-2"></i>
                <span class="flex-grow-1">System & Security</span>
                <i class="bi bi-chevron-right ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled ps-4" id="systemSubmenu">
                <li><a class="nav-link py-1" href="#">Security Settings</a></li>
                <li><a class="nav-link py-1" href="#">Login Logs</a></li>
                <li><a class="nav-link py-1" href="#">Backups</a></li>
                <li><a class="nav-link py-1" href="#">System Logs</a></li>
            </ul>
        </li>


         <!-- Settings -->
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center px-3 py-2"
               data-bs-toggle="collapse" href="#settingsSubmenu">
                <i class="bi bi-gear-fill me-2"></i>
                <span class="flex-grow-1">Settings</span>
                <i class="bi bi-chevron-right ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled ps-4" id="settingsSubmenu">
                <li><a class="nav-link py-1" href="#">General</a></li>
                <li><a class="nav-link py-1" href="#">Profile</a></li>
                <li><a class="nav-link py-1" href="#">Notifications</a></li>
            </ul>
        </li>

    </ul>
</div>

<!-- Sidebar CSS -->
<style>
    .bg-sidbare{
        background-color: #000;
    }
#sidebar {
    background-color: #000000;
    width: 260px;
    transition: all 0.35s ease;
}

#sidebar.hide {
    transform: translateX(-100%);
}

#sidebar .nav-link {
    position: relative;
    color: #bbb;
    padding: 12px 16px;
    margin: 4px 10px;
    border-radius: 6px;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
}

/* ACTIVE MAIN MENU */
#sidebar .nav-link.active {
    background: #000;
    color: #fff;
    border: 1px solid #333;
}

/* Hover */
#sidebar .nav-link:hover {
    background: #111;
    color: #fff;
}

#sidebar .nav-link i {
    transition: color 0.25s ease;
}

#sidebar .nav-link.active i,
#sidebar .nav-link:hover i {
    color: #fff;
}

/* Chevron */
#sidebar .bi-chevron-right {
    transition: transform 0.3s ease;
}

#sidebar .nav-link[aria-expanded="true"] .bi-chevron-right {
    transform: rotate(90deg);
}

/* Submenu LEFT WHITE STRIP */
#sidebar ul ul .nav-link {
    position: relative;
}

/* Active submenu indicator */
#sidebar ul ul .nav-link.active::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: #fff;
    border-radius: 2px;
}


#sidebar ul ul .nav-link:hover {
    background: #111;
    color: #fff;
}

/* Header */
.sidebar-header {
    border-bottom: 1px solid #222;
}

</style>

<!-- Sidebar JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggleTop');
    const sidebarClose = document.getElementById('sidebarClose');

    const mainLinks = document.querySelectorAll('#sidebar .nav-link[data-bs-toggle="collapse"]');
    const allLinks  = document.querySelectorAll('#sidebar .nav-link'); // all links including ones without submenu
    const subLinks  = document.querySelectorAll('#sidebar ul ul .nav-link');

    /* Sidebar open / close */
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('hide');
        });
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', () => {
            sidebar.classList.add('hide');
        });
    }

    /* MAIN MENU CLICK FOR ITEMS WITH SUBMENU */
    mainLinks.forEach(link => {
        link.addEventListener('click', function () {

            // Remove active from all main menus except this
            mainLinks.forEach(l => {
                if (l !== this) {
                    l.classList.remove('active');
                    const target = document.querySelector(l.getAttribute('href'));
                    if (target) target.classList.remove('show');
                }
            });

            // Toggle active on clicked menu
            this.classList.toggle('active');
        });
    });

    /* CLICK FOR ITEMS WITHOUT SUBMENU */
    allLinks.forEach(link => {
        if (!link.hasAttribute('data-bs-toggle')) { // links without submenu
            link.addEventListener('click', function () {
                // Remove active from all main links and sublinks
                allLinks.forEach(l => l.classList.remove('active'));
                subLinks.forEach(s => s.classList.remove('active'));

                // Add active to this
                this.classList.add('active');
            });
        }
    });

    /* SUB MENU CLICK */
    subLinks.forEach(sub => {
        sub.addEventListener('click', function () {

            // Remove active from all sub links
            subLinks.forEach(s => s.classList.remove('active'));

            // Add active to clicked sub
            this.classList.add('active');

            // Keep parent active
            const parentCollapse = this.closest('.collapse');
            const parentLink = document.querySelector(`[href="#${parentCollapse.id}"]`);
            if (parentLink) parentLink.classList.add('active');
        });
    });

});

</script>

