<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseUser;
use App\Models\User;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\StoreMaterialIssued;
use App\Models\DeliveryOrder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Brand;
use App\Models\Project;
use App\Models\Unit;
use App\Models\StockDetail;
use App\Models\DeliveryOrderSerial;
use App\Models\StockRecord;
use App\Exports\StockExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Illuminate\View\View;
use DataTables;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Notifications\DeliveryOrderNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;
use App\Services\SwitchProject;

class DeliveryOrderController extends Controller
{ 

    protected $notificationService;

    protected $switchProject;

    public function __construct(SwitchProject $switchProject)
    {
        $this->switchProject = $switchProject;
        $this->notificationService = new NotificationService();
    }
    
    private static function balanceDoStock()
    {
    //     $transfers = Transfer::where('requestable_id', 7)->get();
    //     $productDoSums = [];
    
    //     foreach ($transfers as $transfer) {
    //         foreach ($transfer->details as $detail) {
    //             $doSum = DeliveryOrder::where('product_id', $detail->product_id)
    //                 ->where('transfer_id', $transfer->id)
    //                 ->sum('quantity');
    
    //             if (isset($productDoSums[$detail->product_id])) {
    //                 $productDoSums[$detail->product_id] += $doSum;
    //             } else {
    //                 $productDoSums[$detail->product_id] = $doSum;
    //             }
    //         }
    //     }
    
    //     $html = '';
    //     foreach ($productDoSums as $productId => $totalDoQuantity) {
    //         $product = Product::find($productId);
    //         $stock = StockRecord::where('stockable_id', 7)->where('product_id', $product->id)->first();
    //         if($stock && $totalDoQuantity > $stock->quantity) {
    //             $issueVoucherSum = StoreMaterialIssued::where('store_id', 7)->where('product_id', $product->id)->sum('quantity');                
    //             if($issueVoucherSum > 0) {
    //                 $totalDoQuantity = $totalDoQuantity - $issueVoucherSum;
    //             }
    //             $extraDoQty = $totalDoQuantity - $stock->quantity;
    //             $stock->quantity = $stock->quantity + $extraDoQty;
    //             $stock->save();
    //             $html .= "{$product->name}: Total DO Quantity = {$totalDoQuantity} Total Stock : {$stock->quantity} Total Transfer Voucher : {$issueVoucherSum} & Extra DO quantity = {$extraDoQty}<br>";    
    //         }
    //     }
    
    //     echo $html;
    //     exit;        
    }

    public function index(Request $request, $projectId = NULL)
    {
        $type = $request->get('type');
        $isWarehouse = $request->get('isWarehouse');

        $user = Auth::user();
        if($isWarehouse && $isWarehouse == 'main') { 
            $projectName = Warehouse::first()->name;
        } else {
            $projectName = $user->hasRole(['Store Incharge']) && $user->project->isNotEmpty() ? Project::find($user->project->first()->id)->name : '';
        }

        if ($projectId) {
            try {
                if($isWarehouse && $isWarehouse == 'main') {
                    $projectName = Warehouse::first()->name;
                } else {
                    $projectName = Project::find(decrypt($projectId))->name;
                }
              
            } catch (\Exception $e) {
                $projectName = 'Project Not Found';
                $projectId = null;
            }            
        }
        if(!$projectId && Auth::user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge'])) {
            $projectIdsWithTransfers = Transfer::where('requestable_type', Project::class)
                ->select('requestable_id')
                ->distinct()
                ->pluck('requestable_id');

            $data = Project::whereIn('id', $projectIdsWithTransfers)->get();

            $warehouseIdsWithTransfers = Transfer::where('requestable_type', Warehouse::class)
                ->select('requestable_id')
                ->distinct()
                ->pluck('requestable_id');

            $warehouses = Warehouse::whereIn('id', $warehouseIdsWithTransfers)->get();          
            $pageName = $type == 'mtv' ? 'Select a Project for M.T.V' : 'Select a Project for Delivery Orders';
            $page = 'deliveryOrders';
            
            return view('inventory.transfer.projects', compact('data', 'pageName', 'page', 'type', 'warehouses'));
        }
        return view('inventory.transfer.deliveryOrders', compact('projectId', 'projectName', 'type' ,'isWarehouse'));
    }

