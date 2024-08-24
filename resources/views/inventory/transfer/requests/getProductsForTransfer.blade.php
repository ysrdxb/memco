<div class="product-row">
    <div class="row">
        <div class="form-group col-md-4 mt-2">
            <label for="supplier_id">Material Request No</label>
            <select class="form-control select2 transfer_request_id" onchange="updateProductRows()" name="via_transfer_id">
                <option value="">Select</option>
                @foreach($transfers as $row)
                    <option value="{{ $row->id }}" {{ $transfer->id == $row->id ? 'selected' : '' }}>{{ $row->transfer_no }}</option>
                @endforeach
            </select>
        </div> 
    </div>
    
    <h2 class="p-2 col-sm-8"><a href="javascript:;" onclick="resetProject()" class="text-danger pr-4">x</a>{{ $transfer->requested_by==\App\Models\Warehouse::class ?$transfer->warehou->name : $transfer->project->name }}</h2>
                                            
    @if(count($data) > 0)
        @foreach($data as $product)
            <div class="row align-items-center product-row border">
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="input-group mt-4">
                            <div class="input-group-prepend">
                                <a href="javascript:;" onclick="deleteItem(this)" class="text-danger input-group-text">X</a>
                            </div>
                            <label> </label>
                            <input type="text" class="form-control" value="{{ $product['name'] }}" readonly>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="product_id[]" value="{{ $product['id'] }}">
                @if($product['type'] == 'tool')
                <div class="col-sm-2 quantity-row">

                <input type="hidden" readonly class="form-control type" name="type[]" required value="tool">
                <input type="hidden" class="form-control quantity" id="quantity" name="quantity[]" value="0" min="1" required>

                @php
                    $serials = [];
                    $stock = \App\Models\StockRecord::where('stockable_type', \App\Models\Warehouse::class)->where('product_id', $product['id'])->first();
                    if($stock) {
                        $serials = \App\Models\StockDetail::where('stock_record_id', $stock->id)->where('quantity', '!=', 0)->get();
                    }
                @endphp
                
                <div class="form-group tool-row">
                    <label>Serials <span class="text-red">*</span></label>
                    <select class="form-control" name="tool_serial[]" id="tool_serial_{{ $product['id'] }}" multiple="multiple">                            
                        @foreach($serials as $row) 
                            <option value="{{ $row->id }}">{{ $row->serial_no }}</option>
                        @endforeach
                    </select>
                </div>
                    
                </div>
                @else
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Quantity <span class="text-red">*</span></label>
                        <input type="hidden" readonly class="form-control type" name="type[]" required value="product">
                        <input style="width:50%" type="number" class="form-controls" name="quantity[]" value="{{ $product['requested_quantity'] - $product['delivered_quantity'] }}" required>
                    </div>
                </div>                
                @endif
                <div class="col-sm-1">
                    <div class="form-group">
                        <label>MR. QTY</label>
                        <p>{{ $product['requested_quantity'] }}</p>
                    </div>
                </div>                
                <div class="col-sm-1">
                    <div class="form-group">
                        <label>Del. QTY</label>
                        <p>{{ $product['delivered_quantity'] }}</p>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="form-group">
                        <label>Bal. QTY</label>
                        <p>{{ $product['requested_quantity'] - $product['delivered_quantity'] }}</p>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="form-group">
                        <label>Current Stock </label>
                        {{ $product['stock'] }}
                    </div>
                </div>   
                <div class="col-sm-1">
                    <div class="form-group">
                        <label>Brand </label>
                        <select name="brand_id[]" id="brand_id">
                            <option value="">Select</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $brand->id == $product['brand_id'] ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>                              
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Remarks</label>
                        <input type="text" class="form-controls" name="remarks[]">
                    </div>
                </div>                
            </div>
        @endforeach
        <div class="form-group text-right mt-4">
            <button type="submit" class="btn btn-success">Save</button>
        </div>        
    @else
        <div class="alert alert-danger" role="alert">
            All products from this material request have already been delivered.
        </div>        
    @endif
</div>
