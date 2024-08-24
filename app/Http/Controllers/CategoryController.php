<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\View\View;
use DataTables;

class CategoryController extends Controller
{
    public function index(): view
    {
        return view('inventory.category.index');
    }   

    public function store(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;      
        $rules = [
            'name' => 'required|unique:categories,name,' . ($id ? $id : 'NULL') . ',id',
            'code' => 'required|unique:categories,name,' . ($id ? $id : 'NULL') . ',id',
        ];
    
        $validatedData = $request->validate($rules);

        $category = Category::updateOrCreate(['id' => $id], $validatedData);
    
        $redirect = route('categories.list');
        return response()->json([
            'message' => 'Category saved successfully',
            'category' => $category,
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }      

    public function destroy($id)
    {
        $id = decrypt($id);

        if($category = Category::where('id', $id))
        {
            $category->delete();
            $redirect = route('categories.list');
            return response()->json([
                'message' => 'Category deleted successfully', 
                'status' => 'success', 
                'redirect' => $redirect
            ], 200);    
        }
        return response()->json([
            'message' => 'Error occured while deleting', 
            'status' => 'error'
        ], 200);    
    }

    public function getList(Request $request): mixed
    {
        $data = Category::select('name', 'code', 'id');
    
        return DataTables::of($data)
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('code', function ($row) {
                return $row->code;
            })
            ->addColumn('action', function ($row) {
                $editId = encrypt($row->id);
                $deleteId = encrypt($row->id);     
                $dataName = $row->name;
                $dataCode = $row->code;           
                          
                return '<a href="#!" class="editBtn" data-id="'.$editId.'" data-name="'.$dataName.'" data-code="'.$dataCode.'" data-toggle="modal" data-target="#editUnitModal"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>  <a href="#!" class="deleteBtn ml-15" item-id="' . $deleteId . '"> <i class="ik ik-trash f-16 mr-15 text-red"></i></a>';
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('code', 'like', '%' . $searchValue . '%');
                    });
                }
            })            
            ->make(true);
    }
       
   
}
