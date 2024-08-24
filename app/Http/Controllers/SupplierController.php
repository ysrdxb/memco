<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\User;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();

        return view('inventory.supplier.list', compact('suppliers'));
    }   

    public function store(Request $request, $id = null)
    {
        $id = $request->id ? decrypt($request->id) : $request->id;
        $rules = [
            'name' => 'required',
            'country' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable|numeric',
            'city' => 'nullable',
            'address' => 'nullable',
            'details' => 'nullable',
        ];
    
        $validatedData = $request->validate($rules);
    
        // Fill other fields with dummy data
        $dummyData = [
            'country' => $validatedData['country'] ?? 'Dummy Country',
            'email' => $validatedData['email'] ?? 'dummy@example.com',
            'phone' => $validatedData['phone'] ?? '123456789',
            'city' => $validatedData['city'] ?? 'Dummy City',
            'address' => $validatedData['address'] ?? '123 Dummy Street',
            'details' => $validatedData['details'] ?? 'Dummy Details',
        ];
    
        // Merge dummy data with validated data
        $validatedData = array_merge($validatedData, $dummyData);
    
        $supplier = Supplier::updateOrCreate(['id' => $id], $validatedData);
    
        //update/create store users
        // $rules = [
        //     'store_id' => 'required|numeric',
        //     'user_id' => 'required|numeric',
        // ];
    
        $redirect = route('suppliers.list');
        return response()->json([
            'message' => 'Supplier saved successfully',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }
      

    public function destroy($id)
    {
        $id = decrypt($id);

        if($store = Supplier::where('id', $id))
        {
            $store->delete();
            $redirect = route('suppliers.list');
            return response()->json([
                'message' => 'Supplier deleted successfully', 
                'status'=>'success', 
                'redirect' => $redirect
            ], 200);    
        }
        return response()->json(['message' => 'Error occured while deleting', 'status'=>'error'], 200);    
    } 
    
}
