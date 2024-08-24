<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::paginate(12);
        return view('inventory.city.index', compact('cities'));
    }   

    public function store(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;      
        $rules = [
            'name' => 'required|unique:cities,name,' . ($id ? $id : 'NULL') . ',id',
        ];
    
        $validatedData = $request->validate($rules);

        $city = City::updateOrCreate(['id' => $id], $validatedData);
    
        $redirect = route('cities.list');
        return response()->json([
            'message' => 'City saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }      

    public function destroy($id)
    {
        $id = decrypt($id);

        if($city = City::where('id', $id))
        {
            $city->delete();
            $redirect = route('cities.list');
            return response()->json([
                'message' => 'City deleted successfully', 
                'status' => 'success', 
                'redirect' => $redirect
            ], 200);    
        }
        return response()->json([
            'message' => 'Error occured while deleting', 
            'status'=>'error'
        ], 200);    
    }
   
}
