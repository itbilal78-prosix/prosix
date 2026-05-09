@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap');
.db * { box-sizing:border-box; }
.db { font-family:'DM Sans',sans-serif; color:#111; }
.db-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:28px; }
.db-top h2 { font-family:'Space Grotesk',sans-serif; font-size:28px; font-weight:700; color:#0a0a0a; margin:0 0 4px; letter-spacing:-0.5px; }
.db-top .welcome { font-size:15px; color:#888; }
.db-top .welcome strong { color:#333; font-weight:600; }
.db-date { display:flex; align-items:center; gap:8px; background:#fff; border:1px solid #e5e5e5; border-radius:50px; padding:10px 18px; font-size:14px; color:#555; font-weight:500; white-space:nowrap; box-shadow:0 2px 8px rgba(0,0,0,.04); }

/* Permission Cards */
.perm-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr)); gap:16px; margin-bottom:28px; }
.perm-card { background:#fff; border-radius:20px; padding:24px 20px; border:1px solid #efefef; box-shadow:0 2px 10px rgba(0,0,0,.04); display:flex; flex-direction:column; gap:12px; transition:transform .2s, box-shadow .2s; text-decoration:none; color:#111; }
.perm-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(0,0,0,.09); color:#111; }
.perm-card.locked { opacity:.45; pointer-events:none; filter:grayscale(1); }
.perm-ico { width:44px; height:44px; border-radius:12px; background:#f2f2f2; display:flex; align-items:center; justify-content:center; font-size:22px; }
.perm-ico.dark { background:#111; color:#fff; }
.perm-title { font-family:'Space Grotesk',sans-serif; font-size:16px; font-weight:700; }
.perm-desc { font-size:13px; color:#aaa; line-height:1.5; }
.perm-badge { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600; padding:4px 10px; border-radius:50px; width:fit-content; }
.perm-badge.allowed { background:#e8f5e9; color:#2e7d32; }
.perm-badge.denied  { background:#fce4ec; color:#c62828; }

/* Welcome Banner */
.welcome-banner { background:#0f0f0f; border-radius:20px; padding:28px 28px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:20px; }
.welcome-banner h3 { font-family:'Space Grotesk',sans-serif; font-size:22px; font-weight:700; color:#fff; margin:0 0 6px; }
.welcome-banner p { font-size:14px; color:#888; margin:0; line-height:1.6; }
.welcome-banner-ico { font-size:52px; opacity:.15; flex-shrink:0; }

/* Quick stats */
.quick-stats { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; margin-bottom:24px; }
.qs-card { background:#fff; border:1px solid #efefef; border-radius:16px; padding:18px 16px; box-shadow:0 2px 8px rgba(0,0,0,.04); }
.qs-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#bbb; margin-bottom:8px; }
.qs-val { font-family:'Space Grotesk',sans-serif; font-size:30px; font-weight:700; color:#111; line-height:1; }
.qs-sub { font-size:12px; color:#bbb; margin-top:4px; }
</style>

<div class="db">

  {{-- TOP --}}
  <div class="db-top">
    <div>
      <h2>Dashboard</h2>
      <div class="welcome">Welcome back, <strong>{{ $admin->name }}</strong></div>
    </div>
    <div class="db-date">
      <i class="bi bi-calendar3"></i>
      {{ now()->format('d M Y') }}
    </div>
  </div>

  {{-- WELCOME BANNER --}}
  <div class="welcome-banner">
    <div>
      <h3>Hello, {{ $admin->name }}! </h3>
      <p>You are logged in as a <strong style="color:#fff;">Sub Admin</strong>. You can access the sections assigned to you below. Contact the Super Admin to update your permissions.</p>
    </div>
  </div>



  {{-- PERMISSIONS CARDS --}}
  <div style="margin-bottom:16px;">
    <h5 style="font-family:'Space Grotesk',sans-serif; font-size:16px; font-weight:700; color:#111; margin-bottom:14px;">Your Access Permissions</h5>
  </div>

  <div class="perm-grid">

    {{-- Orders --}}
    <a href="{{ $admin->can_orders ? route('admin.orders.index') : '#' }}"
       class="perm-card {{ $admin->can_orders ? '' : 'locked' }}">
      <div class="perm-ico {{ $admin->can_orders ? 'dark' : '' }}">
        <i class="bi bi-bag-check{{ $admin->can_orders ? '' : '-fill' }}"></i>
      </div>
      <div class="perm-title">Orders</div>
      <div class="perm-desc">View and manage customer orders, update status, shipping info.</div>
      <div class="perm-badge {{ $admin->can_orders ? 'allowed' : 'denied' }}">
        <i class="bi bi-{{ $admin->can_orders ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
        {{ $admin->can_orders ? 'Access Granted' : 'No Access' }}
      </div>
    </a>

    {{-- Products --}}
    <a href="{{ $admin->can_products ? route('products.index') : '#' }}"
       class="perm-card {{ $admin->can_products ? '' : 'locked' }}">
      <div class="perm-ico {{ $admin->can_products ? 'dark' : '' }}">
        <i class="bi bi-box-seam"></i>
      </div>
      <div class="perm-title">Products</div>
      <div class="perm-desc">Add, edit, and manage product listings and inventory.</div>
      <div class="perm-badge {{ $admin->can_products ? 'allowed' : 'denied' }}">
        <i class="bi bi-{{ $admin->can_products ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
        {{ $admin->can_products ? 'Access Granted' : 'No Access' }}
      </div>
    </a>

    {{-- Categories --}}
    <a href="{{ $admin->can_categories ? route('categories.index') : '#' }}"
       class="perm-card {{ $admin->can_categories ? '' : 'locked' }}">
      <div class="perm-ico {{ $admin->can_categories ? 'dark' : '' }}">
        <i class="bi bi-grid-fill"></i>
      </div>
      <div class="perm-title">Categories</div>
      <div class="perm-desc">Manage product categories and subcategories.</div>
      <div class="perm-badge {{ $admin->can_categories ? 'allowed' : 'denied' }}">
        <i class="bi bi-{{ $admin->can_categories ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
        {{ $admin->can_categories ? 'Access Granted' : 'No Access' }}
      </div>
    </a>

    {{-- Customizer --}}
    <a href="{{ $admin->can_customizer ? route('models.index') : '#' }}"
       class="perm-card {{ $admin->can_customizer ? '' : 'locked' }}">
      <div class="perm-ico {{ $admin->can_customizer ? 'dark' : '' }}">
        <i class="bi bi-paint-bucket"></i>
      </div>
      <div class="perm-title">Customizer</div>
      <div class="perm-desc">Manage models, patterns, colors, templates and fonts.</div>
      <div class="perm-badge {{ $admin->can_customizer ? 'allowed' : 'denied' }}">
        <i class="bi bi-{{ $admin->can_customizer ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
        {{ $admin->can_customizer ? 'Access Granted' : 'No Access' }}
      </div>
    </a>

  </div>

</div>
@endsection