    public function update(Request $request, $id)
    {
            return response()->json([
                'message' => 'Edit Mode Not Allowed!',
                'status' => 'success'
            ]);
            
        $rules = [
            'product_id' => 'required|array',
            'quantities' => 'required|array',
        ];

        $validatedData = $request->validate($rules);
        $deliveryOrder = DeliveryOrder::findOrFail(decrypt($id));
        try {
            \DB::beginTransaction();

            $transfer = Transfer::findOrFail($deliveryOrder->transfer_id);
            $products = $request->product_id;

            // Get the existing product ids in the form
            $existingProductIds = collect($products);

            // Get the current purchase details for the DeliveryOrders
            $deliveryOrderDetails = DeliveryOrder::where('delivery_order_no', $deliveryOrder->delivery_order_no)->get();

            // Check and remove products from DeliveryOrders that are not in the form
            $deliveryOrderDetails->each(function ($deliveryOrderDetail) use ($existingProductIds, $transfer) {
                if (!$existingProductIds->contains($deliveryOrderDetail->product_id)) {
                   
                    // Update stock record
                    $stockRecord = StockRecord::where('product_id', $deliveryOrderDetail->product_id)
                        ->where('stockable_type', Project::class)
                        ->where('stockable_id', $transfer->requestable_id)
                        ->first();
                    //dd($stockRecord);
                    if ($stockRecord && $stockRecord->quantity >= $deliveryOrderDetail->quantity) {
                        $stockRecord->quantity = $stockRecord->quantity - $deliveryOrderDetail->quantity;
                        $stockRecord->save();
                    }

                    $transferDetail = TransferDetail::where('transfer_id', $transfer->id)
                        ->where('product_id', $deliveryOrderDetail->product_id)
                        ->first();
                    if($transferDetail) {
                        $transferDetail->delivered_quantity = $transferDetail->delivered_quantity - $deliveryOrderDetail->quantity;
                        $transferDetail->save();
                    } else {
                        $transferDetail = new TransferDetail();
                        $transferDetail->requested_quantity = $deliveryOrderDetail->quantity;
                        $transferDetail->delivered_quantity = $deliveryOrderDetail->quantity;
                        $transferDetail->transfer_id = $transfer->id;
                    }

                    // If the product is in a delivery order, delete the delivery order
                    $deliveryOrderDetail->delete();                    
                }
            });

            // Add products from the form that are not in the delivery orders
            foreach ($products as $i => $pid) {

              $prod = Product::findOrFail($pid);

              if($request->quantities[$i]) {

                $detail = TransferDetail::where('transfer_id', $transfer->id)
                    ->where('product_id', $pid)
                    ->first();

                if($detail && !empty($detail)) {
                    $quantity = $request->quantities[$i];

                    $detail->delivered_quantity = $quantity;
                    $detail->save();
                    

                    $do = DeliveryOrder::where('transfer_id', $transfer->id)->where('product_id')->first();

                    if(!$do) {
                        $stockRecord = StockRecord::where('product_id', $pid)
                            ->where('stockable_type', Project::class)
                            ->where('stockable_id', $transfer->requestable_id)
                            ->first();
                        if ($stockRecord) {
                            $stockRecord->quantity = $stockRecord->quantity - $quantity;
                            $stockRecord->save();
                        } else {
                            $stockRecord = new StockRecord();
                            $stockRecord->product_id = $pid;
                            $stockRecord->quantity = $quantity;
                            $stockRecord->stockable_id = $transfer->requestable_id;
                            $stockRecord->stockable_type = Project::class;
                            $stockRecord->save();
                        }                       
                    }

                    $deliveryOrderDetail = DeliveryOrder::updateOrCreate(
                        ['delivery_order_no' => $deliveryOrder->delivery_order_no, 'product_id' => $pid],
                        ['quantity' => $quantity, 'transfer_id' => $transfer->id, 'transfer_no' => $transfer->transfer_no, 'unit_id' => $prod->unit_id]
                    );

                } else {
                    return response()->json([
                        'message' => $prod->name .' is not in Material Request',
                        'status' => 'error',
                        'redirect' => ''
                    ]);                    
                }

              } else {

                return response()->json([
                    'message' => $prod->name .' Quantity is empty',
                    'status' => 'error',
                    'redirect' => ''
                ]);

              }
            }

            \DB::commit();

            $redirect = route('deliveryOrder.list');
            return response()->json([
                'message' => 'Delivery Order saved successfully',
                'status' => 'success',
                'redirect' => $redirect
            ]);
        } catch (\Exception $e) {
            \DB::rollback();

            return response()->json([
                'message' => 'An error occurred while processing your request: ' . $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }


    public function getAvailableProducts(Request $request)
    {
        $delivery_order_id = $request->input('delivery_order_id');
    
        $delivery_order = DeliveryOrder::findOrFail($delivery_order_id);
        
        $availableProducts = Product::select('products.id', 'products.name')
            ->leftJoin('transfer_details', 'products.id', '=', 'transfer_details.product_id')
            ->where('transfer_details.transfer_id', $delivery_order->transfer_id)
            ->get();
    
        return response()->json($availableProducts);
    }

    public function getRequestedQuantity(Request $request)
    {
        $productId = $request->input('product_id');
        $delivery_order_id = $request->input('delivery_order_id');

        // Get the requested quantity for the product in the purchase
        $delivery_order = DeliveryOrder::findOrFail($delivery_order_id);
        $requestedQuantity = $delivery_order->transfer->details->where('product_id', $productId)->first()->requested_quantity;

        return response()->json(['requested_quantity' => $requestedQuantity]);
    }

    public function print($id)
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
        $data = DeliveryOrder::with(['transfer', 'product', 'transferDetails'])
        ->where('delivery_order_no', $id)
        ->get();

        return view('inventory.transfer._print_delivery_order', compact('data', 'user'));
    }

    public function getProductsForTransfer(Request $request)
    {
        $material_request_id = decrypt($request->material_request_id);

        $data = DB::table('transfer_details')
            ->join('products', 'transfer_details.product_id', '=', 'products.id')
            ->select('products.id', 'products.name')
            ->where('transfer_details.transfer_id', $material_request_id)
            ->get();

        return response()->json($data);
    }

    public function getPurchasesForTransfer(Request $request)
    {
        $material_request_id = decrypt($request->material_request_id);

        $purchases = Purchase::where('material_request_id', $material_request_id)->get();

        return response()->json($purchases);
    }

    public function getProductsForPurchase(Request $request)
    {
        $purchase_id = $request->purchase_id;

        $data = DB::table('purchase_details')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->join('material_requests', 'purchases.material_request_id', '=', 'material_requests.id')
            ->leftJoin('material_request_details', function ($join) {
                $join->on('material_request_details.material_request_id', '=', $request->material_request_id)
                    ->whereRaw('material_request_details.delivered_quantity >= material_request_details.requested_quantity');
            })
            ->select('products.id', 'products.name')
            ->where('purchase_details.purchase_id', $purchase_id)
            ->whereNotIn('material_requests.status', ['delivered'])
            ->whereNull('material_request_details.id') // Exclude products where material_request_details.delivered_quantity >= material_request_details.requested_quantity
            ->get();
        return response()->json($data);

    }

    public function getProductTable(Request $request)
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

        if(!empty($request->page)) {
            $page = $request->page;
        } else {
            $page = 'transfer._productTable';
        }
        return view('inventory.'.$page, compact('products'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->hasRole(['Store Incharge']) && $user->project->isNotEmpty()) {
            $projectId = $user->project->first()->id;
            $transfers = Transfer::where('requestable_type', Project::class)
                ->where('requestable_id', $projectId)
                ->get();
            $purchases = Purchase::with(['transfer.details' => function ($query) {
                $query->whereColumn('delivered_quantity', '<', 'requested_quantity');
            }])->whereHas('transfer.details', function ($query) use ($projectId) {
                $query->whereColumn('delivered_quantity', '<', 'requested_quantity')
                      ->where('requestable_type', Project::class)
                      ->where('requestable_id', $projectId);
            })->get();
        } else {
            $purchases = Purchase::with(['transfer.details' => function ($query) {
                $query->whereColumn('delivered_quantity', '<', 'requested_quantity');
            }])->whereHas('transfer.details', function ($query) {
                $query->whereColumn('delivered_quantity', '<', 'requested_quantity');
            })->get();
            $transfers = Transfer::all();
        }

        $projects = Project::all();

        $units = Unit::all();

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Store Incharge');
        })->get();

        return view('inventory.transfer.createDeliveryOrder',  compact('purchases', 'projects', 'transfers', 'units', 'users'));
    }

