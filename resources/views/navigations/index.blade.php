@extends('layouts.dashboard')

@section('content')

<div class="container-fluid py-4 navigation-page">

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <span class="page-label">Website Menu</span>

            <h2 class="page-title">
                Navigation Management
            </h2>

            <p class="page-description">
                Manage website navigation links, dropdown menus and visibility.
            </p>
        </div>

        <a
            href="{{ route('navigations.create') }}"
            class="btn add-navigation-btn"
        >
            <i class="bi bi-plus-lg"></i>
            Add Navigation
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="success-alert" id="success-msg">
            <div class="success-alert-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <div class="success-alert-content">
                <strong>Success</strong>
                <span>{{ session('success') }}</span>
            </div>

            <button
                type="button"
                class="success-alert-close"
                onclick="this.closest('.success-alert').remove()"
            >
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    {{-- Navigation Cards --}}
    <div class="row g-3">

        @forelse($navigations as $nav)

            <div class="col-12 col-md-6 col-lg-4 col-xxl-3">

                <div class="navigation-card">

                    {{-- Card Top --}}
                    <div class="navigation-card-top">

                        <div class="navigation-card-icon">
                            <i class="bi bi-menu-button-wide"></i>
                        </div>

                        <div class="navigation-card-heading">
                            <span class="navigation-number">
                                Navigation #{{ $nav->id }}
                            </span>

                            <h4 title="{{ $nav->title }}">
                                {{ $nav->title }}
                            </h4>
                        </div>

                        <span
                            class="status-badge {{ $nav->status ? 'active' : 'inactive' }}"
                        >
                            <span class="status-dot"></span>
                            {{ $nav->status ? 'Active' : 'Inactive' }}
                        </span>

                    </div>

                    {{-- Route --}}
                    <div class="navigation-route-box">
                        <span class="route-label">
                            <i class="bi bi-signpost-2"></i>
                            Route
                        </span>

                        @if($nav->route)
                            <span class="route-value" title="{{ $nav->route }}">
                                {{ $nav->route }}
                            </span>
                        @else
                            <span class="route-value empty">
                                No route
                            </span>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="navigation-details">

                        <div class="navigation-detail-item">
                            <div class="detail-icon">
                                <i class="bi bi-chevron-down"></i>
                            </div>

                            <div class="detail-content">
                                <span>Dropdown</span>
                                <strong>
                                    {{ $nav->has_dropdown ? 'Enabled' : 'Disabled' }}
                                </strong>
                            </div>

                            <span
                                class="detail-badge {{ $nav->has_dropdown ? 'enabled' : 'disabled' }}"
                            >
                                {{ $nav->has_dropdown ? 'Yes' : 'No' }}
                            </span>
                        </div>

                        <div class="navigation-detail-item">
                            <div class="detail-icon">
                                <i class="bi bi-cursor"></i>
                            </div>

                            <div class="detail-content">
                                <span>Clickable</span>
                                <strong>
                                    {{ $nav->clickable ? 'Enabled' : 'Disabled' }}
                                </strong>
                            </div>

                            <span
                                class="detail-badge {{ $nav->clickable ? 'enabled' : 'disabled' }}"
                            >
                                {{ $nav->clickable ? 'Yes' : 'No' }}
                            </span>
                        </div>

                        <div class="navigation-detail-item">
                            <div class="detail-icon">
                                <i class="bi bi-sort-numeric-down"></i>
                            </div>

                            <div class="detail-content">
                                <span>Position</span>
                                <strong>Menu order</strong>
                            </div>

                            <span class="position-badge">
                                {{ $nav->position }}
                            </span>
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="navigation-actions">

                        <a
                            href="{{ route('navigations.edit', $nav->id) }}"
                            class="navigation-action-btn edit"
                        >
                            <i class="bi bi-pencil-square"></i>
                            Edit
                        </a>

                        <form
                            action="{{ route('navigations.destroy', $nav->id) }}"
                            method="POST"
                            class="navigation-action-form"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="navigation-action-btn delete"
                                onclick="return confirm('Are you sure you want to delete this navigation?')"
                            >
                                <i class="bi bi-trash"></i>
                                Delete
                            </button>
                        </form>

                    </div>

                    {{-- Toggle Status --}}
                    <div class="navigation-toggle-area">

                        <div class="navigation-toggle-text">
                            <span>Navigation Status</span>

                            <strong>
                                {{ $nav->status ? 'Visible on website' : 'Hidden from website' }}
                            </strong>
                        </div>

                        <form
                            action="{{ route('navigations.toggle-status', $nav->id) }}"
                            method="POST"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="navigation-switch {{ $nav->status ? 'active' : 'inactive' }}"
                                title="{{ $nav->status ? 'Disable navigation' : 'Enable navigation' }}"
                            >
                                <span class="navigation-switch-circle"></span>
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="empty-navigation-state">

                    <div class="empty-navigation-icon">
                        <i class="bi bi-menu-button-wide"></i>
                    </div>

                    <h4>No Navigations Found</h4>

                    <p>
                        No navigation menu has been created yet.
                        Add your first navigation item to get started.
                    </p>

                    <a
                        href="{{ route('navigations.create') }}"
                        class="btn add-navigation-btn"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Add Navigation
                    </a>

                </div>

            </div>

        @endforelse

    </div>

