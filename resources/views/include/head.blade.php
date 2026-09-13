<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="description" content="MEMCO Enterprise ERP System">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('public/img/logo_white.png')}}" />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('public/css/font.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/all.css') }}">
<link rel="stylesheet" href="{{ asset('public/dist/css/theme.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/plugins/icon-kit/dist/css/iconkit.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/plugins/ionicons/dist/css/ionicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/plugins/jquery-toast-plugin/dist/jquery.toast.min.css') }}">
@stack('head')
<link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
<script src="{{ asset('public/js/app.js') }}"></script>

<style>
    /* MEMCO UNIFIED DESIGN SYSTEM & CLEAN LAYOUT OVERRIDES */
    :root {
        --color-primary: #0D1B2A;
        --color-accent: #E63946;
        --color-surface: #F4F6F9;
        --color-card: #FFFFFF;
        --color-border: #E2E8F0;
        --color-text-primary: #1A202C;
        --color-text-muted: #718096;
        --color-text-sidebar: #CBD5E0;
    }

    body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
        background-color: var(--color-surface) !important;
        color: var(--color-text-primary) !important;
    }

    /* TOP HEADER BAR */
    .wrapper .header-top {
        background: var(--color-card) !important;
        border-bottom: 1px solid var(--color-border) !important;
        box-shadow: none !important;
        height: 64px !important;
    }

    /* SIDEBAR CONTAINER & HEADER */
    .wrapper .page-wrap .app-sidebar {
        background-color: var(--color-primary) !important;
        border-right: none !important;
        width: 260px !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-header {
        background: var(--color-primary) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        padding: 0 20px !important;
        height: 64px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
    }

    .sidebar-action {
        color: var(--color-text-sidebar) !important;
        font-size: 1.25rem !important;
        cursor: pointer !important;
        transition: color 0.2s ease !important;
        margin-left: auto;
    }

    .sidebar-action:hover {
        color: #ffffff !important;
    }

    /* NAVIGATION SECTION LABELS & ITEMS */
    .nav-lables {
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.08em !important;
        color: #4A5568 !important;
        padding: 24px 20px 8px 20px !important;
        display: block !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item a {
        color: var(--color-text-sidebar) !important;
        padding: 0 12px !important;
        margin: 2px 12px !important;
        border-radius: 6px !important;
        font-weight: 500 !important;
        font-size: 14px !important;
        text-transform: none !important;
        transition: all 0.2s ease-in-out !important;
        display: flex !important;
        align-items: center !important;
        height: 44px !important;
        border-left: 3px solid transparent !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item a i {
        margin-right: 12px !important;
        width: 18px !important;
        text-align: center !important;
        font-size: 18px !important;
        color: inherit !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item a:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #ffffff !important;
        transform: none !important;
    }

    /* MEMCO ACTIVE MENU PILL */
    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.active > a {
        background: rgba(230, 57, 70, 0.12) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        border-left: 3px solid var(--color-accent) !important;
        border-radius: 0 6px 6px 0 !important;
        box-shadow: none !important;
    }

    /* SIDEBAR NAV ITEMS */
    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item > a > i {
        width: 30px !important;
        text-align: center !important;
        display: inline-block !important;
        margin-right: 8px !important;
        font-size: 1.1rem !important;
        color: #94a3b8 !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.active > a > i {
        color: var(--color-accent) !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item > a > span {
        font-size: 14px !important;
        font-weight: 500 !important;
        letter-spacing: 0.3px !important;
        color: #e2e8f0 !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.active > a > span {
        color: #ffffff !important;
        font-weight: 600 !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.has-sub .submenu-content {
        background: transparent !important;
        margin: 4px 16px 4px 28px !important;
        padding: 4px 0 !important;
        border-left: 2px solid rgba(255,255,255,0.05) !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.has-sub .submenu-content .menu-item {
        display: block !important;
        color: #94a3b8 !important;
        font-size: 13px !important;
        padding: 8px 16px 8px 24px !important;
        text-transform: none !important;
        transition: all 0.2s !important;
        position: relative !important;
        font-weight: 400 !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.has-sub .submenu-content .menu-item::before {
        display: none !important;
        content: none !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.has-sub .submenu-content .menu-item:hover {
        color: #ffffff !important;
        background: transparent !important;
    }

    .wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.has-sub .submenu-content .menu-item.active {
        color: #ffffff !important;
        font-weight: 600 !important;
        background: transparent !important;
        border-left: 2px solid var(--color-accent) !important;
        margin-left: -2px !important;
        padding-left: 24px !important;
    }


    /* MAIN CONTENT & FOOTER */
    .wrapper .page-wrap .main-content {
        background: var(--color-surface) !important;
        margin-left: 260px !important;
        padding: 24px 32px 40px 32px !important;
        transition: margin-left 0.25s ease-in-out !important;
    }

    body.sidebar-mini .wrapper .page-wrap .main-content,
    body.nav-collapsed .wrapper .page-wrap .main-content,
    body.menu-collapsed .wrapper .page-wrap .main-content {
        margin-left: 70px !important;
        padding: 24px 32px 40px 32px !important;
    }

    .wrapper .page-wrap .footer {
        background: var(--color-card) !important;
        border-top: 1px solid var(--color-border) !important;
        padding: 16px 28px !important;
        margin-left: 260px !important;
        transition: margin-left 0.25s ease-in-out !important;
        position: relative !important;
        z-index: 10 !important;
    }

    body.sidebar-mini .wrapper .page-wrap .footer,
    body.nav-collapsed .wrapper .page-wrap .footer,
    body.menu-collapsed .wrapper .page-wrap .footer {
        margin-left: 70px !important;
    }

    .footer span {
        color: #64748b !important;
    }

    /* HEADER COMPONENTS */
    .btn-track-records {
        background: var(--color-surface) !important;
        color: var(--color-text-primary) !important;
        border: 1px solid var(--color-border) !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    .btn-track-records:hover {
        background: var(--color-border) !important;
        color: var(--color-primary) !important;
    }

    .user-locator-badge {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: 20px;
        padding: 6px 16px;
        color: var(--color-text-primary);
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .user-locator-badge:hover {
        background: var(--color-border);
    }

    .status-dot-online {
        width: 10px;
        height: 10px;
        background: #10b981;
        border: 2px solid var(--color-card);
        border-radius: 50%;
        position: absolute;
        bottom: 0;
        right: 0;
    }

    .header-icon-btn {
        color: var(--color-text-muted) !important;
        transition: color 0.2s;
    }

    .header-icon-btn:hover {
        color: var(--color-primary) !important;
    }

    /* BUTTONS */
    .btn-primary-memco {
        background: var(--color-accent) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        display: inline-flex !important;
        align-items: center !important;
        text-decoration: none !important;
        transition: opacity 0.2s;
    }
    .btn-primary-memco:hover {
        opacity: 0.9;
        color: #ffffff !important;
    }

    .btn-secondary-memco {
        background: transparent !important;
        color: var(--color-primary) !important;
        border: 1.5px solid var(--color-primary) !important;
        border-radius: 8px !important;
        padding: 7.5px 16px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        display: inline-flex !important;
        align-items: center !important;
        text-decoration: none !important;
        transition: all 0.2s;
    }
    .btn-secondary-memco:hover {
        background: rgba(13, 27, 42, 0.05) !important;
    }

    /* GLOBAL LIST PAGE STYLES */
    .header-dashboard-clean {
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--color-border);
    }

    .header-icon-box-clean {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--color-card);
        border: 1px solid var(--color-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--color-primary);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-right: 16px;
        flex-shrink: 0;
    }

    .header-title-text-clean {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--color-text-primary);
        margin-bottom: 2px;
        letter-spacing: -0.4px;
    }

    .header-sub-text-clean {
        color: var(--color-text-muted);
        font-size: 0.85rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .table-card {
        background: var(--color-card);
        border-radius: 12px;
        border: 1px solid var(--color-border);
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .table-custom {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }

    .table-custom thead th {
        background: var(--color-surface) !important;
        color: var(--color-text-muted) !important;
        font-size: 0.78rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        padding: 14px 16px !important;
        border-bottom: 2px solid var(--color-border) !important;
        border-top: none !important;
    }

    .table-custom tbody td {
        padding: 14px 16px !important;
        font-size: 0.88rem !important;
        color: var(--color-text-primary) !important;
        vertical-align: middle !important;
        border-bottom: 1px solid var(--color-border) !important;
    }

    .table-custom tbody tr:hover td {
        background-color: var(--color-surface) !important;
    }

    /* DATATABLES OVERRIDES */
    .dt-buttons .btn {
        border-radius: 6px !important;
        font-weight: 600 !important;
        font-size: 0.8rem !important;
        padding: 6px 12px !important;
        margin-right: 6px !important;
        border: 1px solid var(--color-primary) !important;
        background: transparent !important;
        color: var(--color-primary) !important;
        transition: all 0.2s;
    }

    .dt-buttons .btn:hover {
        background: var(--color-primary) !important;
        color: #ffffff !important;
    }

    .dataTables_filter input {
        border: 1.5px solid var(--color-border) !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        outline: none !important;
        font-size: 0.88rem !important;
        color: var(--color-text-primary) !important;
        background: var(--color-card) !important;
    }

    .dataTables_filter input:focus {
        border-color: var(--color-accent) !important;
        box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.15) !important;
    }

    .dataTables_length select {
        border: 1.5px solid var(--color-border) !important;
        border-radius: 8px !important;
        padding: 4px 8px !important;
        outline: none !important;
        color: var(--color-text-primary) !important;
        background: var(--color-card) !important;
    }

    body.sidebar-mini .navigation-main .nav-item a span,
    body.nav-collapsed .navigation-main .nav-item a span,
    body.menu-collapsed .navigation-main .nav-item a span,
    body.sidebar-mini .header-brand .logo-img,
    body.nav-collapsed .header-brand .logo-img,
    body.menu-collapsed .header-brand .logo-img {
        display: none !important;
    }

    body.sidebar-mini .navigation-main .nav-item a,
    body.nav-collapsed .navigation-main .nav-item a,
    body.menu-collapsed .navigation-main .nav-item a {
        justify-content: center !important;
        margin: 6px 10px !important;
        padding: 12px 10px !important;
        border-radius: 6px !important;
    }

    body.sidebar-mini .navigation-main .nav-item a i,
    body.nav-collapsed .navigation-main .nav-item a i,
    body.menu-collapsed .navigation-main .nav-item a i {
        margin-right: 0 !important;
        font-size: 18px !important;
    }
    /* DATATABLES PAGINATION */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 14px !important;
        margin-left: 4px !important;
        border-radius: 8px !important;
        border: 1px solid var(--color-border) !important;
        background: var(--color-surface) !important;
        color: var(--color-text-muted) !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        transition: all 0.2s !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--color-border) !important;
        color: var(--color-primary) !important;
        border-color: #cbd5e1 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--color-accent) !important;
        color: #ffffff !important;
        border: 1px solid var(--color-accent) !important;
        box-shadow: 0 2px 4px rgba(230, 57, 70, 0.2) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
        background: var(--color-surface) !important;
        color: #94a3b8 !important;
        border: 1px solid var(--color-border) !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
    }

    /* DATATABLES LOADING / PROCESSING */
    .dataTables_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid var(--color-border) !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        color: var(--color-primary) !important;
        font-weight: 700 !important;
        padding: 12px 20px !important;
        z-index: 9999 !important;
    }

    .dataTables_wrapper .dataTables_processing .loader {
        border: 3px solid rgba(0, 0, 0, 0.1);
        border-top: 3px solid var(--color-accent);
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: memco-spin 1s linear infinite;
        display: inline-block;
        vertical-align: middle;
        margin-right: 8px;
    }

    @keyframes memco-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
