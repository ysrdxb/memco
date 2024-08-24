<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Store;
use App\Models\Unit;
use App\Models\StockRecord;
use App\Models\Project;
use App\Models\DeliveryOrder;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\StoreMaterialIssued;
use App\Models\WarehouseMaterialIssued;
use App\Models\Brand;
use App\Models\StockDetail;
use App\Models\DeliveryOrderSerial;
use Illuminate\Support\Facades\DB; 
use App\Services\SwitchProject;

use DataTables;
use Auth;

class StockController extends Controller
{
    protected $switchProject;

    public function __construct(SwitchProject $switchProject)
    {
        $this->switchProject = $switchProject;
    }
        
    public function index()
    {
        $stocks = StockRecord::paginate(12);
    }

    public function getList(Request $request, $id, $type): mixed
    {
        $data = StockRecord::with(['product'])
            ->where('stockable_id', decrypt($id))
            ->where('stockable_type', Warehouse::class)
            ->whereHas('product', function ($query) use ($type) {
                $query->where('type', $type);
            });
    
        // Apply any additional filters before fetching the data
        if ($request->has('search') && !empty($request->input('search')['value'])) {
            $searchValue = $request->input('search')['value'];
            $data->whereHas('product', function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('code', 'like', '%' . $searchValue . '%');
            });
        }
    
        $data = $data->get();
        $counter = 1;
        return DataTables::of($data)
            ->addColumn('id', function ($row) use(&$counter) {
                return $counter++;
            })        
            ->addColumn('product_name', function ($row) {
                return $row->product->name;
            })
            ->addColumn('product_code', function ($row) {
                return $row->product->code;
            })
            ->addColumn('price', function ($row) {
                return $row->product->price;
            })
            ->addColumn('quantity', function ($row) {
                if ($row->quantity < 1) {
                    return 'Out of Stock';
                } else {
                    return '' . $row->quantity;
                }
            })->rawColumns(['quantity'])
            ->addColumn('unit', function ($row) {
                return $row->product->unit ? $row->product->unit->short_name : '';
            })
    
