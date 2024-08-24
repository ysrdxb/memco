<div class="app-sidebar">
    <div class="sidebar-header">
        <a class="header-brand" href="{{ route('dashboard') }}">
            <div class="logo-img">
               <img width="100" src="{{ asset('public/img/logo_white.png')}}" class="header-brand-img" title=""> 
            </div>
        </a>
        <div class="sidebar-action"><i class="ik ik-arrow-left-circle"></i></div>
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
                <div class="nav-item">
                    <a href="{{ route('dashboard')}}" class="{{ ($segment1 == 'dashboard') ? 'text-red' : '' }}"><i class="ik ik-bar-chart-2"></i><span>{{ __('Dashboard')}}</span></a>
                </div>
                @can('manage_store') 
                    @if(Auth::user()->project()->first())                 
                        <div class="nav-item">
                            <a href="{{ route('myprojects') }}" class="menu-item {{ ($segment1 == 'transfers' && $segment2 == 'stores' && $segment3 == 'list' || $segment1 == 'transfers' && $segment2 == 'stores' && $segment3 == 'create') ? 'text-danger' : '' }}">
                                <i class="fas fa-cubes"></i><span>{{ __('My Projects')}}</span>
                            </a>   

                            <a href="{{ route('transfer.list') }}" class="menu-item {{ ($segment1 == 'transfers' && $segment2 == 'stores' && $segment3 == 'list' || $segment1 == 'transfers' && $segment2 == 'stores' && $segment3 == 'create') ? 'text-danger' : '' }}">
                                <i class="fas fa-reply-all"></i><span>{{ __('Material Requests')}}</span>
                            </a>
                            <a href="{{ route('transfer.import.create') }}" class="menu-item {{ ($segment1 == 'transfers' && $segment2 == 'stores' && $segment3 == 'list' || $segment1 == 'transfers' && $segment2 == 'stores' && $segment3 == 'create') ? 'text-danger' : '' }}">
                                <i class="fas fa-file-import"></i><span>{{ __('Import M.R')}}</span>
                            </a>                            
                            
                            <a href="{{ route('purchases.list') }}" class="menu-item {{ ($segment1 == 'purchases' && $segment2 == '' || $segment1 == 'purchases' && $segment2 == 'create') ? 'active' : '' }}">
                            <i class="ik ik-shopping-bag"></i><span>{{ __('Purchases')}}</span>
                            </a>                            
                            <a href="{{ route('deliveryOrder.list') }}" class="menu-item {{ ($segment1 == 'stores' && $segment2 == 'material' && $segment3 == 'issued') ? 'text-danger' : '' }}">
                                <i class="ik ik-truck"></i><span>{{ __('Delivery Orders')}}</span>
                            </a>
                            <a href="{{ route('stores.materialIssued') }}" class="menu-item {{ ($segment1 == 'stores' && $segment2 == 'transferReturns') ? 'text-danger' : '' }}">
                                <i class="ik ik-rotate-cw"></i><span>{{ __('Material Issued')}}</span>
                            </a>                            
                            <a href="{{ route('transferReturns.list') }}" class="menu-item {{ ($segment1 == 'stores' && $segment2 == 'transferReturns') ? 'text-danger' : '' }}">
                                <i class="ik ik-rotate-cw"></i><span>{{ __('Material Returns')}}</span>
                            </a>
                            <a href="{{ route('stores.stock', encrypt(Auth::user()->project->first()->id)) }}" class="menu-item {{ ($segment1 == 'stores' && $segment2 == 'stocks') ? 'text-danger' : '' }}">
                                <i class="ik ik-more-vertical"></i><span>{{ __('Stocks List')}}</span>
                            </a>
                            <a href="{{ route('suppliers.list') }}" class="menu-item {{ ($segment1 == 'supplier' && $segment2 == '') ? 'text-danger' : '' }}">
                                <i class="ik ik-user-plus"></i><span>{{ __('Suppliers')}}</span>
                            </a>                            
                        </div>
                        
                        <div class="nav-item {{ ($segment1 == 'employees' || $segment1 == 'employee') ? 'active open' : '' }} has-sub">
                            <a href="#">
                                <i class="ik ik-users"></i><span>{{ __('Manage Employees')}}</span>
                            </a>
                            <div class="submenu-content">
                                <a href="{{ url('employees') }}" class="menu-item {{ ($segment1 == 'employees') ? 'active' : '' }}">{{ __('Employees')}}</a>
                                <a href="{{ url('employee/create') }}" class="menu-item {{ ($segment1 == 'employee' && $segment2 == 'create') ? 'active' : '' }}">{{ __('Add Employee')}}</a>
                            </div>
                        </div>

                    @endif
                @endcan  
        </div>
    </div>
</div> 