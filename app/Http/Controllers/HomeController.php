<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\MaterialRequest;
use Auth;
use App\Models\User;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\StockRecord;
use App\Models\Purchase;
use App\Models\DeliveryOrder;
use App\Models\Project;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\StoreMaterialIssued;
use App\Models\WarehouseMaterialIssued;
use Illuminate\Http\Request;
use App\Services\SwitchProject;

class HomeController extends Controller
{
    protected $switchProject;

    public function __construct(SwitchProject $switchProject)
    {
        $this->switchProject = $switchProject;
    }

    public function index(): View
    { 
        return view('home');
    }

    public function myprojects()
    {
        $projects = $this->switchProject->getUserProjects();
        return view('projects.myprojects', compact('projects'));
    }
    
    public function switchProject(Request $request) 
    {
        $id = decrypt($request->project_id);
        $user = Auth::user();
    
        $project = Project::whereHas('users', function($query) use ($user, $id) {
            $query->where('user_id', $user->id)
                  ->where('project_id', $id);
        })->firstOrFail();
    
        $this->switchProject->switchToProject($id);
    
        return redirect()->route('dashboard');
    }       

    public function dashboard(): view
    {        
        return view('inventory.dashboard');
    }

    public function dashboardData()
    {
        $user = Auth::user();

        $transfers = Transfer::query();
        $transfer_status = 0;
        $purchases = Purchase::query();
        $deliveryOrders = DeliveryOrder::query();
        $deliveryOrdersByWarehouse = DeliveryOrder::query();
        $projects = Project::query();
        // Add other queries for counts
        $total_transfers = $transfers->count();
        $count_myprojects = 0;

        if ($user->hasRole(['Store Incharge'])) {
            // Filter transfers based on the current project ID
            $currentProjectId = $this->switchProject->getCurrentProjectId();
            $transfers->where('requestable_id', $currentProjectId)->where('requestable_type', Project::class);
            $total_transfers = $transfers->count();
            $count_myprojects = count($this->switchProject->getUserProjects());
            
                $transfers = Transfer::where('requestable_type', Project::class)
                    ->where('requestable_id', $currentProjectId)
                    ->whereNull('deleted_at')
                    ->with(['details', 'deliveryOrders'])
                    ->get();
    
                $transfer_status = 0;
                $total_transfers = $transfers->count();
    
                foreach ($transfers as $transfer) {
                    $isPending = false;
    
                    foreach ($transfer->details as $detail) {
                        $totalDelivered = $transfer->deliveryOrders
                            ->where('product_id', $detail->product_id)
                            ->sum('quantity');

                        $balance = $detail->requested_quantity - $totalDelivered;
    
                        if ($detail->status !== 'closed' && $balance > 0) {
                            $isPending = true;
                            break;
                        }
                    }
    
                    if ($isPending) {
                        $transfer_status++;
                    }
                }            

            // Filter other tables based on the project ID associated with the transfers
            $purchases->whereIn('transfer_id', function ($query) use ($currentProjectId) {
                $query->select('id')
                      ->from('transfers')
                      ->where('requestable_type', Project::class)
                      ->where('requestable_id', $currentProjectId);
            });
    
            $deliveryOrders->where('delivered_by', 'supplier')->whereIn('transfer_id', function ($query) use ($transfers) {
                $query->select('id')
                      ->from('transfers')
                      ->whereIn('id', $transfers->pluck('id'));
            });     
            
            $deliveryOrdersByWarehouse->where('delivered_by', 'store')->whereIn('transfer_id', function ($query) use ($transfers) {
                $query->select('id')
                      ->from('transfers')
                      ->whereIn('id', $transfers->pluck('id'));
            });            
    
            $projects->where('id', $currentProjectId);
            // Add other filters based on user's access
            $count_transfers = $transfers->count();
            $count_purchases = $purchases->count();
            
            $count_deliveryOrders = $deliveryOrders->distinct('delivery_order_no')->count('delivery_order_no');  
            $count_deliveryOrdersByWarehouse = $deliveryOrdersByWarehouse->distinct('delivery_order_no')->count('delivery_order_no');           
            $count_projects = $projects->count();
            $count_users = 0;
            $count_products = Product::where('type', 'tool')->count();
            $count_tools = Product::where('type', 'tool')->count();
            $count_brands = 0;
            $count_suppliers = 0;
            $count_categories = 0;
            $count_subcategories = 0;
            $count_projectmissued = StoreMaterialIssued::count();
           // $count_warehousemissued = 0;
            $count_warehouse = 0;
        } else {
            $count_transfers = $transfers->count();
            $count_purchases = $purchases->count();
            $count_deliveryOrders = $deliveryOrders->where('delivered_by', 'supplier')->distinct('delivery_order_no')->count('delivery_order_no');
            $count_projects = $projects->count();
            $count_users = User::where('employee_no', NULL)->count();
            $count_products = Product::where('type', 'product')->count();
            $count_tools = Product::where('type', 'tool')->count();
            $count_brands = Brand::count();
            $count_suppliers = Supplier::count();
            $count_categories = Category::count();
            $count_subcategories = SubCategory::count();
            $count_projectmissued = StoreMaterialIssued::count();
          //  $count_warehousemissued = WarehouseMaterialIssued::count();
            $count_warehouse = Warehouse::count();


            $projectsWithTransferStatus = [];
            $projectIdsWithTransfers = Transfer::where('requestable_type', Project::class)
                ->select('requestable_id')
                ->distinct()
                ->pluck('requestable_id');
    
            $data = Project::whereIn('id', $projectIdsWithTransfers)->get();
            $transfer_status = 0;
            $total_transfers = 0;
            foreach ($data as $project) {
                $transfers = Transfer::where('requestable_type', Project::class)
                    ->where('requestable_id', $project->id)
                    ->whereNull('deleted_at')
                    ->with(['details', 'deliveryOrders'])
                    ->get();
    
                $total_transfers = $transfers->count();
    
                foreach ($transfers as $transfer) {
                    $isPending = false;
    
                    foreach ($transfer->details as $detail) {
                        $deliveredBySupplier = $transfer->deliveryOrders
                            ->where('product_id', $detail->product_id)
                            ->where('delivered_by', 'supplier')
                            ->sum('quantity');
    
                        $deliveredByStore = $transfer->deliveryOrders
                            ->where('product_id', $detail->product_id)
                            ->where('delivered_by', 'store')
                            ->sum('quantity');
    
                        $totalDelivered = $deliveredBySupplier + $deliveredByStore;
                        $balance = $detail->requested_quantity - $totalDelivered;
    
                        if ($detail->status !== 'closed' && $balance > 0) {
                            $isPending = true;
                            break;
                        }
                    }
    
                    if ($isPending) {
                        $transfer_status++;
                    }
                }
    
            }
            
                $transfers_warehouse = Transfer::where('requestable_type', Warehouse::class)
                    ->where('requestable_id', Warehouse::first()->id)
                    ->whereNull('deleted_at')
                    ->with(['details', 'deliveryOrders'])
                    ->get();
    
                $total_transfers = $count_transfers;
                $transfer_status_warehouse = 0;
                foreach ($transfers_warehouse as $transfer) {
                    $isPending = false;
    
                    foreach ($transfer->details as $detail) {
                        $totalDelivered = $transfer->deliveryOrders
                            ->where('product_id', $detail->product_id)
                            ->sum('quantity');
    
                        $balance = $detail->requested_quantity - $totalDelivered;
    
                        if ($detail->status !== 'closed' && $balance > 0) {
                            $isPending = true;
                            break;
                        }
                    }
    
                    if ($isPending) {
                        $transfer_status_warehouse++;
                    }
                }            
            
            $transfer_status = $transfer_status + $transfer_status_warehouse;
            $count_deliveryOrdersByWarehouse = $deliveryOrdersByWarehouse->where('delivered_by', 'store')->distinct('delivery_order_no')->count('delivery_order_no');
        }
    
        $html = view('inventory._dashboardCounters', compact(
            'count_transfers',
            'count_purchases',
            'count_deliveryOrders',
            'count_projects',
            'count_users',
            'count_products',
            'count_tools',
            'count_brands',
            'count_suppliers',
            'count_categories',
            'count_subcategories',
            'count_projectmissued',
           // 'count_warehousemissued',
            'count_warehouse',
            'transfer_status',
            'total_transfers',
            'count_myprojects',
            'count_deliveryOrdersByWarehouse',
        ))->render();
        
        return response()->json(['html' => $html]);
                 
    }          

