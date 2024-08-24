@if(empty($products))
<p class="alert alert-warning">No product found.</p>
{{ exit; }}
@endif
@php $i = 1; @endphp
<table class="table table-bordered table-stripped table-hovered">
    <thead>
        <tr>
            <th>#</th>
            <th>Product Code</th>
            <th>Product Name</th>
            <th>Activity</th>
            <th>SubActivity</th>
            <th>Brand</th>
            <th>Unit</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)

        <tr>
            <input type="hidden" name="product_id[]" value="{{ $product['id'] }}">
            <td>{{ $i++ }}</td>
            <td><input readonly type="text" name="product_code[]" class="form-control" value="{{ $product['code'] }}"></td>
            <td><input readonly type="text" name="name[]" value="{{ $product['name'] }}" class="form-control"></td>
            <td><input readonly type="text" name="activity[]" value="{{ $product['activity'] }}" class="form-control"></td>
            <td><input readonly type="text" name="subactivity[]" value="{{ $product['subactivity'] }}" class="form-control"></td>
            <td><input readonly type="text" name="brand[]" value="{{ $product['brand'] }}" class="form-control"></td>
            <td><input readonly type="text" name="unit[]" value="{{ $product['unit'] }}" class="form-control"></td>
            <td><input readonly type="text" name="quantity[]" value="{{ $product['quantity'] }}" class="form-control"></td>
        </tr>
        @endforeach        
    </tbody>
</table>


<div class="form-group text-right">
    <button type="button" class="btn btn-success saveBtn">Save Products</button>
</div>