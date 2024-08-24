<table class="table table-stripped purchasesTable">
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Current Stock</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        @php $grand_total = 0; @endphp        
        @foreach($products as $product)
            <tr>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td>
                    <select class="form-control" name="brand_id[{{ $product->id }}]" id="brand_id_{{ $product->id }}">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : ''}}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    @if($product->stockRecords->isNotEmpty())
                        {{ $product->stockRecords->sum('quantity') }}
                    @else
                        Out of Stock
                    @endif
                </td> 
                <td>
                    <input oninput="updateSubtotalAndGrandTotal(this.value)" 
                           onkeyup="validateQuantity(this, {{ $product->stockRecords->isNotEmpty() ? $product->stockRecords->sum('quantity') : '0' }})"
                           type="number" 
                           id="quantities_{{ $product->id }}"  
                           name="quantities[{{ $product->id }}]" 
                           value="1" 
                           min="1" 
                           {!! $product->stockRecords->isNotEmpty() ? 'max="'.$product->stockRecords->sum('quantity').'"' : '' !!} 
                           {!! $product->stockRecords->isNotEmpty() ? 'data-max="'.$product->stockRecords->sum('quantity').'"' : '' !!} 
                           class="form-control quantity-input">                    
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="form-group text-right">
    <button type="button" class="btn btn-primary saveBtn">Save</button>
</div>
