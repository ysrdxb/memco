<div class="app-sidebar">
    <div class="sidebar-header">
        <a class="header-brand" href="{{route('dashboard')}}">
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
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-chart-line"></i><span>{{ __('Dashboard')}}</span>
                    </a>
                </div>
    
                <!-- Inventory Pages -->
                <div class="nav-item {{ ($segment1 == 'products' || $segment1 == 'categories' || $segment1 == 'brands' || $segment1 == 'units') ? 'active open' : '' }} has-sub">
                    <a href="#">
                        <i class="fas fa-cubes"></i><span>{{ __('Products / Tools')}}</span>
                    </a>
                    <div class="submenu-content">
                        @can('manage_products')
                        <a href="{{ url('products/create') }}" class="menu-item {{ ($segment1 == 'products' && $segment2 == 'create') ? 'active' : '' }}">{{ __('Add Product')}}</a>
                        <a href="{{ route('products.list') }}" class="menu-item {{ ($segment1 == 'products' && $segment2 == '') ? 'active' : '' }}">{{ __('List Products')}}</a>
                        @endcan
                        <a href="{{ route('categories.list') }}" class="menu-item {{ ($segment1 == 'categories') ? 'active' : '' }}">{{ __('Categories')}}</a>
                        <a href="{{ route('brands.list') }}" class="menu-item {{ ($segment1 == 'brands') ? 'active' : '' }}">{{ __('Brands')}}</a>
                        <a href="{{ route('units.list') }}" class="menu-item {{ ($segment1 == 'units') ? 'active' : '' }}">{{ __('Units')}}</a>
                    </div>
                </div>
    
                <!-- Material Requests -->
                <div class="nav-item {{ ($segment1 == 'transfers' || $segment1 == 'purchases' || $segment1 == 'deliveryOrder' || $segment1 == 'directTransfers') ? 'active open' : '' }} has-sub">
                    <a href="#">
                        <i class="fas fa-exchange-alt"></i><span>{{ __('Material Requests')}}</span>
                    </a>
                    <div class="submenu-content">
                        @can('manage_material_request')
                        <a href="{{ route('transfer.list') }}" class="menu-item {{ ($segment1 == 'transfers' && $segment2 == '') ? 'active' : '' }}">{{ __('Material Requests')}}</a>
                        <a href="{{ route('transfer.import.create') }}" class="menu-item {{ ($segment1 == 'transfers' && $segment2 == 'import') ? 'active' : '' }}">{{ __('Import M.R')}}</a>
                        @endcan
                        
                        @can('manage_purchase')
                        <a href="{{ route('purchases.list') }}" class="menu-item {{ ($segment1 == 'purchases') ? 'active' : '' }}">{{ __('Purchases')}}</a>
                        @endcan
                        @can('manage_delivery_orders')
                        <a href="{{ route('deliveryOrder.list') }}" class="menu-item {{ ($segment1 == 'deliveryOrder') ? 'active' : '' }}">{{ __('Delivery Orders')}}</a>
                        @endcan
                        <a href="{{ route('directTransfers.list') }}" class="menu-item {{ ($segment1 == 'directTransfers') ? 'active' : '' }}">{{ __('Direct Transfer')}}</a>
                        <a href="{{ route('warehouses.materialIssued') }}" class="menu-item">{{ __('Material Issued')}}</a>
                    </div>
                </div>

                <div class="nav-lables">Store & Supply Management</div>
                
                @can('manage_warehouse')
                <div class="nav-item {{ ($segment1 == 'warehouses') ? 'active' : '' }}">
                    <a href="{{ route('warehouses.list') }}" class="menu-item"><i class="fas fa-warehouse"></i><span>{{ __('Manage Warehouse')}}</span></a>
                </div>
                @endcan

                @can('manage_store')
                <div class="nav-item {{ ($segment1 == 'stores') ? 'active' : '' }}">
                    <a href="{{ route('stores.list') }}" class="menu-item"><i class="fas fa-project-diagram"></i><span>{{ __('Manage Projects')}}</span></a>
                </div>
                @endcan

                @can('manage_supplier')
                <div class="nav-item {{ ($segment1 == 'suppliers' || $segment1 == 'supplier') ? 'active' : '' }}">
                    <a href="{{ route('suppliers.list') }}" class="menu-item"><i class="fas fa-truck-loading"></i><span>{{ __('Suppliers')}}</span></a>
                </div>
                @endcan
                
                <!-- Manage Users -->
                @can('manage_user')
                <div class="nav-lables">User Administration</div>

                <div class="nav-item {{ ($segment1 == 'users' || $segment1 == 'user' || $segment1 == 'departments' || $segment1 == 'positions' || $segment1 == 'roles' || $segment1 == 'permission' || $segment1 == 'employees') ? 'active open' : '' }} has-sub">
                    <a href="#">
                        <i class="fas fa-users-cog"></i><span>{{ __('Manage Users')}}</span>
                    </a>
                    <div class="submenu-content">
                        <a href="{{ url('users') }}" class="menu-item {{ ($segment1 == 'users') ? 'active' : '' }}">{{ __('Users')}}</a>
                        <a href="{{ url('user/create') }}" class="menu-item {{ ($segment1 == 'user' && $segment2 == 'create') ? 'active' : '' }}">{{ __('Add User')}}</a>
                        <a href="{{ route('departments.list') }}" class="menu-item {{ ($segment1 == 'departments') ? 'active' : '' }}">{{ __('Departments')}}</a>
                        <a href="{{ route('positions.list') }}" class="menu-item {{ ($segment1 == 'positions') ? 'active' : '' }}">{{ __('Positions')}}</a>
                        @can('manage_role')
                        <a href="{{ url('roles') }}" class="menu-item {{ ($segment1 == 'roles') ? 'active' : '' }}">{{ __('Roles')}}</a>
                        @endcan
                        @can('manage_permission')
                        <a href="{{ url('permission') }}" class="menu-item {{ ($segment1 == 'permission') ? 'active' : '' }}">{{ __('Permission')}}</a>
                        @endcan
                        <a href="{{ url('employees') }}" class="menu-item {{ ($segment1 == 'employees') ? 'active' : '' }}">{{ __('Employees')}}</a>
                        <a href="{{ url('employee/create') }}" class="menu-item {{ ($segment1 == 'employee' && $segment2 == 'create') ? 'active' : '' }}">{{ __('Add Employee')}}</a>
                    </div>
                </div>
                @endcan

                <!-- Settings -->
                @can('manage_settings')
                <div class="nav-item {{ ($segment1 == 'cities' || $segment1 == 'settings') ? 'active open' : '' }} has-sub">
                    <a href="#">
                        <i class="fas fa-cog"></i><span>{{ __('Settings')}}</span>
                    </a>
                    <div class="submenu-content">
                        <a href="{{ route('settings.index') }}" class="menu-item {{ ($segment1 == 'settings') ? 'active' : '' }}">{{ __('System Settings')}}</a>
                        <a href="{{ route('cities.list') }}" class="menu-item {{ ($segment1 == 'cities') ? 'active' : '' }}">{{ __('Cities')}}</a>
                    </div>
                </div>
                @endcan

                <div class="nav-lables">AI Assistant</div>
                <div class="nav-item {{ ($segment1 == 'ai-chat') ? 'active' : '' }}">
                    <a href="{{ route('ai.chat') }}">
                        <i class="fas fa-robot" style="color: var(--color-accent);"></i><span style="font-weight: 700;">{{ __('Ask AI')}}</span>
                    </a>
                </div>
            </nav>
        </div>
    </div>
</div>