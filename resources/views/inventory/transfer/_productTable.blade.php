<table class="table table-stripped table purchasesTable table-bordered">
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Current Stock</th>
            <th>Req QTY</th>
            <th>Del QTY</th>
            <th>Balance</th>
            <th>Unit</th>
            <th>Qty</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $grand_total = 0; 
        @endphp

        @foreach($products as $product)
            @php 
                $grand_total += $product['price']; 
                $balance = $product['balance'];
            @endphp

            <tr>
                <td>{{ $product['code'] }}</td>
                <td>{{ $product['name'] }}</td>
                <td>
                    @if(!empty($product['stockRecords']))
                        {{ array_sum(array_column($product['stockRecords'], 'quantity')) }}
                    @else
                        0
                    @endif
                </td>
                <td>{{ $product['requested_quantity'] }}</td>
                <td>{{ $product['delivered_quantity'] }}</td>
                <td>
                    @if($balance > 0)
                        <span class="badge badge-warning">{{ $balance }}</span>
                    @else
                        <span class="badge badge-success"><i class="ik ik-check-square"></i> Delivered</span>
                    @endif
                </td>

                <td>{{ $product['unit']['name'] }}</td>
                <td>
                    <div class="input-group">
                        <input type="hidden" name="unit_id[{{ $product['id'] }}]" value="{{ $product['unit']['id'] }}">
                        <input 
                            {{ $balance > 0 ? 'value=' . $balance : 'value=0 readonly' }} 
                            oninput="updateSubtotalAndGrandTotal(this.value)" 
                            type="number" 
                            id="quantities_{{ $product['id'] }}"  
                            name="quantities[{{ $product['id'] }}]" 
                            min="0" 
                            max="{{ $product['requested_quantity'] }}" 
                            class="form-control quantity-input"
                        >
                    </div>
                </td>
            </tr>
        @endforeach

    </tbody>
</table>

<div class="form-group text-right">
    <button type="submit" class="btn btn-primary">Save</button>
</div>