    public function clearCache(): View
    {
        Artisan::call('cache:clear');

        return view('clear-cache');
    }

    public function getNotifications()
    {
        $notifications = MaterialRequest::where('user_id', Auth::user()->id)
        ->where('status', 'pending')
        ->get();  
        return view('inventory.includes._getNotifications', compact('notifications'));      
    }
    
    public function getCurrentUser()
    {
        $projectName = 'Admin';
        
        if(Auth::user()->project->first())
        {
            $projectName = $this->switchProject->getCurrentProjectName();
        }
        elseif(Auth::user()->warehouses->first())
        {
            $projectName = Auth::user()->warehouses->first()->name;
        }
        
        $projectName = '<marquee behavior="scroll" direction="left" scrollamount="2" style="height: 100%; animation: marquee-up-down 10s linear infinite;">[ '.Auth::user()->name.' '.' ( '.$projectName.' ) ]</marquee>';
        return $projectName;
    }

    public function track_records()
    {
        if(Auth::user()->project->first()) {
            $transfers = Transfer::where('requestable_id', $this->switchProject->getCurrentProjectId())
            ->where('requestable_type', Project::class)
            ->get();
            $projects = Project::where('id', $this->switchProject->getCurrentProjectId())->get();
        } else {
            $transfers = Transfer::all();
            $projects = Project::all();
        }
        $products = Product::all();
        $transfer_records = [];
        $deliveryOrders = [];
        $transferVouchers = [];
        $purchases = [];
        $product = null;
        $project_transfers = [];
        return view('inventory.tracking', compact('transfers', 'transfer_records', 'products', 'product', 'projects', 'project_transfers'));
    }
    
