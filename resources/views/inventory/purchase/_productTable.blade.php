@if($data->isEmpty())
    @if($transfer)
        @if($transfer->deliveryOrders->isNotEmpty())
            <p class="alert alert-danger">All products from this material request have already been delivered.</p>
        @else
            <div class="alert alert-warning" role="alert">
                Delivery orders are pending for the following products related to Material Request No. {!! $transfer ? '<strong>' . $transfer->transfer_no .'</strong>.' : '' !!}
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Purchase Code</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $purchases = \App\Models\Purchase::where('transfer_id', $transfer->id)->get();
                        $i=0;
                        foreach($purchases as $purchase) {
                            $purchase_details = \App\Models\PurchaseDetail::where('purchase_id', $purchase->id)->get();
                            foreach($purchase_details as $row) {
                                $i++;
                                echo '<tr>';
                                echo '<td>'.$i.'</td>';
                                echo '<td>'.$purchase->purchase_no.'</td>';
                                echo '<td>'.$row->product->name.'</td>';
                                echo '<td>'.$row->quantity.'</td>';
                                echo '<td>'.$row->unit->name.'</td>';
                                echo '</tr>';
                            }
                        }
                    @endphp        
                </tbody>
            </table>
       
        @endif
    @else
        <p class="alert alert-danger">Invalid material request ID or material request not found.</p>
    @endif
@else
    <table class="table table-bordered table purchasesTable">
        <thead>
            <tr>
                <th class="text-center">Code</th>
                <th class="text-center">Name</th>
                <!-- <th class="text-center">Current Stock</th> -->
                <th class="text-center">MR Qty</th>
                <th class="text-center">MTV Qty</th>
                <th class="text-center">L.P.O</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <?php 
                    $mtv = \App\Models\DeliveryOrder::where('delivered_by', 'store')
                        ->where('product_id', $row->product_id)
                        ->where('transfer_id', $row->transfer_id)
                        ->sum('quantity');
                    $do_qty =\App\Models\DeliveryOrder::where('transfer_id', $row->transfer_id)->where('product_id', $row->product_id)->sum('quantity');
                ?>            
                <tr class="product">
                    <td>{{ $row->product->code }}</td>
                    <td>{{ $row->product->name }}</td>
                    <td>{{ $row->requested_quantity }}</td>
                    <td>{{ $mtv ?: 'Not Created' }}</td>
                    <td>
                        <div class="input-group">
                            <input type="hidden" name="product_id[]" value="{{ $row->product_id }}">
                            <input type="hidden" name="unit_id[]" value="{{ $row->unit_id }}">
                            <input type="number" id="quantities"  name="quantities[]" value="{{ $row->requested_quantity - $do_qty }}" class="form-control quantity-input">
                        </div>                       
                    </td>
                    <td><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a></td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
@endif
