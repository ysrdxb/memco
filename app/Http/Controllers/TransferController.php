<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;
use DataTables;
use Carbon\Carbon;
use App\Imports\TransferImport;

use App\Models\Warehouse;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\StoreUser;
use App\Models\WarehouseUser;
use App\Models\User;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\TransferDelivery;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\StockRecord;
use App\Models\StoreItemLimit;
use App\Models\TransferReturn;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\DeliveryOrder;
use App\Exports\StockExport;
use App\Models\StoreMaterialIssued;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;
use App\Services\SwitchProject;

class TransferController extends Controller
{
    protected $notificationService;
    protected $switchProject;

    public function __construct(SwitchProject $switchProject)
    {
        $this->switchProject = $switchProject;

        $this->notificationService = new NotificationService();

        // $transfers = Transfer::where('requestable_type', Project::class)
        // ->where('requestable_id', 5)
        // ->get();

        // foreach ($transfers as $transfer) {
        //     // Get the delivery orders for the transfer
        //     $deliveryOrders = DeliveryOrder::where('transfer_id', $transfer->id)
        //         ->get();

        //     foreach ($deliveryOrders as $deliveryOrder) {
        //         $productId = $deliveryOrder->product_id;
        //         $quantity = $deliveryOrder->quantity;

        //         // Check if the product exists in the transfer details
        //         $transferDetail = TransferDetail::where('transfer_id', $transfer->id)
        //             ->where('product_id', $productId)
        //             ->first();

        //         if (!$transferDetail) {
        //             // Create a new transfer detail if it doesn't exist
        //             TransferDetail::create([
        //                 'transfer_id' => $transfer->id,
        //                 'product_id' => $productId,
        //                 'requested_quantity' => $quantity,
        //                 'delivered_quantity' => $quantity,
        //                 'unit_id' => Product::find($productId)->unit_id,
        //                 'brand_id' => Product::find($productId)->brand_id,
        //             ]);
        //         } else {
        //             // Update the delivered quantity if it is different from the delivery order quantity
        //             if ($transferDetail->delivered_quantity !== $quantity) {
        //                 $transferDetail->delivered_quantity = $quantity;
        //                 $transferDetail->save();
        //             }
        //         }
        //     }

        //     // Recalculate the sum of delivered quantities for each product in the transfer details
        //     $productIds = $deliveryOrders->pluck('product_id')->unique();
        //     foreach ($productIds as $productId) {
        //         $deliveredQuantitySum = TransferDetail::where('transfer_id', $transfer->id)
        //             ->where('product_id', $productId)
        //             ->sum('delivered_quantity');

        //         // Update or insert into the stock records table for each product
        //         $stockRecord = StockRecord::where('stockable_type', Project::class)
        //             ->where('stockable_id', 5)
        //             ->where('product_id', $productId)
        //             ->first();

        //         if (!$stockRecord) {
        //             // Insert a new stock record if it doesn't exist
        //             StockRecord::create([
        //                 'stockable_type' => Project::class,
        //                 'stockable_id' => 5,
        //                 'product_id' => $productId,
        //                 'quantity' => $deliveredQuantitySum,
        //             ]);
        //         } else {
        //             // Update the existing stock record
        //             $stockRecord->quantity = $deliveredQuantitySum;
        //             $stockRecord->save();
        //         }
        //     }
        // }

    }

    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()->toArray()]);
        }
    
        $file = $request->file('file');
        $filePath = $file->getPathname();
    
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return response()->json(['status' => 'error', 'message' => 'File does not exist or is not readable.']);
        }
    
        $handle = fopen($filePath, 'r');
    
        if (!$handle) {
            return response()->json(['status' => 'error', 'message' => 'Failed to open the file for reading.']);
        }
    
        fgetcsv($handle); // Skip header row
    
        $products = [];
    
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            $product = Product::where('code', $row[1])
            ->orWhere('name', $row[2])
            ->first();       
            if($product) {
                $products[] = [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'activity' => $row[3],
                    'subactivity' => $row[4],
                    'brand' => $row[5],
                    'unit' => $row[6],
                    'quantity' => (int)$row[7],
                ];
            }
        }
    
        fclose($handle);
    
        return view('inventory.transfer.imported_products', compact('products'));
    }
    
    public function importTransfers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'transfer_no' => 'required|unique:transfers,transfer_no',
            'product_code.*' => 'required',
            'name.*' => 'required',
            'brand.*' => 'required',
            'unit.*' => 'required',
            'quantity.*' => 'required|integer|min:1',
        ], [
            'transfer_no.unique' => 'The Material Request Number already exists.',
            'product_code.*.required' => 'Product code is required.',
            'name.*.required' => 'Product name is required.',
            'brand.*.required' => 'Brand is required.',
            'unit.*.required' => 'Unit is required.',
            'quantity.*.required' => 'Quantity is required.',
            'quantity.*.integer' => 'Quantity must be an integer.',
            'quantity.*.min' => 'Quantity must be at least 1.',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()->toArray()]);
        }
    
        DB::beginTransaction();

        if(Auth::user()->hasRole(['Admin', 'Super Admin'])) {
            if($request->store_id) {
                $store_id = Warehouse::find($request->store_id) ? $request->store_id : $request->store_id;
                $requestable = Warehouse::find($request->store_id) ? Warehouse::class : Project::class;
                $projectName = Warehouse::find($request->store_id) ? Warehouse::first()->name : Project::find($store_id)->name;
            } else {
                $store_id = $this->switchProject->getCurrentProjectId();
                $requestable = Project::class;     
                $projectName = Project::find($store_id)->name;           
            }
        } else {
            $store_id = $this->switchProject->getCurrentProjectId();
            $requestable = Project::class;  
            $projectName = Project::find($store_id)->name;           
        }       
        try {
            $transfer = Transfer::create([
                'transfer_no' => $request->transfer_no,
                'date' => $request->date,
                'requestable_id' => $store_id,
                'requestable_type' => $requestable,
                'project_id' => $store_id,
                'store_id' => Warehouse::first()->id,
                'user_id' => Auth::id(),
                'purchase_applicable' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
    
            // Save transfer details
            $errors = [];
            for ($i = 0; $i < count($request->product_id); $i++) {
                try {
                    $productCode = $request->product_code[$i];
                    $productName = $request->name[$i];
                    $brandName = $request->brand[$i];
                    $unitName = $request->unit[$i];
                    $quantity = $request->quantity[$i];
    
                    $product = Product::find($request->product_id[$i]);
    
                    DB::table('transfer_details')->insert([
                        'transfer_id' => $transfer->id,
                        'product_id' => $product->id,
                        'requested_quantity' => $quantity,
                        'delivered_quantity' => 0,
                        'unit_id' => $product->unit_id, // Adjust based on your logic
                        'brand_id' => $product->brand_id, // Adjust based on your logic
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                } catch (\Exception $e) {
                    $errors[] = 'Error: ' . $e->getMessage();
                }
            }
    
            if (empty($errors)) {
                DB::commit();
                $message = 'New Material Request # '.$transfer->transfer_no.' by '.Auth::user()->name.' Imported for '.$projectName;
                $this->notificationService->notifyAdmins($message);                 
                return response()->json(['status' => 'success', 'message' => 'Transfers imported successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status' => 'error', 'message' => 'Error importing transfers.', 'errors' => $errors]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Error importing transfers: ' . $e->getMessage()]);
        }
    }
    
    
    public function downloadImportedFile()
    {
        $filePath = public_path('import.csv');
        if (file_exists($filePath)) {
            return response()->download($filePath, 'import.csv');
        } else {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }
    }
    
        
    public function showTransfer($id)
    {
        $id = decrypt($id);

        $request = Transfer::with(['statuses', 'project', 'user', 'product', 'warehouse'])->findOrFail($id);

        return view('inventory.transfer.warehouse.showTransfer', compact('request'));
    }

    public function changeStatus(Request $request, $id)
    {
        $id = $id ? decrypt($id) : $id;

        $transfer = Transfer::findOrFail($id);
        if($request->ajax() && !$transfer->purchase) {

            $status = $transfer->purchase_applicable == 1 ? 0 : 1;

            $transfer->purchase_applicable = $status;
            $transfer->save();

            $redirect = route('transfer.getStatus');

                return response()->json([
                    'message' => 'Request status changed successfully',
                    'status' => 'success',
                    'redirect' => $redirect
                ]);
        } else {
            $redirect = route('transfer.getStatus');
            return response()->json([
                'message' => 'Invalid Request or L.P.O already created',
                'status' => 'success',
                'redirect' => $redirect
            ]);
        }
    }


    public function detail($id)
    {
        $id = decrypt($id);

        $transfer = Transfer::with(['project', 'details', 'purchases', 'user', 'warehouse', 'deliveryOrders'])->where('id', $id)->first();

        return view('inventory.transfer.data', compact('transfer'));
    }
 
    public function details($id)
    {
        $id = decrypt($id);

        $transfer = Transfer::with(['project', 'details', 'purchases', 'user', 'warehouse', 'deliveryOrders'])->where('id', $id)->first();

        return view('inventory.transfer.detail', compact('transfer'));
    }

    public function print($id)
    {
        $id = decrypt($id);

        $transfer = Transfer::with(['project', 'details', 'user', 'warehouse'])->where('id', $id)->first();

        return view('invoice.transfer', compact('transfer'));
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

        $transfer = Transfer::with(['project', 'details', 'user', 'warehouse'])->where('id', $id)->first();

        return view('invoice.delivery_order', compact('transfer', 'user'));
    }

    public function updateQuantity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transfer_id' => 'required|integer|exists:transfers,id',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'row_id' => 'required|integer|exists:transfer_details,id',
            'balance' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
    
        $transferId = $request->input('transfer_id');
        $productId = $request->input('product_id');
        $newQuantity = $request->input('quantity');
        $rowId = $request->input('row_id');
    
        DB::beginTransaction();
        try {
            $transferDetail = TransferDetail::find($rowId);
    
            if (!$transferDetail || $transferDetail->transfer_id != $transferId || $transferDetail->product_id != $productId) {
                return response()->json(['success' => false, 'message' => 'Transfer detail not found.'], 404);
            }
    
            $transferDetail->status = $transferDetail->status === 'active' ? 'closed' : 'active';
            $transferDetail->save();
    
            DB::commit();
    
            $btnText = $transferDetail->status === 'active' ? 'Unlock & Open' : 'Lock & Close';
            $balance = $transferDetail->status === 'active' ? 0 : $request->balance;
            $status = $transferDetail->status;
    
            return response()->json([
                'success' => true,
                'row_id' => $rowId,
                'status' => $status,
                'btn_text' => $btnText,
                'balance' => $balance
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    
    public function _dataTable(Request $request)
    {
        $user = Auth::user();

        $requests = null;

        if ($user->hasRole(['Admin', 'Super Admin']) || $user->warehouse->isNotEmpty()) {
            // User is Admin, Super Admin, or has a warehouse or stores
            $requests = Transfer::query();
            //$requests->orderBy('id', 'desc');
           // $requests = Transfer::orderBy('id', 'desc');
                
        } elseif($user->project->isNotEmpty()) {
            // User is not Admin or Super Admin and has no warehouse or stores
            $projectId = $this->switchProject->getCurrentProjectId();
            $requests = Transfer::where('requestable_type', Project::class)
                ->orderBy('id', 'desc')
                ->where('requestable_id', $projectId);
        }
        return DataTables::eloquent($requests)
            ->addColumn('number', function ($row) {
                static $counter = 1; // Initialize a static counter
                return $counter++;
            })
            ->addColumn('date', function ($row) {
                return $row->created_at;
            })
            ->addColumn('material_request_no', function ($row) {
                return $row->transfer_no;
            })
            ->addColumn('requestable', function ($row) {
                return $row->requested_by == Warehouse::class ? $row->warehouse->name : $row->project->name;
            })
            ->addColumn('requested_by', function ($row) {
                return $row->user->name ?? '';
            })
            ->addColumn('status', function ($row) {
                $statusUpper = strtoupper($row->status);
                if ($statusUpper === 'PENDING') {
                    return '<span style="background: #fef3c7; color: #b45309; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;"><i class="fas fa-clock mr-1"></i> ' . $statusUpper . '</span>';
                } elseif ($statusUpper === 'COMPLETED' || $statusUpper === 'DELIVERED') {
                    return '<span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;"><i class="fas fa-check-circle mr-1"></i> ' . $statusUpper . '</span>';
                }
                return '<span style="background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">' . $statusUpper . '</span>';
            })
            ->addColumn('action', function ($row) {
                $view = route('transfer.data', encrypt($row->id));
                $actions = '<a class="mr-4 text-danger font-weight-bold" style="font-size: 13px; text-decoration: none;" href="'.$view.'">View &rarr;</a>';

                return $actions;
            })
            ->rawColumns(['status', 'action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('transfer_no', 'like', '%' . $searchValue . '%')
                            ->orWhere('status', 'like', '%' . $searchValue . '%');
                            // ->orWhereHas('store', function ($subQuery) use ($searchValue) {
                            //     $subQuery->where('name', 'like', '%' . $searchValue . '%');
                            // });
                    });
                }
            })
            ->toJson();
    }


    public function getStatusList(Request $request, $projectId = NULL)
    {
        $projectId = $projectId ? decrypt($projectId) : null;
        $user = Auth::user();
        $requests = null;
    
        if ($user->hasRole(['Admin', 'Super Admin']) || $user->warehouse->isNotEmpty()) {
            if ($projectId) {
                $project = Project::find($projectId);
                if ($project) {
                    $requests = Transfer::where('requestable_type', Project::class)
                        ->where('requestable_id', $project->id)
                        ->orderBy('id', 'desc')
                        ->with(['details', 'purchases.details', 'deliveryOrders'])
                        ->get();
                } else {
                    $requests = Transfer::where('requestable_type', Warehouse::class)
                        ->where('requestable_id', $projectId)
                        ->orderBy('id', 'desc')
                        ->with(['details', 'purchases.details', 'deliveryOrders'])
                        ->get();
                }
            } else {
                $requests = Transfer::query()
                    ->with(['details', 'purchases.details', 'deliveryOrders'])
                    ->get();
            }
        } elseif ($user->project->isNotEmpty()) {
            $projectId = $this->switchProject->getCurrentProjectId();
            $requests = Transfer::where('requestable_type', Project::class)
                ->where('requestable_id', $projectId)
                ->orderBy('id', 'desc')
                ->with(['details', 'purchases.details', 'deliveryOrders'])
                ->get();
        }
    
        $requests = $requests->map(function ($row) {
            $completed = true;
    
            if ($row->details) {
                foreach ($row->details as $detail) {
                    $deliveredBySupplier = $row->deliveryOrders
                        ->where('product_id', $detail->product_id)
                        ->where('delivered_by', 'supplier')
                        ->sum('quantity');
    
                    $deliveredByStore = $row->deliveryOrders
                        ->where('product_id', $detail->product_id)
                        ->where('delivered_by', 'store')
                        ->sum('quantity');
    
                    $totalDelivered = $deliveredBySupplier + $deliveredByStore;
                    $balance = $detail->requested_quantity - $totalDelivered;
    
                    if ($detail->status !== 'closed' && $balance > 0) {
                        $completed = false;
                        break;
                    }
                }
            } else {
                $completed = false;
            }
    
            $row->status = $completed ? 'Completed' : 'Pending';
            return $row;
        });
    
        $searchValue = $request->input('search')['value'] ?? '';
    
        if (!empty($searchValue)) {
            $requests = $requests->filter(function ($row) use ($searchValue) {
                return stripos($row->transfer_no, $searchValue) !== false ||
                       stripos($row->status, $searchValue) !== false ||
                       stripos(optional($row->user)->name, $searchValue) !== false;
            });
        }
    
        return DataTables::of($requests)
            ->addColumn('number', function ($row) {
                static $counter = 1;
                return $counter++;
            })
            ->addColumn('date', function ($row) {
                $date = Carbon::parse($row->created_at)->format('d-M-Y h:i A');
                $dateClass = '';
    
                if ($row->status == 'pending' && !$row->purchase && $row->deliveryOrders->where('delivered_by', 'store')->isEmpty() && Carbon::parse($row->created_at)->diffInDays(Carbon::now()) >= 7) {
                    $dateClass = 'blink badge badge-pill';
                }
    
                return '<span class="' . $dateClass . '">' . $date . '</span>';
            })
            ->addColumn('material_request_no', function ($row) {
                return $row->transfer_no;
            })
            ->addColumn('requestable', function ($row) {
                return $row->requestable->name;
            })
            ->addColumn('requested_by', function ($row) {
                return optional($row->user)->name;
            })
            ->addColumn('status', function ($row) {
                $statusClass = $row->status == 'Completed' ? 'badge-success' : 'badge-danger';
                return '<span class="badge badge-pill ' . $statusClass . ' w-100">' . $row->status . '</span>';
            })
            ->addColumn('lpo', function ($row) {
                if (!$row->purchase_applicable) {
                    $lpo_text = 'Not Applicable';
                    $lpo_status = 'text-secondary';
                } elseif ($row->deliveryOrders->where('delivered_by', 'store')->isNotEmpty()) {
                    $lpo_text = '<i class="fa fa-times"></i>';
                    $lpo_status = 'text-danger';
                } else {
                    $transferDetailsQty = $row->details->sum('requested_quantity');
                    $totalPurchasedQty = 0;
    
                    if (!$row->purchases->isEmpty()) {
                        foreach ($row->purchases as $purchase) {
                            $totalPurchasedQty += $purchase->details->sum('quantity');
                        }
    
                        if ($transferDetailsQty == $totalPurchasedQty) {
                            $lpo_text = '<i class="fa fa-check text-success"></i>';
                            $lpo_status = 'text-success';
                        } else {
                            $lpo_text = '<a href="' . route('purchases.detail', encrypt($row->purchases->first()->id)) . '" class="text-primary" target="_blank">Partially Created</a>';
                            $lpo_status = 'text-warning';
                        }
                    } else {
                        $lpo_text = '<i class="fa fa-times"></i>';
                        $lpo_status = 'text-danger';
                    }
                }
    
                return '<span class="' . $lpo_status . ' w-100">' . $lpo_text . '</span>';
            })
            ->addColumn('mtv', function ($row) {
                if ($row->purchase && $row->deliveryOrders->where('delivered_by', 'store')->isEmpty()) {
                    $mtv_text = 'Not Applicable';
                    $mtv_status = 'text-secondary';
                } elseif ($row->deliveryOrders->where('delivered_by', 'store')->isNotEmpty() &&
                    $row->details->every(function ($detail) use ($row) {
                        return $detail->requested_quantity == $row->deliveryOrders->where('product_id', $detail->product_id)->where('delivered_by', 'store')->sum('quantity');
                    })
                ) {
                    $deliveryOrder = DeliveryOrder::where('transfer_id', $row->id)->first();
                    $encryptedId = encrypt($deliveryOrder->delivery_order_no);
                    $do_detail = route('deliveryOrder.detail', $encryptedId) . '?type=mtv';
                    $mtv_text = '<a href="' . $do_detail . '" target="_blank" class="text-success"><i class="fa fa-check"></i></a>';
                    $mtv_status = 'text-success';
                } else {
                    if (!$row->purchase && $row->deliveryOrders->isEmpty()) {
                        $mtv_text = '<i class="fa fa-times"></i>';
                        $mtv_status = 'text-danger';
                    } elseif (!$row->purchase && $row->deliveryOrders->where('delivered_by', 'store')->isNotEmpty()) {
                        $deliveryOrder = DeliveryOrder::where('transfer_id', $row->id)->first();
                        $encryptedId = encrypt($deliveryOrder->delivery_order_no);
                        $do_detail = route('deliveryOrder.detail', $encryptedId) . '?type=mtv';
    
                        $st = false;
                        foreach ($row->details as $r) {
                            $doq = DeliveryOrder::where('transfer_id', $row->id)->where('delivered_by', 'store')->where('product_id', $r->product_id)->sum('quantity');
                            if ($doq === $r->requested_quantity) {
                                $st = true;
                            }
                        }
                        if ($st) {
                            $mtv_text = '<a href="' . $do_detail . '" target="_blank" class="text-success"><i class="fa fa-check"></i></a>';
                        } else {
                            $mtv_text = '<a href="' . $do_detail . '" target="_blank" class="text-primary">Partially Delivered</a>';
                        }
                        $mtv_status = 'text-warning';
                    } else {
                        $mtv_text = '<i class="fa fa-times"></i>';
                        $mtv_status = 'text-danger';
                    }
                }
    
                return '<span class="' . $mtv_status . ' w-100">' . $mtv_text . '</span>';
            })
            ->addColumn('do', function ($row) {
                if (!$row->purchase_applicable) {
                    $do_text = 'Not Applicable';
                    $do_status = 'text-secondary';
                } elseif (!$row->purchase) {
                    $do_text = '<i class="fa fa-times"></i>';
                    $do_status = 'text-danger';
                } else {
                    if ($row->deliveryOrders->isEmpty()) {
                        $do_text = '<i class="fa fa-times"></i>';
                        $do_status = 'text-danger';
                    } else {
                        $transferDetailsQty = $row->details->sum('requested_quantity');
                        $totalDeliveredQty = 0;
    
                        foreach ($row->deliveryOrders as $deliveryOrder) {
                            if ($deliveryOrder->delivered_by == 'supplier') {
                                $totalDeliveredQty += $deliveryOrder->quantity;
                            }
                        }
    
                        if ($transferDetailsQty == $totalDeliveredQty) {
                            $do_text = '<i class="fa fa-check"></i>';
                            $do_status = 'text-success';
                        } else {
                            $do_text = '<i class="fa fa-times"></i>';
                            $do_status = 'text-danger';
                        }
                    }
                }
    
                return '<span class="' . $do_status . ' w-100">' . $do_text . '</span>';
            })
            ->addColumn('action', function ($row) {
                $transfer_voucher_route = route('transfer.voucher.create', encrypt($row->id));
                $view = route('transfer.detail', encrypt($row->id));
                $actions = '<a class="mr-4 text-info" href="'.$view.'">View</a>';
                $actions .= '<a class="mr-4 text-success" href="'.route('transfer.edit', encrypt($row->id)).'">Edit</a>';
    
                if(Auth::user()->type=='data_entry') {
                        $actions .= '<a href="'.$transfer_voucher_route.'" class="mr-4 text-danger" item-id="' . encrypt($row->id) . '">Make MTV</a>';
                }

                $actions .= '<a href="#!" class="deleteBtn float-right text text-danger" item-id="' . encrypt($row->id) . '"><i class="ik ik-trash-2 f-16"></i></a>';
            
                $transferDetailsQty = $row->details->sum('requested_quantity');
                $deliveredQTY = $row->deliveryOrders->where('transfer_id', $row->id)->sum('quantity');
                if(Auth::user()->hasRole(['Admin','Super Admin', 'Warehouse Incharge']) && !$row->purchase && $row->status =='pending' && $transferDetailsQty > $deliveredQTY) {
                    $lpo_status_route = route('transfer.changeStatus', encrypt($row->id));
                    $actions .= '<div class="form-check">';
                    $actions .= '<input type="checkbox" class="form-check-input" id="customSwitch'.$row->id.'" name="my-checkbox" '.($row->purchase_applicable == 1 ? 'checked' : '').' onchange="updateSwitchValue(\''.$lpo_status_route.'\')">';
                    $actions .= '<label class="form-check-label" for="customSwitch'.$row->id.'">Enable L.P.O</label></div>';
                }
    
                return $actions;
            })
            ->rawColumns(['action', 'status', 'do', 'mtv', 'lpo', 'date'])
            ->toJson();
    }



    public function createTransferVoucher($transfer_request_id)
    {
        $transfer_request_id = decrypt($transfer_request_id);
        $transfer_request = Transfer::findOrFail($transfer_request_id);
        $units = Unit::all();
        return view('inventory.transfer.requests.create_transfer_voucher', compact('transfer_request','units'));
    }

    public function storeTransferVoucher(Request $request, $transfer_request_id)
    {
        $transfer_request_id = decrypt($transfer_request_id);
        $transfer_request = Transfer::findOrFail($transfer_request_id);

        $rules = [
            'date'  => 'required',
            'delivery_order_no' => 'required'
        ];

        $validatedData = $request->validate($rules);

        $quantity = $request->mtv_qty; // Updated
        $unit_ids = $request->unit_id; // Updated
        $details = [];

        for ($index = 0; $index < count($request->product_id); $index++) {
            $pid = $request->product_id[$index];
            if (!empty($quantity[$index]) && $quantity[$index] != 0) {
                $details[] = [
                    'transfer_id' => $transfer_request->id,
                    'transfer_no' => $transfer_request->transfer_no,
                    'quantity' => $quantity[$index],
                    'date' => $request->date,
                    'delivery_order_no' => $request->delivery_order_no,
                    'product_id' => $pid,
                    'unit_id' => $unit_ids[$index] ?? null,
                    'status' => 'success',
                ];
            }
        }

        foreach ($details as $detail) {

            $deliveryOrder = DeliveryOrder::where('transfer_id', $transfer_request->id)
                ->where('product_id', $detail['product_id'])
                ->first();

            DeliveryOrder::updateOrCreate(
                [
                    'transfer_id' => $transfer_request->id,
                    'delivery_order_no' => $request->delivery_order_no,
                    'product_id' => $detail['product_id']
                ],
                $detail
            );

            // Update or create stock record
            $stockRecord = StockRecord::where('stockable_id', $this->switchProject->getCurrentProjectId())
                ->where('stockable_type', Project::class)
                ->where('product_id', $detail['product_id'])
                ->first();

            if ($stockRecord) {
                // Subtract the delivered order quantity from the existing stock quantity
                $stockQuantity = $stockRecord->quantity ?? 0;
                $stockQuantity -= $deliveryOrder->quantity ?? 0;

                // Add the new quantity from the transfer detail
                $stockQuantity += $detail['quantity'];

                // Project Material Issued Quantity
                $issuedQTY = StoreMaterialIssued::where('product_id', $detail['product_id'])->where('store_id', $this->switchProject->getCurrentProjectId())->sum('quantity');
                $stockRecord->quantity = $stockQuantity - $issuedQTY;
                $stockRecord->save();
            } else {
                StockRecord::create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'stockable_id' => $this->switchProject->getCurrentProjectId(),
                    'unit_id' => $detail['unit_id'],
                    'stockable_type' => Project::class,
                ]);
            }

            $brand_id = Product::find($detail['product_id'])->brand->id;

            TransferDetail::updateOrCreate(
                ['transfer_id' => $transfer_request->id, 'product_id' => $detail['product_id']],
                ['delivered_quantity' => $detail['quantity'], 'requested_quantity' => $detail['quantity'], 'unit_id' => $detail['unit_id'], 'brand_id' => $brand_id]
            );
        }

        $redirect = route('transfer.list');

        return response()->json([
            'message' => 'Voucher saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }

    //store related transfer data
    public function getStoreTransfers()
    {
        $transfers = Transfer::paginate(24);
        return view('inventory.transfer.requests.list', compact('transfers'));
    } 

    public function getTransferStatus($projectId = null)
    {
        if (!$projectId && Auth::user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge'])) {
            $projectsWithTransferStatus = [];
            $projectIdsWithTransfers = Transfer::where('requestable_type', Project::class)
                ->select('requestable_id')
                ->distinct()
                ->pluck('requestable_id');
    
            $data = Project::whereIn('id', $projectIdsWithTransfers)->get();
    
            foreach ($data as $project) {
                $transfers = Transfer::where('requestable_type', Project::class)
                    ->where('requestable_id', $project->id)
                    ->whereNull('deleted_at')
                    ->with(['details', 'deliveryOrders'])
                    ->get();
    
                $pending_transfers = 0;
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
                        $pending_transfers++;
                    }
                }
    
                $projectsWithTransferStatus[] = [
                    'project' => $project,
                    'pending_transfers' => $pending_transfers,
                    'total_transfers' => $total_transfers,
                ];
            }
    
            $warehouse = Warehouse::first();
            $warehouseTransfers = Transfer::where('requestable_type', Warehouse::class)
                ->where('requestable_id', $warehouse->id)
                ->whereNull('deleted_at')
                ->with(['details', 'deliveryOrders'])
                ->get();
    
            $warehouse_pending_transfers = 0;
            $warehouse_total_transfers = $warehouseTransfers->count();
    
            foreach ($warehouseTransfers as $transfer) {
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
                    $warehouse_pending_transfers++;
                }
            }
    
            $pageName = 'Select a Project for Material Requests';
            $page = 'transfers';
            return view('inventory.transfer.projects', compact('projectsWithTransferStatus', 'warehouse_total_transfers', 'warehouse_pending_transfers', 'pageName', 'page'));
        }
    
        return view('inventory.transfer.requests.transfer_status', compact('projectId'));
    }


    private function transfersCompleted()
    {
        $transfers = Transfer::with('details')
            ->where('status', '!=', 'completed')
            ->get();

        foreach ($transfers as $transfer) {
            $allDelivered = true;

            foreach ($transfer->details as $detail) {
                if ($detail->delivered_quantity < $detail->requested_quantity) {
                    $allDelivered = false;
                    break;
                }
            }

            if ($allDelivered) {
                $transfer->update(['status' => 'completed']);
            }
        }
    }

    public function create()
    {
        $units = Unit::all();
        $brands = Brand::all();
        return view('inventory.transfer.create_request', compact('units','brands'));
    }

    public function store(Request $request, $id = null)
    {
        $id = $id ? decrypt($id) : null;
        $rules = [
            'date' => 'required',
            'transfer_no' => 'nullable|numeric'
        ];
        
        $validatedData['store_id'] = Warehouse::first()->id;
        $validatedData = $request->validate($rules);

        $constantPrefix = "MRQ";
        $lastRequest = Transfer::latest()->first();
        $sequenceNumber = $lastRequest ? (int)substr($lastRequest->ref_no, strlen($constantPrefix)) + 1 : 1;
        $validatedData['ref_no'] = $constantPrefix . str_pad($sequenceNumber, 5, '0', STR_PAD_LEFT);
        $transfer_no = $lastRequest->transfer_no + 1;
        $validatedData['transfer_no'] = $request->transfer_no ? $request->transfer_no : $transfer_no;
        $validatedData['user_id'] = Auth::user()->id;
        $currentUser = auth()->user();
        

        if ($currentUser->hasRole('Store Incharge')) {
            $validatedData['requestable_type'] = Project::class;
            $validatedData['requestable_id'] = $this->switchProject->getCurrentProjectId();
            $validatedData['project_id'] = $this->switchProject->getCurrentProjectId();
            $validatedData['store_id'] = $this->switchProject->getCurrentProjectId();
        } else {
            $validatedData['requestable_id'] = Warehouse::first()->id;
            $validatedData['requestable_type'] = Warehouse::class;
            $validatedData['project_id'] = Warehouse::first()->id;
            $validatedData['store_id'] = Warehouse::first()->id;
        }
        
        $transfer = Transfer::create($validatedData);
        $product_ids = $request->product_id;
        $orderDetails = [];
        for ($i = 0; $i < count($product_ids); $i++)
        {
            $pid = $product_ids[$i];
            TransferDetail::create([
                'transfer_id' => $transfer->id,
                'product_id'    => $pid,
                'requested_quantity'  => $request->quantity[$i],
                'delivered_quantity'  => 0,
                'unit_id' => !empty($request->unit_id[$pid]) ? $request->unit_id[$pid] : Product::find($pid)->unit->id,
                'brand_id' => !empty($request->brand_id[$pid]) ? $request->brand_id[$pid] : Product::find($pid)->brand->id,
            ]);
        }

        $redirect = route('transfer.list');

        return response()->json([
            'message' => 'Request saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }

    public function importStoreTransfers()
    {
        $warehouses = Warehouse::all();
        $projects = Project::all();
        return view('inventory.transfer.import', compact('warehouses', 'projects'));
    }
    
    public function createStoreTransfers()
    {
        $warehouses = Warehouse::all();
        $suppliers = Supplier::all();
        $activities = Category::all();
        $units = Unit::all();
        $brands = Brand::all();
        return view('inventory.transfer.requests.old_create', compact('warehouses','suppliers', 'activities','brands','units'));

        // if(Auth::user()->type=='data_entry' || Auth::user()->hasRole('Store Incharge')) {
        //     $warehouses = Warehouse::all();
        //     $suppliers = Supplier::all();
        //     $activities = Category::all();
        //     $units = Unit::all();
        //     $brands = Brand::all();
        //     return view('inventory.transfer.requests.old_create', compact('warehouses','suppliers', 'activities','brands','units'));
        // } else {
        //     $units = Unit::all();
        //     $brands = Brand::all();
        //     return view('inventory.transfer.create_request', compact('units','brands'));
        // }
    }

    public function saveStoreTransfers(Request $request, $id = null)
    {
        $id = $id ? decrypt($id) : null;
        $rules = [
            'date' => 'required',
            'transfer_no' => [
                'nullable',
                $id ? Rule::unique('transfers', 'transfer_no')->ignore($id) : Rule::unique('transfers', 'transfer_no')
            ],
            'purchase_applicable' => 'required|in:0,1',
        ];


        $this->validate($request, $rules, [
            'transfer_no.unique' => 'The Material Request Number already exists.'
        ]);

        $user = Auth::user();
        $project = $this->switchProject->getUserCurrentProject();

        if ($user->hasRole(['Admin', 'Super Admin']) || $user->warehouse->isNotEmpty()) {
            $store_id = Warehouse::first()->id;
            $requestable_type = Warehouse::class;
            $requestable_id = $store_id;
            $projectName = 'Main Warehouse Sajaa';
        } else {
            $store_id = Warehouse::first()->id;
            $requestable_type = Project::class;
            $requestable_id = $project->id;
            $projectName = $project->name;
        }

        $ref_no_prefix = "MRQ";
        $transfer_no = !empty($request->transfer_no)
        ? $request->transfer_no
        : $this->generateUniqueTransferNo();
        $ref_no = $ref_no_prefix . str_pad(Transfer::latest()->value('id') + 1, 5, '0', STR_PAD_LEFT);

        $transferData = [
            'ref_no' => $ref_no,
            'transfer_no' => $transfer_no,
            'user_id' => $user->id,
            'requestable_type' => $requestable_type,
            'requestable_id' => $requestable_id,
            'project_id' => $requestable_id,
            'store_id' => $store_id,
            'date' => $request->date,
            'purchase_applicable' => $request->purchase_applicable,
        ];

        try {
            DB::beginTransaction();

            $transfer = Transfer::create($transferData);

            // if ($request->purchase_no != '0' && $request->purchase_no != '') {
            //     $purchase_code = "TRN" . str_pad(Purchase::latest()->value('id') + 1, 5, '0', STR_PAD_LEFT);
            //
            //     $purchaseData = [
            //         'purchase_no' => $request->purchase_no,
            //         'transfer_id' => $transfer->id,
            //         'date' => $request->purchase_date,
            //         'ref_no' => $purchase_code,
            //         'supplier_id' => $request->supplier_id,
            //         'total_amount' => 0,
            //         'paid_amount' => 0,
            //         'status' => 'pending',
            //         'payment_status' => 'unpaid',
            //         'notes' => $request->remarks,
            //         'buyer_id' => $user->id,
            //     ];
            //
            //     $purchase = Purchase::create($purchaseData);
            // }

            // $orderDetails = [];
            foreach ($request->product_id as $i => $pid) {
                $qty = $request->lpo_qty[$i] ?? $request->quantity[$i];
                // $makeLPO = $request->makeLPO[$i] ?? 0;

                $transferDetails = TransferDetail::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $pid,
                    'requested_quantity' => $request->quantity[$i],
                    'delivered_quantity' => 0,
                    'unit_id' => $request->unit_id[$i] ?? Product::find($pid)->unit->id,
                    'brand_id' => Product::find($pid)->brand->id,
                ]);

                // if ($request->purchase_no != '' && $request->purchase_no != '0' && $makeLPO == 1) {
                //     $orderDetails[] = [
                //         'date' => $request->date,
                //         'purchase_id' => $purchase->id,
                //         'quantity' => $qty,
                //         'cost' => 0,
                //         'unit_id' => Product::find($pid)->unit->id,
                //         'product_id' => $pid,
                //         'total_amount' => 0,
                //         'notes' => $request->remarks,
                //     ];
                // }
            }

          //  PurchaseDetail::insert($orderDetails);

            DB::commit();

            $message = 'New Material Request # '.$transfer->transfer_no.' by '.Auth::user()->name.' for '.$projectName;
            $this->notificationService->notifyAdmins($message);
            
            $redirect = route('transfer.create');

            return response()->json([
                'message' => 'Request saved successfully',
                'status' => 'success',
                'redirect' => $redirect,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while saving the request',
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function generateUniqueTransferNo()
    {
        $unique = false;
        $transfer_no = '';
    
        while (!$unique) {
            $transfer_no = mt_rand(100000, 999999);
    
            $existing = Transfer::where('transfer_no', $transfer_no)->first();
    
            if (!$existing) {
                $unique = true;
            }
        }
    
        return $transfer_no;
    } 

    public function edit($id)
    {
        $products = [];
        // $transfers = Transfer::where('requestable_type', Project::class)
        //     ->where('requestable_id', 5)
        //     ->get();

        // foreach ($transfers as $transfer) {
        //     $details = $transfer->details;

        //     $deliveryOrders = DeliveryOrder::where('transfer_id', $transfer->id)->get();
        //     foreach($deliveryOrders as $deliveryOrder) {
        //         $product_id = $deliveryOrder->product_id;
        //         $quantity = $deliveryOrder->quantity;
        //         $existingDetail = $details->where('product_id', $product_id)->first();
        //         if(!$existingDetail) {
        //             TransferDetail::create([
        //                 'transfer_id' => $transfer->id,
        //                 'product_id' => $product_id,
        //                 'requested_quantity' => $quantity,
        //                 'delivered_quantity' => $quantity,
        //                 'unit_id' => Product::find($product_id)->unit_id,
        //                 'brand_id' => Product::find($product_id)->brand_id
        //             ]);
        //         }
        //         // If product_id already exists in $products array, add quantity to existing quantity
        //         if (array_key_exists($product_id, $products)) {
        //             $products[$product_id]['quantity'] += $quantity;
        //         } else {
        //             // If product_id does not exist, create a new entry
        //             $products[$product_id] = [
        //                 'product_id' => $product_id,
        //                 'quantity' => $quantity,
        //                 'project_id' => $transfer->requestable_id
        //             ];
        //         }
        //     }
        // }

        // foreach ($products as $key => $row) {
        //     $stock = StockRecord::where('product_id', $row['product_id'])
        //         ->where('stockable_type', Project::class)
        //         ->where('stockable_id', $row['project_id'])
        //         ->first();

        //     if ($stock) {
        //         if ($stock->quantity != $row['quantity']) {
        //             $stock->quantity = $row['quantity'];
        //             $stock->save();
        //         }
        //     }
        // }


        $id = decrypt($id);
        $transfer_request = Transfer::findOrFail($id);
        $units = Unit::all();
        $brands = Brand::all();
        return view('inventory.transfer.edit', compact('transfer_request', 'units'));
    }

    public function update(Request $request, $id)
    {
        $id = $id ? decrypt($id) : null;
        $transfer = Transfer::findOrFail($id);
        $rules = [
            'date' => 'required',
            'transfer_no' => 'required|unique:transfers,transfer_no,' . $id,
        ];

        $this->validate($request, $rules, [
            'transfer_no.unique' => 'The Material Request No. already exists.'
        ]);

        try {
            DB::beginTransaction();

            // Check if the transfer number is being updated
            if ($transfer->transfer_no !== $request->transfer_no) {
                // Check if the new transfer number already exists
                if (Transfer::where('transfer_no', $request->transfer_no)->exists()) {
                    return response()->json([
                        'message' => 'The Transfer Number must be unique.',
                        'status' => 'error',
                    ], 422);
                }
                $transfer->remarks = $request->remarks;
                $transfer->save();
            }

            $uniqueProducts = collect($request->product_id)->unique();

            foreach ($uniqueProducts as $i => $pid) {
                $transferDetail = TransferDetail::where('transfer_id', $transfer->id)
                    ->where('product_id', $pid)
                    ->first();

                TransferDetail::updateOrCreate([
                    'transfer_id' => $id,
                    'product_id' => $pid, // Use $pid directly
                ], [
                    'requested_quantity' => $request->quantity[$i],
                    'delivered_quantity' => $transferDetail ? $transferDetail->delivered_quantity : 0,
                    'unit_id' => $request->unit_id[$i] ?? Product::find($pid)->unit_id,
                    'brand_id' => $request->brand_id[$i] ?? Product::find($pid)->brand_id
                ]);
            }

            // Delete any extra products that are not in the request
            $extraProducts = TransferDetail::where('transfer_id', $id)
                ->whereNotIn('product_id', $uniqueProducts)
                ->get();

            $extraProductsDeliveryOrder = DeliveryOrder::where('transfer_id', $id)
                ->whereNotIn('product_id', $uniqueProducts)
                ->get();

            if(!empty($extraProducts)) {
                foreach ($extraProducts as $extraProduct) {
                    $extraProduct->delete();
                }
            }

            if(!empty($extraProductsDeliveryOrder)) {
                foreach ($extraProductsDeliveryOrder as $extraProduct) {
                    $extraProduct->delete();
                }
            }

            DB::commit();

            $redirect = route('transfer.create');

            return response()->json([
                'message' => 'Request updated successfully',
                'status' => 'success',
                'redirect' => $redirect,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while updating the request',
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function saveTransferPurchase(Request $request, $id = NULL)
    {
        $rules = [
            'material_request_id' => 'required',
            'product_id' => 'required|array',
            'date' => 'required',
            'supplier_id' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        \DB::transaction(function () use ($request) {

            $material_request_id = decrypt($request->material_request_id);

            $materialRequest = MaterialRequest::findOrFail($material_request_id);

            $order = new Purchase;
            $total_amount = 0;
            $order->date = $request->date;
            $order->ref_no = 'MEMCO-' . date("Ymd") . '-'. date("his");
            $order->supplier_id = $request->supplier_id;
            $order->total_amount = $total_amount;
            $order->paid_amount = $total_amount;
            $order->warehouse_id = $materialRequest->warehouse_id;
            $order->status = 'pending';
            $order->payment_status = 'unpaid';
            $order->notes = $request->notes;
            $order->material_request_id = $material_request_id;
            $order->buyer_id = Auth::user()->id;

            $products = $request->product_id;

            foreach ($products as $pid) {

                $unit = Unit::where('id', $request->unit_id[$pid])->first();
                $prod = Product::findOrFail($pid);
                $orderDetails[] = [
                    'date' => $request->date,
                    'purchase_id' => $order->id,
                    'quantity' => $request->quantities[$pid],
                    'cost' => $prod->price,
                    'unit_id' =>  $unit->id,
                    'product_id' => $pid,
                    'total_amount' => $prod->price * $request->quantities[$pid],
                    'notes' => $request->notes,
                ];

                $total_amount = $prod->price * $request->quantities[$pid];

                // $stock = StockRecord::where('product_id', $pid)
                //         ->where('stockable_id', $request->warehouse_id)
                //         ->where('stockable_type', Warehouse::class)
                //         ->first();

                // if(!empty($stock))
                // {
                //     $arr['quantity'] = $stock->quantity + $request->quantities[$pid];
                //     StockRecord::where('product_id', $pid)
                //         ->where('stockable_id', $request->warehouse_id)
                //         ->update($arr);
                // }
                // else
                // {
                //     $arr['stockable_type'] = Warehouse::class;
                //     $arr['stockable_id'] = $request->warehouse_id;
                //     $arr['quantity'] = $request->quantities[$pid];
                //     $arr['product_id'] = $pid;
                //     StockRecord::create($arr);
                // }


            }
            Purchase::where('id', $order->id)->update(['total_amount' => $total_amount, 'paid_amount' => $total_amount]);
            PurchaseDetail::insert($orderDetails);
        }, 10);

        $redirect = route('purchases.list');
        return response()->json([
            'message' => 'Purchase saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);

    }

    public function deleteStoreTransfers($id)
    {
        $id = decrypt($id);

        // Get the transfer
        $transfer = Transfer::find($id);

        if (!$transfer) {
            return response()->json([
                'message' => 'Transfer not found',
                'status' => 'error'
            ], 404);
        }

        // Get all delivery orders related to the transfer
        $deliveryOrders = DeliveryOrder::where('transfer_id', $id)->get();

        if(!empty($deliveryOrders)) {
            foreach ($deliveryOrders as $order) {
                // Subtract the quantity from the stock records
                $stockRecord = StockRecord::where('stockable_type', $transfer->requestable_type)
                    ->where('stockable_id', $transfer->requestable_id)
                    ->where('product_id', $order->product_id)
                    ->first();

                if ($stockRecord) {
                    $stockRecord->quantity -= $order->quantity;
                    $stockRecord->save();
                }
            }
            DeliveryOrder::where('transfer_id', $transfer->id)->delete();
        }
        TransferDetail::where('transfer_id', $transfer->id)->delete();
        $transfer->transfer_no = null;
        $transfer->save();
        $transfer->delete();

        $redirect = route('transfer.list');
        return response()->json([
            'message' => 'Transfer deleted successfully',
            'status' => 'success',
            'redirect' => $redirect
        ], 200);
    }

    // warehouse related transfer data
    public function getWarehouseTransfers()
    {
        $transfers = Auth::user()->warehouse->first() ? Transfer::where('warehouse_id', Auth::user()->warehouse->first()->id)->paginate(12) : array();
        return view('inventory.transfer.warehouse.list', compact('transfers'));
    }

    public function showWarehouseTransfer($id)
    {
        $id = decrypt($id);

        $transfer = Transfer::with(['project', 'details', 'user', 'warehouse'])->where('id', $id)->first();

        return view('inventory.transfer.warehouse._transferData', compact('transfer'));
    }

    public function createWarehouseTransfers()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $units = Unit::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('inventory.transfer.warehouse.create', compact('categories','brands','units', 'products', 'warehouses'));
    }

    public function saveWarehouseTransfers(Request $request, $id = NULL)
    {
        dd('Under process');
        $id = $request->id ? decrypt($request->id) : $request->id;

        $transfer = Transfer::findOrFail($id);

        $rules = [
            'driver_id'  => 'required',
            'comments' => 'string'
        ];

        $validatedData = $request->validate($rules);
        $constantPrefix = "DO";

        $lastRequest = Transfer::latest()->first();
        $sequenceNumber = $lastRequest ? (int)substr($lastRequest->ref_no, strlen($constantPrefix)) + 1 : 1;

        $validatedData['ref_no'] = $constantPrefix . str_pad($sequenceNumber, 5, '0', STR_PAD_LEFT);

        $validatedData['transfer_id'] = $id;
        $validatedData['user_id'] = Auth::user()->id;

        $product_ids = $request->product_id;

        $stock_availablity = true;


        foreach($product_ids as $pid)
        {
            //stock records
            $warehouseId = Auth::user()->warehouse->first()->id;

            $stock = StockRecord::where('stockable_id', $warehouseId)
                ->where('stockable_type', Warehouse::class)
                ->where('product_id', $pid)
                ->first();

            $stock_store = StockRecord::where('stockable_id', $transfer->store_id)
                ->where('stockable_type', Project::class)
                ->where('product_id', $pid)
                ->first();

            $success = false;

            if($stock && $stock->quantity > $request->delivered_quantity[$pid])
            {
                // update transfer status
                $transfer->update(['status' => 'delivered']);
                // save transfer details
                $transferDetail = TransferDetail::where('transfer_id', $transfer->id)
                ->where('product_id', $pid)
                ->update(['delivered_quantity' => $request->delivered_quantity[$pid]]);

                // update stock record
                StockRecord::where('stockable_id', Auth::user()->warehouse->first()->id)
                ->where('stockable_type', Warehouse::class)
                ->where('product_id', $pid)
                ->update(['quantity' => $stock->quantity - $request->delivered_quantity[$pid]]);

                // Update / Create Store Stock
                $store_stock['quantity'] = $stock_store ? $stock_store->quantity + $request->delivered_quantity[$pid] : $request->delivered_quantity[$pid];
                StockRecord::updateOrCreate(
                    [
                        'stockable_id' => $transfer->store_id,
                        'stockable_type' => Project::class,
                        'product_id' => $pid
                    ],
                    $store_stock
                );
                $success = true;
            }

        }

        if(!$success)
        {
            $redirect = route('transfer.warehouses.list');
            return response()->json([
                'message' => '<span class="text text-warning">All products out of stock!</span>',
                'status' => 'error',
                'redirect' => $redirect
            ]);
        }

        $TransferDelivery = TransferDelivery::updateOrCreate(['transfer_id' => $id], $validatedData);

        $redirect = route('transfer.warehouses.list');

        return response()->json([
            'message' => 'Transfer saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);

    }

    private function checkTransferLimit($store_id, $product_id, $quantity)
    {
        $ItemLimits = StoreItemLimit::where('store_id', $store_id)
            ->where('product_id', $product_id)
            ->first();
        if($ItemLimits)
        {
            $limit_quantity = $ItemLimits->max_quantity;
            if($limit_quantity >= $quantity)
            {
                return $quantity;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
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
        return view('inventory.transfer.requests._get_products', compact('products', 'brands', 'units'));
    }

    public function _getTransferProducts(Request $request): mixed
    {
        $productIds = $request->input('products', []);
        $storeId = $this->switchProject->getCurrentProjectId();
        $products = Product::with(['stockRecords' => function ($query) use ($storeId) {
            $query->where('stockable_id', $storeId)->where('stockable_type', Project::class)->select('id', 'product_id', 'quantity', 'stockable_id','stockable_type');
        }, 'unit'])->whereIn('id', $productIds)->get();

        foreach ($products as $product) {
            if ($product->stockRecords->isNotEmpty()) {
                $quantities = $product->stockRecords->pluck('quantity')->toArray();
                $product->setAttribute('quantities', $quantities);

                $maxQuantity = $product->max_quantity ?? 0;
                $totalStockQuantity = array_sum($quantities);
                $remainingQuantity = max(0, $maxQuantity - $totalStockQuantity);
            } else {
                $product->setAttribute('quantities', null);
                $product->setAttribute('remaining_quantity', $product->max_quantity ?? null);
                continue;
            }

            $storeItemLimit = StoreItemLimit::where('product_id', $product->id)->first();
            $maxQuantity = $storeItemLimit ? $storeItemLimit->max_quantity : 0;
            $remainingQuantity = max(0, $maxQuantity - $totalStockQuantity);
            $product->setAttribute('max_quantity', $maxQuantity);
            $product->setAttribute('remaining_quantity', $remainingQuantity);
        }

        $brands = Brand::all();
        $units = Unit::all();
        return view('inventory.transfer.requests._get_transfer_products', compact('products', 'brands', 'units'));
    }

    public function transferReturns()
    {
        $data = TransferReturn::where('store_id', $this->switchProject->getCurrentProjectId())
            ->get();
            

        $stocks = StockRecord::where('stockable_id', $this->switchProject->getCurrentProjectId())
            ->where('stockable_type', Project::class)
            ->where('quantity', '>=' , 0)
            ->get();

        $products = $stocks->map(function ($stock) {
            return ['id' => $stock->product_id, 'text' => $stock->product->name];
        });
        
        return view('inventory.transfer.requests.transfer_returns', compact('data','products'));
    }
    
    public function createReturnRequest()
    {
        
        return view('inventory.transfer.requests.createReturnRequest');
    }    

    public function transferReturnsSave(Request $request)
    {
        $rules = [
            'quantity' => 'required|numeric',
            'product_id' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['remarks'] = $request->remarks;
        $validatedData['store_id'] = $this->switchProject->getCurrentProjectId();
        $validatedData['user_id'] = Auth::user()->id;
        $stock = StockRecord::where('stockable_id', $this->switchProject->getCurrentProjectId())
        ->where('stockable_type', Project::class)
        ->where('product_id', $request->product_id)
        ->first();

        if($stock && $stock->quantity >= $request->quantity)
        {
            $save = TransferReturn::create($validatedData);

            $redirect = route('transferReturns.list');

            if($save)
            {
                $message = 'Material Return From '.$this->switchProject->getCurrentProjectName().' by '.Auth::user()->name.' for '.Product::find($request->product_id)->name;
                $this->notificationService->notifyAdmins($message);  
                
                return response()->json([
                    'message' => 'Material Return saved Successfully!',
                    'status' => 'success',
                    'redirect' => $redirect
                ], 200);
            }
            else
            {
                return response()->json([
                    'message' => 'Material Return Error!',
                    'status' => 'error',
                    'redirect' => $redirect
                ], 200);

            }
        }
        else
        {
            $message = $stock
                ? '<span class="text text-warning">Product Insufficient Quantity Available in Stock!</span>'
                : '<span class="text text-danger">Product out of stock!</span>';

            return response()->json([
                'message' => $message,
                'status' => 'error',
            ]);
            exit;
        }
    }

    public function approveTransferReturnRequest(Request $request)
    {
        $id = decrypt($request->id);
        $data = TransferReturn::findOrFail($id);
        $redirect = route('returnables.list', encrypt($data->store_id));
        if($request->status == 'success')
        {
            $stock_store = StockRecord::where('stockable_id', $data->store_id)
                ->where('stockable_type', Project::class)
                ->where('product_id', $data->product_id)
                ->first();

            if($stock_store && $stock_store->quantity >= $data->quantity)
            {
                $stock_store->quantity = $stock_store->quantity - $data->quantity;
                $stock_store->save();
                $warehouse = Warehouse::first();
                $warhouse_stock = StockRecord::where('stockable_id', $warehouse->id)
                ->where('stockable_type', Warehouse::class)
                ->where('product_id', $data->product_id)
                ->first();

                if($warhouse_stock)
                {
                    $warhouse_stock->quantity = $warhouse_stock->quantity + $data->quantity;
                    $warhouse_stock->save();
                }
                else
                {
                    StockRecord::create([
                        'stockable_id' => $warehouse->id,
                        'stockable_type' => Warehouse::class,
                        'quantity' => $data->quantity,
                        'product_id' => $data->product_id,
                    ]);
                }

                $save = TransferReturn::where('id', $data->id)
                    ->update(['status' => $request->status]);

                return response()->json([
                    'message' => 'Material Return saved Successfully!',
                    'status' => 'success',
                    'redirect' => $redirect
                ], 200);
            }
            else
            {
                $message = $stock
                    ? '<span class="text text-warning">Product Insufficient Quantity Available in Stock!</span>'
                    : '<span class="text text-danger">Product out of stock!</span>';

                return response()->json([
                    'message' => $message,
                    'status' => 'error',
                ]);
                exit;
            }
        }
        else
        {
            $message = '<span class="text text-warning">Status Not Received!</span>';
            return response()->json([
                'message' => $message,
                'status' => 'error',
                'redirect' => $redirect
            ]);
            exit;
        }

    }

    public function transferReturnsDelete($id)
    {
        $id = decrypt($id);
        if($data = TransferReturn::where('id', $id)->first())
        {
            $store = Project::find($data->store_id);
            // Deduct direct transfer quantity from store stock
            $store_stock = StockRecord::where('stockable_id', $data->store_id)
            ->where('stockable_type', Project::class)
            ->where('product_id', $data->product_id)
            ->first();
            $store_stock->quantity = $store_stock->quantity + $data->quantity;
            $store_stock->save();
            // Add direct transfer quantity for warehouse
            $stock = StockRecord::where('stockable_id', Warehouse::first()->id)
            ->where('stockable_type', Warehouse::class)
            ->where('product_id', $data->product_id)
            ->first();
            $stock->quantity = $stock->quantity - $data->quantity;
            $stock->save();

            $data->delete();
            $redirect = route('transferReturns.list');
            return response()->json([
                'message' => 'Material Return deleted successfully',
                'status'=>'success',
                'redirect' => $redirect
            ], 200);
        }
        return response()->json([
            'message' => 'Error occured while deleting',
            'status'=>'error'
        ], 200);
    }

    public function _getReturnProducts(Request $request): mixed
    {
        $productIds = $request->input('products', []);

        $stocks = StockRecord::where('stockable_id', $this->switchProject->getCurrentProjectId())
            ->where('stockable_type', Project::class)
            ->where('quantity', '>=' , 0)
            //->whereIn('product_id', $productIds)
            ->get();

        $formattedProducts = $stocks->map(function ($stock) {
            return ['id' => $stock->product_id, 'text' => $stock->product->name];
        });

        return response()->json($formattedProducts);
    }

    public function showTransferReturns($id)
    {
        $id = decrypt($id);
        $data = TransferReturn::findOrFail($id);
        return view('inventory.store.approve_return', compact('data'));
    }

    public function getProductsForTransfers(Request $request)
    {
        $transfer_id = decrypt($request->transfer_id);

        $purchaseIds = Purchase::where('transfer_id', $transfer_id)->pluck('id');

        if ($purchaseIds->isEmpty()) {
            // If there are no purchases, get all transfer details
            $data = TransferDetail::where('transfer_id', $transfer_id)
                ->where('requested_quantity', '>', 'delivered_quantity')
                ->get();
        } else {
            // If there are purchases, get all products from purchase details
            $excludeItems = PurchaseDetail::whereIn('purchase_id', $purchaseIds)->pluck('product_id')->toArray();

            // Get transfer details excluding the products in purchase details
            $data = TransferDetail::where('transfer_id', $transfer_id)
                ->where('requested_quantity', '>', 'delivered_quantity')
                ->whereNotIn('product_id', $excludeItems)
                ->get();
        }

        // Pass $transfer_id to the view
        $transfer = Transfer::findOrFail($transfer_id);
        $html = view('inventory.purchase._productTable', compact('data', 'transfer'));
        return $html;
    }

    public function getPurchasesForTransfer(Request $request)
    {
        $material_request_id = decrypt($request->material_request_id);

        $purchases = Purchase::where('transfer_id', $material_request_id)->get();

        return response()->json($purchases);
    }

    public function getProductsForPurchase(Request $request)
    {
        // Transfer::query()->update(['requestable_type' => Project::class]);
        $purchase_id = $request->purchase_id;

        $purchase = Purchase::findOrFail($purchase_id);
        $products = PurchaseDetail::where('purchase_id', $purchase_id)->get();
        $transfer_id = $purchase->transfer_id;

        $data = [];

        foreach ($products as $product)
        {
            $transferDetail = TransferDetail::where('product_id', $product->product_id)
                ->where('transfer_id', $transfer_id)
                ->first();
            
            $doQuantity = DeliveryOrder::where('product_id', $product->product_id)
                ->where('transfer_id', $transfer_id)
                ->where('purchase_id', $purchase_id)
                ->sum('quantity');
            
            if ($transferDetail->requested_quantity > $doQuantity)
            {
                $data[] = [
                    'id' => $product->product_id,
                    'name' => $product->product->name,
                    'delivered_quantity' => $doQuantity,
                    'requested_quantity' => $transferDetail->requested_quantity,
                    'balance' => $transferDetail->requested_quantity - $doQuantity,
                ];
            }
        }
        $material_request = Transfer::find($transfer_id);
        // Pass the products data to the Blade file

        return view('inventory.transfer.requests.getProductsForPurchase', compact('data', 'material_request'));
    }

    public function getTransferForPurchase(Request $request)
    {
        $purchaseId = $request->purchase_id;
        $purchase = Purchase::findOrFail($purchaseId);

        // Assuming Purchase has a relationship with Transfer
        $currentTransferId = $purchase->transfer_id;

        $purchases = Purchase::where('id', '!=', $purchase->id)
            ->where('transfer_id', '!=', $currentTransferId)
            ->get();

        $data = [];

        // Add the current transfer on top of the list
        $currentTransfer = Transfer::find($currentTransferId);
        if ($currentTransfer) {
            $data[] = [
                'id' => $currentTransfer->id,
                'transfer_no' => $currentTransfer->transfer_no,
            ];
        }

        foreach ($purchases as $row) {
            if ($row->transfer->status !== 'delivered') {
                $data[] = [
                    'id' => $row->transfer->id,
                    'transfer_no' => $row->transfer->transfer_no,
                ];
            }
        }

        return response()->json($data);
    }

    // public function getProductsForTransferRequest(Request $request)
    // {
    //     $transfer_id = decrypt($request->transfer_id);

    //     $purchaseIds = Purchase::where('transfer_id', $transfer_id)->pluck('id');

    //     if ($purchaseIds->isEmpty()) {
    //         $data = TransferDetail::where('transfer_id', $transfer_id)
    //             ->where('requested_quantity', '>', 'delivered_quantity')
    //             ->get();
    //     } else {
    //         $excludeItems = PurchaseDetail::whereIn('purchase_id', $purchaseIds)->pluck('product_id')->toArray();

    //         $data = TransferDetail::where('transfer_id', $transfer_id)
    //             ->where('requested_quantity', '>', 'delivered_quantity')
    //             ->whereNotIn('product_id', $excludeItems)
    //             ->get();
    //     }

    //     $transfer = Transfer::findOrFail($transfer_id);
    //     $html = view('inventory.purchase._productTable', compact('data', 'transfer'));
    //     return $html;
    // }
    
    public function getProductsForTransferRequest(Request $request)
    {
        $transfer_id = decrypt($request->transfer_id);
    
        $purchaseIds = Purchase::where('transfer_id', $transfer_id)->pluck('id');
    
        $excludeItems = [];
    
        if (!$purchaseIds->isEmpty()) {
            $purchaseDetails = PurchaseDetail::whereIn('purchase_id', $purchaseIds)->get();
    
            foreach ($purchaseDetails as $purchaseDetail) {
                $productId = $purchaseDetail->product_id;
                $quantity = $purchaseDetail->quantity;
    
                $transferDetail = TransferDetail::where('transfer_id', $transfer_id)
                                                 ->where('product_id', $productId)
                                                 ->first();
    
                if ($transferDetail) {
                    if ($transferDetail->requested_quantity <= $quantity) {
                        $excludeItems[] = $productId;
                    }
                }
            }
        }
    
        $data = TransferDetail::where('transfer_id', $transfer_id)
                              //->where('requested_quantity', '>', 'delivered_quantity')
                              ->whereNotIn('product_id', $excludeItems)
                              ->get();
    
        $transfer = Transfer::findOrFail($transfer_id);
    
        $html = view('inventory.purchase._productTable', compact('data', 'transfer'))->render();
        return $html;
    }    

    public function getProductsForTransfer(Request $request)
    {
        $transfer_id = $request->via_transfer_id;

        $transfer = Transfer::findOrFail($transfer_id);

        $data = [];
        $transferDetail = TransferDetail::where('transfer_id', $transfer_id)->whereColumn('requested_quantity', '>', 'delivered_quantity')->get();
        $brands = Brand::all();
        if(!empty($transferDetail) && count($transferDetail) > 0)
        {
            foreach ($transferDetail as $detail)
            {
                $stock = StockRecord::where('stockable_type', Warehouse::class)
                ->where('stockable_id', Warehouse::first()->id)
                ->where('product_id', $detail->product_id)
                ->first();

                $stockQuantity = $stock ? $stock->quantity : 0;
                //$unitName = $stock ? ($stock->unit ? $stock->unit->name : '') : '';

                $data[] = [
                    'id' => $detail->product_id,
                    'name' => $detail->product->name,
                    'type' => $detail->product->type,
                    'brand_id' => $detail->product->brand_id,
                    'delivered_quantity' => $detail->delivered_quantity,
                    'requested_quantity' => $detail->requested_quantity,
                    'stock' => $stockQuantity,
                    'balance' => $detail->requested_quantity - $detail->delivered_quantity,
                ];
            }
        }
        $transfers = Transfer::all();
        $html = view('inventory.transfer.requests.getProductsForTransfer', compact('data', 'transfer', 'transfers', 'brands'))->render();

        return response()->json(['html' => $html]);
    }

    public function getProductForTransfer(Request $request)
    {
        $data = [];
        $units = Unit::all();
        $html = view('inventory.transfer.requests.addProduct', compact('units'))->render();
        return response()->json(['html' => $html]);
    }

    public function getProductTable(Request $request)
    {
        $productIds = $request->input('products', []);
        $materialRequestId = decrypt($request->input('material_request_id'));

        $data = [];

        foreach ($productIds as $pid) {
            $product = Product::findOrFail($pid);
            // Check if there is a corresponding record in material_request_details
            $materialRequestDetail = TransferDetail::where('product_id', $pid)
                ->where('transfer_id', $materialRequestId)
                ->whereColumn('requested_quantity', '>', 'delivered_quantity')
                ->first();

            // If a matching record is found, add the product to the result
            if ($materialRequestDetail && $materialRequestDetail->requested_quantity > $materialRequestDetail->delivered_quantity) {
                $data[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'price' => $product->price,
                    'unit'  => $product->unit,
                    'delivered_quantity' => $materialRequestDetail->delivered_quantity,
                    'requested_quantity' => $materialRequestDetail->requested_quantity,
                    'balance' => $materialRequestDetail->requested_quantity - $materialRequestDetail->delivered_quantity,
                ];
            }
        }

        // If a matching record is found, add the product to the result

        $products = $data;

        $page = 'materialRequest._productTable';

        return view('inventory.' . $page, compact('products', 'materialRequestId'));
    }

    public function getProductTableForPurchases(Request $request)
    {

        $productIds = $request->input('products', []);
        $materialRequestId = decrypt($request->input('material_request_id'));

        $data = [];

        foreach ($productIds as $pid) {
            $product = Product::with('stockRecords')->findOrFail($pid);
            // Check if there is a corresponding record in material_request_details
            $materialRequestDetail = TransferDetail::where('product_id', $pid)
                ->where('transfer_id', $materialRequestId)
                ->first();

            // If a matching record is found, add the product to the result
            if ($materialRequestDetail) {
                $data[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'price' => $product->price,
                    'unit'  => $product->unit->short_name,
                    'stocks' => $product->stockRecords->sum('quantity'),
                    'unit_id' => $product->unit_id,
                    'requested_quantity' => $materialRequestDetail->requested_quantity,
                ];
            }
        }

        // If a matching record is found, add the product to the result

        $products = $data;

        $page = 'purchase._productTable';

        return view('inventory.' . $page, compact('products'));
    }

}