    public function filter_records(Request $request)
    {
        try {
            $transfer_records = [];
            $deliveryOrders = [];
            $transferVouchers = [];
            $purchases = [];
            $product = null;
            $stock = null;
            $project_transfers = [];
            $product_transfers = [];
            $transfers = [];
            $projects = [];
            $products = Product::all(); // Pre-fetch all products to reduce query overhead
    
            // Filter by Transfer ID
            if ($request->filled('transferId')) {
                $transfer_records = Transfer::find($request->transferId);
            }
    
            // Filter by Project ID
            if ($request->filled('projectId')) {
                $project_transfers = $this->getProjectTransfers($request->projectId);
            }
    
            // Filter by Delivery Order ID
            if ($request->filled('deliveryOrderId')) {
                $deliveryOrders = DeliveryOrder::where('id', $request->deliveryOrderId)->get();
            }
    
            // Filter by Transfer Voucher ID
            if ($request->filled('transferVoucherId')) {
                $transferVouchers = TransferVoucher::where('id', $request->transferVoucherId)->get();
            }
    
            // Filter by Purchase ID
            if ($request->filled('purchaseId')) {
                $purchases = Purchase::where('id', $request->purchaseId)->get();
            }
    
            // Filter by Product ID and fetch related transfers and stock
            if ($request->filled('productId')) {
                $product = Product::find($request->productId);
                $product_transfers = Transfer::whereHas('details', function ($query) use ($request) {
                    $query->where('product_id', $request->productId);
                })->get();
    
                $requestable_id = Auth::user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge']) 
                    ? Warehouse::first()->id 
                    : $this->switchProject->getCurrentProjectId();
    
                $stock = StockRecord::where('stockable_id', $requestable_id)
                    ->where('product_id', $request->productId)
                    ->first();
            }
    
            // Determine user role and fetch corresponding transfers and projects
            if ($project = Auth::user()->project->first()) {
                $projectId = $project->id;
                $transfers = Transfer::where('requestable_id', $projectId)
                    ->where('requestable_type', Project::class)
                    ->get();
                $projects = Project::where('id', $projectId)->get();
            } else {
                $transfers = Transfer::all();
                $projects = Project::all();
            }
    
            // Return view with the filtered data
            return view('inventory.tracking', compact(
                'transfers', 
                'deliveryOrders', 
                'transferVouchers', 
                'purchases', 
                'transfer_records', 
                'product', 
                'products', 
                'product_transfers', 
                'project_transfers',
                'stock', 
                'projects'
            ));
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while filtering records.'], 500);
        }
    }
    
    public function getProjectTransfers($projectId)
    {
        try {
            $transfers = Transfer::where('requestable_type', Project::class)
                ->where('requestable_id', $projectId)
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->with('details') 
                ->get();
    
            $filteredTransfers = $transfers->filter(function ($transfer) {
                foreach ($transfer->details as $detail) {
                    if($detail->status === 'closed') {
                        return false;
                    }
                    $requestedQuantity = $detail->requested_quantity;
                    $deliveredQuantity = DeliveryOrder::where('product_id', $detail->product_id)
                        ->where('transfer_id', $detail->transfer_id)
                        ->sum('quantity');
                    if ($deliveredQuantity < $requestedQuantity) {
                        return true;
                    }
                }
                return false;
            });
    
            return $filteredTransfers->values();
    
        } catch (\Exception $e) {
            \Log::error('Error in getProjectTransfers: ' . $e->getMessage());
            throw $e;
        }
    }

    
}