</div>


<style>
/* =========================================================
   PAGE
========================================================= */

.navigation-page {
    max-width: 1700px;
}

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 22px;
    margin-bottom: 22px;
}

.page-label {
    display: block;
    margin-bottom: 4px;
    color: #777;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}

.page-title {
    margin: 0 0 5px;
    color: #111;
    font-size: 29px;
    font-weight: 850;
    line-height: 1.2;
}

.page-description {
    margin: 0;
    color: #707070;
    font-size: 15px;
    line-height: 1.5;
}

.add-navigation-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 43px;
    padding: 10px 18px;
    border: 1px solid #000;
    border-radius: 9px;
    background: #000;
    color: #fff;
    font-size: 14px;
    font-weight: 750;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.add-navigation-btn:hover {
    border-color: #222;
    background: #222;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 7px 16px rgba(0, 0, 0, 0.14);
}

/* =========================================================
   SUCCESS ALERT
========================================================= */

.success-alert {
    position: relative;
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 20px;
    padding: 13px 48px 13px 14px;
    border: 1px solid #dcdcdc;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 7px 20px rgba(0, 0, 0, 0.05);
}

.success-alert-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 36px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #000;
    color: #fff;
    font-size: 17px;
}

.success-alert-content strong {
    display: block;
    margin-bottom: 1px;
    color: #111;
    font-size: 15px;
    font-weight: 800;
}

.success-alert-content span {
    display: block;
    color: #666;
    font-size: 13px;
}

.success-alert-close {
    position: absolute;
    top: 50%;
    right: 15px;
    padding: 4px;
    border: 0;
    background: transparent;
    color: #777;
    font-size: 15px;
    transform: translateY(-50%);
}

/* =========================================================
   NAVIGATION CARD
========================================================= */

.navigation-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    border: 1px solid #dedede;
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 5px 17px rgba(0, 0, 0, 0.05);
    transition:
        transform 0.22s ease,
        box-shadow 0.22s ease,
        border-color 0.22s ease;
}

.navigation-card:hover {
    border-color: #bcbcbc;
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
}

/* =========================================================
   CARD TOP
========================================================= */

.navigation-card-top {
    position: relative;
    display: flex;
    align-items: center;
    gap: 11px;
    min-height: 78px;
    padding: 14px;
    border-bottom: 1px solid #ededed;
}

.navigation-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 40px;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #111;
    color: #fff;
    font-size: 18px;
}

.navigation-card-heading {
    min-width: 0;
    padding-right: 74px;
}

.navigation-number {
    display: block;
    margin-bottom: 2px;
    color: #929292;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.35px;
    text-transform: uppercase;
}

.navigation-card-heading h4 {
    margin: 0;
    overflow: hidden;
    color: #111;
    font-size: 17px;
    font-weight: 850;
    line-height: 1.3;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.status-badge {
    position: absolute;
    top: 14px;
    right: 13px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border-radius: 18px;
    font-size: 11px;
    font-weight: 750;
}

.status-badge.active {
    background: #111;
    color: #fff;
}

.status-badge.inactive {
    background: #ededed;
    color: #666;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

/* =========================================================
   ROUTE
========================================================= */

.navigation-route-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 11px;
    margin: 11px 14px 0;
    padding: 9px 10px;
    border: 1px solid #e4e4e4;
    border-radius: 8px;
    background: #f8f8f8;
}

.route-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #555;
    font-size: 12px;
    font-weight: 800;
    white-space: nowrap;
}

.route-value {
    max-width: 150px;
    overflow: hidden;
    color: #111;
    font-family: monospace;
    font-size: 12px;
    font-weight: 750;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.route-value.empty {
    color: #999;
    font-family: inherit;
    font-weight: 600;
}

/* =========================================================
   DETAILS
========================================================= */

.navigation-details {
    display: grid;
    gap: 7px;
    padding: 11px 14px;
}

.navigation-detail-item {
    display: flex;
    align-items: center;
    gap: 9px;
    min-height: 51px;
    padding: 7px 8px;
    border: 1px solid #ececec;
    border-radius: 8px;
    background: #fff;
}

.detail-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f0f0f0;
    color: #111;
    font-size: 14px;
}