            ->addColumn('stock_amount', function ($row) {
                return $row->price * $row->quantity;
            })
            ->addColumn('date', function ($row) {
                return $row->updated_at;
            })
            ->addColumn('action', function ($row) use ($type) {
                if ($type == 'tool') {
                    $detail = route('stock.detail', encrypt($row->id));
                    $encryptedId = encrypt($row->id);
                    $html ='<a href="' . $detail . '" class="mr-15 btn btn-warning add-stock-link" style="padding:1px 15px;"><i class="fas fa-barcode"></i> SERIALS</a>';
                    $html .='<a class="mr-15 btn btn-primary" href="'.route('tools.transfer.history', $encryptedId).'">TRANSFER HISTORY</a>';
                    return $html;
                } else {
                    return NULL;
                }
            })
            ->rawColumns(['date', 'action'])
            ->make(true);
    }
    
    public function transfer_history($id) 
    {
        $id = decrypt($id);
        $stockDetailIds = DeliveryOrderSerial::whereHas('stockDetail', function ($query) use ($id) {
            $query->where('stock_record_id', $id);
        })->pluck('stock_detail_id')->toArray();
    
        $stockDetail = StockDetail::with(['deliveryOrderSerials.deliveryOrder.transfer.project', 'product'])
                          ->whereIn('id', $stockDetailIds)
                          ->get();
        return view('inventory.warehouse.tools_history', compact('stockDetail'));
    }

    public function stockDetail($id)
    {
        $id = $id ? decrypt($id) : null;
        $stock = StockRecord::findOrFail($id);
        $stockDetails = StockDetail::where('stock_record_id', $stock->id)->get();
        if($stock && $stockDetails->isEmpty()){
            for($i=1; $i<=$stock->quantity;$i++){
                StockDetail::insert([
                    'product_id' => $stock->product_id,
                    'stock_record_id' => $stock->id,
                    'serial_no' => $i,
                    'quantity' => 1,
                    'model_no' => 0,
                    'warranty' => 2,
                    'purchase_date' => date('Y-m-d'),
                    'product_condition' => 'new',
                    'brand_id' => Product::find($stock->product_id)->brand->id,
                ]);
            }
            $stockDetails = StockDetail::where('stock_record_id', $stock->id)->get();
        }
        $brands = Brand::all();
        return view('inventory.warehouse.stock_detail', compact('stock', 'brands', 'stockDetails'));
    }

    public function storeStocksList(Request $request, $id): mixed
    {
        $id = decrypt($id);

        // $data = StockRecord::with(['product'])
        //     ->where('stockable_id', $id)
        //     ->where('stockable_type', Project::class)
        //     ->get();

        // foreach ($data as $row) {
        //     $stock = StockRecord::where('stockable_type', Project::class)
        //         ->where('stockable_id', $id)
        //         ->where('product_id', $row->product_id)
        //         ->first();

        //     $transferDetails = TransferDetail::whereIn('transfer_id', function ($query) use ($id) {
        //         $query->select('id')
        //             ->from('transfers')
        //             ->where('requestable_type', Project::class)
        //             ->where('requestable_id', $id);
        //     })
        //         ->where('product_id', $row->product_id)
        //         ->get();

        //     if ($transferDetails->isEmpty()) {
        //         if ($stock) {
        //            // $stock->delete();
        //         }
        //     } else {
        //         foreach ($transferDetails as $detail) {
        //             $deliveryOrder = DeliveryOrder::where('transfer_id', $detail->transfer_id)
        //                 ->where('product_id', $detail->product_id)
        //                 ->first();
        //             if ($deliveryOrder && $detail->delivered_quantity < $deliveryOrder->quantity) {
        //                 $transfer_detail = TransferDetail::where('product_id', $detail->product_id)
        //                     ->where('transfer_id', $detail->transfer_id)
        //                     ->first();
        //                 if ($transfer_detail) {
        //                     $deliveredQuantity = $deliveryOrder->quantity;
        //                     $transfer_detail->delivered_quantity = $deliveredQuantity;
        //                     $transfer_detail->save();
        //                 }
        //             }
        //         }

        //         $totalDeliveredQuantity = $transferDetails->sum('delivered_quantity');
        //         if ($stock) {
        //             if ($totalDeliveredQuantity != $stock->quantity) {
        //                 if ($totalDeliveredQuantity <= 0) {
        //                     $stock->delete();
        //                 } else {
        //                     $stock->quantity = $totalDeliveredQuantity;
        //                     $stock->remarks = 'Updated with the sum of delivered quantity and delivery order quantity.';
        //                     $stock->save();
        //                 }
        //             }
        //         } else {
        //             $stock = new StockRecord();
        //             $stock->stockable_type = Project::class;
        //             $stock->stockable_id = $id;
        //             $stock->product_id = $row->product_id;
        //             $stock->quantity = $totalDeliveredQuantity;
        //             $stock->remarks = 'Initial stock created with the sum of delivered quantity from transfer details.';
        //             $stock->save();
        //         }
        //     }
        //     if($stock->quantity == 0 || $stock->quantity =='') {
        //         // $stock->delete();
        //        // $stock = null;
        //     }
        // }

        $data = StockRecord::with(['product'])
            ->where('stockable_id', $id)
            ->where('stockable_type', Project::class)
            ->get();

        return DataTables::of($data)
            ->addColumn('product_name', function ($row) {
                return $row->product->name;
            })
            ->addColumn('product_code', function ($row) {
                return $row->product->code;
            })
            ->addColumn('price', function ($row) {
                return $row->product->price;
            })
            ->addColumn('quantity', function ($row) {
                if ($row->quantity <= 0) {
                    return 'Out of Stock';
                } else {
                    return '' . $row->quantity;
                }
            })->rawColumns(['quantity'])
            ->addColumn('unit', function ($row) {
                return $row->product->unit ? $row->product->unit->short_name : '';
            })->addColumn('stock_amount', function ($row) {
                return $row->price * $row->quantity;
            })
            ->addColumn('date', function ($row) {
                return $row->updated_at;
            })
            ->addColumn('action', function ($row) use ($id) {
                $route = route('project.stock.history', [encrypt($id), encrypt($row->product->id)]);
                return '<a href="' . $route . '" class="mr-15 btn btn-primary" style="padding:1px 15px;">Stock History</a>';
            })
            ->rawColumns(['action', 'date'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->filterColumn('product_name', function($query, $keyword) {
                        $query->where('products.name', 'like', '%' . $keyword . '%');
                    });
                    $query->filterColumn('product_code', function($query, $keyword) {
                        $query->where('products.code', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->make(true);
    }

    public function getProjectProductStock($projectId, $productId)
    {
        $projectId = decrypt($projectId);
        $productId = decrypt($productId);
        $product = Product::findOrFail($productId);
        $project = Project::findOrFail($projectId);

        $transferDetails = TransferDetail::whereIn('transfer_id', function ($query) use ($projectId) {
            $query->select('id')
                ->from('transfers')
                ->where('requestable_type', Project::class)
                ->where('requestable_id', $projectId);
        })
            ->where('product_id', $productId)
            ->get();

        $stock = StockRecord::where('stockable_type', Project::class)
            ->where('stockable_id', $projectId)
            ->where('product_id', $productId)
            ->first();

        $stockDetails = collect();
        foreach ($transferDetails as $detail) {
            $stockDetails->push([
                'transfer_id' => $detail->transfer_id,
                'requested_quantity' => $detail->requested_quantity,
                'delivered_quantity' => $detail->delivered_quantity,
                'product_id' => $detail->product_id,
                'unit' => $detail->unit->name,
                'date' => $detail->updated_at,
                'transfer_no' => Transfer::find($detail->transfer_id)->transfer_no
            ]);
        }

        $materialIssued = StoreMaterialIssued::where('store_id', $projectId)
            ->where('product_id', $productId)
            ->get();

        return view('inventory.store.stock_history', compact('stockDetails', 'product', 'project', 'stock', 'materialIssued'));
    }

    public function saveDetail(Request $request, $id)
    {
        $id = $id ? decrypt($id) : null;
        $stock = StockRecord::findOrFail($id);
        $product_id = $stock->product_id;
        $passkey = $request->passkey;
    
        if (!$request->has('passkey') || $passkey !== env('PASSKEY') || empty($request->passkey)) {
            return response()->json([
                'message' => 'Pass Key entered is Wrong!',
                'status' => 'error',
                'redirect' => ''
            ]);
        }
        
        try {
            DB::beginTransaction();
    
            $requestedSerialCount = count($request->serial_no);
            $stockQuantity = $stock->quantity;
    
            for ($index = 0; $index < $requestedSerialCount; $index++) {
                $serial_no = $request->serial_no[$index] ?? null;
                $quantity = isset($request->quantity[$index]) && $request->quantity[$index] > 0 ? 1 : 0;
                $model_no = $request->model_no[$index] ?? null;
                $warranty = $request->warranty[$index] ?? null;
                $brand_id = $request->brand_id[$index] ?? null;
                $purchase_date = $request->purchase_date[$index] ?? null;
                $product_condition = $request->product_condition[$index] ?? null;
    
                $stockDetail = isset($stock->detail[$index]) ? $stock->detail[$index] : null;
    
                if ($stockDetail) {
                    $stockDetail->update([
                        'quantity' => $quantity,
                        'model_no' => $model_no,
                        'warranty' => $warranty,
                        'brand_id' => $brand_id,
                        'serial_no' => $serial_no,
                        'purchase_date' => $purchase_date,
                        'product_condition' => $product_condition,
                    ]);
                } else {
                    StockDetail::create([
                        'stock_record_id' => $stock->id,
                        'product_id' => $product_id,
                        'serial_no' => $serial_no,
                        'model_no' => $model_no,
                        'warranty' => $warranty,
                        'brand_id' => $brand_id,
                        'quantity' => $quantity,
                        'purchase_date' => $purchase_date,
                        'product_condition' => $product_condition,
                    ]);
                }
            }
    
            DB::commit();
    
            $redirect = route('stock.detail', encrypt($stock->id));
            return response()->json([
                'message' => 'Data saved successfully',
                'status' => 'success',
                'redirect' => $redirect
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to save data: ' . $e->getMessage(),
                'status' => 'error',
                'redirect' => ''
            ]);
        }
    }
       
    public function balanceStockRecordWithStoreMaterialIssued()
    {
        DB::table('stock_records as sr')
        ->join(DB::raw('(SELECT store_id, product_id, SUM(quantity) AS total_quantity
                        FROM store_material_issued
                        GROUP BY store_id, product_id) smi'), function ($join) {
                            $join->on('sr.stockable_id', '=', 'smi.store_id')
                                ->on('sr.product_id', '=', 'smi.product_id');
                        })
        ->update(['sr.quantity' => DB::raw('sr.quantity - smi.total_quantity')]);        
    }

    public function getCurrentStock(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole(['Admin', 'Super Admin']) || $user->warehouse->isNotEmpty()) {
            $projectId = Warehouse::first()->id;
            $stockRecord = StockRecord::where('stockable_id', $projectId)
                ->where('stockable_type', Warehouse::class)
                ->where('product_id', $request->product_id)
                ->first();
            $stock = $stockRecord ? $stockRecord->quantity : 0;
            return response()->json(['current_stock' => $stock]);
        } else {
            $projectId = Auth::user()->project ? $this->switchProject->getCurrentProjectId() : 0;
            $stockRecord = StockRecord::where('stockable_id', $projectId)
                ->where('stockable_type', Project::class)
                ->where('product_id', $request->product_id)
                ->first();
            $stock = $stockRecord ? $stockRecord->quantity : 0;
            return response()->json(['current_stock' => $stock]);
        }
    }

    public function getCurrentStockWarehouse(Request $request)
    {
        $projectId = Warehouse::first()->id;
        $stockRecord = StockRecord::where('stockable_id', $projectId)
            ->where('stockable_type', Warehouse::class)
            ->where('product_id', $request->product_id)
            ->first();
        $stock = $stockRecord ? $stockRecord->quantity : 0;
        return response()->json(['current_stock' => $stock]);
    }

    public function fetchToolSerials(Request $request)
    {
        $productId = $request->input('product_id');
        $stock = StockRecord::where('stockable_type', Warehouse::class)->where('product_id', $productId)->first();
        $stockRecord = StockRecord::where('stockable_type', Warehouse::class)
            ->whereHas('stockDetails.product', function ($query) use ($productId) {
                $query->where('id', $productId);
            })->first();

        if($stock && $stock->quantity <=0) {
            return response()->json(['status' => 'error', 'message' => 'Product stock insufficient']);
        }
        elseif (!$stockRecord) {
            return response()->json(['status' => 'error', 'message' => 'Empty Stock Serials or Not found']);
        }

        $toolSerials = $stockRecord->stockDetails
            ->where('quantity', '>', 0) // Filter out stock details with quantity <= 0
            ->pluck('serial_no', 'id')
            ->toArray();

        return response()->json(['status' => 'success', 'serials' => $toolSerials]);
    }

}
