<header class="header-top mb-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="top-menu d-flex align-items-center">
                <button type="button" class="btn-icon mobile-nav-toggle d-lg-none"><span></span></button>
                <div class="header-search">
                    <a href="{{ route('track.home') }}" class="btn-track-records">
                        <i class="fas fa-magnifying-glass mr-2"></i>
                        <span>Track Records</span>
                    </a>
                </div>                
            </div>

            <div class="text-center store-name mx-auto">
                <div class="current_user_locator">
                    <div class="user-locator-badge">
                        <i class="fas fa-location-dot mr-2 text-danger"></i>
                        <span id="current_user_locator" class="text-name mr-2"></span>
                        <i class="fas fa-chevron-down text-muted" style="font-size: 10px;"></i>
                    </div>
                </div>
            </div>            

            <div class="top-menu d-flex align-items-center">
                <div class="dropdown mr-4">
                    <a class="nav-link dropdown-toggle header-icon-btn position-relative" href="#" id="notiDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ik ik-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger badge-pill position-absolute" style="top: 2px; right: 2px; font-size: 0.65rem; padding: 3px 6px;">0</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right notification-dropdown shadow-lg border-0" aria-labelledby="notiDropdown" style="border-radius: 12px; min-width: 300px;">
                        <h5 class="header px-3 py-2 font-weight-bold text-dark border-bottom mb-0" style="font-size: 0.95rem;">{{ __('Notifications')}}</h5>
                        <div class="notifications-wrap" style="max-height:420px;overflow:auto"></div>
                    </div>
                </div>

                <div style="width: 1px; height: 32px; background: var(--color-border); margin-right: 16px;"></div>

                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-wrapper position-relative">
                            <img class="avatar rounded-circle" src="{{ asset('public/img/user.jpg')}}" alt="Profile" style="width: 36px; height: 36px; object-fit: cover; border: 2px solid var(--color-surface);">
                            <span class="status-dot-online"></span>
                        </div>
                        <span class="ml-2 font-weight-bold text-dark d-none d-md-inline" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down ml-2 text-muted" style="font-size: 0.75rem;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 mt-3" aria-labelledby="userDropdown" style="border-radius: 12px; min-width: 220px; padding: 8px 0;">
                        <div class="px-3 py-2 border-bottom mb-1">
                            <strong class="d-block text-dark" style="font-size: 0.9rem;">{{ Auth::user()->name }}</strong>
                            <small class="text-muted d-block text-truncate" style="max-width: 180px;">{{ Auth::user()->email }}</small>
                        </div>
                        <a class="dropdown-item py-2" href="{{ route('profile.edit') }}" style="font-size: 0.88rem; font-weight: 500;">
                            <i class="ik ik-user dropdown-icon text-primary mr-2"></i> 
                            {{ __('Edit Profile')}}
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <a class="dropdown-item py-2 text-danger" href="{{ url('logout') }}" style="font-size: 0.88rem; font-weight: 600;">
                            <i class="ik ik-power dropdown-icon text-danger mr-2"></i> 
                            {{ __('Logout')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>