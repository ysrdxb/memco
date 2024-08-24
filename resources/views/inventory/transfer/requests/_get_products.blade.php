<table class="table table-stripped purchasesTable table-bordered">
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>U.O.M</th>
            <th>Brand</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        @php $grand_total = 0; @endphp
        
        @foreach($products as $product)
        
        @php $grand_total += $product->price; @endphp
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
                    <input type="hidden" name="unit_id[{{ $product->id }}]" value="{{ $product->unit->id }}">
                    <input oninput="updateSubtotalAndGrandTotal(this.value)" type="number" id="quantities"  name="quantities[{{ $product->id }}]" value="1" min="1" class="form-control quantity-input">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="form-group text-right">
    <button type="button" class="btn btn-primary saveBtn">Save</button>
</div>