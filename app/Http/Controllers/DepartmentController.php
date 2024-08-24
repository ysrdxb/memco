<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\View\View;
use DataTables;

class DepartmentController extends Controller
{
    public function index(): view
    {
        return view('inventory.department.index');
    }   

    public function store(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;      
        $rules = [
            'name' => 'required|unique:departments,name,' . ($id ? $id : 'NULL') . ',id',
        ];
    
        $validatedData = $request->validate($rules);

        Department::updateOrCreate(['id' => $id], $validatedData);
    
        $redirect = route('departments.list');
        return response()->json([
            'message' => 'Department saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }      

    public function destroy($id)
    {
        $id = decrypt($id);

        if($department = Department::where('id', $id))
        {
            $department->delete();
            $redirect = route('departments.list');
            return response()->json([
                'message' => 'Department deleted successfully', 
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
        $data = Department::select('name', 'id');
    
        return DataTables::of($data)
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('action', function ($row) {
                $editId = encrypt($row->id);
                $deleteId = encrypt($row->id);     
                $dataName = $row->name;
                          
                return '<a href="#!" class="editBtn" data-id="'.$editId.'" data-name="'.$dataName.'" data-toggle="modal" data-target="#editUnitModal"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>  <a href="#!" class="deleteBtn ml-15" item-id="' . $deleteId . '"> <i class="ik ik-trash f-16 mr-15 text-red"></i></a>';
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('name', 'like', '%' . $searchValue . '%');
                    });
                }
            })            
            ->make(true);
    }
       
   
}
