@php
    $admin = auth()->guard('admin')->user();
@endphp

<div id="sidebar" class="bg-sidbare text-light position-fixed mt-5">

@if ($admin)

<ul class="nav flex-column mt-3">

    {{-- =========================================================
        DASHBOARD
    ========================================================= --}}
    <li class="nav-item">

        <a
            class="nav-link text-light d-flex align-items-center px-3 py-2"
            href="{{ route('admin.dashboard') }}"
        >

            <i class="bi bi-speedometer2 me-2"></i>

            <span class="flex-grow-1">
                Dashboard
            </span>

        </a>

    </li>


    {{-- =========================================================
        WEBSITE MANAGEMENT - SUPER ADMIN
    ========================================================= --}}
    @if ($admin->is_super_admin)

        @php

            $newContactMessages =
                \App\Models\ContactMessage::where(
                    'is_read',
                    false
                )->count();

        @endphp


        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#websiteSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-window-stack me-2"></i>

                <span class="flex-grow-1">
                    Website Management
                </span>


                {{-- CONTACT NOTIFICATION ON MAIN MENU --}}
                <span
                    id="websiteContactBadge"
                    class="badge bg-danger rounded-pill me-2"
                    style="{{ $newContactMessages > 0 ? '' : 'display:none;' }}"
                >

                    {{ $newContactMessages }}

                </span>


                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="websiteSubmenu"
            >


                {{-- WEBSITE INFO --}}
                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('admin.website-info.edit') }}"
                    >

                        <i class="bi bi-info-circle me-1"></i>

                        Website Info

                    </a>

                </li>


                {{-- PAGES --}}
                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="#"
                    >

                        <i class="bi bi-file-earmark-text me-1"></i>

                        Pages

                    </a>

                </li>


                {{-- BANNERS --}}
                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('banners.index') }}"
                    >

                        <i class="bi bi-image me-1"></i>

                        Banner

                    </a>

                </li>


                {{-- FLIP BOOKS --}}
                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('admin.flipbooks.index') }}"
                    >

                        <i class="bi bi-book me-1"></i>

                        Flip Books

                    </a>

                </li>


                {{-- BLOG --}}
                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('blogs.index') }}"
                    >

                        <i class="bi bi-newspaper me-1"></i>

                        Blog / News

                    </a>

                </li>


                {{-- TESTIMONIALS --}}
                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('testimonials.index') }}"
                    >

                        <i class="bi bi-chat-quote me-1"></i>

                        Testimonials

                    </a>

                </li>


                {{-- CONTACT US DATA --}}
                <li>

                    <a
                        class="nav-link text-light py-1 d-flex align-items-center justify-content-between"
                        href="{{ route('admin.contact-messages.index') }}"
                    >

                        <span>

                            <i class="bi bi-envelope me-1"></i>

                            Contact Us Data

                        </span>


                        <span
                            id="contactMessageBadge"
                            class="badge bg-danger rounded-pill"
                            style="{{ $newContactMessages > 0 ? '' : 'display:none;' }}"
                        >

                            {{ $newContactMessages }}

                        </span>

                    </a>

                </li>


            </ul>

        </li>

    @endif



    {{-- =========================================================
        FORMS - SUPER ADMIN
    ========================================================= --}}
    @if ($admin->is_super_admin)

        @php

            $newArtwork =
                \App\Models\ArtworkRequest::where(
                    'is_read',
                    false
                )->count();


            $newMemberships =
                \App\Models\MembershipRequest::where(
                    'is_read',
                    false
                )->count();


            $newPlaceOrders =
                \App\Models\PlaceOrder::where(
                    'is_read',
                    false
                )->count();


            $totalFormsBadge =
                $newArtwork +
                $newMemberships +
                $newPlaceOrders;

        @endphp


        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#formsSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-ui-checks-grid me-2"></i>

                <span class="flex-grow-1">
                    Forms
                </span>


                <span
                    id="totalFormsBadge"
                    class="badge bg-danger rounded-pill me-1"
                    style="{{ $totalFormsBadge > 0 ? '' : 'display:none;' }}"
                >

                    {{ $totalFormsBadge }}

                </span>


                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="formsSubmenu"
            >


                {{-- ARTWORK --}}
                <li>

                    <a
                        class="nav-link text-light py-1 d-flex align-items-center justify-content-between"
                        href="{{ route('admin.artwork') }}"
                    >

                        <span>
                            Artwork Requests
                        </span>


                        <span
                            id="artworkBadge"
                            class="badge bg-danger rounded-pill"
                            style="{{ $newArtwork > 0 ? '' : 'display:none;' }}"
                        >

                            {{ $newArtwork }}

                        </span>

                    </a>

                </li>


                {{-- MEMBERSHIPS --}}
                <li>

                    <a
                        class="nav-link text-light py-1 d-flex align-items-center justify-content-between"
                        href="{{ route('admin.memberships') }}"
                    >

                        <span>
                            Memberships
                        </span>


                        <span
                            id="membershipBadge"
                            class="badge bg-danger rounded-pill"
                            style="{{ $newMemberships > 0 ? '' : 'display:none;' }}"
                        >

                            {{ $newMemberships }}

                        </span>

                    </a>

                </li>


                {{-- PLACE ORDERS --}}
                <li>

                    <a
                        class="nav-link text-light py-1 d-flex align-items-center justify-content-between"
                        href="{{ route('admin.placeorder') }}"
                    >

                        <span>
                            Place Orders Data
                        </span>


                        <span
                            id="placeOrderBadge"
                            class="badge bg-danger rounded-pill"
                            style="{{ $newPlaceOrders > 0 ? '' : 'display:none;' }}"
                        >

                            {{ $newPlaceOrders }}

                        </span>

                    </a>

                </li>


            </ul>

        </li>

    @endif



    {{-- =========================================================
        PRODUCT MANAGEMENT
    ========================================================= --}}
    @if ($admin->is_super_admin || $admin->can_products)

        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#productSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-box-seam me-2"></i>

                <span class="flex-grow-1">
                    Product Management
                </span>

                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="productSubmenu"
            >

                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('products.index') }}"
                    >
                        All Products
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('deals.index') }}"
                    >
                        Deals & Offers
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('videos.index') }}"
                    >
                        Video Management
                    </a>

                </li>

            </ul>

        </li>

    @endif



    {{-- =========================================================
        NAVIGATION & CATEGORIES
    ========================================================= --}}
    @if ($admin->is_super_admin || $admin->can_categories)

        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#navigationSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-list-ul me-2"></i>

                <span class="flex-grow-1">
                    Navi & Cata..
                </span>

                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="navigationSubmenu"
            >

                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('categories.index') }}"
                    >
                        Categories
                    </a>

                </li>


                @if ($admin->is_super_admin)

                    <li>

                        <a
                            class="nav-link text-light py-1"
                            href="{{ route('navigations.index') }}"
                        >
                            Navigation Menu
                        </a>

                    </li>

                @endif

            </ul>

        </li>

    @endif



    {{-- =========================================================
        CUSTOMIZER
    ========================================================= --}}
    @if ($admin->is_super_admin || $admin->can_customizer)

        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#customizerSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-paint-bucket me-2"></i>

                <span class="flex-grow-1">
                    Customizer
                </span>

                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="customizerSubmenu"
            >

                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="#"
                    >
                        Customizer Control
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('models.index') }}"
                    >
                        Models
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('patterns.index') }}"
                    >
                        Patterns
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('colors.index') }}"
                    >
                        Color
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('templates.index') }}"
                    >
                        Templates
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('fonts.index') }}"
                    >
                        Font
                    </a>

                </li>

            </ul>

        </li>

    @endif



    {{-- =========================================================
        USER MANAGEMENT
    ========================================================= --}}
    @if ($admin->is_super_admin)

        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#userSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-people-fill me-2"></i>

                <span class="flex-grow-1">
                    User Management
                </span>

                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="userSubmenu"
            >

                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('admin.users.index') }}"
                    >
                        All Users
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="#"
                    >
                        Add User
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="#"
                    >
                        Roles
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="#"
                    >
                        Permissions
                    </a>

                </li>


                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('admin.admins.index') }}"
                    >

                        <i class="bi bi-person-gear me-1"></i>

                        Manage Admins

                    </a>

                </li>

            </ul>

        </li>

    @endif



    {{-- =========================================================
        ORDERS
    ========================================================= --}}
    @if ($admin->is_super_admin || $admin->can_orders)

        @php

            $newOrdersCount =
                \App\Models\Order::where(
                    'is_read',
                    false
                )->count();

        @endphp


        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                href="{{ route('admin.orders.index') }}"
            >

                <i class="bi bi-bag me-2"></i>


                <span class="flex-grow-1">
                    Orders
                </span>


                <span
                    id="orderBadge"
                    class="badge bg-danger rounded-pill"
                    style="{{ $newOrdersCount > 0 ? '' : 'display:none;' }}"
                >

                    {{ $newOrdersCount }}

                </span>

            </a>

        </li>


        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                href="{{ route('admin.payments.index') }}"
            >

                <i class="bi bi-credit-card me-2"></i>

                <span class="flex-grow-1">
                    Payments
                </span>

            </a>

        </li>

    @endif



    {{-- =========================================================
        SUPER ADMIN ONLY
    ========================================================= --}}
    @if ($admin->is_super_admin)

        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                href="{{ route('admin.customers') }}"
            >

                <i class="bi bi-people-fill me-2"></i>

                <span class="flex-grow-1">
                    Customers
                </span>

            </a>

        </li>


        {{-- REPORTS --}}
        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                href="#"
            >

                <i class="bi bi-bar-chart-line me-2"></i>

                <span class="flex-grow-1">
                    Reports
                </span>

            </a>

        </li>


        {{-- SEO --}}
        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#seoSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-graph-up-arrow me-2"></i>

                <span class="flex-grow-1">
                    SEO & Marketing
                </span>

                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="seoSubmenu"
            >

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        SEO Settings
                    </a>
                </li>

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        Meta Tags
                    </a>
                </li>

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        Campaigns
                    </a>
                </li>

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        Analytics
                    </a>
                </li>

            </ul>

        </li>


        {{-- SYSTEM SECURITY --}}
        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#systemSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-shield-lock-fill me-2"></i>

                <span class="flex-grow-1">
                    System & Security
                </span>

                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="systemSubmenu"
            >

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        Security Settings
                    </a>
                </li>

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        Login Logs
                    </a>
                </li>

                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('recycle-bin.index') }}"
                    >
                        Recycle Bin
                    </a>

                </li>

                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('admin.website-backup.index') }}"
                    >
                        Website Backup
                    </a>

                </li>

                <li>

                    <a
                        class="nav-link text-light py-1"
                        href="{{ route('admin.activity-logs.index') }}"
                    >
                        Activity Logs
                    </a>

                </li>

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        System Logs
                    </a>
                </li>

            </ul>

        </li>


        {{-- SETTINGS --}}
        <li class="nav-item">

            <a
                class="nav-link text-light d-flex align-items-center px-3 py-2"
                data-bs-toggle="collapse"
                href="#settingsSubmenu"
                role="button"
                aria-expanded="false"
            >

                <i class="bi bi-gear-fill me-2"></i>

                <span class="flex-grow-1">
                    Settings
                </span>

                <i class="bi bi-chevron-right ms-auto"></i>

            </a>


            <ul
                class="collapse list-unstyled ps-4"
                id="settingsSubmenu"
            >

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        General
                    </a>
                </li>

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        Profile
                    </a>
                </li>

                <li>
                    <a class="nav-link text-light py-1" href="#">
                        Notifications
                    </a>
                </li>

            </ul>

        </li>

    @endif


