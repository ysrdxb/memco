<div class="app-sidebar">
    <div class="sidebar-header">
        <a class="header-brand" href="{{route('dashboard')}}">
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
                    <a href="{{ route('dashboard') }}">
                        <i class="ik ik-bar-chart-2"></i><span>{{ __('Dashboard')}}</span>
                    </a>
                </div>
    
                <!-- Inventory Pages -->
                <div class="nav-item has-sub">
                    <a href="#">
                        <i class="fas fa-cubes"></i><span>{{ __('Products / Tools')}}</span>
                    </a>
                    <div class="submenu-content">
                        @can('manage_products')
                        <a href="{{ url('products/create') }}" class="menu-item">{{ __('Add Product')}}</a>
                        <a href="{{ route('products.list') }}" class="menu-item">{{ __('List Products')}}</a>
                        @endcan
                        <a href="{{ route('categories.list') }}" class="menu-item">{{ __('Categories')}}</a>
                        <a href="{{ route('brands.list') }}" class="menu-item">{{ __('Brands')}}</a>
                        <a href="{{ route('units.list') }}" class="menu-item">{{ __('Units')}}</a>
                    </div>
                </div>
    
                <!-- Material Requests -->
                <div class="nav-item has-sub">
                    <a href="#">
                        <i class="fas fa-reply-all"></i><span>{{ __('Material Requests')}}</span>
                    </a>
                    <div class="submenu-content">
                        @can('manage_material_request')
                        <a href="{{ route('transfer.list') }}" class="menu-item">{{ __('Material Requests')}}</a>
                        <a href="{{ route('transfer.import.create') }}" class="menu-item">{{ __('Import M.R')}}</a>
                        
                        @endcan
                        
                        @can('manage_purchase')
                        <a href="{{ route('purchases.list') }}" class="menu-item">{{ __('Purchases')}}</a>
                        @endcan
                        @can('manage_delivery_orders')
                        <a href="{{ route('deliveryOrder.list') }}" class="menu-item">{{ __('Delivery Orders')}}</a>
                        @endcan
                        <a href="{{ route('directTransfers.list') }}" class="menu-item">{{ __('Direct Transfer')}}</a>
                        <a href="{{ route('warehouses.materialIssued') }}" class="menu-item">{{ __('Material Issued')}}</a>
                        
                    </div>
                </div>
                
    
                <!-- Suppliers -->
                
                <div class="nav-item">
                    @can('manage_warehouse')
                    <a href="{{ route('warehouses.list') }}" class="menu-item"><i class="fa fa-home"></i><span>{{ __('Manage Warehouse')}}</span></a>
                    
                    @endcan                    
                    @can('manage_store')
                    <a href="{{ route('stores.list') }}" class="menu-item"><i class="fa fa-project-diagram"></i><span>{{ __('Manage Projects')}}</span></a>
                    @endcan   
                    @can('manage_supplier')
                    <a href="{{ route('suppliers.list') }}" class="menu-item">
                        <i class="ik ik-user-plus"></i><span>{{ __('Suppliers')}}</span>
                    </a>
                    @endcan                    
                </div>
                
    
                <!-- Manage Users -->
                @can('manage_user')
                <div class="nav-item has-sub">
                    <a href="#">
                        <i class="ik ik-users"></i><span>{{ __('Manage Users')}}</span>
                    </a>
                    <div class="submenu-content">
                        <a href="{{ url('users') }}" class="menu-item">{{ __('Users')}}</a>
                        <a href="{{ url('user/create') }}" class="menu-item">{{ __('Add User')}}</a>
                        <a href="{{ route('departments.list') }}" class="menu-item">{{ __('Departments')}}</a>
                        <a href="{{ route('positions.list') }}" class="menu-item">{{ __('Positions')}}</a>
                        @can('manage_role')
                        <a href="{{ url('roles') }}" class="menu-item">{{ __('Roles')}}</a>
                        @endcan
                        @can('manage_permission')
                        <a href="{{ url('permission') }}" class="menu-item">{{ __('Permission')}}</a>
                        @endcan
                        <a href="{{ url('employees') }}" class="menu-item">{{ __('Employees')}}</a>
                        <a href="{{ url('employee/create') }}" class="menu-item">{{ __('Add Employee')}}</a>
                    </div>
                </div>
                @endcan

                <!-- Settings -->
                @can('manage_settings')
                <div class="nav-item has-sub">
                    <a href="#">
                        <i class="ik ik-settings"></i><span>{{ __('Settings')}}</span>
                    </a>
                    <div class="submenu-content">
                        <a href="{{ route('cities.list') }}" class="menu-item">{{ __('Cities')}}</a>
                    </div>
                </div>
                @endcan
            </nav>
        </div>
    </div>
    
</div>