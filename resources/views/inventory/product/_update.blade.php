<form id="submitForms">
    <div class="form-group">
        <label for="editName">Product</label>
        <input type="text" class="form-control" readonly name="product" id="pid" value="{{ $product->name }}">
        <input type="hidden" value="{{ $product->id }}" name="product_id" id="product_id">
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <label for="">Quantity (Available {{ $quantity }})</label>
            <input type="number" value="0" id="pquantity" name="quantity" class="form-control" placeholder="Enter Quantity" required>
        </div>
        <div class="col-md-12 col-">
            <label>Plus / Minus</label>
            <select class="form-control" name="type">
                <option value="plus">Plus</option>
                <option value="minus">Minus</option>
            </select>
        </div>
    </div>
    <input type="hidden" value="{{ $product->price }}" id="pprice" name="price" class="form-control" placeholder="Enter Price" required>

    @if($product->type === 'tool')
    <div class="form-group">
        <label for="">Print Serials?</label>
        <select class="form-control" name="print_serials">
            <option value="1" {{ $product->print_serials === '1' ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ $product->print_serials === '0' ? 'selected' : '' }}>No</option>
        </select>                        
    </div> 
    @endif
    <div class="form-group">
        <label for="type">Unit <span class="text-red">*</span></label>
        <select class="form-control select2" name="unit_id" required>
            <option selected="selected" value="" >-Select-</option>
            @foreach($units as $unit)
                <option value="{{$unit->id}}" {{$unit->id==$product->unit_id?'selected' : ''}}>{{$unit->name}}</option>
            @endforeach
        </select>                                                    
    </div>   
    <div class="form-group">
        <label for="type">Brand <span class="text-red">*</span></label>
        <select class="form-control select2" name="brand_id" required>
            <option selected="selected" value="" >-Select-</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : ''}}>{{ $brand->name }}</option>
            @endforeach
        </select>                                                    
    </div>        
    <div class="form-group">
        <label for="type">Pass Key <span class="text-red">*</span></label>
        <input type="password" name="passkey" class="form-control" placeholder="Enter Pass Key" required>
    </div>     
    <div class="form-group mt-4">
        <button class="btn btn-primary" type="submit" name="Save">Update</button>
    </div>
</form>