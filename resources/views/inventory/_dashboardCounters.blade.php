<style>
    .stat-section-header {
        margin-top: 24px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-section-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--color-text-muted);
        display: inline-flex;
        align-items: center;
        margin: 0;
    }

    .section-dot-bar {
        width: 4px;
        height: 14px;
        border-radius: 4px;
        display: inline-block;
        margin-right: 8px;
        background: var(--color-primary);
    }

    .dash-auto-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .card-link-wrapper {
        text-decoration: none !important;
        color: inherit !important;
        display: block;
        height: 100%;
    }

    .stat-card-clean {
        background: var(--color-card);
        border-radius: 12px;
        border: 1px solid var(--color-border);
        padding: 20px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        height: 100%;
        min-height: 104px;
    }

    .stat-card-clean:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .stat-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        margin-left: 12px;
    }

    .icon-bg-navy { background: var(--color-primary); color: #ffffff; }
    .icon-bg-red { background: var(--color-accent); color: #ffffff; }
    .icon-bg-slate { background: #4A5568; color: #ffffff; }

    .stat-card-info {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .stat-card-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 8px 0;
    }

    .stat-card-number {
        font-size: 28px;
        font-weight: 800;
        color: var(--color-text-primary);
        line-height: 1.1;
        letter-spacing: -0.5px;
    }

    /* M.R Status Pill Styling */
    .status-badge-completed {
        background: #dcfce7;
        color: #15803d;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
    }

    .status-badge-pending {
        background: #fef3c7;
        color: #b45309;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
    }
</style>

<!-- ==================== SECTION 1: OPERATIONS & TRANSFERS ==================== -->
<div class="stat-section-header">
    <h6 class="stat-section-title">
        <span class="section-dot-bar"></span> Operations & Material Requests
    </h6>
</div>

<div class="dash-auto-grid">
    <!-- M.R Status -->
    <a href="{{ route('transfer.getStatus') }}" class="card-link-wrapper">
        <div class="stat-card-clean">
            <div class="stat-card-info">
                <h6 class="stat-card-title">M.R Status</h6>
                <div class="mt-1 d-flex align-items-center flex-wrap" style="gap: 5px;">
                    <span class="status-badge-completed">
                        <i class="fas fa-check-circle mr-1"></i> {{ intval($total_transfers - $transfer_status) }} Done
                    </span>
                    <span class="status-badge-pending">
                        <i class="fas fa-clock mr-1"></i> {{ $transfer_status }} Pend.
                    </span>
                </div>
            </div>
            <div class="stat-card-icon icon-bg-navy">
                <i class="fas fa-tasks"></i>
            </div>
        </div>
    </a>

    <!-- M.R Tracking -->
    <a href="{{ route('transfer.list') }}" class="card-link-wrapper">
        <div class="stat-card-clean">
            <div class="stat-card-info">
                <h6 class="stat-card-title">M.R Tracking</h6>
                <div class="stat-card-number">{{ $total_transfers }}</div>
            </div>
            <div class="stat-card-icon icon-bg-slate">
                <i class="fas fa-route"></i>
            </div>
        </div>
    </a>

    <!-- L.P.O Records -->
    <a href="{{ route('purchases.list') }}" class="card-link-wrapper">
        <div class="stat-card-clean">
            <div class="stat-card-info">
                <h6 class="stat-card-title">L.P.O Records</h6>
                <div class="stat-card-number">{{ $count_purchases }}</div>
            </div>
            <div class="stat-card-icon icon-bg-navy">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>
    </a>

    <!-- Delivery Orders -->
    <a href="{{ route('deliveryOrder.list') }}?type=do" class="card-link-wrapper">
        <div class="stat-card-clean">
            <div class="stat-card-info">
                <h6 class="stat-card-title">Delivery Orders</h6>
                <div class="stat-card-number" id="deliveryOrders">{{ $count_deliveryOrders }}</div>
            </div>
            <div class="stat-card-icon icon-bg-slate">
                <i class="fas fa-truck"></i>
            </div>
        </div>
    </a>

    <!-- M.T.V -->
    <a href="{{ route('deliveryOrder.list') }}?type=mtv" class="card-link-wrapper">
        <div class="stat-card-clean">
            <div class="stat-card-info">
                <h6 class="stat-card-title">M.T.V</h6>
                <div class="stat-card-number" id="deliveryOrdersByWarehouse">{{ $count_deliveryOrdersByWarehouse }}</div>
            </div>
            <div class="stat-card-icon icon-bg-red">
                <i class="fas fa-shipping-fast"></i>
            </div>
        </div>
    </a>
</div>

<!-- ==================== SECTION 2: INVENTORY & MASTERS ==================== -->
<div class="stat-section-header">
    <h6 class="stat-section-title">
        <span class="section-dot-bar"></span> Inventory & Resources
    </h6>
</div>

<div class="dash-auto-grid">
    @if(Auth::user()->hasRole('Super Admin'))
        <a href="{{ route('stores.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Projects / Stores</h6>
                    <div class="stat-card-number" id="warehouse">{{ \App\Models\Project::count() }}</div>
                </div>
                <div class="stat-card-icon icon-bg-navy">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
        </a>
    @endif

    @if($count_products)
        <a href="{{ route('products.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Products</h6>
                    <div class="stat-card-number" id="products">{{ $count_products }}</div>
                </div>
                <div class="stat-card-icon icon-bg-slate">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
        </a>
    @endif

    @if($count_tools)
        <a href="{{ route('products.list') }}?type=tool" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Tools</h6>
                    <div class="stat-card-number" id="tools">{{ $count_tools }}</div>
                </div>
                <div class="stat-card-icon icon-bg-navy">
                    <i class="fas fa-tools"></i>
                </div>
            </div>
        </a>
    @endif

    @if($count_brands)
        <a href="{{ route('brands.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Brands</h6>
                    <div class="stat-card-number" id="brands">{{ $count_brands }}</div>
                </div>
                <div class="stat-card-icon icon-bg-slate">
                    <i class="fas fa-award"></i>
                </div>
            </div>
        </a>
    @endif

    @if($count_suppliers)
        <a href="{{ route('suppliers.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Suppliers</h6>
                    <div class="stat-card-number" id="suppliers">{{ $count_suppliers }}</div>
                </div>
                <div class="stat-card-icon icon-bg-red">
                    <i class="fas fa-truck-loading"></i>
                </div>
            </div>
        </a>
    @endif

    @if($count_warehouse)
        <a href="{{ route('warehouses.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Main Stores</h6>
                    <div class="stat-card-number" id="warehouse">{{ $count_warehouse }}</div>
                </div>
                <div class="stat-card-icon icon-bg-navy">
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>
        </a>
    @endif
</div>

<!-- ==================== SECTION 3: PROJECT SPECIFIC DATA ==================== -->
@if(Auth::user()->project->first())
    <div class="stat-section-header">
        <h6 class="stat-section-title">
            <span class="section-dot-bar"></span> Project Store Details
        </h6>
    </div>
    <div class="dash-auto-grid">
        <a href="{{ route('myprojects') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">My Projects</h6>
                    <div class="stat-card-number">{{ $count_myprojects }}</div>
                </div>
                <div class="stat-card-icon icon-bg-slate">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('stores.materialIssued') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Material Issued</h6>
                    <div class="stat-card-number">{{ $count_projectmissued }}</div>
                </div>
                <div class="stat-card-icon icon-bg-navy">
                    <i class="fas fa-dolly"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('stores.stock', encrypt(Auth::user()->project->first()->id)) }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Track Stock</h6>
                    <div class="mt-2">
                        <span class="text-danger font-weight-bold" style="font-size: 14px;">View &rarr;</span>
                    </div>
                </div>
                <div class="stat-card-icon icon-bg-slate">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('transferReturns.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Material Returns</h6>
                    <div class="mt-2">
                        <span class="text-danger font-weight-bold" style="font-size: 14px;">View &rarr;</span>
                    </div>
                </div>
                <div class="stat-card-icon icon-bg-navy">
                    <i class="fas fa-undo"></i>
                </div>
            </div>
        </a>
    </div>
@endif

<!-- ==================== SECTION 4: ADMINISTRATION ==================== -->
<div class="stat-section-header">
    <h6 class="stat-section-title">
        <span class="section-dot-bar"></span> Administration & Master Data
    </h6>
</div>

<div class="dash-auto-grid">
    @if($count_categories)
        <a href="{{ route('categories.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Activities</h6>
                    <div class="stat-card-number" id="categories">{{ $count_categories }}</div>
                </div>
                <div class="stat-card-icon icon-bg-slate">
                    <i class="fas fa-list"></i>
                </div>
            </div>
        </a>
    @endif

    @if($count_subcategories)
        <a href="{{ route('categories.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Sub Activities</h6>
                    <div class="stat-card-number" id="subcategories">{{ $count_subcategories }}</div>
                </div>
                <div class="stat-card-icon icon-bg-navy">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </a>
    @endif

    @if($count_users)
        <a href="{{ route('users.list') }}" class="card-link-wrapper">
            <div class="stat-card-clean">
                <div class="stat-card-info">
                    <h6 class="stat-card-title">Users</h6>
                    <div class="stat-card-number" id="users">{{ $count_users }}</div>
                </div>
                <div class="stat-card-icon icon-bg-red">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </a>
    @endif

    <a href="{{ route('employees.list') }}" class="card-link-wrapper">
        <div class="stat-card-clean">
            <div class="stat-card-info">
                <h6 class="stat-card-title">Employees</h6>
                <div class="stat-card-number">{{ \App\Models\Employee::count() }}</div>
            </div>
            <div class="stat-card-icon icon-bg-navy">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </a>
</div>