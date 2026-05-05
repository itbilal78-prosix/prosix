@php
    $admin = auth()->guard('admin')->user();
@endphp

<!-- Sidebar -->
<div id="sidebar" class="bg-sidbare text-light position-fixed mt-5">

    @if ($admin)
        <ul class="nav flex-column mt-3">

            {{-- DASHBOARD --}}
            <li class="nav-item">
                <a class="nav-link text-light d-flex align-items-center px-3 py-2" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>
                    <span class="flex-grow-1">Dashboard</span>
                </a>
            </li>

            {{-- WEBSITE MANAGEMENT --}}
            @if ($admin->is_super_admin)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                        href="#websiteSubmenu" role="button" aria-expanded="false">
                        <i class="bi bi-window-stack me-2"></i>
                        <span class="flex-grow-1">Website Management</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="websiteSubmenu">
                        <li><a class="nav-link text-light py-1" href="#">Website Info</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Pages</a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.banners.index') }}">Banner</a></li>
                        <li><a class="nav-link text-light py-1 d-flex align-items-center gap-2" href="{{ route('admin.flipbooks.index') }}"><span>Flip Books</span></a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.blogs.index') }}">Blog / News</a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.testimonials.index') }}">Testimonials</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Media Library</a></li>
                    </ul>
                </li>
            @endif

            {{-- FORMS MANAGEMENT --}}
            <li class="nav-item">
                <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                    href="#formsSubmenu" role="button" aria-expanded="false">
                    <i class="bi bi-ui-checks-grid me-2"></i>
                    <span class="flex-grow-1">Forms</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
                <ul class="collapse list-unstyled ps-4" id="formsSubmenu">
                    <li><a class="nav-link text-light py-1" href="{{ route('admin.artwork') }}">Artwork Requests</a></li>
                    <li><a class="nav-link text-light py-1" href="{{ route('admin.memberships') }}">Memberships</a></li>
                    <li><a class="nav-link text-light py-1" href="{{ route('admin.placeorder') }}">Place Orders Data</a></li>
                </ul>
            </li>

            {{-- PRODUCT MANAGEMENT --}}
            @if ($admin->is_super_admin || $admin->can_products)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                        href="#productSubmenu" role="button" aria-expanded="false">
                        <i class="bi bi-box-seam me-2"></i>
                        <span class="flex-grow-1">Product Management</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="productSubmenu">
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.products.index') }}">All Products</a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.deals.index') }}">Deals & Offers</a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.videos.index') }}">Video Management</a></li>
                    </ul>
                </li>
            @endif

            {{-- NAVIGATION & CATEGORIES --}}
            <li class="nav-item">
                <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                    href="#navigationSubmenu" role="button" aria-expanded="false">
                    <i class="bi bi-list-ul me-2"></i>
                    <span class="flex-grow-1">Navi & Cata..</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
                <ul class="collapse list-unstyled ps-4" id="navigationSubmenu">
                    @if ($admin->is_super_admin || $admin->can_categories)
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.categories.index') }}">Categories</a></li>
                    @endif
                    <li><a class="nav-link text-light py-1" href="{{ route('admin.navigations.index') }}">Navigation Menu</a></li>
                </ul>
            </li>

            {{-- CUSTOMIZER MANAGEMENT --}}
            @if ($admin->is_super_admin || $admin->can_customizer)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                        href="#customizerSubmenu" role="button" aria-expanded="false">
                        <i class="bi bi-paint-bucket me-2"></i>
                        <span class="flex-grow-1">Customizer</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="customizerSubmenu">
                        <li><a class="nav-link text-light py-1" href="#">Customizer Control</a></li>
                        {{-- <li><a class="nav-link text-light py-1" href="{{ route('customizer.models.index') }}">Models</a></li> --}}
                        {{-- <li><a class="nav-link text-light py-1" href="https://customizer.prosix.com/models">Models</a></li> --}}
                        <li>
    <a class="nav-link text-light py-1" href="{{ url('/models') }}">Models</a>
