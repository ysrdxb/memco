<?php

namespace App\Http\Controllers;

use App\Models\DirectTransfer;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\StockRecord;
use Illuminate\Http\Request;

class DirectTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['Admin','Super Admin']);
        })->get();         
        $data = DirectTransfer::all();
        $stores = Store::all();
        return view('inventory.transfer.direct_transfer', compact('data','users','stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $rules = [
            'user_id' => 'required|numeric',
            'store_id' => 'required|numeric',
            'sub_store_id' => 'required|numeric',
            'quantity' => 'required|numeric',
            'product_id' => 'required|numeric',
        ];
    
        $validatedData = $request->validate($rules);  
        $validatedData['remarks'] = $request->remarks;

        $stock = StockRecord::where('stockable_id', Warehouse::first()->id)
        ->where('stockable_type', Warehouse::class)
        ->where('product_id', $request->product_id)
        ->first();
        
        if($stock && $stock->quantity > $request->quantity) 
        {
            $save = DirectTransfer::create($validatedData);
            $redirect = route('directTransfers.list');
            if($save) 
            {
                $stock->quantity = $stock->quantity - $request->quantity;
                $stock->save();
                $store = Store::find($request->store_id);

                $store_stock = StockRecord::where('stockable_id', $request->store_id)
                ->where('stockable_type', Store::class)
                ->where('product_id', $request->product_id)
                ->first();

                if($store_stock)
                {
                    $store_stock->quantity = $store_stock->quantity + $request->quantity;
                    $store_stock->save();
                }
                else
                {
                    StockRecord::create([
                        'stockable_id' => $request->store_id,
                        'stockable_type' => Store::class,
                        'quantity' => $request->quantity,
                        'product_id' => $request->product_id,
                    ]);
                }

                return response()->json([
                    'message' => 'Direct Material Issued Successfully!',
                    'status' => 'success',
                    'redirect' => $redirect
                ], 200);            
            }
            else 
            {
                return response()->json([
                    'message' => 'Direct Material Issued Error!',
                    'status' => 'error',
                    'redirect' => $redirect
                ], 200);            

            }
        }
        else
        {
            $message = $stock
                ? '<span class="text text-warning">Product Insufficient Quantity Available in Stock!</span>'
                : '<span class="text text-danger">Product out of stock!</span>';

            return response()->json([
                'message' => $message,
                'status' => 'error',
            ]);
            exit;            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DirectTransfer  $directTransfer
     * @return \Illuminate\Http\Response
     */
    public function show(DirectTransfer $directTransfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DirectTransfer  $directTransfer
     * @return \Illuminate\Http\Response
     */
    public function edit(DirectTransfer $directTransfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DirectTransfer  $directTransfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DirectTransfer $directTransfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DirectTransfer  $directTransfer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        if($data = DirectTransfer::where('id', $id)->first())
        {
            $store = Store::find($data->store_id);
            // Deduct direct transfer quantity from store stock
            $store_stock = StockRecord::where('stockable_id', $data->store_id)
            ->where('stockable_type', Store::class)
            ->where('product_id', $data->product_id)
            ->first();
            $store_stock->quantity = $store_stock->quantity - $data->quantity;
            $store_stock->save();  
            // Add direct transfer quantity for warehouse
            $stock = StockRecord::where('stockable_id', Warehouse::first()->id)
            ->where('stockable_type', Warehouse::class)
            ->where('product_id', $data->product_id)
            ->first();
            $stock->quantity = $stock->quantity + $data->quantity;
            $stock->save();             

            $data->delete();
            $redirect = route('directTransfers.list');
            return response()->json([
                'message' => 'Direct Transfer deleted successfully', 
                'status'=>'success', 
                'redirect' => $redirect
            ], 200);    
        }
        return response()->json([
            'message' => 'Error occured while deleting', 
            'status'=>'error'
        ], 200);         
    }
}
