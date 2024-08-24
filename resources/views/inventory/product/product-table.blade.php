<table class="table table-hovered purchasesTable">
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Current Stock</th>
            <th>Price</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Sub Total</th>
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
                    @if($product->stockRecords->isNotEmpty())
                        {{ $product->stockRecords->sum('quantity') }}
                    @else
                        0
                    @endif
                </td>
                <td>AED {{ $product->price }}</td>
                <td>{{ $product->unit->name }}</td>
                <td>
                    <div class="input-group">
                        <input type="hidden" name="unit_id[{{ $product->id }}]" value="{{ $product->unit->id }}">
                        <input oninput="updateSubtotalAndGrandTotal(this.value)" type="number" id="quantities"  name="quantities[{{ $product->id }}]" value="1" min="1" class="form-control quantity-input">
                    </div>
                </td>
                <td class="subtotal">AED {{ $product->price }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="offset-md-9 col-md-3 mt-4">
    <table class="table table-bordered table-stripped">
        <tbody class="totals">
            <tr>
                <td><span class="font-weight-bold">Grand Total</span></td> 
                <td><span class="font-weight-bold grandTotal">AED {{ $grand_total }}</span></td>
            </tr>
        </tbody>
    </table>                                            
</div>
<div class="form-group text-right">
    <button type="submit" class="btn btn-primary">Save</button>
</div>