</li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.patterns.index') }}">Patterns</a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.colors.index') }}">Color</a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.templates.index') }}">Templates</a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.fonts.index') }}">Font</a></li>
                    </ul>
                </li>
            @endif

            {{-- USER MANAGEMENT --}}
            @if ($admin->is_super_admin)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                        href="#userSubmenu" role="button" aria-expanded="false">
                        <i class="bi bi-people-fill me-2"></i>
                        <span class="flex-grow-1">User Management</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="userSubmenu">
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.users.index') }}">All Users</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Add User</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Roles</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Permissions</a></li>
                        <li><a class="nav-link text-light py-1" href="{{ route('admin.admins.index') }}"><i class="bi bi-person-gear me-1"></i> Manage Admins</a></li>
                    </ul>
                </li>
            @endif

            {{-- ORDERS --}}
            @if ($admin->is_super_admin || $admin->can_orders)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" href="{{ route('admin.orders.index') }}">
                        <i class="bi bi-bag me-2"></i>
                        <span class="flex-grow-1">Orders</span>
                    </a>
                </li>
            @endif

            {{-- PAYMENTS --}}
            @if ($admin->is_super_admin || $admin->can_orders)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" href="{{ route('admin.payments.index') }}">
                        <i class="bi bi-credit-card me-2"></i>
                        <span class="flex-grow-1">Payments</span>
                    </a>
                </li>
            @endif

            {{-- CUSTOMERS --}}
            @if ($admin->is_super_admin)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" href="#">
                        <i class="bi bi-people-fill me-2"></i>
                        <span class="flex-grow-1">Customers</span>
                    </a>
                </li>
            @endif

            {{-- REPORTS --}}
            @if ($admin->is_super_admin)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" href="#">
                        <i class="bi bi-bar-chart-line me-2"></i>
                        <span class="flex-grow-1">Reports</span>
                    </a>
                </li>
            @endif

            {{-- SEO & MARKETING --}}
            @if ($admin->is_super_admin)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                        href="#seoSubmenu" role="button" aria-expanded="false">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        <span class="flex-grow-1">SEO & Marketing</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="seoSubmenu">
                        <li><a class="nav-link text-light py-1" href="#">SEO Settings</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Meta Tags</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Campaigns</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Analytics</a></li>
                    </ul>
                </li>
            @endif

            {{-- SYSTEM & SECURITY --}}
            @if ($admin->is_super_admin)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                        href="#systemSubmenu" role="button" aria-expanded="false">
                        <i class="bi bi-shield-lock-fill me-2"></i>
                        <span class="flex-grow-1">System & Security</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="systemSubmenu">
                        <li><a class="nav-link text-light py-1" href="#">Security Settings</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Login Logs</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Backups</a></li>
                        <li><a class="nav-link text-light py-1" href="#">System Logs</a></li>
                    </ul>
                </li>
            @endif

            {{-- SETTINGS --}}
            @if ($admin->is_super_admin)
                <li class="nav-item">
                    <a class="nav-link text-light d-flex align-items-center px-3 py-2" data-bs-toggle="collapse"
                        href="#settingsSubmenu" role="button" aria-expanded="false">
                        <i class="bi bi-gear-fill me-2"></i>
                        <span class="flex-grow-1">Settings</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="settingsSubmenu">
                        <li><a class="nav-link text-light py-1" href="#">General</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Profile</a></li>
                        <li><a class="nav-link text-light py-1" href="#">Notifications</a></li>
                    </ul>
                </li>
            @endif

        </ul>
    @endif

</div>

<style>
    .bg-sidbare { background-color: #000; }
    #sidebar {
        background-color: #000000;
        width: 260px;
        transition: all 0.35s ease;
        overflow-y: auto;
        z-index: 999;
        height: 100vh;
        padding-bottom: 40px;
    }
    #sidebar.hide { transform: translateX(-100%); }
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
    #sidebar .nav-link.active {
        background: #1a1a1a;
        color: #fff;
        border: 1px solid #333;
    }
    #sidebar .nav-link:hover { background: #111; color: #fff; }
    #sidebar .nav-link i { transition: color 0.25s ease; }
    #sidebar .nav-link.active i, #sidebar .nav-link:hover i { color: #fff; }
    #sidebar .bi-chevron-right { transition: transform 0.3s ease; }
    #sidebar .nav-link[aria-expanded="true"] .bi-chevron-right { transform: rotate(90deg); }
    #sidebar ul ul .nav-link { position: relative; font-size: 13px; color: #999; padding: 8px 14px; }
    #sidebar ul ul .nav-link.active::before {
        content: "";
        position: absolute;
        left: 0; top: 0;
        width: 4px; height: 100%;
        background: #fff;
        border-radius: 2px;
    }
    #sidebar ul ul .nav-link:hover { background: #111; color: #fff; }
    .sidebar-header { border-bottom: 1px solid #222; min-height: 50px; padding: 10px 15px; }
    #sidebar::-webkit-scrollbar { width: 4px; }
    #sidebar::-webkit-scrollbar-track { background: #000; }
    #sidebar::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggleTop');
        const sidebarClose = document.getElementById('sidebarClose');
        const mainLinks = document.querySelectorAll('#sidebar .nav-link[data-bs-toggle="collapse"]');
        const allLinks = document.querySelectorAll('#sidebar .nav-link');
        const subLinks = document.querySelectorAll('#sidebar ul ul .nav-link');

        if (sidebarToggle) sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('hide'));
        if (sidebarClose) sidebarClose.addEventListener('click', () => sidebar.classList.add('hide'));

        mainLinks.forEach(link => {
            link.addEventListener('click', function() {
                mainLinks.forEach(l => {
                    if (l !== this) {
                        l.classList.remove('active');
                        const target = document.querySelector(l.getAttribute('href'));
                        if (target) target.classList.remove('show');
                    }
                });
                this.classList.toggle('active');
            });
        });

        allLinks.forEach(link => {
            if (!link.hasAttribute('data-bs-toggle')) {
                link.addEventListener('click', function() {
                    allLinks.forEach(l => l.classList.remove('active'));
                    subLinks.forEach(s => s.classList.remove('active'));
                    this.classList.add('active');
                });
            }
        });

        subLinks.forEach(sub => {
            sub.addEventListener('click', function() {
                subLinks.forEach(s => s.classList.remove('active'));
                this.classList.add('active');
                const parentCollapse = this.closest('.collapse');
                if (parentCollapse) {
                    const parentLink = document.querySelector(`[href="#${parentCollapse.id}"]`);
                    if (parentLink) parentLink.classList.add('active');
                }
            });
        });

        const currentUrl = window.location.href;
        allLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href !== '#' && currentUrl.includes(href)) {
                link.classList.add('active');
                const parentCollapse = link.closest('.collapse');
                if (parentCollapse) {
                    parentCollapse.classList.add('show');
                    const parentLink = document.querySelector(`[href="#${parentCollapse.id}"]`);
                    if (parentLink) {
                        parentLink.classList.add('active');
                        parentLink.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        });
    });
</script>
