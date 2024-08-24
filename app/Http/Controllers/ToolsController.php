<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;
use DataTables;

use App\Models\Warehouse;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\StoreUser;
use App\Models\WarehouseUser;
use App\Models\User;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\StockRecord;
use App\Models\TransferReturn;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\DeliveryOrder;
use App\Models\ToolsTransfer;
use App\Services\SwitchProject;

class ToolsController extends Controller
{
    protected $switchProject;

    public function __construct(SwitchProject $switchProject)
    {
        $this->switchProject = $switchProject;
    }
        
    public function index()
    {
        $stockRecord = StockRecord::with(['product'])
            ->where('stockable_id', Warehouse::first()->id)
            ->where('stockable_type', Warehouse::class)
            ->get();        
        return view('inventory.tools.list', compact('stockRecord'));
    }

    public function create()
    {
        return view('inventory.tools.create');
    }

    public function save(Request $request)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;
    
        $rules = [
            'project_id' => 'required|numeric',
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
            $issue_order_no = str_pad($this->generateIssueOrderNo(), 4, '0', STR_PAD_LEFT);
            // Process file upload
            $filePath = '';
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                if ($file->isValid()) {
                    $fileName = 'issue_voucher_no_' . $issue_order_no . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('tools/' . Auth::user()->project->first()->code . '/', $fileName, 'public');
                }
            }            
    
            for ($i=0; $i<=$products; $i++) {
                $product = Product::find($request->product_id[$i]);
                $stockRecord = StockRecord::where('stockable_id', $request->project_id)
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
                        'file_path' => $filePath,
                        'issue_order_no' => $issue_order_no,
                    ];
    
                    $stockRecord->quantity -= $request->quantity[$i];
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
    
            ToolsTransfer::insert($details);
    
            DB::commit();
            $redirect = route('tools.list');
            return response()->json([
                'message' => 'Tools Issued saved successfully',
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

    public function delete(Request $request)
    {

    }

    public function getlist(Request $request)
    {
        
        $data = ToolsTransfer::all();

        return DataTables::of($data)
            ->addColumn('product_code', function ($row) {
                return $row->product->code;
            })
            ->addColumn('quantity', function ($row) {
                return $row->quantity;
            })
            ->addColumn('current_stock', function ($row) {
                $stockRecord = StockRecord::where('stockable_id', Auth::user()->stores->first()->id)
                ->where('stockable_type', Project::class)
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
                return $row->employee->name;
            })                          
            ->addColumn('worker_department', function ($row) {
                return $row->employee->position ? $row->employee->position->name : '';
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

    public function getStockRecord(Request $request)
    {

    }
}