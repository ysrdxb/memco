<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Store;
use App\Models\User;
use App\Models\ProjectUser;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\StoreMaterialIssued;
use App\Models\MaterialIssuedHistory;
use App\Models\StockRecord;
use App\Models\StockDetail;
use App\Models\StoreItemLimit;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderSerial;
use Auth;
use Illuminate\View\View;
use DataTables;
use App\Models\Role;
use App\Models\SubStore;
use App\Models\TransferReturn;
use Illuminate\Support\Facades\DB;
use App\Services\SwitchProject;

class ProjectController extends Controller
{
    protected $switchProject;

    public function __construct(SwitchProject $switchProject)
    {
        $this->switchProject = $switchProject;
    }
        
    public function index()
    {
        $stores = Project::all();
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Store Incharge');
        })->get();

        return view('inventory.store.list', compact('stores', 'users'));
    }

    public function store(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;
        $rules = [
            'name' => 'required|unique:projects,name,' . ($id ? $id : 'NULL') . ',id',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['address'] = $request->address;
        $validatedData['phone'] = $request->phone;
        $validatedData['email'] = $request->email;

        if ($request->has('user_id')) {
            $project = Project::updateOrCreate(['id' => $id], $validatedData);

            $userIds = $request->user_id ?? [];
            $project->users()->detach();
            foreach ($userIds as $userId) {
                $project->users()->attach($userId);
            }
        }


        $redirect = route('stores.list');

        return response()->json([
            'message' => 'Project saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);

    }

    public function returnables($id)
    {
        $id = decrypt($id);
        $store = Project::findOrFail($id);
        $data = TransferReturn::where('store_id', $store->id)->get();

        return view('inventory.store.returnables', compact('data'));
    }

    public function saveSubStore(Request $request, $id = NULL)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;
        $rules = [
            'name' => 'required|unique:sub_stores,name,' . ($id ? $id : 'NULL') . ',id',
            'store_id' => 'required|numeric',
         // 'detail' => ''
        ];

        $validatedData = $request->validate($rules);
        $validatedData['detail'] = $request->detail;

        $subStore = SubStore::updateOrCreate(['id' => $id], $validatedData);

        $redirect = route('stores.list');
        return response()->json([
            'message' => 'Sub Store saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }

    public function destroy($id)
    {
        $id = decrypt($id);

        if($store = Project::where('id', $id))
        {
            $store->delete();
            $redirect = route('stores.list');
            return response()->json(['message' => 'Store deleted successfully', 'status'=>'success', 'redirect' => $redirect], 200);
        }
        return response()->json([
            'message' => 'Error occured while deleting',
            'status'=>'error'
        ], 200);
    }

    public function show()
    {
        $store = Project::findOrFail($this->switchProject->getCurrentProjectId());
        $access = ProjectUser::where('store_id', $store->id)->where('user_id', Auth::user()->id)->first();
        if($access)
        {
            return view('inventory.dashboard');
        }
        else
        {
            exit('You dont have access to this store!!');
        }
    }

    public function updateStock()
    {
        $products = [];
        $transfers = Transfer::where('requestable_type', Project::class)
            ->where('requestable_id', 5)
            ->get();

        foreach ($transfers as $transfer) {
            $details = $transfer->details;

            $deliveryOrders = DeliveryOrder::where('transfer_id', $transfer->id)->get();
            foreach($deliveryOrders as $deliveryOrder) {
                $product_id = $deliveryOrder->product_id;
                $quantity = $deliveryOrder->quantity;
                $existingDetail = $details->where('product_id', $product_id)->first();
                if(!$existingDetail) {
                    TransferDetail::create([
                        'transfer_id' => $transfer->id,
                        'product_id' => $product_id,
                        'requested_quantity' => $quantity,
                        'delivered_quantity' => $quantity,
                        'unit_id' => Product::find($product_id)->unit_id,
                        'brand_id' => Product::find($product_id)->brand_id
                    ]);
                }
                // If product_id already exists in $products array, add quantity to existing quantity
                if (array_key_exists($product_id, $products)) {
                    $products[$product_id]['quantity'] += $quantity;
                } else {
                    // If product_id does not exist, create a new entry
                    $products[$product_id] = [
                        'product_id' => $product_id,
                        'quantity' => $quantity,
                        'project_id' => $transfer->requestable_id
                    ];
                }
            }
        }

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
    }

    public function stock($id)
    {
        $id = decrypt($id);

        $data = Project::findOrFail($id);

        return view('inventory.store.stock', compact('data'));
    }

    public function exportStock()
    {
        $file = 'stock-'.date('dmy').'-'.date('his');
        return Excel::download(new StockExport, $file.'.xlsx');
    }

    public function materialIssued()
    {

        $stockRecord = StockRecord::with(['product'])
            ->where('stockable_id', Auth::user()->stores->first()->id)
            ->where('stockable_type', Project::class)
            ->get();

        return view('inventory.store.material_issued', compact('stockRecord'));
    }


    public function getMaterialIssuedList(Request $request)
    {
        $storeId = $this->switchProject->getCurrentProjectId();
    
        $data = StoreMaterialIssued::with(['user', 'employee.position', 'store'])
            ->where('store_id', $storeId)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy('issue_order_no');
    
        $result = [];
        $counter = 1;
    
        foreach ($data as $issueOrderNo => $group) {
            foreach ($group as $item) {
                $result[$issueOrderNo] = [
                    'counter' => $counter++,
                    'id' => $item->id,
                    'issue_order_no' => $issueOrderNo,
                    'issued_by' => optional($item->user)->name,
                    'employee_code' => $item->employee->employee_no ?? '',
                    'employee_name' => optional($item->employee)->name ?? '',
                    'employee_department' => optional($item->employee->position)->name ?? '',
                    'store_name' => $item->store->name,
                    'date' => $item->updated_at->diffForHumans(),
                    'remarks' => $item->remarks ?? '',
                ];
            }
        }
    
        return DataTables::of($result)
            ->addColumn('action', function ($row) {
                $rowId = encrypt($row['id']);
                $printRoute = route('project.material_issued.print', $rowId);
                $updateRoute = route('project.material_issued.update', $rowId);
                $editRoute = route('project.material_issued.edit', $rowId);
                return '<a href="' . $printRoute . '" target="_blank" class="editBtn"><i class="ik ik-printer f-16 mr-15 text-green"></i></a>' .
                        '<a href="'.$editRoute.'" class="btn btn-sm btn-primary" style="font-size:12px;padding:4px 12px"><i class="fas fa-reply"></i> Return</a>' .
                        '<a href="#!" class="deleteBtn" item-id="' . $rowId . '"><i class="ik ik-trash f-16 ml-15 text-red"></i></a>';
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where('issue_order_no', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('employee', function($query) use ($searchValue) {
                            $query->where('name', 'like', '%' . $searchValue . '%')
                                ->orWhereHas('position', function($query) use ($searchValue) {
                                    $query->where('name', 'like', '%' . $searchValue . '%');
                                });
                        });
                }
            })
            ->make(true);
    }


    public function track_material_issued_records()
    {
        if (Auth::user()->project->first()) {
            $storeId = $this->switchProject->getCurrentProjectId();
            $materialIssued = StoreMaterialIssued::select('employee_id', DB::raw('MAX(id) as id'))
                ->where('store_id', $storeId)
                ->groupBy('employee_id')
                ->get();
        } else {
            $materialIssued = StoreMaterialIssued::all();
        }
        $records = null;
        return view('inventory.material_issued_tracking', compact('materialIssued', 'records'));
    }
    
    
    public function filter_material_issued(Request $request)
    {
        $records = null;
        if ($request->has('employeeId') && $request->employeeId > 0) {
            $records = StoreMaterialIssued::where('employee_id', $request->employeeId)->get();
        }
    
        if (Auth::user()->project->first()) {
            $storeId = $this->switchProject->getCurrentProjectId();
            $materialIssued = StoreMaterialIssued::select('employee_id', DB::raw('MAX(id) as id'))
                ->where('store_id', $storeId)
                ->groupBy('employee_id')
                ->get();
        } else {
            $materialIssued = StoreMaterialIssued::all();
        } 
            
        return view('inventory.material_issued_tracking', compact('records', 'materialIssued'));
    }

    public function printMaterialIssued($id)
    {
        $id = decrypt($id);

        $issueNo = StoreMaterialIssued::where('id', $id)->first()->issue_order_no;

        $data = StoreMaterialIssued::where('issue_order_no', $issueNo)->get();

        return view('invoice.storeMaterialIssued', compact('data'));
    }

    public function deleteMaterialIssued($id)
    {
        try {
            $id = decrypt($id);
            $materialIssued = StoreMaterialIssued::findOrFail($id);
    
            $data = StoreMaterialIssued::where('issue_order_no', $materialIssued->issue_order_no)->get();
    
            foreach($data as $item) {
    
                $store_id = $materialIssued->store_id;
                $product_id = $item->product_id;
    
                $stock = StockRecord::where('stockable_id', $store_id)
                    ->where('stockable_type', Project::class)
                    ->where('product_id', $product_id)
                    ->firstOrFail();
    
                // Increment the stock quantity
                $stock->quantity += $item->quantity;
                $stock->save();
    
                // Delete the material issued record
                StoreMaterialIssued::where('id', $item->id)->delete();
            }
    
            $redirect = route('stores.materialIssued');
    
            return response()->json([
                'message' => 'Material Issued deleted successfully',
                'status' => 'success',
                'redirect' => $redirect
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error',
                'error' => $e->getMessage()
            ], 200);
        }
    }



    public function createMaterialIssued()
    {
        // $stockRecord = StockRecord::with(['product'])
        //     ->where('stockable_id', Auth::user()->stores->first()->id)
        //     ->where('stockable_type', Project::class)
        //     ->get();
        $units = Unit::all();
        $brands = Brand::all();
        return view('inventory.store.create_material_issued', compact('units','brands'));
    }

    public function getStockRecord(Request $request)
    {
        $term = $request->input('term');

        $products = StockRecord::with('product')
            ->where('stockable_id', Auth::user()->stores->first()->id)
            ->where('stockable_type', Project::class)
            ->whereHas('product', function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%');
            })
            ->get();

        $formattedProducts = $products->map(function ($product) {
            return ['id' => $product->product_id, 'text' => $product->product->name];
        });

        return response()->json($formattedProducts);
    }

    public function _getProducts(Request $request): mixed
    {
        $productIds = $request->input('products', []);

        $products = Product::with(['stockRecords' => function ($query) {
                $query->where('stockable_id', Auth::user()->stores->first()->id)
                    ->where('stockable_type', Project::class);
            }])
            ->whereIn('id', $productIds)
            ->get();

        $brands = Brand::all();

        return view('inventory.store._get_material_issued_products', compact('products', 'brands'));
    }

    public function editMaterialIssued($id)
    {
      $id = decrypt($id);
      $data = StoreMaterialIssued::findOrFail($id);
      $materialissued = StoreMaterialIssued::where('issue_order_no', $data->issue_order_no)->get();
      return view('inventory.store.edit_material_issued', compact('materialissued', 'data'));
    }
    
    public function updateMaterialIssued(Request $request, $id)
    {
        $id = decrypt($id);
    
        $issued = StoreMaterialIssued::findOrFail($id);
        foreach ($request->product_id as $index => $pid) {
            $issuedData = StoreMaterialIssued::where('issue_order_no', $issued->issue_order_no)
                ->where('product_id', $pid)
                ->first();
    
            if ($issuedData && $issuedData->quantity > 0) {
                $issuedData->update([
                    'remarks' => $request->remarks,
                    'quantity' => $issuedData->quantity - $request->return_quantity[$index],
                ]);
    
                MaterialIssuedHistory::create([
                    'issued_id' => $issuedData->id,
                    'product_id' => $pid,
                    'quantity' => $request->return_quantity[$index],
                ]);
    
                $stock = StockRecord::where('product_id', $pid)
                    ->where('stockable_id', $issued->store_id)
                    ->where('stockable_type', Project::class)
                    ->first();
    
                if ($stock) {
                    $stock->quantity += $request->return_quantity[$index];
                    $stock->save();
                }
            } else {
                return redirect()->route('stores.materialIssued')->with('error', 'Material issue record not found.');
            }
        }
    
        return redirect()->route('stores.materialIssued')->with('success', 'Material issued updated successfully.');
    }
    


    public function saveMaterialIssued(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;
        $rules = [
            'user_id' => 'required|string',
            'product_id' => 'required|array',
            'quantity' => 'required|array',
            'document' => 'nullable|image|max:1024',
        ];
        $validatedData = $request->validate($rules);
        $errors = [];
        DB::beginTransaction();

        try {
            $details = [];
            $products = count($request->product_id);//Product::whereIn('id', $request->product_id)->get();
            $issue_order_no = Auth::user()->project->first()->code . '-' . str_pad($this->generateIssueOrderNo(), 4, '0', STR_PAD_LEFT);
            // Process file upload
            $filePath = '';
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                if ($file->isValid()) {
                    $fileName = 'issue_voucher_no_' . $issue_order_no . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('issue_vouchers/' . Auth::user()->project->first()->code . '/', $fileName, 'public');
                }
            }

            for ($i=0; $i < $products; $i++) {
                $product = Product::find($request->product_id[$i]);
                $stockRecord = StockRecord::where('stockable_id', $this->switchProject->getCurrentProjectId())
                    ->where('stockable_type', Project::class)
                    ->where('product_id', $product->id)
                    ->first();

                if ($stockRecord && $stockRecord->quantity >= $request->quantity[$i]) {

                    $details[] = [
                        'product_id' => $product->id,
                        'brand_id' => $request->brand_id[$i],
                        'quantity' => $request->quantity[$i],
                        'user_id' => Auth::user()->id,
                        'store_id' => $this->switchProject->getCurrentProjectId(),
                        'employee_id' => $request->user_id,
                        'address' => $request->address,
                        'remarks' => $request->remarks,
                        'file_path' => $filePath,
                        'issue_order_no' => $issue_order_no,
                    ];
                    $stockRecord->quantity = $stockRecord->quantity - $request->quantity[$i];
                    $stockRecord->save();

                } else {
                    $errors[] = 'Sorry, the current stock quantity exceeds the issuing quantity for ' . $product->name;
                }
            }

            if (!empty($errors)) {
                DB::rollback();
                return response()->json([
                    'message' => $errors,
                    'status' => 'error',
                ]);
            }


            StoreMaterialIssued::insert($details);

            DB::commit();
            $redirect = route('stores.materialIssued');
            return response()->json([
                'message' => 'Material Issued saved successfully',
                'status' => 'success',
                'redirect' => $redirect
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while processing the request: ' . $e->getMessage(),
                'status' => 'error',
            ]);
        }
    }

    private function generateIssueOrderNo()
    {
        $latestOrder = StoreMaterialIssued::latest()->first();
        $issueOrderNo = 1; // Default value if no previous order exists

        if ($latestOrder) {

            $latestIssueOrderNo = $latestOrder->issue_order_no;
            $latestOrderNumber = intval(substr($latestIssueOrderNo, strrpos($latestIssueOrderNo, '-') + 1));
            $issueOrderNo = $latestOrderNumber + 1;
        }

        // Format the issue order number with leading zeros
        return sprintf('%04d', $issueOrderNo);
    }

    public function createItemsList($store_id)
    {
        $store_id = decrypt($store_id);
        $store = Project::findOrFail($store_id);
        return view('inventory.store.create_item_list', compact('store'));
    }

    public function storeItemsList($store_id)
    {
        $store_id = decrypt($store_id);

        $store = Project::findOrFail($store_id);

        $items = StoreItemLimit::with(['product', 'store', 'user'])
            ->where('store_id', $store_id)
            ->get();

        return view('inventory.store.item_list', compact('items', 'store'));
    }

    public function saveStoreItems(Request $request)
    {
        $store_id = decrypt($request->store_id);

        $rules = [
            'store_id' => 'required',
            'product_id' => 'required|array'
        ];

        $validatedData = $request->validate($rules);

        $store = Project::findOrFail($store_id);

        $product_ids = $request->product_id;
        foreach ($product_ids as $pid) {
            StoreItemLimit::updateOrCreate(
                [
                    'store_id' => $store->id,
                    'product_id' => $pid,
                ],
                [
                    'brand_id' => $request->brand_id[$pid],
                    'unit_id' => $request->unit_id[$pid],
                    'max_quantity' => $request->quantities[$pid],
                    'user_id' => Auth::user()->id
                ],
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Store limit saved successfully!!',
            'redirect' => route('stores.items.list', encrypt($store->id))
        ]);
    }

    public function deleteItem($id)
    {
        $id = decrypt($id);

        if($item = StoreItemLimit::where('id', $id))
        {
            $item = StoreItemLimit::findOrFail($id);
            $store_id = encrypt($item->store_id);
            $item->delete();
            $redirect = route('stores.items.list', $store_id);
            return response()->json([
                'message' => 'Item deleted successfully',
                'status' => 'success',
                'redirect' => $redirect
            ], 200);
        }
        return response()->json([
            'message' => 'Error occured while deleting',
            'status'=>'error'
        ], 200);
    }

    public function getSubStore($store_id)
    {
        $subStores = SubStore::where('store_id', $store_id)->get();
        return view('inventory.store.get_sub_stores', compact('subStores'));
    }

    public function storeProducts()
    {
        $activities = Category::all();
        $subactivities = SubCategory::all();
        return view('inventory.store.productsList', compact('activities','subactivities'));
    }


    public function getProductsList(Request $request): mixed
    {
        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
            ->leftJoin('stock_records', 'stock_records.product_id', '=', 'products.id')
            ->select([
                'products.id',
                'products.name',
                'products.code',
                'categories.name as category_name',
                'subcategories.name as subcategory_name',
                'units.name as unit_name',
                'products.subcategory_id as subcategory',
                'brands.name as brand_name',
                'products.price',
                'stock_records.quantity as total_quantity',
            ])
            ->orderByDesc('total_quantity');

        if ($request->has('search') && !empty($request->input('search')['value'])) {
            $searchValue = $request->input('search')['value'];
            $products->where(function ($query) use ($searchValue) {
                $query->where('products.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('products.code', 'like', '%' . $searchValue . '%')
                    ->orWhere('products.subcategory_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('categories.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('subcategories.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('brands.name', 'like', '%' . $searchValue . '%');
            });
        }

        // if ($request->filled('category_name')) {
        //     $categoryName = $request->input('category_name');
        //     $products->where('categories.name', 'like', '%' . strtolower($categoryName) . '%');
        // }

        return DataTables::of($products)
            ->addColumn('category', function ($product) {
                return $product->category_name;
            })
            ->addColumn('brand', function ($product) {
                return $product->brand_name;
            })
            ->addColumn('subcategory', function ($product) {
                return $product->subcategory_name;
            })
            ->addColumn('uom', function ($product) {
                return $product->unit_name;
            })
            ->addColumn('price', function ($product) {
                return number_format($product->price);
            })
            ->addColumn('quantity', function ($product) {
                $stockRecord = StockRecord::where('stockable_type', Project::class)
                    ->where('stockable_id', Auth::user()->stores->first()->id)
                    ->where('product_id', $product->id)
                    ->first();

                return $stockRecord && ($stockRecord->quantity === 0 || $stockRecord->quantity === null)
                    ? '<span class="text-danger">Out of stock</span>'
                    : ($stockRecord ? '<span class="text-success">' . number_format($stockRecord->quantity) . '</span>' : '<span class="text-danger">Out of Stock</span>');

            })
            ->addColumn('action', function ($product) {
                $editUrl = route('products.edit', encrypt($product->id));
                $deleteId = encrypt($product->id);
                $stockRecord = StockRecord::where('stockable_type', Project::class)
                    ->where('stockable_id', Auth::user()->stores->first()->id)
                    ->where('product_id', $product->id)
                    ->first();

                // If $stockRecord is not null (i.e., it exists), use its quantity; otherwise, use 0.
                $stock_quantity = $stockRecord !== null && $stockRecord->quantity !== null ? $stockRecord->quantity : 0;

                return '<a data-product-name="' . $product->name . '" data-pquantity="' . $stock_quantity . '" data-pprice="' . $product->price . '" data-product-id="' . $product->id . '" href="' . $editUrl . '" class="mr-15 btn btn-primary add-stock-link" style="padding:1px 15px;font-size:14px" data-toggle="modal" data-target="#categoryAdd">Update Stock</a>';

            })
            ->rawColumns(['action', 'quantity'])
            ->make(true);
    }

    public function showUpdateForm($id)
    {
        $product = Product::findOrFail($id);
        $stockRecord = StockRecord::where('product_id', $id)
            ->where('stockable_id', Auth::user()->stores->first()->id)
            ->where('stockable_type', Project::class)
            ->first();
        return view('inventory.store._updateStock', compact('product', 'stockRecord'));
    }

    public function updateProductStock(Request $request, $store_id)
    {
        $store_id = decrypt($store_id);
        $rules = [
            //'quantity' => 'required|numeric',
            'product_id' => 'required|numeric'
        ];

        $request->validate($rules);

        $product = Product::findOrFail($request->product_id);

        if(!empty($request->quantity) || $request->quantity == 0)
        {
            $quantity = $request->quantity;
            $product_id = $request->product_id;

            $stock = StockRecord::where('stockable_id', $store_id)
                ->where('stockable_type', Project::class)
                ->where('product_id', $product_id)
                ->first();

            if($stock)
            {
                // Stock record exists, update quantity
                if($request->type && $request->type == 'minus')
                {
                    if($stock->quantity >= $quantity)
                    {
                        $stock->quantity -= $quantity;
                    }
                }
                else
                {
                    $stock->quantity += $quantity;
                }

                if(!empty($request->delivery_order_no))
                {
                    $stock->delivery_order_no = $request->delivery_order_no;
                }
                if(!empty($request->material_request_no))
                {
                    $stock->material_request_no = $request->material_request_no;
                }
                if(!empty($request->remarks))
                {
                    $stock->remarks = $request->remarks;
                }

                $stock->save();

                // $stock = StockRecord::where('stockable_id', $store_id)
                //     ->where('stockable_type', Project::class)
                //     ->where('product_id', $product_id)
                //     ->first();
                // if($stock)
                // {
                //     if($stock->quantity == 0 || $stock->quantity == null)
                //     {
                //         $stock->delete();
                //         $redirect = route('stores.storeProducts');
                //         return response()->json([
                //             'message' => 'Stock deleted successfully',
                //             'status' => 'success',
                //             'redirect' => $redirect
                //         ], 200);
                //     }
                // }
            }
            else
            {
                // Stock record does not exist, create new record
                $stock = new StockRecord();
                $stock->stockable_id = $store_id;
                $stock->stockable_type = Project::class;
                $stock->product_id = $product_id;
                $stock->quantity = $quantity;

                if(!empty($request->delivery_order_no))
                {
                    $stock->delivery_order_no = $request->delivery_order_no;
                }
                if(!empty($request->material_request_no))
                {
                    $stock->material_request_no = $request->material_request_no;
                }
                if(!empty($request->remarks))
                {
                    $stock->remarks = $request->remarks;
                }

                $stock->save();
            }
        }

        $redirect = route('stores.storeProducts');
        return response()->json([
            'message' => 'Stock quantity saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ], 200);
    }

    public function getStoreIncharges(Request $request)
    {
        $project = Project::findOrFail(decrypt($request->id));
        $incharges = $project->users->pluck('id')->toArray();

        return response()->json(['incharges' => $incharges]);
    }

}
