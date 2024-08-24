<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseUser;
use App\Models\User;
use App\Models\MaterialRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\StockRecord;
use App\Exports\StockExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use App\Models\Role;
use App\Models\WarehouseMaterialIssued;
use Illuminate\View\View;
use DataTables;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::paginate(12);
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Warehouse Incharge');
        })->get();
        return view('inventory.warehouse.list', compact('warehouses', 'users'));
    }
    
    public function store(Request $request, $id = NULL)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;      
        $rules = [
            'name' => 'required|unique:warehouses,name,' . ($id ? $id : 'NULL') . ',id',
            'address' => 'required|max:255'
        ];
    
        $validatedData = $request->validate($rules);
    
        $warehouse = Warehouse::updateOrCreate(['id' => $id], $validatedData);
        
        if ($request->has('user_id')) {
            $warehouse->users()->sync($request->user_id);
        }
        
        $redirect = route('warehouses.list');
        return response()->json([
            'message' => 'Warehouse saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);       
    }   

    public function destroy($id)
    {
        $id = decrypt($id);

        if($warehouse = Warehouse::where('id', $id))
        {
            $warehouse->delete();
            $redirect = route('warehouses.list');
            return response()->json([
                'message' => 'Warehouse deleted successfully', 
                'status'=> 'success', 
                'redirect' => $redirect
            ], 200);    
        }
        
        return response()->json([
            'message' => 'Error occured while deleting', 
            'status'=>'error'
        ], 200);    
    }     

    public function stock($id, $type='product')
    {
        $id = decrypt($id);

        $warehouse = Warehouse::findOrFail($id);
        
        return view('inventory.warehouse.stock', compact('warehouse','type'));
    }

    public function stockTools($id)
    {
        $id = decrypt($id);

        $warehouse = Warehouse::findOrFail($id);

        // $stockRecords = StockRecord::with('product', 'stockable', 'stockable.purchases.supplier')
        //     ->where('stockable_id', $id)
        //     ->where('stockable_type', Warehouse::class)
        //     //->groupBy('purchases.warehouse_id')
        //     ->paginate(24);
        $type = 'tool';
        return view('inventory.warehouse.stock', compact('warehouse', 'type'));
    }    

    public function exportStock()
    {
        $file = 'stock-'.date('dmy').'-'.date('his');
       
        return Excel::download(new StockExport, $file.'.xlsx');
    }    
    
    public function materialIssued()
    {
        $warehouseId = Auth::user()->warehouses->first()?->id ?? Warehouse::first()->id;

        $stockRecord = StockRecord::with(['product'])
            ->where('stockable_id', $warehouseId)
            ->where('stockable_type', Warehouse::class)
            ->get();        
        return view('inventory.warehouse.material_issued', compact('stockRecord'));
    } 
    
    public function getMaterialIssuedList(Request $request)
    {
        $warehouseId = Auth::user()->warehouses->first()?->id ?? Warehouse::first()->id;
        $data = WarehouseMaterialIssued::where('warehouse_id', $warehouseId)->get();

        return DataTables::of($data)
            ->addColumn('product_code', function ($row) {
                return $row->product->code;
            })
            ->addColumn('quantity', function ($row) {
                return $row->quantity;
            })
            ->addColumn('current_stock', function ($row) {
                $stockRecord = StockRecord::where('stockable_id', $warehouseIdd)
                ->where('stockable_type', Warehouse::class)
                ->where('product_id', $row->product->id)
                ->first();
                return $stockRecord ? $stockRecord->quantity : 0;
            })            
            ->addColumn('updated_date', function ($row) {
                return $row->updated_at;
            })  
            ->addColumn('product_name', function ($row) {
                return $row->product->name;
            })       
            ->addColumn('worker_name', function ($row) {
                return $row->worker_name;
            })                          
            ->addColumn('worker_department', function ($row) {
                return $row->worker_department;
            })       
            ->addColumn('store_name', function ($row) {
                return $row->store->name;
            })                         
            ->addColumn('action', function ($row) {
                $deleteId = encrypt($row->id);
                
                return '<a href="#!" class="deleteBtn" item-id="' . $deleteId . '"><i class="ik ik-trash f-16 ml-15 text-red"></i></a>';
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('products.name', 'like', '%' . $searchValue . '%')
                            ->orWhere('products.code', 'like', '%' . $searchValue . '%')
                            ->orWhere('products.subcategory_id', 'like', '%' . $searchValue . '%')
                            ->orWhere('categories.name', 'like', '%' . $searchValue . '%')
                            ->orWhere('brands.name', 'like', '%' . $searchValue . '%');
                    });
                }
            })            
            ->make(true);        
    }
    
    public function deleteMaterialIssued($id)
    {
        $id = decrypt($id);

        if($data = WarehouseMaterialIssued::where('id', $id)->first())
        {
            $warehouse_id = $data->warehouse_id;
            $product_id = $data->product_id;
            $user_id = $data->user_id;
            if(Auth::user()->id == $user_id)
            {
                $stock = StockRecord::where('stockable_id', $warehouse_id)
                    ->where('stockable_type', Warehouse::class)
                    ->where('product_id', $product_id)
                    ->first();
                if($stock)
                {
                    $stock->quantity = $stock->quantity + $data->quantity;
                    $stock->save();
                }
                $data->delete();
                $redirect = route('warehouse.materialIssued');
                return response()->json(['message' => 'Material Issued deleted successfully', 'status'=>'success', 'redirect' => $redirect], 200);    
            }
        }
        return response()->json([
            'message' => 'Error occured while deleting', 
            'status'=>'error'
        ], 200);         
    }
    public function createMaterialIssued()
    {
        $warehouseId = Auth::user()->warehouses->first()?->id ?? Warehouse::first()->id;
        $stockRecord = StockRecord::with(['product'])
            ->where('stockable_id', $warehouseId)
            ->where('stockable_type', Warehouse::class)
            ->get();

        return view('inventory.warehouse.create_material_issued', compact('stockRecord'));
    }   
    
    public function getStockRecord(Request $request)
    {
        $term = $request->input('term');
        $warehouseId = Auth::user()->warehouses->first()?->id ?? Warehouse::first()->id;
        $products = StockRecord::with('product')
            ->where('stockable_id', $warehouseId)
            ->where('stockable_type', Warehouse::class)
            ->whereHas('product', function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%');
            })
            ->first();
    
        $formattedProducts = $products->map(function ($product) {
            return ['id' => $product->product_id, 'text' => $product->product->name];
        });
    
        return response()->json($formattedProducts);
    }    
    
    public function _getProducts(Request $request): mixed
    {
        $productIds = $request->input('products', []);
    
        $warehouseId = Auth::user()->warehouses->first()?->id ?? Warehouse::first()->id;
    
        $products = Product::with(['stockRecords' => function ($query) use ($warehouseId) {
            $query->where('stockable_id', $warehouseId)
                ->where('stockable_type', Warehouse::class)
                ->where('quantity', '>', 0); // Exclude records with quantity 0
        }])
        ->whereIn('id', $productIds)
        ->get();
    
        $brands = Brand::all();
    
        return view('inventory.warehouse._get_material_issued_products', compact('products', 'brands'));
    }

  
    
    public function saveMaterialIssued(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;
        $warehouseId = Warehouse::first()->id;
    
        $rules = [
            'worker_name' => 'required|string',
            'worker_department' => 'required|string',
            //'address' => 'string'
        ];
    
        $validatedData = $request->validate($rules);
    
        $details = [];
        $products = Product::whereIn('id', $request->product_id)->get();
    
        foreach ($products as $product) {
            $stockRecord = StockRecord::where('stockable_id', $warehouseId)
                ->where('stockable_type', Warehouse::class)
                ->where('product_id', $product->id)
                ->first();
    
            if ($stockRecord && $stockRecord->quantity >= $request->quantities[$product->id]) {
                $details[] = [
                    'product_id' => $product->id,
                    'brand_id' => $request->brand_id[$product->id],
                    'quantity' => $request->quantities[$product->id],
                    'user_id' => Auth::user()->id,
                    'warehouse_id' => $warehouseId,
                    'worker_name' => $request->worker_name,
                    'worker_department' => $request->worker_department,
                    'address' => $request->address,
                    'date' => $request->date,
                    'issue_order_no' => 'ISSUE-' . date("Ymd") . '-'. date("his")
                ];
    
                $stockRecord->quantity -= $request->quantities[$product->id];
                $stockRecord->save();
            }
        }
    
        WarehouseMaterialIssued::insert($details);
    
        $redirect = route('warehouses.materialIssued');
        return response()->json([
            'message' => 'Material Issued saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }      

}