    public function saveDeliveryOrder(Request $request, $id = null)
    {
        if ($id) {
            return response()->json(['message' => 'Not allowed'], 403);
        }
    
        $rules = [
            'product_id' => 'required|array',
            'material_request_id' => 'nullable',
            'purchase_id' => 'required|numeric',
            'delivery_order_no' => 'required|numeric',
            'date' => 'nullable',
            'add_stock' => 'nullable'
        ];
    
        $request->validate($rules);
        $errors = [];
    
        $purchase = Purchase::find($request->purchase_id);
        if (!$purchase) {
            return response()->json(['message' => 'No Purchase ID found.', 'status' => 'error']);
        }
    
        $transfer = Transfer::find($purchase->transfer_id);
        if (!$transfer) {
            return response()->json(['message' => 'No Transfer Request found.', 'status' => 'error']);
        }
    
        $deliveryNo = $request->delivery_order_no;
    
        foreach ($request->product_id as $pid) {
            if ($request->quantity[$pid] > 0) {
                $detail = TransferDetail::where('transfer_id', $transfer->id)
                    ->where('product_id', $pid)
                    ->first();

                $deliveryOrderSum =  DeliveryOrder::where('transfer_id', $transfer->id)->where('product_id', $pid)->sum('quantity');
                
                $balanceQuantity = $detail->requested_quantity - $deliveryOrderSum;
    
                if ($balanceQuantity >= $request->quantity[$pid]) {

                    $this->createDeliveryOrders($request, $purchase, $pid, $deliveryNo);

                    $stockRecord = StockRecord::where('product_id', $pid)
                    ->where('stockable_id', $transfer->requestable_id)
                    ->first();
                    
                    if ($stockRecord) {
                        $stockRecord->quantity += $request->quantity[$pid];
                        $stockRecord->save();
                    } else {
                        $stockRecord = StockRecord::create([
                            'product_id' => $pid,
                            'stockable_id' => $transfer->requestable_id,
                            'stockable_type' => $transfer->requestable_type === Project::class ? Project::class : Warehouse::class,
                            'quantity' => $request->quantity[$pid]
                        ]);
                    }  

                    $this->updateTransferDetail($transfer->id, $pid, $request->quantity[$pid]);
                }
            }
        }
    
        $this->sendNotification($request, $deliveryNo, $transfer);
        return response()->json(['message' => 'Request saved successfully', 'status' => 'success', 'redirect' => route('deliveryOrder.create')]);
    }
    
    private function createDeliveryOrders($request, $purchase, $pid, $deliveryNo)
    {
        $unit_id = Product::find($pid)->unit->id;
        $purchase_id = $purchase->id;
        $remarks = $request->remarks;
    
        DeliveryOrder::create([
            'user_id' => Auth::user()->id,
            'transfer_id' => $purchase->transfer_id,
            'transfer_no' => $purchase->transfer->transfer_no,
            'purchase_id' => $purchase_id,
            'date' => date('Y-m-d'),
            'product_id' => $pid,
            'delivered_by' => $purchase_id ? 'supplier' : 'store',
            'quantity' => $request->quantity[$pid],
            'unit_id' => $unit_id,
            'add_stock' => 'yes',
            'delivery_order_no' => $deliveryNo,
            'status' => 'success',
            'delivery_note' => $purchase->transfer->user_id,
            'requested_by' => $purchase->transfer->user_id,
            'remarks' => $remarks
        ]);                  
    }
    