</ul>

@endif

</div>


<style>

.bg-sidbare {
    background-color: #000;
}


#sidebar {

    background-color: #000000;

    width: 260px;

    transition: all 0.35s ease;

    overflow-y: auto;

    z-index: 999;

    height: 100vh;

    padding-bottom: 40px;

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


#sidebar .nav-link.active {

    background: #1a1a1a;

    color: #fff;

    border: 1px solid #333;

}


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


#sidebar .bi-chevron-right {
    transition: transform 0.3s ease;
}


#sidebar .nav-link[aria-expanded="true"] .bi-chevron-right {
    transform: rotate(90deg);
}


#sidebar ul ul .nav-link {

    position: relative;

    font-size: 13px;

    color: #999;

    padding: 8px 14px;

}


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


#sidebar .badge {

    font-size: 10px;

    min-width: 20px;

    height: 20px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 0 6px;

}


#sidebar::-webkit-scrollbar {
    width: 4px;
}


#sidebar::-webkit-scrollbar-track {
    background: #000;
}


#sidebar::-webkit-scrollbar-thumb {

    background: #333;

    border-radius: 4px;

}

</style>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const sidebar =
        document.getElementById('sidebar');


    const sidebarToggle =
        document.getElementById('sidebarToggleTop');


    const sidebarClose =
        document.getElementById('sidebarClose');


    const mainLinks =
        document.querySelectorAll(
            '#sidebar .nav-link[data-bs-toggle="collapse"]'
        );


    const allLinks =
        document.querySelectorAll(
            '#sidebar .nav-link'
        );


    const subLinks =
        document.querySelectorAll(
            '#sidebar ul ul .nav-link'
        );



    /*
    |--------------------------------------------------------------------------
    | SIDEBAR TOGGLE
    |--------------------------------------------------------------------------
    */

    if (sidebarToggle && sidebar) {

        sidebarToggle.addEventListener(
            'click',
            () => {

                sidebar.classList.toggle('hide');

            }
        );

    }



    if (sidebarClose && sidebar) {

        sidebarClose.addEventListener(
            'click',
            () => {

                sidebar.classList.add('hide');

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | COLLAPSE MENU
    |--------------------------------------------------------------------------
    */

    mainLinks.forEach(link => {

        link.addEventListener(
            'click',
            function () {

                mainLinks.forEach(l => {

                    if (l !== this) {

                        l.classList.remove('active');


                        const selector =
                            l.getAttribute('href');


                        if (
                            selector &&
                            selector.startsWith('#')
                        ) {

                            const target =
                                document.querySelector(
                                    selector
                                );


                            if (target) {

                                target.classList.remove(
                                    'show'
                                );

                            }

                        }

                    }

                });


                this.classList.toggle(
                    'active'
                );

            }
        );

    });



    /*
    |--------------------------------------------------------------------------
    | NORMAL LINKS
    |--------------------------------------------------------------------------
    */

    allLinks.forEach(link => {

        if (
            !link.hasAttribute(
                'data-bs-toggle'
            )
        ) {

            link.addEventListener(
                'click',
                function () {

                    allLinks.forEach(l => {

                        l.classList.remove(
                            'active'
                        );

                    });


                    subLinks.forEach(s => {

                        s.classList.remove(
                            'active'
                        );

                    });


                    this.classList.add(
                        'active'
                    );

                }
            );

        }

    });



    /*
    |--------------------------------------------------------------------------
    | SUB MENU ACTIVE
    |--------------------------------------------------------------------------
    */

    subLinks.forEach(sub => {

        sub.addEventListener(
            'click',
            function () {

                subLinks.forEach(s => {

                    s.classList.remove(
                        'active'
                    );

                });


                this.classList.add(
                    'active'
                );


                const parentCollapse =
                    this.closest(
                        '.collapse'
                    );


                if (parentCollapse) {

                    const parentLink =
                        document.querySelector(
                            `[href="#${parentCollapse.id}"]`
                        );


                    if (parentLink) {

                        parentLink.classList.add(
                            'active'
                        );

                    }

                }

            }
        );

    });



    /*
    |--------------------------------------------------------------------------
    | CURRENT URL ACTIVE MENU
    |--------------------------------------------------------------------------
    */

    const currentUrl =
        window.location.href;


    allLinks.forEach(link => {

        const href =
            link.getAttribute('href');


        if (
            href &&
            href !== '#' &&
            currentUrl.includes(href)
        ) {

            link.classList.add(
                'active'
            );


            const parentCollapse =
                link.closest(
                    '.collapse'
                );


            if (parentCollapse) {

                parentCollapse.classList.add(
                    'show'
                );


                const parentLink =
                    document.querySelector(
                        `[href="#${parentCollapse.id}"]`
                    );


                if (parentLink) {

                    parentLink.classList.add(
                        'active'
                    );


                    parentLink.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                }

            }

        }

    });



    /*
    |--------------------------------------------------------------------------
    | HELPER: SET BADGE
    |--------------------------------------------------------------------------
    */

    function setBadge(id, count) {

        const element =
            document.getElementById(id);


        if (!element) {
            return;
        }


        count =
            Number(count || 0);


        if (count > 0) {

            element.textContent =
                count;


            element.style.display =
                'inline-flex';

        } else {

            element.textContent =
                '';


            element.style.display =
                'none';

        }

    }



    /*
    |--------------------------------------------------------------------------
    | ORDER BADGE
    |--------------------------------------------------------------------------
    */

    async function updateOrderBadge() {

        try {

            const badge =
                document.getElementById(
                    'orderBadge'
                );


            if (!badge) {
                return;
            }


            const response =
                await fetch(
                    "{{ route('admin.orders.unreadCount') }}",
                    {
                        headers: {

                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'

                        }
                    }
                );


            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            setBadge(
                'orderBadge',
                data.count
            );


        } catch (error) {

            console.log(
                'Order count check failed'
            );

        }

    }



    /*
    |--------------------------------------------------------------------------
    | FORMS BADGES
    |--------------------------------------------------------------------------
    */

    async function updateAllFormBadges() {

        const totalFormsBadge =
            document.getElementById(
                'totalFormsBadge'
            );


        if (!totalFormsBadge) {
            return;
        }


        try {

            const artRes =
                await fetch(
                    '/admin/artwork-unread-count',
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );


            const artData =
                await artRes.json();


            setBadge(
                'artworkBadge',
                artData.count
            );



            const memRes =
                await fetch(
                    '/admin/membership-unread-count',
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );


            const memData =
                await memRes.json();


            setBadge(
                'membershipBadge',
                memData.count
            );



            const poRes =
                await fetch(
                    '/admin/place-orders-unread-count',
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );


            const poData =
                await poRes.json();


            setBadge(
                'placeOrderBadge',
                poData.count
            );



            const total =

                Number(
                    artData.count || 0
                )

                +

                Number(
                    memData.count || 0
                )

                +

                Number(
                    poData.count || 0
                );


            setBadge(
                'totalFormsBadge',
                total
            );


        } catch (error) {

            console.log(
                'Form badge update failed'
            );

        }

    }



    /*
    |--------------------------------------------------------------------------
    | CONTACT US BADGE
    |--------------------------------------------------------------------------
    */

    async function updateContactMessageBadge() {

        const contactBadge =
            document.getElementById(
                'contactMessageBadge'
            );


        const websiteBadge =
            document.getElementById(
                'websiteContactBadge'
            );


        /*
         * Agar super admin nahi hai
         * to ye elements nahi honge.
         */

        if (
            !contactBadge &&
            !websiteBadge
        ) {
            return;
        }


        try {

            const response =
                await fetch(
                    "{{ route('admin.contact-messages.unreadCount') }}",
                    {
                        headers: {

                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'

                        }
                    }
                );


            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            const count =
                Number(
                    data.count || 0
                );


            setBadge(
                'contactMessageBadge',
                count
            );


            setBadge(
                'websiteContactBadge',
                count
            );


        } catch (error) {

            console.log(
                'Contact messages count check failed'
            );

        }

    }



    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    */

    updateOrderBadge();

    updateAllFormBadges();

    updateContactMessageBadge();



    /*
    |--------------------------------------------------------------------------
    | AUTO REFRESH EVERY 5 SECONDS
    |--------------------------------------------------------------------------
    */

    setInterval(
        updateOrderBadge,
        5000
    );


    setInterval(
        updateAllFormBadges,
        5000
    );


    setInterval(
        updateContactMessageBadge,
        5000
    );

});

</script>
