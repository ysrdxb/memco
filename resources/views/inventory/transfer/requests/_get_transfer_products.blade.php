<table class="table table-stripped purchasesTable table-bordered">
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>U.O.M</th>
            <th>Brand</th>
            <th>Remaining Quantity</th>
            <th>Quantity</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td>
                    <select class="form-control" name="unit_id[{{ $product->id }}]" id="unit_id">
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ $unit->id == $product->unit_id ? 'selected' : ''}}>{{ $unit->name }}</option>
                        @endforeach
                    </select>   
                </td>
                <td>
                    <select class="form-control" name="brand_id[{{ $product->id }}]" id="brand_id">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    @php
                        $badge = ($product->quantities === null || $product->remaining_quantity === 0 || $product->remaining_quantity === null) 
                            ? '<span class="badge badge-success">Open</span>'
                            : '<span class="badge badge-warning">' . $product->remaining_quantity . '</span>';
                        echo $badge;
                    @endphp
                </td>                
                <td>
                    <input type="hidden" name="unit_id[{{ $product->id }}]" value="{{ $product->unit->id }}">
                    <input oninput="updateSubtotalAndGrandTotal(this.value)" 
                           onkeyup="validateQuantity(this, {{ $product->max_quantity ? $product->max_quantity : '0' }})"
                           type="number" 
                           id="quantities_{{ $product->id }}"  
                           name="quantities[{{ $product->id }}]" 
                           value="1" 
                           min="1" 
                           {!! $product->max_quantity ? 'max="'.$product->max_quantity.'"' : '' !!} 
                           {!! $product->max_quantity ? 'data-max="'.$product->max_quantity.'"' : '' !!} 
                           class="form-control quantity-input">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


<div class="form-group text-right">
    <button type="button" class="btn btn-primary saveBtn">Save</button>
</div>
