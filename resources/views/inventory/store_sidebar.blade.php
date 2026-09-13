<div class="app-sidebar">
    <div class="sidebar-header">
        <a class="header-brand" href="{{ route('dashboard') }}">
            <div class="logo-img">
               <img width="105" src="{{ asset('public/img/logo_white.png')}}" class="header-brand-img" alt="MEMCO"> 
            </div>
        </a>
        <div class="sidebar-action" title="Toggle Sidebar"><i class="ik ik-arrow-left-circle"></i></div>
        <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
    </div>

    @php
        $segment1 = request()->segment(1);
        $segment2 = request()->segment(2);
        $segment3 = request()->segment(3);
    @endphp
    
    <div class="sidebar-content">
        <div class="nav-container">
            <nav id="main-menu-navigation" class="navigation-main">
                
                <div class="nav-lables">Main Navigation</div>

                <div class="nav-item {{ ($segment1 == 'dashboard' || $segment1 == '') ? 'active' : '' }}">
                    <a href="{{ route('dashboard')}}">
                        <i class="fas fa-chart-line"></i><span>{{ __('Dashboard')}}</span>
                    </a>
                </div>

                @can('manage_store') 
                    @if(Auth::user()->project()->first())                 
                        <div class="nav-lables">Store & Operations</div>

                        <div class="nav-item {{ ($segment1 == 'myprojects') ? 'active' : '' }}">
                            <a href="{{ route('myprojects') }}">
                                <i class="fas fa-building"></i><span>{{ __('My Projects')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'transfers' && $segment2 != 'import') ? 'active' : '' }}">
                            <a href="{{ route('transfer.list') }}">
                                <i class="fas fa-exchange-alt"></i><span>{{ __('Material Requests')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'transfers' && $segment2 == 'import') ? 'active' : '' }}">
                            <a href="{{ route('transfer.import.create') }}">
                                <i class="fas fa-file-import"></i><span>{{ __('Import M.R')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'purchases') ? 'active' : '' }}">
                            <a href="{{ route('purchases.list') }}">
                                <i class="fas fa-shopping-bag"></i><span>{{ __('Purchases')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'deliveryOrder' || $segment1 == 'delivery-orders') ? 'active' : '' }}">
                            <a href="{{ route('deliveryOrder.list') }}">
                                <i class="fas fa-truck"></i><span>{{ __('Delivery Orders')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'stores' && $segment2 == 'material' && $segment3 == 'issued') ? 'active' : '' }}">
                            <a href="{{ route('stores.materialIssued') }}">
                                <i class="fas fa-dolly"></i><span>{{ __('Material Issued')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'stores' && $segment2 == 'transferReturns') ? 'active' : '' }}">
                            <a href="{{ route('transferReturns.list') }}">
                                <i class="fas fa-undo"></i><span>{{ __('Material Returns')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'stores' && $segment2 == 'stocks') ? 'active' : '' }}">
                            <a href="{{ route('stores.stock', encrypt(Auth::user()->project->first()->id)) }}">
                                <i class="fas fa-boxes-stacked"></i><span>{{ __('Stocks List')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'supplier' || $segment1 == 'suppliers') ? 'active' : '' }}">
                            <a href="{{ route('suppliers.list') }}">
                                <i class="fas fa-truck-loading"></i><span>{{ __('Suppliers')}}</span>
                            </a>
                        </div>

                        <div class="nav-lables">HR & Staff</div>

                        <div class="nav-item {{ ($segment1 == 'employees' || $segment1 == 'employee') ? 'active open' : '' }} has-sub">
                            <a href="#">
                                <i class="fas fa-users"></i><span>{{ __('Manage Employees')}}</span>
                            </a>
                            <div class="submenu-content">
                                <a href="{{ url('employees') }}" class="menu-item {{ ($segment1 == 'employees') ? 'active' : '' }}">{{ __('All Employees')}}</a>
                                <a href="{{ url('employee/create') }}" class="menu-item {{ ($segment1 == 'employee' && $segment2 == 'create') ? 'active' : '' }}">{{ __('Add Employee')}}</a>
                            </div>
                        </div>

                        <div class="nav-lables">AI Assistant</div>
                        <div class="nav-item {{ ($segment1 == 'ai-chat') ? 'active' : '' }}">
                            <a href="{{ route('ai.chat') }}">
                                <i class="fas fa-robot" style="color: var(--color-accent);"></i><span style="font-weight: 700;">{{ __('Ask AI')}}</span>
                            </a>
                        </div>
                    @endif
                @endcan  
                
                @can('manage_user')
                    <div class="nav-lables">System</div>
                    <div class="nav-item {{ ($segment1 == 'settings') ? 'active' : '' }}">
                        <a href="{{ route('settings.index') }}">
                            <i class="fas fa-cogs"></i><span>{{ __('Settings')}}</span>
                        </a>
                    </div>
                @endcan
            </nav>
        </div>
    </div>
</div>