    private function updateTransferDetail($transferId, $pid, $quantity)
    {
        TransferDetail::where('transfer_id', $transferId)
            ->where('product_id', $pid)
            ->update([
                'delivered_quantity' => DB::raw("delivered_quantity + $quantity"),
            ]);
    }
    
    
    private function createStockDetails($pid, $quantity, $stockId)
    {
        for ($i = 1; $i <= $quantity; $i++) {
            StockDetail::create([
                'stock_record_id' => $stockId,
                'product_id' => $pid,
                'quantity' => 1,
                'serial_no' => $i,
                'model_no' => '1',
                'warranty' => '2',
                'product_condition' => 'new',
            ]);
        }
    }
    
    private function sendNotification($request, $deliveryNo, $transfer)
    {
        $projectName = $transfer->requestable_type === Project::class ? $transfer->project->name : Warehouse::first()->name;
        $message = 'New Delivery Order # ' . $deliveryNo . ' created by ' . Auth::user()->name . ' for ' . $projectName;
        $this->notificationService->notifyAdmins($message);
    }
    

    public function saveDeliveryOrderWithoutLPO(Request $request, $id = null)
    {
        if($id) {
            dd('Not allowed');
        }

        $request->validate([
            'product_id.*' => ['required', Rule::exists('products', 'id')],
            'date' => 'nullable',
            'remarks' => 'nullable'
        ]);

        $errors = [];
        DB::beginTransaction();

        try {
            if ($request->filled('via_transfer_id')) {

                $transfer = Transfer::findOrFail($request->via_transfer_id);
                $lastDeliveryOrderNo = DeliveryOrder::max('delivery_order_no') ?? 0;
                $deliveryNo = $lastDeliveryOrderNo + 1;
    
                $lastTransferVoucherNo = DeliveryOrder::max('transfer_voucher_no') ?? 0;
                $transfer_voucher_no = $lastTransferVoucherNo + 1;
    
                $voucherId = DB::table('transfer_vouchers')->insertGetId([
                    'transfer_id' => $transfer->id,
                    'voucher_no' => DB::table('transfer_vouchers')->max('voucher_no') + 1
                ]);

                foreach ($request->product_id as $i => $pid) {

                    $unit_id = $request->unit_id[$i] ?? Product::find($pid)->unit->id;
                    $quantities = $request->quantity[$i];
                    
                    if ($request->type[$i] === 'tool' && $quantities > 0) {

                        $detail = TransferDetail::where('transfer_id', $transfer->id)
                            ->where('product_id', $pid)
                            ->first();

                        if (!$detail) {
                            $errors[] = 'Transfer detail not found for product : ' . Product::find($pid)->name;
                            continue;
                        }

                        $balanceQuantity = $detail->requested_quantity - $detail->delivered_quantity;

                        if ($balanceQuantity < $request->quantity[$i]) {
                            $errors[] = 'Current QTY is greater than remaining QTY for : ' . Product::find($pid)->name ;
                            continue;
                        }

                        $detail->delivered_quantity += $request->quantity[$i];
                        $detail->save();
                                                
                        $deliveryOrders = DeliveryOrder::create([
                            'user_id' => Auth::user()->id,
                            'transfer_id' => $transfer->id,
                            'transfer_no' => $transfer->transfer_no,
                            'purchase_id' => null,
                            'date' => date('Y-m-d'),
                            'product_id' => $pid,
                            'delivered_by' => 'store',
                            'quantity' => $quantities,
                            'unit_id' => $unit_id,
                            'delivery_order_no' => $deliveryNo,
                            'transfer_voucher_no' => $transfer_voucher_no,
                            'transfer_voucher_id' => $voucherId,
                            'status' => 'success',
                            'requested_by' => $request->requested_by,
                            'remarks' => $request->remarks[$i],
                            'product_type' => $request->type[$i],
                        ]);


                        foreach ($request->tool_serial as $serial) {

                            $stockDetail = StockDetail::findOrFail($serial);

                            $existingSerial = DeliveryOrderSerial::where('stock_detail_id', $serial)
                                ->where('delivery_order_id', $deliveryOrders->id)
                                ->where('product_id', '!=', $pid)
                                ->exists();
                        
                            if (!$existingSerial) {

                                $stock = StockRecord::find($stockDetail->stock_record_id);
                                
                                if($stock->product_id == $pid) {
                                    DeliveryOrderSerial::create([
                                        'stock_detail_id' => $serial,
                                        'product_id' => $pid,
                                        'delivery_order_id' => $deliveryOrders->id,
                                    ]);

                                    $stockDetail->quantity = 0;
                                    $stockDetail->save();                                    
                                }

                            }
                        }
                        
                        $this->updateStockRecord(Warehouse::first()->id, $pid, $quantities);
                        $this->updateProjectStockRecord($transfer->requestable_id, $pid, $quantities);
                                              

                    }
                    else {

                        $detail = TransferDetail::where('transfer_id', $transfer->id)
                            ->where('product_id', $pid)
                            ->first();

                        if (!$detail) {
                            $errors[] = 'Transfer detail not found for product : ' . Product::find($pid)->name;
                            continue;
                        }

                        $balanceQuantity = $detail->requested_quantity - $detail->delivered_quantity;

                        if ($balanceQuantity < $request->quantity[$i]) {
                            $errors[] = 'Current QTY is greater than remaining QTY for : ' . Product::find($pid)->name ;
                            continue;
                        }
                        
                        $detail->delivered_quantity += $request->quantity[$i];
                        $detail->save();  
                                              
                        DeliveryOrder::create([
                            'user_id' => Auth::user()->id,
                            'transfer_id' => $transfer->id,
                            'transfer_no' => $transfer->transfer_no,
                            'purchase_id' => null, //$transfer->purchase ?  $transfer->purchase->id : null,
                            'date' => date('Y-m-d'),
                            'product_id' => $pid,
                            'delivered_by' => 'store', //$transfer->purchase ?  'supplier' : 'store',
                            'quantity' => $request->quantity[$i],
                            'unit_id' => $unit_id,
                            'delivery_order_no' => $deliveryNo,
                            'transfer_voucher_no' => $transfer_voucher_no,
                            'transfer_voucher_id' => $voucherId,
                            'status' => 'success',
                            'requested_by' => $transfer->user_id,
                            'remarks' => $request->remarks[$i],
                        ]);
    
                        $this->updateStockRecord(Warehouse::first()->id, $pid, $request->quantity[$i]);
                        $this->updateProjectStockRecord($transfer->requestable_id, $pid, $request->quantity[$i]);                      
                        
                    }
                }

                $message = 'Transfer Voucher # '.$transfer_voucher_no.' from Main Store Sajaa created by '.Auth::user()->name;
                $this->notificationService->notifyStore($message, $transfer->requestable_id);                

                $this->checkTransferCompletion($transfer);

            } else {
                $transfer = $this->createTransferForProject($request->project_id);
                $voucherId = DB::table('transfer_vouchers')->insertGetId([
                    'transfer_id' => $transfer->id,
                    'voucher_no' => DB::table('transfer_vouchers')->max('voucher_no') ? DB::table('transfer_vouchers')->max('voucher_no') + 1 : 1
                ]);      
                         
                $lastRequest = DeliveryOrder::max('delivery_order_no');
                $deliveryNo = $lastRequest ? $lastRequest + 1 : 1;
                $transfer_voucher_no = DeliveryOrder::max('transfer_voucher_no');
                $transfer_voucher_no = $transfer_voucher_no ? $transfer_voucher_no + 1 : 1;   
                             
                foreach ($request->product_id as $i => $pid) {
                    $productStock = $this->getStockRecord(Warehouse::first()->id, $pid);
                    $quantities = $request->quantity[$i];
                    if (!$productStock || $productStock->quantity < $quantities) {
                        $errors[] = 'Insufficient quantity for product Name: ' . Product::find($pid)->name;
                        continue;
                    }

                    // Create transfer detail
                    $unit_id = $request->unit_id[$i] ?? Product::find($pid)->unit->id;
                    $total_serials = 0;
                    if ($request->type[$i] === 'tool') {
                        foreach($request->tool_serial as $serial) {

                            $serialDetail = StockDetail::findOrFail($serial);
                            $serialQty = $serialDetail->quantity;

                            $checkStockRecord = StockRecord::where('stockable_type', Warehouse::class)
                                ->where('stockable_id', Warehouse::first()->id)
                                ->where('product_id', $pid)
                                ->first();
                            if($checkStockRecord->quantity < $serialQty) {
                                $errors[] = 'Insufficient quantity for product name: '.Product::find($pid)->name;
                                continue;
                            }

                            $serialDetail->quantity = 0;
                            $serialDetail->save();

                            $total_serials += 1; 

                        }

                        TransferDetail::create([
                            'transfer_id' => $transfer->id,
                            'product_id' => $pid,
                            'requested_quantity' => $quantities,
                            'delivered_quantity' => $quantities,
                            'unit_id' => $unit_id,
                            'brand_id' => Product::find($pid)->brand->id,
                        ]);

                        // Create delivery order
                        $deliveryOrder = DeliveryOrder::create([
                            'user_id' => Auth::user()->id,
                            'transfer_id' => $transfer->id,
                            'transfer_no' => $transfer->transfer_no,
                            'purchase_id' => null,
                            'date' => date('Y-m-d'),
                            'product_id' => $pid,
                            'delivered_by' => 'store',
                            'quantity' => $quantities,
                            'unit_id' => $unit_id,
                            'delivery_order_no' => $deliveryNo,
                            'transfer_voucher_no' => $transfer_voucher_no,
                            'transfer_voucher_id' => $voucherId,
                            'status' => 'success',
                            'requested_by' => $request->requested_by,
                            'remarks' => $request->remarks[$i] ?? '',
                            'product_type' => $request->type[$i],
                        ]);


                        foreach ($request->tool_serial as $serial) {

                            $stockDetail = StockDetail::findOrFail($serial);

                            $existingSerial = DeliveryOrderSerial::where('stock_detail_id', $serial)
                                ->where('delivery_order_id', $deliveryOrder->id)
                                ->where('product_id', '!=', $pid)
                                ->exists();
                        
                            if (!$existingSerial) {
                                // Insert the new entry
                                $stock = StockRecord::find($stockDetail->stock_record_id);
                                
                                if($stock->product_id == $pid) {
                                    DeliveryOrderSerial::create([
                                        'stock_detail_id' => $serial,
                                        'product_id' => $pid,
                                        'delivery_order_id' => $deliveryOrder->id,
                                    ]);

                                    $stockDetail->quantity = 0;
                                    $stockDetail->save();                                    
                                }

                            }
                        }                     

                   
                        $this->updateStockRecord(Warehouse::first()->id, $pid, $quantities);                        
                        $this->updateProjectStockRecord($transfer->requestable_id, $pid, $quantities);                                                                                        

                        $quantities = $total_serials;

                    } else {
                        TransferDetail::create([
                            'transfer_id' => $transfer->id,
                            'product_id' => $pid,
                            'requested_quantity' => $quantities,
                            'delivered_quantity' => $quantities,
                            'unit_id' => $unit_id,
                            'brand_id' => Product::find($pid)->brand->id,
                        ]);

                        // Create delivery order
                        $deliveryOrder = DeliveryOrder::create([
                            'user_id' => Auth::user()->id,
                            'transfer_id' => $transfer->id,
                            'transfer_no' => $transfer->transfer_no,
                            'purchase_id' => null,
                            'date' => date('Y-m-d'),
                            'product_id' => $pid,
                            'delivered_by' => 'store',
                            'quantity' => $quantities,
                            'unit_id' => $unit_id,
                            'delivery_order_no' => $deliveryNo,
                            'transfer_voucher_no' => $transfer_voucher_no,
                            'transfer_voucher_id' => $voucherId,
                            'status' => 'success',
                            'requested_by' => $request->requested_by,
                            'remarks' => '', //$request->remarks[$i],
                            'product_type' => $request->type[$i],
                        ]);
                        
                        $this->updateStockRecord(Warehouse::first()->id, $pid, $quantities);
                        $this->updateProjectStockRecord($transfer->requestable_id, $pid, $quantities);                        

                    }


                }

                $message = 'Transfer Voucher # '.$transfer_voucher_no.' from Main Store Sajaa created by '.Auth::user()->name;
                $this->notificationService->notifyStore($message, $request->project_id);                  

            }

            if (!empty($errors)) {
                DB::rollback();
                return response()->json([
                    'message' => $errors,
                    'status' => 'error',
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Request saved successfully',
                'status' => 'success',
                'redirect' => route('deliveryOrder.create')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while processing the request: ' . $e->getMessage(),
                'status' => 'error',
            ]);
        }
    }
    
    private static function checkSerials(Product $product) {
        if($product) {
            $stock = StockRecord::where('stockable_type', Warehouse::class)
                ->where('stockable_id', Warehouse::first()->id)
                ->where('product_id', $product->id)
                ->first();

            if($stock && $stock->quantity > 0) {
                $stockDetail = StockDetail::where('stock_record_id', $stock->id)
                    ->where('product_id', $product->id)
                    ->first();

                if(!$stockDetail) {
                    for($i=1; $i<= $stock->quantity; $i++) {
                        $stockDetail = StockDetail::create([
                            'stock_record_id' => $stock->id,
                            'product_id' => $product->id,
                            'quantity' => 1,
                            'serial_no' => $i,
                            'model_no' => '1',
                            'warranty' => '2',
                            'product_condition' => 'new',
                        ]);
                    }
                }

                // Check if the product is available in the delivery orders table
                $deliveryOrder = DeliveryOrder::where('product_id', $product->id)->first();
                if ($deliveryOrder && $deliveryOrder->transfer->requestable_type === Project::class) {
                    // Check if the product is not available in the delivery_order_serials table
                    $deliveryOrderSerial = DeliveryOrderSerial::where('product_id', $product->id)
                        ->where('delivery_order_id', $deliveryOrder->id)
                        ->first();

                    if (!$deliveryOrderSerial) {
                        DeliveryOrderSerial::create([
                            'delivery_order_id' => $deliveryOrder->id,
                            'product_id' => $product->id,
                            'stock_detail_id' => $stockDetail->id,
                        ]);

                        $stockDetail->quantity = 0;
                        $stockDetail->save();
                    }
                }
            }
        }
               
    }

    private function getStockRecord($stockable_id, $product_id)
    {
        return StockRecord::where('product_id', $product_id)
            ->where('stockable_id', $stockable_id)
            ->where('stockable_type', Warehouse::class)
            ->first();
    }

    private function updateStockRecord($stockable_id, $product_id, $quantity)
    {
        $product = Product::find($product_id);
        $stock = $this->getStockRecord($stockable_id, $product_id);
        if ($stock && $stock->quantity >= $quantity) {
            $stock->quantity -= $quantity;
            $stock->save();
            
            if($product->type ==='tool') {
    
                for($i=1; $i<= $stock->quantity; $i++) {
                   StockDetail::create([
                        'stock_record_id' => $stock->id,
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'serial_no' => $i,
                        'model_no' => '1',
                        'warranty' => '2',
                        'product_condition' => 'new',
                    ]);
                }
                            
            }
        
        }
    }

    private function getProjectStockRecord($stockable_id, $product_id)
    {
        return StockRecord::where('product_id', $product_id)
            ->where('stockable_id', $stockable_id)
            ->where('stockable_type', Project::class)
            ->first();
    }    

    private function updateProjectStockRecord($stockable_id, $product_id, $quantity)
    {
        $projectStockRecord = $this->getProjectStockRecord($stockable_id, $product_id);
        if ($projectStockRecord) {
            $projectStockRecord->quantity += $quantity;
            $projectStockRecord->save();
        } else {
            $projectStockRecord = StockRecord::create([
                'product_id' => $product_id,
                'stockable_id' => $stockable_id,
                'stockable_type' => Project::class,
                'quantity' => $quantity
            ]);
        }

    }


    private function checkTransferCompletion($transfer)
    {
        $details = TransferDetail::where('transfer_id', $transfer->id)->get();
        $status = true;

        foreach ($details as $detail) {
            if ($detail->requested_quantity > $detail->delivered_quantity) {
                $status = false;
                break;
            }
        }

        if ($status) {
            $transfer->status = Transfer::STATUS_COMPLETED;
            $transfer->save();
        }
    }


    private function createTransferForProject($projectId)
    {
        $constantPrefix = "MRQ";
        $latestTransfer = Transfer::latest()->first();
    
        $sequenceNumber = $latestTransfer ? (int)substr($latestTransfer->ref_no, strlen($constantPrefix)) + 1 : 1;
    
        $nextTransferNo = Transfer::max('transfer_no') + 1;
    
        while (Transfer::where('transfer_no', $nextTransferNo)->exists()) {
            $nextTransferNo++;
        }
    
        $transfer = Transfer::create([
            'ref_no' => $constantPrefix . str_pad($sequenceNumber, 5, '0', STR_PAD_LEFT),
            'transfer_no' => $nextTransferNo,
            'user_id' => Auth::user()->id,
            'requestable_type' => Project::class,
            'requestable_id' => $projectId,
            'project_id' => $projectId,
            'date' => now(),
            'store_id' => Warehouse::first()->id,
        ]);
    
        return $transfer;
    }
    

    private function createDeliveryOrder($do,$transferId, $productId, $quantity, $unit_id, $purchase_id = NULL, $remarks)
    {
        $transfer = Transfer::find($transferId);
        DeliveryOrder::create([
            'user_id' => Auth::user()->id,
            'transfer_id' => $transferId,
            'transfer_no' => $transfer->transfer_no,
            'purchase_id' => $purchase_id,
            'date' => date('Y-m-d'),
            'product_id' => $productId,
            'delivered_by' => $purchase_id ? 'supplier' : 'store',
            'quantity' => $quantity,
            'unit_id' => $unit_id,
            'delivery_order_no' => $do,
            'status' => 'success',
            'delivery_note' => $transfer->user_id,
            'requested_by' => $transfer->user_id,
            'remarks' => $remarks
        ]);
    }


    public function delete($id)
    {
        try {
            $id = decrypt($id);
    
            DB::beginTransaction();
    
            $deliveryOrder = DeliveryOrder::with('transferDetails')
                ->where('delivery_order_no', $id)->first();
    
            if ($deliveryOrder) {
                $data = DeliveryOrder::with('transferDetails')
                    ->where('delivery_order_no', $id)
                    ->get();
    
                $transfer_id = $deliveryOrder->transfer_id;
    
                $transfer = Transfer::findOrFail($transfer_id);
    
                foreach ($data as $row) {
                    if ($row->delivered_by == 'store') {
                        $stock = StockRecord::where('product_id', $row->product_id)
                            ->where('stockable_id', Warehouse::first()->id)
                            ->where('stockable_type', Warehouse::class)
                            ->first();
    
                        if ($stock) {
                            $stock->quantity += $row->quantity;
                            $stock->save();
                        }
                    }
    
                    $issued = StoreMaterialIssued::where('product_id', $row->product_id)
                        ->where('store_id', $row->transfer->requestable_id)
                        ->sum('quantity');
                    
                    $stock = StockRecord::where('product_id', $row->product_id)
                        ->where('stockable_id', $transfer->requestable_id)
                        ->where('stockable_type', $transfer->requestable_type)
                        ->first();
                    
                    if ($issued > 0 && $issued > $stock->quantity) {
                        return response()->json([
                            'message' => 'Product ' . $row->product->name . ' exists in Material Issued Voucher for quantity ' . $issued .'<br>',
                            'status' => 'error'
                        ], 200);
                    }
                    
                    if ($stock) {
                        if ($stock->quantity > $row->quantity) {
                            $stock->quantity -= $row->quantity;
                        } else {
                            $stock->quantity = 0;
                        }
                        $stock->save();
                    }
    
                    $transferDetail = TransferDetail::where('transfer_id', $transfer->id)
                        ->where('product_id', $row->product_id)
                        ->first();
    
                    if ($transferDetail && $transferDetail->delivered_quantity > 0) {
                        $transferDetail->delivered_quantity -= $row->quantity;
                        $transferDetail->save();
                    }
                }
    
                DeliveryOrder::where('delivery_order_no', $id)
                    ->delete();
    
                DB::commit();
    
                $redirect = route('deliveryOrder.list');
    
                return response()->json([
                    'message' => 'Delivery Order deleted successfully',
                    'status' => 'success',
                    'redirect' => $redirect
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error occurred while deleting: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    
        return response()->json([
            'message' => 'Delivery Order not found',
            'status' => 'error'
        ], 404);
    }


    public function _dataTable(Request $request, $projectId = NULL)
    {
        $type = $request->type;
        $isWarehouse = $request->isWarehouse;
        $isWarehouse = $isWarehouse && $isWarehouse == 'main' ? 'main' : null;
        $delivered_by = $type == 'mtv' ? 'store' : 'supplier';
        $projectId = $projectId ? decrypt($projectId) : null;
        try {
            $user = Auth::user();
            if ($user->hasRole(['Store Incharge']) && Auth::user()->project) {
                $orders = DeliveryOrder::where('delivered_by', $delivered_by)
                    ->whereHas('transfer', function ($query) {
                        $query->where('requestable_type', 'App\Models\Project')
                            ->where('requestable_id', $this->switchProject->getCurrentProjectId());
                    })
                    ->get()
                    ->groupBy('delivery_order_no');
            }
            elseif ($user->hasRole(['Admin', 'Super Admin']) || $user->warehouse->isNotEmpty()) {
                if ($projectId) {         
                    if($isWarehouse == 'main') {
                        $orders = DeliveryOrder::where('delivered_by', $delivered_by)
                        ->whereHas('transfer', function ($query) use ($projectId) {
                            $query->where('requestable_type', Warehouse::class)
                                ->where('requestable_id', Warehouse::first()->id);
                        })
                        ->get()
                        ->groupBy('delivery_order_no');
                    } else {
                        $orders = DeliveryOrder::where('delivered_by', $delivered_by)
                        ->whereHas('transfer', function ($query) use ($projectId) {
                            $query->where('requestable_type', Project::class)
                                ->where('requestable_id', $projectId);
                        })
                        ->get()
                        ->groupBy('delivery_order_no');
                    }        

                } else {
                    $orders = DeliveryOrder::where('delivered_by', $delivered_by)
                        ->get()
                        ->groupBy('delivery_order_no');
                }
            }
            $counter = 1;

            return DataTables::of($orders)
                ->addColumn('id', function ($group) use (&$counter) {
                    return $counter++;
                })
                ->addColumn('created_date', function ($group) {
                    return $group->first()->created_at->diffForHumans();
                })
                ->addColumn('material_request_no', function ($group) {
                    $view = route('transfer.detail', encrypt($group->first()->transfer_id));
                    return '<a href="'.$view.'" class="text-primary" target="_blank">'.$group->first()->transfer_no.'</a>';
                })
                ->addColumn('purchase_code', function ($group) {
                    $purchaseCodes = [];
                    foreach ($group as $deliveryOrder) {
                        $purchaseId = $deliveryOrder->purchase_id ?? null;
                        if ($purchaseId) {
                            $purchase = Purchase::find($purchaseId);
                            if ($purchase) {
                                $encryptedId = encrypt($purchase->id);
                                $lpo_detail = route('purchases.detail', $encryptedId);
                                $purchaseCodes[] = '<a href="'.$lpo_detail.'" class="text-primary" target="_blank">'.$purchase->purchase_no.'</a>';
                            }
                        } else {
                            $purchaseCodes[] = 'M.T.V';
                        }
                    }
                    return is_array($purchaseCodes) && count($purchaseCodes) > 0 ? $purchaseCodes[0] : $purchaseCodes;
                })
                
                ->addColumn('delivery_order_no', function ($group) {
                    return $group->first()->delivery_order_no;
                })
                ->addColumn('status', function ($group) {
                    return strtoupper($group->first()->status);
                })
                ->rawColumns(['status'])
                ->addColumn('action', function ($group) {
                    $view = route('deliveryOrder.detail', encrypt($group->first()->delivery_order_no));
                    $actions = '<a href="' . $view . '" class="mr-2"> <i class="ik ik-eye text-primary"></i></a>';

                    $actions .= '<a href="#!" class="deleteBtn float-right" item-id="' . encrypt($group->first()->delivery_order_no) . '"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                   
                    return $actions;
                })
                ->rawColumns(['action','material_request_no','purchase_code'])
                ->toJson();
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to fetch data.',
                'status' => 'error',
            ]);
        }
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $deliveryOrders = DeliveryOrder::with('transfer.details','product')->where('delivery_order_no', $id)->get();

        if($deliveryOrders->isNotEmpty()) {
            return view('inventory.transfer.deliveryOrderDetail', compact('deliveryOrders'));
        }
    }


    public function upload(Request $request, $id)
    {
        $order = DeliveryOrder::findOrFail(decrypt($id));

        // Delete existing file if it exists
        if ($order->file_path && Storage::disk('public')->exists($order->file_path)) {
            Storage::disk('public')->delete($order->file_path);
        }

        // Process file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'do_no_' . $order->delivery_order_no .'-do_id_'.$order->id .'.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('delivery_orders', $fileName, 'public');

            // Update delivery order record with file path
            $order->file_path = $filePath;
            $order->save();

            return response()->json(['message' => 'File uploaded successfully']);
        }

        return response()->json(['message' => 'File upload failed'], 500);
    }

    public function download(Request $request)
    {
        $filePath = $request->input('file_path');
        $filePath = storage_path('app/public/' . $filePath);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }
    }
}
