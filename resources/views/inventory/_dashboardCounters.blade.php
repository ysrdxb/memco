<!-- Card 1 - Bootstrap Brain Component -->
<div class="row">
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="card st-cir-card">
            <div class="card-body p-3">
                <a href="{{ route('transfer.getStatus') }}">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h5 class="card-title widget-card-title mb-3">M.R Status</h5>
                            <div class="d-flex align-items-center">
                                <div class="completed-status mr-2" style="background: #26c281;padding: 5px;border-radius: 9px;color: white;">
                                    <h4 class="m-0">{{ intval($total_transfers - $transfer_status) }}</h4>
                                    <span>Completed</span>
                                </div>
                                <div class="pending-status" style="background: #bdbd04;padding: 6px;border-radius: 9px;color: white;">
                                    <h4 class="m-0">{{ $transfer_status }}</h4>
                                    <span>Pending</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-flex justify-content-end">
                                <div class="status-icon p-3 d-flex align-items-center justify-content-center">
                                    <div class="d-flex align-items-center">
                                        <span class="arrow-icon bg-white-subtle text-white rounded-circle p-1 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-right bsb-rotate-45"></i>
                                        </span>
                                        <div>
                                            <p class="fs-7 mb-0">{{ $total_transfers }}</p>
                                            <span>Total</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <a href="{{ route('transfer.list') }}">
            <div class="card st-cir-card p-1">
                <div class="card-block">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="card-title widget-card-title mb-3">M.R TRACKING</h5>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class=" fw-700 mb-4">{{ $total_transfers }}</h3>
                            <h6 class="mb-0 ">All</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>        
                

    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <a href="{{ route('purchases.list') }}">
            <div class="card st-cir-card p-1">
                <div class="card-block">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="card-title widget-card-title mb-3">L.P.O Records</h5>
                        </div>                        
                        <div class="col-6 text-center">
                            <h3 class=" fw-700 mb-4">{{ $count_purchases }}</h3>
                            <h6 class="mb-0 ">All</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>         


    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-orange elevation-2">
                <i class="fas fa-truck"></i></span>
            <div class="info-box-content">
                <a href="{{ route('deliveryOrder.list') }}?type=do">
                    <span class="info-box-text">Delivery Orders</span>
                    <span class="info-box-number">
                        <h2><b id="deliveryOrders">{{ $count_deliveryOrders }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-pink elevation-2">
                <i class="fas fa-truck"></i></span>
            <div class="info-box-content">
                <a href="{{ route('deliveryOrder.list') }}?type=mtv">
                    <span class="info-box-text">M.T.V</span>
                    <span class="info-box-number">
                        <h2><b id="deliveryOrders">{{ $count_deliveryOrdersByWarehouse }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>     
    
    @if(Auth::user()->hasRole('Super Admin'))
        <div class="col-12 col-lg-3 col-md-6 col-sm-12">
            <div class="info-box elevation-2">
                <span class="info-box-icon bg-gradient-olive elevation-2">
                    <i class="fa fa-project-diagram"></i></span>
                <div class="info-box-content">
                    <a href="{{ route('stores.list') }}">
                        <span class="info-box-text">All Projects / Stores</span>
                        <span class="info-box-number">
                            <h2><b id="warehouse">{{ \App\Models\Project::count() }}</b></h2>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    
    @if($count_products)
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-info elevation-2">
                <i class="fas fa-cubes"></i></span>
            <div class="info-box-content">
                <a href="{{ route('products.list') }}">
                    <span class="info-box-text">Products</span>
                    <span class="info-box-number">
                        <h2><b id="products">{{ $count_products }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>
    @endif     
    
    @if($count_tools)
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-pink elevation-2">
                <i class="ik ik-settings"></i></span>
            <div class="info-box-content">
                <a href="{{ route('products.list') }}?type=tool">
                    <span class="info-box-text">Tools</span>
                    <span class="info-box-number">
                        <h2><b id="products">{{ $count_tools }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($count_brands)
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-warning elevation-2">
                <i class="fas fa-award"></i></span>
            <div class="info-box-content">
                <a href="{{ route('brands.list') }}">
                    <span class="info-box-text">Brands</span>
                    <span class="info-box-number">
                        <h2><b id="brands">{{ $count_brands }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($count_suppliers)
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-danger elevation-2">
                <i class="fas fa-user-plus"></i></span>
            <div class="info-box-content">
                <a href="{{ route('suppliers.list') }}">
                    <span class="info-box-text">Suppliers</span>
                    <span class="info-box-number">
                        <h2><b id="suppliers">{{ $count_suppliers }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($count_categories)
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-dark elevation-2">
                <i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <a href="{{ route('categories.list') }}">
                    <span class="info-box-text">Activities</span>
                    <span class="info-box-number">
                        <h2><b id="categories">{{ $count_categories }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($count_subcategories)
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-primary elevation-2">
                <i class="fas fa-list-alt"></i></span>
            <div class="info-box-content">
                <a href="{{ route('categories.list') }}">
                    <span class="info-box-text">Sub Activities</span>
                    <span class="info-box-number">
                        <h2><b id="subcategories">{{ $count_subcategories }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>
    @endif

    @if(Auth::user()->project->first())
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-dark elevation-2">
                <i class="fas fa-cubes"></i></span>
            <div class="info-box-content">
                <a href="{{ route('myprojects') }}">
                    <span class="info-box-text">My Projects</span>
                    <span class="info-box-number">
                        <h2><b id="products">{{ $count_myprojects }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-secondary elevation-2">
                <i class="fas fa-home"></i></span>
            <div class="info-box-content">
                <a href="{{ route('stores.materialIssued') }}">
                    <span class="info-box-text">Material Issued</span>
                    <span class="info-box-number">
                        <h2><b id="projectmissued">{{ $count_projectmissued }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-primary elevation-2">
                <i class="fas fa-box"></i></span>
            <div class="info-box-content">
                <a href="{{ route('stores.stock', encrypt(Auth::user()->project->first()->id)) }}">
                    <span class="info-box-text">Track Stock</span>
                    <span class="info-box-number">
                        <h2><b id="deliveryOrders"></b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div> 
    
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-info elevation-2">
                <i class="fas fa-box"></i></span>
            <div class="info-box-content">
                <a href="{{ route('transferReturns.list') }}">
                    <span class="info-box-text">Material Returns</span>
                    <span class="info-box-number">
                        <h2><b id="deliveryOrders"></b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>        
    @endif        


    @if($count_users)
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-primary elevation-2">
                <i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <a href="{{ route('users.list') }}">
                    <span class="info-box-text">Users</span>
                    <span class="info-box-number">
                        <h2><b id="users">{{ $count_users }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($count_warehouse)
    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-pink elevation-2">
                <i class="fas fa-home"></i></span>
            <div class="info-box-content">
                <a href="{{ route('warehouses.list') }}">
                    <span class="info-box-text">Main Stores</span>
                    <span class="info-box-number">
                        <h2><b id="warehouse">{{ $count_warehouse }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>

    @endif

    <div class="col-12 col-lg-3 col-md-6 col-sm-12">
        <div class="info-box elevation-2">
            <span class="info-box-icon bg-gradient-primary elevation-2">
                <i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <a href="{{ route('employees.list') }}">
                    <span class="info-box-text">Employees</span>
                    <span class="info-box-number">
                        <h2><b id="users">{{ \App\Models\Employee::count() }}</b></h2>
                    </span>
                </a>
            </div>
        </div>
    </div>

</div>