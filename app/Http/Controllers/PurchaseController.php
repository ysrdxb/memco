<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Unit;
use App\Models\StockRecord;
use App\Models\Transfer;
use App\Models\Project;
use App\Models\TransferDetail;
use App\Models\DeliveryOrder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

use Auth;
use DB;
use DataTables;

class PurchaseController extends Controller
{
    public function index(Request $request, $projectId = null)
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

            $pageName = 'Select a Project for Purchases';
            $page = 'purchases';
            return view('inventory.transfer.projects', compact('data', 'pageName', 'page', 'warehouses'));
        }
        return view('inventory.purchase.list', compact('projectId', 'projectName', 'isWarehouse'));        
    }
    
    public function details($id)
    {
        $id = decrypt($id);
        $purchases = Purchase::with(['details', 'deliveryOrders', 'transfer'])
            ->where('id', $id)
            ->get();
        return view('inventory.purchase.purchaseDetails', compact('purchases'));        
    }    
    
    public function create(Request $request)
    {
        $warehouses = Warehouse::all();
        //$products = Product::all();
        $suppliers = Supplier::all();

        $user = Auth::user();

        if ($user->hasRole(['Store Incharge']) && $user->project->isNotEmpty()) {
            $projectId = $user->project->first()->id;
            $requests = Transfer::where('requestable_type', 'App\Models\Project')
                ->where('status', '!=', Transfer::STATUS_COMPLETED)
                ->where('requestable_id', $projectId)
                ->get();
        } else {
            $requests = Transfer::where('status', '!=', Transfer::STATUS_COMPLETED)
                ->get();
        }            
        return view('inventory.purchase.create', compact('warehouses', 'suppliers', 'requests'));
    }

    public function store(Request $request, $id = NULL)
    {
        $rules = [
            'transfer_id' => 'required',
            'product_id' => 'required|array',
            'date' => 'required',
            'supplier_id' => 'required|numeric',
            'purchase_no' => 'required|numeric',
            'quantities' => 'required|array',
        ];
    
        $validatedData = $request->validate($rules);
    
        \DB::transaction(function () use ($request) {
            $transfer_id = decrypt($request->transfer_id);
    
            $transfer = Transfer::findOrFail($transfer_id);
            $lastRequest = Purchase::latest()->first();
            $purchaseNo = $request->purchase_no;    
            $order = new Purchase;
            $total_amount = 0;
            $order->user_id = Auth::user()->id;
            $order->date = $request->date;
            $order->ref_no = 'PO-' . $purchaseNo . '-' . date("his");
            $order->purchase_no = $purchaseNo;
            $order->supplier_id = $request->supplier_id;
            $order->total_amount = $total_amount;
            $order->paid_amount = $total_amount;
            $order->status = 'pending';
            $order->payment_status = 'unpaid';
            $order->notes = $request->notes;
            $order->transfer_id = $transfer_id;
            $order->buyer_id = Auth::user()->id;
            $order->save();
            $products = $request->product_id;
            $orderDetails = [];
            foreach ($products as $i => $pid) {
                $unit = Unit::where('id', $request->unit_id[$i])->first();
                $prod = Product::findOrFail($pid);
               
                $detail = TransferDetail::where('transfer_id', $transfer->id)
                    ->where('product_id', $pid)
                    ->first();

                $quantity = $request->quantities[$i];   

                $balance = $detail->requested_quantity - $detail->delivered_quantity;

                if($detail && $detail->requested_quantity > $detail->delivered_quantity && $balance >= $quantity) 
                {                                  
                    $orderDetails[] = [
                        'date' => $request->date,
                        'purchase_id' => $order->id,
                        'quantity' => $quantity,
                        'cost' => $prod->price,
                        'unit_id' =>  $unit->id,
                        'product_id' => $pid,
                        'total_amount' => $prod->price * $quantity,
                        'notes' => $request->notes,
                    ];
        
                    $total_amount += $prod->price * $quantity;
                }
            }
    
            $order->total_amount = $total_amount;
            $order->paid_amount = $total_amount;
            $order->save();

            if(!empty($orderDetails)) 
            {
                PurchaseDetail::insert($orderDetails);
            }

        }, 10);
    
        $redirect = route('purchases.list');
        return response()->json([
            'message' => 'Purchase saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'product_id' => 'required|array',
            'quantities' => 'required|array',
        ];
    
        $validatedData = $request->validate($rules);
        $purchase = Purchase::findOrFail(decrypt($id));
    

        try {
            \DB::beginTransaction();
        
            $transfer = Transfer::findOrFail($purchase->transfer_id);
            $products = $request->product_id;
        
            // Get the existing product ids in the form
            $existingProductIds = collect($products);
        
            // Get the current purchase details for the purchase
            $purchaseDetails = PurchaseDetail::where('purchase_id', $purchase->id)->get();
        
            // Check and remove products from purchase details that are not in the form
            $purchaseDetails->each(function ($purchaseDetail) use ($existingProductIds, $transfer) {
                if (!$existingProductIds->contains($purchaseDetail->product_id)) {
                    // Check if the product is in a delivery order
                    $deliveryOrder = DeliveryOrder::where('purchase_id', $purchaseDetail->purchase_id)
                    ->where('product_id', $purchaseDetail->product_id)
                    ->first();                
        
                    if ($deliveryOrder) {
                        // If the product is in a delivery order, delete the delivery order
                        $stockRecord = StockRecord::where('product_id', $deliveryOrder->product_id)
                            ->where('stockable_type', Project::class)
                            ->where('stockable_id', $transfer->requestable_id)
                            ->first();
                        if($stockRecord && $stockRecord->quantity - $deliveryOrder->quantity >=0) {
                            $stockRecord->quantity -= $deliveryOrder->quantity;
                            $stockRecord->save();
                        }
                        $deliveryOrder->delete();
                    }
        
                    // Delete the purchase detail
                    $purchaseDetail->delete();
                }
            });
        
            // Add products from the form that are not in the purchase details
            foreach ($products as $i => $pid) {
                $prod = Product::findOrFail($pid);
        
                $detail = TransferDetail::where('transfer_id', $transfer->id)
                    ->where('product_id', $pid)
                    ->first();
        
                $quantity = $request->quantities[$i];
        
                //if ($detail && $detail->requested_quantity > $detail->delivered_quantity) {
                    $purchase_detail = PurchaseDetail::updateOrCreate(
                        ['purchase_id' => $purchase->id, 'product_id' => $pid],
                        ['quantity' => $quantity]
                    );
                //}
            }
        
            \DB::commit();
        
            $redirect = route('purchases.list');
            return response()->json([
                'message' => 'Purchase saved successfully',
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
    
    public function destroy($id)
    {
        $id = decrypt($id);
        $user = Auth::user();
        $purchase = Purchase::with('details')->where('id', $id)->first();

        if ($purchase) {

            PurchaseDetail::where('purchase_id', $purchase->id)->delete();

            $dos = DeliveryOrder::where('purchase_id', $purchase->id)->get();

            foreach($dos as $do) {

                $stock = StockRecord::where('product_id', $do->product_id)->where('stockable_id', $purchase->transfer->requestable_id)->where('stockable_type', $purchase->transfer->requestable_type)->first();
                
                if($stock && $stock->quantity >= $do->quantity) {
                    $stock->quantity = $stock->quantity - $do->quantity;
                    $stock->save();
                }

                $do->delete();
            }

            $purchase->delete();
    
            $redirect = route('purchases.list');

            return response()->json([
                'message' => 'Purchase deleted successfully', 
                'status' => 'success', 
                'redirect' => $redirect
            ], 200);

        }
    
        return response()->json([
            'message' => 'Error occurred while deleting', 
            'status' => 'error'
        ], 200);
    }     
    
    public function getList(Request $request, $projectId = NULL): mixed
    {
        $projectId = $projectId ? decrypt($projectId) : null;
        $isWarehouse = $request->isWarehouse;
        $isWarehouse = $isWarehouse && $isWarehouse == 'main' ? 'main' : null;

        $data = Purchase::with('supplier', 'transfer');
        $user = Auth::user();
        
        if ($user->hasRole(['Store Incharge']) && $user->project->isNotEmpty()) {
            $projectId = $user->project->first()->id;
            $data = Purchase::whereHas('transfer', function ($query) use ($projectId) {
                $query->where('requestable_type', 'App\Models\Project')->where('requestable_id', $projectId);
            })->with('supplier', 'transfer');
        } elseif ($user->hasRole(['Admin', 'Super Admin']) || $user->warehouse->isNotEmpty()) {
            if($isWarehouse == 'main') {
                $data = Purchase::whereHas('transfer', function ($query) use ($projectId) {
                    $query->where('requestable_type', Warehouse::class)->where('requestable_id', $projectId);
                })->with('supplier', 'transfer');                
            } else {
                $data = Purchase::whereHas('transfer', function ($query) use ($projectId) {
                    $query->where('requestable_type', 'App\Models\Project')->where('requestable_id', $projectId);
                })->with('supplier', 'transfer');                
            }
        }
              
        $counter = 0;
        return DataTables::of($data)
            ->addColumn('counter', function () use (&$counter) {
                $counter++; // Increment counter
                return $counter;
            })        
            ->addColumn('date', function ($row) {
                return $row->date;
            })
            ->addColumn('ref_no', function ($row) {
                return $row->purchase_no;
            })
            ->addColumn('transfer_no', function ($row) {
                $view = route('transfer.detail', encrypt($row->transfer->id));
                return '<a href="'.$view.'" class="text-primary" target="_blank">'.$row->transfer->transfer_no.'</a>';
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier ? $row->supplier->name : '';
            })
            ->addColumn('total_amount', function ($row) {
                return $row->total_amount;
            })
            ->addColumn('status', function ($row) {
                return $row->status;
            })
            ->addColumn('action', function ($row) {
               // if(Auth::user()->type=='data_entry') {
                    $encryptedId = encrypt($row->id);
                    $action = '<a href="'.route('purchases.detail', $encryptedId).'" class="btn btn-primary mr-4">Detail</a>'; 
                    $action .='<a href="#!" class="deleteBtn" item-id="' . $encryptedId . '"><i class="ik ik-trash f-16 ml-15 text-red"></i></a>';
                   return $action;
               // }
            })
            ->rawColumns(['action','transfer_no'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('purchase_no', 'like', '%' . $searchValue . '%')
                            ->orWhereHas('transfer', function ($query) use ($searchValue) {
                                $query->where('transfer_no', 'like', '%' . $searchValue . '%');
                            })
                            ->orWhereHas('supplier', function ($query) use ($searchValue) {
                                $query->where('name', 'like', '%' . $searchValue . '%');
                            });                            
                    });
                }
            })
            ->make(true);
    }
    
    public function getDeliveryOrder($id)
    {
        $purchases = Purchase::with(['details', 'deliveryOrders'])->get();
    }

    public function getAvailableProducts(Request $request)
    {
        $purchaseId = $request->input('purchase_id');
        
        $purchase = Purchase::findOrFail($purchaseId);
    
        $availableProducts = Product::whereNotIn('id', PurchaseDetail::where('purchase_id', $purchaseId)->pluck('product_id')->toArray())
            ->whereIn('id', TransferDetail::where('transfer_id', $purchase->transfer_id)->pluck('product_id')->toArray())
            ->get(['id', 'name']);
    
        return response()->json($availableProducts);
    }
    
    
    public function getRequestedQuantity(Request $request)
    {
        $productId = $request->input('product_id');
        $purchaseId = $request->input('purchase_id');
    
        // Get the requested quantity for the product in the purchase
        $purchase = Purchase::findOrFail($purchaseId);
        $requestedQuantity = $purchase->transfer->details->where('product_id', $productId)->first()->requested_quantity;
    
        return response()->json(['requested_quantity' => $requestedQuantity]);
    }
    
    public function upload(Request $request, $id)
    {
        $purchase = Purchase::findOrFail(decrypt($id));
    
        // Delete existing file if it exists
        if ($purchase->file_path && Storage::disk('public')->exists($purchase->file_path)) {
            Storage::disk('public')->delete($purchase->file_path);
        }
    
        // Process file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'lpo_no_' . $purchase->purchase_no .'-lpo_id_'.$purchase->id .'.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('purchases', $fileName, 'public');
    
            // Update delivery order record with file path
            $purchase->file_path = $filePath;
            $purchase->save();
    
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
