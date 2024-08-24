<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use DataTables;

class UnitsController extends Controller
{
    public function index(): view
    {
        return view('inventory.unit.index');
    }   

    public function store(Request $request, $id = null)
    {
        $id = $request->unit_id ? decrypt($request->unit_id) : $id;
        $rules = [
            'name' => 'required|unique:units,name,' . ($id ? $id : 'NULL') . ',id',
            'short_name' => 'required|unique:units,short_name,' . ($id ? $id : 'NULL') . ',id',
        ];
    
        $validatedData = $request->validate($rules);
    
        $unit = Unit::updateOrCreate(['id' => $id], $validatedData);
    
        $redirect = route('units.list');
        return response()->json([
            'message' => 'Unit saved successfully',
            'unit' => $unit,
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }  

    public function destroy($id)
    {
        $id = decrypt($id);

        if($data = Unit::where('id', $id))
        {
            $data->delete();
            $redirect = route('units.list');
            return response()->json(['message' => 'Unit deleted successfully', 'status'=>'success', 'redirect'=>$redirect], 200);    
        }
        return response()->json(['message' => 'Error occured while deleting', 'status'=>'error'], 200);    
    }

    public function getList(Request $request): mixed
    {
        $data = Unit::select('name', 'short_name', 'id');
        
        return DataTables::of($data)
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('short_name', function ($row) {
                return $row->short_name;
            })
            ->addColumn('action', function ($row) {
                $editId = encrypt($row->id);
                $deleteId = encrypt($row->id);     
                $dataName = $row->name;
                $dataCode = $row->short_name;           
                          
                return '<a href="#!" class="editBtn" data-id="'.$editId.'" data-name="'.$dataName.'" data-code="'.$dataCode.'" data-toggle="modal" data-target="#editUnitModal"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>  <a href="#!" class="deleteBtn ml-15" item-id="' . $deleteId . '"> <i class="ik ik-trash f-16 mr-15 text-red"></i></a>';
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('short_name', 'like', '%' . $searchValue . '%');
                    });
                }
            })            
            ->make(true);
    }     
   
}
