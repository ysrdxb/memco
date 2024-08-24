
<div class="">
    <div class="" role="alert">
       <h5 class="text-info">Project Name: {{ $material_request->requestable->name }} </h5>
    </div>
    @if(count($data))
    <div class="form-group">
        <label class="select-box"><input type="checkbox" id="select-all"> <span class="checkmark" style="left:-12px"></span> Select All</label>
    </div>

    @foreach($data as $product)
    <?php 
    $lpo = \App\Models\Purchase::where('transfer_id', $material_request->id)->first();

    $purchase_quantity = 0;

    if($lpo && $lpo->details) {
        foreach($lpo->details as $detail) {
            if($detail->product_id == $product['id']) {
                $purchase_quantity += $detail->quantity;
            }
        }
    }
    $mtv = \App\Models\DeliveryOrder::where('delivered_by', 'store')
    ->where('product_id', $product['id'])
    ->where('transfer_id', $material_request->transfer_id)
    ->sum('quantity');    
    ?>
        <div class="row align-items-center product-row">
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="input-group mt-4">
                        <div class="input-group-prepend">
                            <label class="select-box"><input type="checkbox" class="checkbox-custom form-control product-checkbox" name="product_id[]" value="{{ $product['id'] }}"><span class="checkmark"></span></label>
                        </div>                        
                        <div class="input-group-prepend">
                            <a href="javascript:;" onclick="confirmDeleteItem(this)" class="text-danger input-group-text">X</a>
                        </div>
                        <label> </label>
                        <input type="text" class="form-control" value="{{ $product['name'] }}" readonly>                    
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Qty <span class="text-red">*</span></label>
                    <input type="number" class="form-control" name="quantity[{{ $product['id'] }}]" value="{{ $product['requested_quantity'] - $product['delivered_quantity'] }}" max="{{ $product['requested_quantity'] - $product['delivered_quantity'] }}" required>
                </div>
            </div>   
            <div class="col-sm-1">
                <div class="form-group">
                    <label>MR Qty </label>
                    <p>{{ $product['requested_quantity'] }}</p>
                </div>
            </div>                      
            <div class="col-sm-1">
                <div class="form-group">
                    <label>M.T.V Qty </label>
                    <p>{{ $mtv }}</p>
                </div>
            </div>             
            <div class="col-sm-1">
                <div class="form-group">
                    <label>L.P.O Qty </label>
                    <p>{{ $purchase_quantity }}
                </div>
            </div>            
            <div class="col-sm-1">
                <div class="form-group">
                    <label>Rec Qty <span class="text-red">*</span></label>
                    {{ $product['delivered_quantity'] }}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Bal Qty <span class="text-red">*</span></label>
                    {{ $product['requested_quantity'] - $product['delivered_quantity'] }}
                </div>
            </div>
        </div>
    @endforeach 

    @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
    <div class="product-row row align-items-right mb-4 mt-4">
        <div class="form-group col-sm-4">
            <label for="">Add to Stock?</label>
            <select name="add_stock" id="" class="form-control select2" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
            <p class="alert alert-warning mt-2">Note: When selected "YES" all products quantity will be added to the stock record. If selected "NO" then Quantity will not be added.</p>

        </div> 
    </div>
    @endif    

    <div class="text-right float-right">
        <button type="submit" class="btn btn-success">Save Delivery Order</button>       
    </div>
  
    @else
        <div class="alert alert-danger" role="alert">
            All products from this material request have already been delivered.
        </div>        
    @endif
</div>