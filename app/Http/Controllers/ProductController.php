<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockRecord;
use App\Models\StockDetail;
use App\Models\DeliveryOrderSerial;
use App\Models\DeliveryOrder;
use App\Models\Warehouse;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Project;
use App\Models\Unit;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;
use DataTables;
use Auth;
use Log;
use App\Services\SwitchProject;

class ProductController extends Controller
{
    protected $switchProject;

    public function __construct(SwitchProject $switchProject)
    {
        $this->switchProject = $switchProject;
    }
        
    public function index(Request $request): View
    {       
        $type = $request->query('type') ?? 'product';
        return view('inventory.product.list', compact('type'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $units = Unit::all();
        $code = Product::latest('id')->value('code');
        $code = $code;
        return view('inventory.product.create', compact('categories','brands','units', 'code'));
    }

    public function store(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;
        $rules = [
            'name' => 'required|unique:products,name,' . ($id ? $id : 'NULL') . ',id',
            // 'code' => 'required',
            'status' => 'numeric',
            'type' => 'required',
            'category_id' => 'required|numeric',
            'subcategory_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'unit_id' => 'required|numeric',
            'passkey' => ['required', 'numeric', function($attribute, $value, $fail) {
                if ($value != 111) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],            
        ];

        $validatedData = $request->validate($rules);
        unset($validatedData['passkey']);

        $validatedData['price'] = 0;
        $validatedData['cost'] = 0;
        $validatedData['user_id'] = Auth::user()->id;

        $category = Category::find($request->category_id);
        if ($category) {
            $categoryCode = strtoupper(substr($category->name, 0, 2));
            $latestProduct = Product::where('category_id', $category->id)->latest()->first();
            $numericPart = $latestProduct ? intval(substr($latestProduct->code, 2)) + 1 : 1;
            $newCode = $categoryCode . str_pad($numericPart, 5, '0', STR_PAD_LEFT);
            $validatedData['code'] = $newCode;
        }

        $validatedData['code'] = $request->code ? $request->code : $validatedData['code'];
        $product = Product::updateOrCreate(['id' => $id], $validatedData);

        // Additional logic for tool type products
        if ($id && $request->type === 'tool') {
            $product = Product::find($id);
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

        $redirect = route('products.list');
        return response()->json([
            'message' => 'Product saved successfully',
            'product' => $product,
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }


    public function getSubactivities(Request $request)
    {
        $activityId = $request->input('activity_id');
        $subactivities = SubCategory::where('category_id', $activityId)->get();
        return response()->json($subactivities);
    }    

    public function edit($id)
    {
        $id = decrypt($id);
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brand::all();
        $units = Unit::all();
        $code = Product::latest('id')->value('code');
        $code = $code;
        return view('inventory.product.edit', compact('product','categories','brands','units', 'code', 'subcategories'));
    }

    public function destroy($id)
    {
        $id = decrypt($id);

        if($product = Product::where('id', $id))
        {
            $product->delete();
            $redirect = route('products.list');
            return response()->json([
                'message' => 'Product deleted successfully', 
                'status' => 'success', 
                'redirect' => $redirect
            ], 200);    
        }
        return response()->json([
            'message' => 'Error occured while deleting', 
            'status'=>'error'
        ], 200);    
    }    

    public function getList(Request $request): mixed
    {
        $type = $request->query('type');

        $products = Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
            ->when($type, function ($query, $type) {
                return $query->where('products.type', $type);
            })
            ->select([
                'products.id',
                'products.name',
                'products.code',
                'products.type',
                'categories.name as category_name', 
                'subcategories.name as subcategory_name', 
                'units.name as unit_name', 
                'products.subcategory_id as subcategory',
                'brands.name as brand_name', 
                'products.price'
            ]);
    
        $counter = 0;
        return DataTables::of($products)
            ->addColumn('id', function () use (&$counter) {
                $counter++; // Increment counter
                return $counter;
            })        
            ->addColumn('category', function ($product) {
                return $product->category_name;
            })
            ->addColumn('type', function ($product) {
                return $product->type;
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
                if(Auth::user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge'])) {
                    $project_id = Warehouse::first()->id;
                    $stockable = Warehouse::class;
                } else {
                    $project_id = $this->switchProject->getCurrentProjectId();
                    $stockable = Project::class;
                }
                $stockRecord = StockRecord::where('stockable_type', $stockable)
                    ->where('stockable_id', $project_id)
                    ->where('product_id', $product->id)
                    ->first();
                
                return $stockRecord && ($stockRecord->quantity === 0 || $stockRecord->quantity === null)
                    ? '<span class="text-danger">Out of stock</span>'
                    : ($stockRecord ? '<span class="text-success">'.number_format($stockRecord->quantity).'</span>' : '<span class="text-danger">Out of Stock</span>');
    
            })                           
            ->addColumn('action', function ($product) {
                if(Auth::user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge'])) {
                    $editUrl = route('products.edit', encrypt($product->id));
                    $deleteId = encrypt($product->id);
                    $stockRecord = StockRecord::where('stockable_type', Warehouse::class)
                        ->where('stockable_id', Warehouse::first()->id)
                        ->where('product_id', $product->id)
                        ->first();
                    
                    // If $stockRecord is not null (i.e., it exists), use its quantity; otherwise, use 0.
                    $stock_quantity = $stockRecord !== null && $stockRecord->quantity !== null ? $stockRecord->quantity : 0;
                    
                    return '<a data-product-name="'.$product->name.'" data-pquantity="'.$stock_quantity.'" data-pprice="'.$product->price.'" data-product-id="'.$product->id.'" href="' . $editUrl . '" class="mr-15 btn btn-primary add-stock-link" style="padding:1px 15px;" data-toggle="modal" data-target="#categoryAdd">Update</a> <a href="' . $editUrl . '" style="float:right;" class="text-right"><i class="ik ik-edit f-16 mr-15 text-green"></i></a> <a href="#!" style="float:right;display:none" class="deleteBtn" item-id="' . $deleteId . '"><i class="ik ik-trash f-16 ml-15 text-red"></i></a>';
                }
            })
            ->rawColumns(['action','quantity'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('products.name', 'like', '%' . $searchValue . '%')
                            ->orWhere('products.code', 'like', '%' . $searchValue . '%')
                            ->orWhere('products.type', $searchValue)
                            ->orWhere('products.subcategory_id', 'like', '%' . $searchValue . '%')
                            ->orWhere('categories.name', 'like', '%' . $searchValue . '%')
                            ->orWhere('brands.name', 'like', '%' . $searchValue . '%');
                    });
                }
            })            
            ->make(true);
    
    }
    
    
    public function import(Request $request)
    {
        exit;
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');

        Excel::import(new ProductImport, $file);

        $products = Product::all();
        // Check if there are any brands in the database
        // if (Brand::count() === 0) {
        //     // If no brands exist, insert a new brand
        //     $brand = new Brand();
        //     $brand->name = 'Local'; // Replace with the actual brand name
        //     $brand->save();
        //     $brandId = $brand->id;

        // } else {
        //     // If brands exist, get the ID of the first brand
        //     $brandId = Brand::first()->id;
        // }
        // In case of Newly Imported products ?? then insert category and units and get id and update in products table
        foreach($products as $product)
        {
            if($product->category_id != NULL && !is_numeric($product->category_id) && $product->unit_id != NULL && !is_numeric($product->unit_id) && $product->subcategory_id != NULL && !is_numeric($product->subcategory_id))
            {

                $category = Category::firstOrCreate([
                    'name' => $product->category_id, 
                    'code' => strtolower($product->category_id)
                ]);
                $categoryId = $category->id;         

                $subcategory = SubCategory::firstOrCreate([
                    'category_id' => $categoryId, 
                    'name' => $product->subcategory_id, 
                    'code' => strtolower($product->subcategory_id)
                ]);
                $subCategoryId = $subcategory->id;

                 $unit = Unit::firstOrCreate(['short_name' => $product->unit_id, 'name' => $product->unit_id]);
                 $unitId = $unit->id;         
                        
                Product::where('id', $product->id)->update([
                    'category_id'   => $categoryId,
                    'subcategory_id' => $subCategoryId,
                    'brand_id'  => $brandId,
                    'unit_id'   => $unitId
                ]);

            }
        }

        $redirect = route('products.list');

        return response()->json([
            'message' => 'Products imported successfully!',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    } 
    
    public function updateProduct(Request $request)
    {
        $rules = [
            'passkey' => ['required', 'numeric', function($attribute, $value, $fail) {
                if ($value != env('PASSKEY')) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],            
            'product_id' => 'required|numeric'
        ];   
    
        $request->validate($rules);
        
        $product = Product::findOrFail($request->product_id);
        if($request->has('print_serials'))
        {
            $print_serials = $request->print_serials;
            $product->print_serials = $print_serials;
            $product->save();
        }
        
        if($request->brand_id)
        {
            $brand_id = $request->brand_id;
            if($product->brand_id != $brand_id)
            {
                $product->brand_id = $brand_id;
                $product->save();
            }
        }   
        if($request->unit_id)
        {
            $unit_id = $request->unit_id;
            if($product->brand_id != $unit_id)
            {
                $product->unit_id = $unit_id;
                $product->save();
            }
        }        
        
        if(!empty($request->quantity) && $request->quantity > 0)
        {
            $quantity = $request->quantity;
            $product_id = $request->product_id;
        
            $stock = StockRecord::where('stockable_id', Warehouse::first()->id)
                ->where('stockable_type', Warehouse::class)
                ->where('product_id', $product_id)
                ->first();
        
            if($stock)
            {
                if ($request->type && $request->type == 'minus') {
                    if ($stock->quantity >= $quantity) {
                        $stock->quantity -= $quantity;
                        $stock->save();
                        if($product->type == 'tool') {
                            $stockDetails = StockDetail::where('stock_record_id', $stock->id)
                                ->where('product_id', $request->product_id)
                                ->orderBy('id', 'desc')
                                ->take($quantity)
                                ->get();
                    
                            foreach ($stockDetails as $stockDetail) {
                                $stockDetail->delete();
                            }
                        }
                    }
                } else {
                    $stock->quantity += $quantity;
                    $stock->save();                    
                    if($product->type == 'tool') {
                        for($i = 1; $i <= $quantity; $i++) {
                            StockDetail::create([
                                'stock_record_id' => $stock->id,
                                'product_id' => $request->product_id,
                                'quantity' => 1,
                                'serial_no' => $i,
                                'model_no' => '1',
                                'warranty' => '2',
                                'product_condition' => 'new',                                                                
                            ]);
                        } 
                    }                   
                }
                
                
                // $stock = StockRecord::where('stockable_id', Warehouse::first()->id)
                //     ->where('stockable_type', Warehouse::class)
                //     ->where('product_id', $product_id)
                //     ->first();
                // if($stock)
                // {
                //     if($stock->quantity == 0 || $stock->quantity == null)
                //     {
                //         $stock->delete();
                //         $redirect = route('products.list');
                //         return response()->json([
                //             'message' => 'Product stock deleted successfully', 
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
                $stock->stockable_id = Warehouse::first()->id;
                $stock->stockable_type = Warehouse::class;
                $stock->product_id = $product_id;
                $stock->quantity = $quantity;
                $stock->save();

                if($product->type == 'tool') {
                    for($i = 1; $i <= $quantity; $i++) {
                        StockDetail::create([
                            'stock_record_id' => $stock->id,
                            'product_id' => $request->product_id,
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
        
        $redirect = route('products.list');
        return response()->json([
            'message' => 'Product quantity saved successfully', 
            'status' => 'success', 
            'redirect' => $redirect
        ], 200);
    }
    

    public function search(Request $request)
    {
        $term = $request->input('term');

        $products = Product::where('name', 'like', '%' . $term . '%')->orWhere('code', 'like', '%' .$term.'%')->get();
    
        $formattedProducts = $products->map(function ($product) {
            return ['id' => $product->id, 'text' => $product->name];
        });
    
        return response()->json($formattedProducts);
    } 
    
    public function searchByStock(Request $request)
    {
        $term = $request->input('term');
    
        $products = Product::whereHas('stockRecords', function ($query) {
                $query->where('quantity', '>', 0)
                    ->where('stockable_type', Warehouse::class)
                    ->where('stockable_id', Warehouse::first()->id);
            })
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('code', 'like', '%' . $term . '%');
            })
            ->get();
    
        $formattedProducts = $products->map(function ($product) {
            return ['id' => $product->id, 'text' => $product->name];
        });
    
        return response()->json($formattedProducts);
    }

    
    public function searchByCategorySubcategory($category_id,$subcategory_id)
    {
        $term = $request->input('term');

        $products = Product::where('category_id', $category_id)
            ->where('subcategory_id', $subcategory_id)
            ->where('name', 'like', '%' . $term . '%')
            ->orWhere('code', 'like', '%' .$term.'%')
            ->get();
    
        $formattedProducts = $products->map(function ($product) {
            return ['id' => $product->id, 'text' => $product->name];
        });
    
        return response()->json($formattedProducts);
    }    

    public function getProducts(Request $request)
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
    
        //return response()->json($products);
        if(!empty($request->page)) {
            $page = $request->page;
        } else {
            $page = 'product.product-table';
        }
        return view('inventory.'.$page, compact('products'));
    }  
    
    public function showUpdateForm($id)
    {
        $product = Product::findOrFail($id);
        $stockRecord = StockRecord::where('product_id', $id)
            ->where('stockable_id', Warehouse::first()->id)
            ->where('stockable_type', Warehouse::class)
            ->first();
        $quantity = $stockRecord ? $stockRecord->quantity : 0;
        $brands = Brand::all();
        $units = Unit::all();
        return view('inventory.product._update', compact('product', 'quantity', 'units', 'brands'));
    }

    public function checkProductType(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::find($productId);
    
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found']);
        }
    
        return response()->json(['status' => 'success', 'productType' => $product->type]);
    }
      
}