.detail-content {
    flex: 1;
    min-width: 0;
}

.detail-content span {
    display: block;
    margin-bottom: 1px;
    color: #888;
    font-size: 11px;
    font-weight: 650;
}

.detail-content strong {
    display: block;
    color: #222;
    font-size: 13px;
    font-weight: 800;
}

.detail-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    padding: 5px 7px;
    border-radius: 18px;
    font-size: 11px;
    font-weight: 800;
}

.detail-badge.enabled {
    background: #111;
    color: #fff;
}

.detail-badge.disabled {
    background: #eee;
    color: #777;
}

.position-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #111;
    color: #fff;
    font-size: 13px;
    font-weight: 850;
}

/* =========================================================
   ACTIONS
========================================================= */

.navigation-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 7px;
    margin-top: auto;
    padding: 0 14px 10px;
}

.navigation-action-form {
    width: 100%;
}

.navigation-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    min-height: 35px;
    padding: 7px 9px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    transition: all 0.2s ease;
}

.navigation-action-btn.edit {
    border: 1px solid #111;
    background: #111;
    color: #fff;
}

.navigation-action-btn.edit:hover {
    background: #2b2b2b;
    color: #fff;
}

.navigation-action-btn.delete {
    border: 1px solid #d3d3d3;
    background: #fff;
    color: #222;
}

.navigation-action-btn.delete:hover {
    border-color: #111;
    background: #111;
    color: #fff;
}

/* =========================================================
   STATUS TOGGLE
========================================================= */

.navigation-toggle-area {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 14px;
    border-top: 1px solid #e9e9e9;
    background: #fafafa;
}

.navigation-toggle-text span {
    display: block;
    margin-bottom: 1px;
    color: #555;
    font-size: 12px;
    font-weight: 800;
}

.navigation-toggle-text strong {
    display: block;
    color: #929292;
    font-size: 10px;
    font-weight: 650;
}

.navigation-switch {
    position: relative;
    width: 42px;
    height: 23px;
    padding: 0;
    border: 0;
    border-radius: 30px;
    transition: background 0.2s ease;
}

.navigation-switch.active {
    background: #111;
}

.navigation-switch.inactive {
    background: #c9c9c9;
}

.navigation-switch-circle {
    position: absolute;
    top: 4px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.18);
    transition: left 0.2s ease;
}

.navigation-switch.active .navigation-switch-circle {
    left: 23px;
}

.navigation-switch.inactive .navigation-switch-circle {
    left: 4px;
}

/* =========================================================
   EMPTY STATE
========================================================= */

.empty-navigation-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    min-height: 330px;
    padding: 35px 20px;
    border: 1px solid #e1e1e1;
    border-radius: 14px;
    background: #fff;
    text-align: center;
}

.empty-navigation-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 66px;
    height: 66px;
    margin-bottom: 16px;
    border-radius: 16px;
    background: #111;
    color: #fff;
    font-size: 27px;
}

.empty-navigation-state h4 {
    margin: 0 0 7px;
    color: #111;
    font-size: 20px;
    font-weight: 850;
}

.empty-navigation-state p {
    max-width: 450px;
    margin: 0 0 18px;
    color: #777;
    font-size: 14px;
    line-height: 1.6;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (min-width: 1400px) and (max-width: 1599px) {
    .route-value {
        max-width: 125px;
    }
}

@media (max-width: 767px) {
    .page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .page-title {
        font-size: 25px;
    }

    .page-description {
        font-size: 14px;
    }

    .add-navigation-btn {
        width: 100%;
    }

    .navigation-card-heading {
        padding-right: 0;
    }

    .status-badge {
        position: static;
        margin-left: auto;
    }

    .navigation-card-top {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .navigation-route-box {
        align-items: flex-start;
        flex-direction: column;
    }

    .route-value {
        max-width: 100%;
        text-align: left;
    }
}

@media (max-width: 420px) {
    .navigation-actions {
        grid-template-columns: 1fr;
    }

    .navigation-toggle-area {
        align-items: flex-start;
        flex-direction: column;
    }

    .navigation-switch {
        align-self: flex-end;
    }
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const message = document.getElementById('success-msg');

    if (!message) {
        return;
    }

    setTimeout(function () {
        message.style.opacity = '0';
        message.style.transform = 'translateY(-6px)';
        message.style.transition = 'all 0.3s ease';

        setTimeout(function () {
            message.remove();
        }, 300);
    }, 2500);
});
</script>

@endsection
