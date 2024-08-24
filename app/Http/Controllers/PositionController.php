<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\Department;
use Illuminate\View\View;
use DataTables;

class PositionController extends Controller
{
    public function index(): view
    {
        $departments = Department::all();
        return view('inventory.position.index', compact('departments'));
    }   

    public function store(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;      
        $rules = [
            'name' => 'required|unique:positions,name,' . ($id ? $id : 'NULL') . ',id',
            'department_id' => 'required|exists:departments,id',
        ];
    
        $validatedData = $request->validate($rules);
    
        // Save the position
        $position = Position::updateOrCreate(['id' => $id], ['name' => $validatedData['name']]);
    
        // Associate the position with the department using the pivot table
        $position->departments()->sync([$validatedData['department_id']]);
    
        $redirect = route('positions.list');
        return response()->json([
            'message' => 'Position saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }    
         
    public function destroy($id)
    {
        $id = decrypt($id);

        if($position = Position::where('id', $id))
        {
            $position->delete();
            $redirect = route('positions.list');
            return response()->json([
                'message' => 'Position deleted successfully', 
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
        $data = Position::select('name','id');
    
        return DataTables::of($data)
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('action', function ($row) {
                $editId = encrypt($row->id);
                $deleteId = encrypt($row->id);     
                $dataName = $row->name;
                          
                return '<a href="#!" class="editBtn" data-id="'.$editId.'" data-name="'.$dataName.'" data-toggle="modal" data-target="#editPositionModalLabel"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>  <a href="#!" class="deleteBtn ml-15" item-id="' . $deleteId . '"> <i class="ik ik-trash f-16 mr-15 text-red"></i></a>';
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

    public function getPositions(Request $request)
    {
        $department_id = $request->department_id;
        $positions = Position::all();
    
        return response()->json($positions);
    }    
   
}
