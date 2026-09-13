@extends('inventory.layout')
@section('title', 'Dashboard Overview')

@section('content')
    @push('head')
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            .dashboard-wrapper {
                font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            }

            /* HERO HEADER (CLEAN) */
            .hero-dashboard-clean {
                margin-bottom: 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                padding-bottom: 16px;
                border-bottom: 1px solid var(--color-border);
            }

            .user-avatar-badge-clean {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                background: var(--color-card);
                border: 1px solid var(--color-border);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                font-weight: 800;
                color: var(--color-primary);
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                margin-right: 16px;
                flex-shrink: 0;
            }

            .hero-title-clean {
                font-size: 1.4rem;
                font-weight: 800;
                letter-spacing: -0.4px;
                color: var(--color-text-primary);
                margin-bottom: 2px;
            }

            .hero-sub-clean {
                color: var(--color-text-muted);
                font-size: 0.85rem;
                font-weight: 500;
                margin-bottom: 0;
            }
        </style>
    @endpush

    <div class="container-fluid dashboard-wrapper">
        <!-- Hero Header -->
        <div class="hero-dashboard-clean">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="user-avatar-badge-clean">
                    {{ strtoupper(substr(Auth::user()->name ?? 'M', 0, 1)) }}
                </div>
                <div>
                    <h2 class="hero-title-clean">Welcome back, {{ Auth::user()->name }} 👋</h2>
                    <p class="hero-sub-clean">MEMCO Enterprise ERP System • Overview & Live Analytics</p>
                </div>
            </div>

            <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                <span class="badge text-uppercase" style="background: var(--color-card); color: var(--color-text-muted); border: 1px solid var(--color-border); padding: 8px 12px; font-weight: 600; font-size: 11px;">
                    <i class="fas fa-user-shield text-info mr-1"></i> {{ Auth::user()->getRoleNames()->first() ?? 'User' }}
                </span>

                <a href="{{ route('transfer.list') }}" class="btn-primary-memco">
                    View M.R Transfers
                </a>
            </div>
        </div>

        <!-- Dynamic Counter Grid Container -->
        <div id="counter_grid">
            @include('inventory._dashboardCounters')
        </div>
    </div>
@endsection
