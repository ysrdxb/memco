<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseUser;
use App\Models\User;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\DeliveryOrder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Project;
use App\Models\PurchaseDetail;
use App\Models\StockRecord;
use App\Exports\StockExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Illuminate\View\View;
use DataTables;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Events\NewMaterialRequest;
use Illuminate\Support\Facades\Broadcast;

class MaterialRequestController extends Controller
{
    public function __construct()
    {
        //$materialRequestMessage = 'New material request created successfully !!!';
        //broadcast(new NewMaterialRequest($materialRequestMessage))->toOthers();
        // event(new NewMaterialRequest('hello world'));
    }
    public function show($id)
    {        
        $id = decrypt($id);

        $data = Transfer::with(['details', 'warehouse', 'user'])->findOrFail($id);

        return view('inventory.materialRequest.show', compact('data'));
    }

    public function changeStatus(Request $request, $id)
    {        
        $id = $request->reqid ? decrypt($request->reqid) : $request->reqid; 
    
        $materialRequest = Transfer::findOrFail($id);       
        
        if($materialRequest->status == 'delivered') 
        {
            return response()->json([
                'message' => $id,
                'status' => 'error'
            ], 200);  

            return null;          
        }

        $rules = [
            'comments' => 'required|max:255',
            'status' => 'required',
            'delivered_quantity' => 'required|numeric'
        ];
    
        $validatedData = $request->validate($rules);
        $validatedData['request_id'] = $materialRequest->id;
        //$validatedData['quantity'] = $materialRequest->quantity;

        $userRole = Auth::user()->roles->first()->name;
        if ($userRole === 'Store Incharge') {
            $validatedData['action'] = 'store';
        } else {
            $validatedData['action'] = 'warehouse'; 
        } 
    
        $validatedData['user_id'] = Auth::user()->id;

        if($request->status=='delivered')
        {
            $warehouseId = Auth::user()->warehouse->first()->id;
            $product = $materialRequest->product;
            
            $stock = StockRecord::where('stockable_id', $warehouseId)
                ->where('stockable_type', Warehouse::class)
                ->where('product_id', $product->id)
                ->first();
            
            if (!$stock) {
                return response()->json([
                    'message' => 'Product is <strong class="text-red">Out of Stock</strong>!',
                    'status' => 'error'
                ], 200);
            }
            
            if ($stock->quantity < $request->quantity) {
                return response()->json([
                    'message' => 'Only <strong class="text-red">'.$stock->quantity.' '.$materialRequest->product->unit->short_name.'</strong> in current stock available!',
                    'status' => 'error'
                ], 200);
            }
            StockRecord::where('stockable_id', Auth::user()->warehouse->first()->id)
            ->where('stockable_type', Warehouse::class)
            ->where('product_id', $materialRequest->product_id)
            ->update(['quantity' => $stock->quantity - $request->quantity]);            
        }
    
        TransferDetail::updateOrCreate(['user_id' => Auth::user()->id], $validatedData);

        Transfer::where('id', $id)->update(['status' => $request->status]);

        $redirect = route('warehouses.transfers');
        return response()->json([
            'message' => 'Request status changed successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }    

    public function print($id)
    {
        $id = decrypt($id);

        $data = Transfer::with(['details', 'user', 'warehouse'])->where('id', $id)->first();

        return view('inventory.materialRequest._printRequest', compact('data'));
    }   
    
    public function deliveryOrders()
    {
        $data = DeliveryOrder::with(['purchase', 'transfer', 'product', 'transferDetails', 'deliveryOrder'])
        ->join(
            \DB::raw('(SELECT MAX(id) as max_id, delivery_order_no FROM material_deliveries GROUP BY delivery_order_no) as subquery'),
            function ($join) {
                $join->on('material_deliveries.id', '=', 'subquery.max_id');
                $join->on('material_deliveries.delivery_order_no', '=', 'subquery.delivery_order_no');
            }
        )
        ->get();
           
        return view('inventory.materialRequest.deliveryOrders', compact('data'));
    }    

    public function printDeliveryOrder($id)
    {

        $permissionName = 'verify_delivery_order';
        $permission = Permission::where('name', $permissionName)->first();
        $usersWithPermission = User::permission($permissionName)->get();

        if ($usersWithPermission->isNotEmpty()) {
            $user = $usersWithPermission->first();
        } else {
            $user = Auth::user();
        }

        $id = decrypt($id);

        $data = DeliveryOrder::with(['transfer', 'product', 'transferDetails', 'purchase'])
        ->where('delivery_order_no', $id)
        ->get();
        //dd($data);
        return view('inventory.materialRequest._print_delivery_order', compact('data', 'user'));
    }      

    // warehouse related transfer data
    public function getRequest()
    {
        $data = Auth::user()->hasRole(['Admin', 'Super Admin']) || Auth::user()->warehouse->first() 
        ? Transfer::paginate(12) 
        : null;
            
        return view('inventory.materialRequest.list', compact('data'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $units = Unit::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('inventory.materialRequest.create', compact('categories','brands','units', 'products', 'warehouses'));        
    }    

    public function save(Request $request, $id = NULL)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;      
        $rules = [
            'remarks' => 'required',
        ];
    
        $validatedData = $request->validate($rules);
        $constantPrefix = "MR";
        // $randomNumber = mt_rand(10000, 99999);         
        // $randomCode = $constantPrefix . $randomNumber;        
        // $validatedData['ref_no'] = $randomCode; //'MRQ'.date('dmYhis');
        $lastRequest = Transfer::latest()->first();
        // Increment the last ID to get the next sequence number
        $sequenceNumber = $lastRequest ? (int)substr($lastRequest->ref_no, strlen($constantPrefix)) + 1 : 1;
        // Generate the new reference number
        $validatedData['ref_no'] = $constantPrefix . str_pad($sequenceNumber, 5, '0', STR_PAD_LEFT);        
        $warehouse = Auth::user()->warehouse()->first();

        if ($warehouse) {
            $validatedData['warehouse_id'] = $warehouse->id;
            $validatedData['user_id'] = $warehouse->users()->first()->id;
        } else {
            $firstWarehouse = Warehouse::first();
            $validatedData['warehouse_id'] = $firstWarehouse->id;
            $validatedData['user_id'] = $firstWarehouse->users()->first()->id;
        }
                      
        if($request->has('product_id'))
        {
            $product_ids = $request->product_id;

            $materialRequest = Transfer::updateOrCreate(['id' => $id], $validatedData);

            if($materialRequest)
            {
                foreach($product_ids as $pid)
                {
                    TransferDetail::create([
                        'transfer_id' => $materialRequest->id,
                        'product_id'    => $pid,
                        'requested_quantity'  => $request->quantities[$pid],
                        'delivered_quantity'  => 0,
                        'unit_id' => $request->unit_id[$pid],
                        'brand_id' => $request->brand_id[$pid],
                    ]);
                }
        
                $redirect = route('materialRequest.list');
        
                return response()->json([
                    'message' => 'Request saved successfully',
                    'status' => 'success',
                    'redirect' => $redirect
                ]);
            }
        
        }

        return response()->json([
            'message' => 'An error occured, please select all fields !!',
            'status' => 'error',
        ]);         
       
    }   

    public function _getProducts(Request $request): mixed
    {
        $productIds = $request->input('products', []);
        $products = Product::with(['stockRecords' => function ($query) {
            $query->select('id', 'product_id', 'quantity'); 
        }, 'unit'])->whereIn('id', $productIds)->get();
    
        foreach ($products as $product) {
            if ($product->stockRecords->isNotEmpty()) {
                $quantities = $product->stockRecords->pluck('quantity')->toArray();
                $product->setAttribute('quantities', $quantities);
            } else {
                $product->setAttribute('quantities', null);
            }
        }
        $brands = Brand::all();
        $units = Unit::all();
        return view('inventory.transfer.store._get_products', compact('products', 'brands', 'units'));        
    }        

    public function createdeliveryOrders()
    {
        $purchases = Purchase::with('transfer')->get();
        return view('inventory.materialRequest.createDeliveryOrder',  compact('purchases'));
    }

    public function saveDeliveryOrders(Request $request)
    {
        $rules = [
            'product_id' => 'required|array',
            'material_request_id' => 'required',
            'purchase_id' => 'required|numeric',
            'date'  => 'required'
        ];
    
        $message = [];

        $countDelivered = 0;
        $request->validate($rules);    
    
        $delivery_no = 'DO-'.date('mdY').'-'.date('his');
        $redirect = route('deliveryOrder.list');
        $status = 'success';
    
        if ($request->has('product_id')) 
        {
            $products = $request->product_id;
            $mrid = decrypt($request->material_request_id);
    
            foreach ($products as $pid) 
            {            
                $detail = TransferDetail::where('transfer_id', $mrid)
                    ->where('product_id', $pid)
                    ->first();
    
                if ($detail) 
                {
                    if ($request->quantities[$pid] > 0)
                    {
                        $remainingQuantity = $detail->requested_quantity - $detail->delivered_quantity;
    
                        if ($remainingQuantity > 0 && $request->quantities[$pid] <= $remainingQuantity) 
                        {
                            // Update delivered_quantity in MaterialRequestDetail
                            $newDeliveredQuantity = $detail->delivered_quantity + $request->quantities[$pid];
                            $detail->update(['delivered_quantity' => $newDeliveredQuantity]);                    
    
                            // Create a new MaterialDelivery entry
                            DeliveryOrder::create([
                                'transfer_id' => $mrid,
                                'purchase_id' => $request->purchase_id,
                                'date' => $request->date,
                                'product_id' => $pid,
                                'quantity' => $request->quantities[$pid],
                                'delivery_order_no' => $delivery_no,
                                'delivery_note' => $request->delivery_note,
                                'status' => 'success',
                            ]);
    
                            // Add to stock records
                            $stock = StockRecord::where('product_id', $pid)
                                ->where('stockable_id', $detail->transfer->requestable_id)
                                ->where('stockable_type', $detail->transfer->requestable_type)
                                ->first();
    
                            if ($stock) 
                            {
                                $stock->update(['quantity' => $stock->quantity + $request->quantities[$pid]]);
                            } 
                            else 
                            {
                                StockRecord::create([
                                    'stockable_type' => Warehouse::class,
                                    'stockable_id' => $detail->transfer->requestable_id,
                                    'quantity' => $request->quantities[$pid],
                                    'product_id' => $pid,
                                ]);
                            }
    
                            $countDelivered += 1;
                        } 
                        else 
                        {
                            // If the quantity exceeds the remaining requested quantity, skip this item
                            $message[] = 'The Delivery Order QTY of <strong class="text-danger">'. $detail->product->name .'</strong> exceeds the remaining requested QTY.<br><br>';
                            $status = 'error';                            
                        }
                    }
                } 
            }
            
            // Check if all requested quantities are delivered
            $allDelivered = true;
            $requestDetails = TransferDetail::where('transfer_id', $mrid)->get();
            
            foreach ($requestDetails as $reqDetail) 
            {
                if ($reqDetail->delivered_quantity < $reqDetail->requested_quantity) 
                {
                    $allDelivered = false;
                    break;
                }
            }
                        
            // Update material_requests status
            if ($allDelivered) 
            {
                Transfer::where('id', $mrid)->update(['status' => 'delivered']);
                $countDelivered += 1;
            } 
            else
            {
                $message[] = 'An error occurred, please select all fields !!';
            }
    
            if ($countDelivered > 0) 
            {
                return response()->json([
                    'message' => 'Delivery Order saved successfully', 
                    'status' => 'success', 
                    'redirect' => $redirect
                ], 200);                 
            } 
            
            return response()->json([
                'message' => $message, 
                'status' => 'error', 
            ], 200);                 
            
        }
    
        return response()->json([
            'message' => 'An error occurred, please select all fields !!',
            'status' => 'error',
        ]);        
    }
    

    public function deleteDeliveryOrder($id)
    {
        $id = decrypt($id);

        if($materialDelivery = DeliveryOrders::with('transferDetails')
        ->where('delivery_order_no', $id))
        {       
            $data = DeliveryOrders::with('transferDetails')
                ->where('delivery_order_no', $id)
                ->get();            

            foreach($data as $row)
            {   
                $stock = StockRecord::where('product_id', $row->product_id)
                ->where('stockable_id', $row->transferDetail->transfer->requestable_id)
                ->where('stockable_type', $row->transferDetail->transfer->requestable_type)
                ->first();

                if(!empty($stock))
                {
                    $arr['quantity'] = $stock->quantity - $row->quantity;
                    StockRecord::where('product_id', $row->product_id)
                        ->where('stockable_id', $row->transferDetail->transfer->warehouse_id)
                        ->update($arr);
                }
            }

            $materialDelivery->delete();

            $redirect = route('deliveryOrder.list');

            return response()->json([
                'message' => 'Delivery Order deleted successfully', 
                'status' => 'success', 
                'redirect'=> $redirect
            ], 200);    
        }
        return response()->json(['message' => 'Error occured while deleting', 'status'=>'error'], 200);    
    }    

    public function _dataTable(Request $request)
    {
        $user = Auth::user();

        $requests = null;
    
        if ($user->hasRole(['Admin', 'Super Admin']) || $user->warehouse->isNotEmpty()) {
            // User is Admin, Super Admin, or has a warehouse or stores
            $requests = Transfer::query();
        } elseif($user->stores->isNotEmpty()) {
            // User is not Admin or Super Admin and has no warehouse or stores
            $requests = Transfer::where('user_id', $user->id);
        }
    
        return DataTables::eloquent($requests)
            ->addColumn('date', function ($row) {
                return $row->created_at;
            })
            ->addColumn('ref_no', function ($row) {
                return $row->ref_no;
            })
            ->addColumn('requestable', function ($row) {
                return $row->warehouse->name;
            })
            ->addColumn('requested_by', function ($row) {
                return $row->user->name;
            })
            ->addColumn('status', function ($row) {
                //$status_badge = '<span class="badge badge-pill badge-' . ($row->status == 'received' || $row->status == 'delivered' ? 'primary' : 'danger') . ' mb-1">' . $row->status . '</span>';
                return strtoupper($row->status);
            })
            ->rawColumns(['status'])            
            ->addColumn('action', function ($row) {
                $actions = '<button class="showRequest pr-4 btn btn-secondary btn-rounded" data-tid="' . encrypt($row->id) . '" data-toggle="modal" data-target="#InvoiceModal">Show</button>';
                if (Auth::user()->hasPermissionTo('deleteRequest')) {
                    $actions .= '<a href="#!" class="deleteBtn pl-4" item-id="' . encrypt($row->id) . '"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                }
                return $actions;
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('ref_no', 'like', '%' . $searchValue . '%')
                            ->orWhere('status', 'like', '%' . $searchValue . '%')
                            ->orWhereHas('user', function ($subQuery) use ($searchValue) {
                                $subQuery->where('name', 'like', '%' . $searchValue . '%');
                            })
                            ->orWhereHas('warehouse', function ($subQuery) use ($searchValue) {
                                $subQuery->where('name', 'like', '%' . $searchValue . '%');
                            });
                    });
                }
            })            
            ->toJson();
    }

    public function delete($id)
    {
        $id = decrypt($id);

        if($data = Transfer::where('id', $id)->first())
        {       

            $data->delete();

            $redirect = route('materialRequest.list');

            return response()->json([
                'message' => 'Material Request deleted successfully', 
                'status' => 'success', 
                'redirect' => $redirect
            ], 200);    
        }
        return response()->json([
            'message' => 'Error occured while deleting', 
            'status' => 'error'
        ], 200);    
    }       
}