@extends('inventory.layout')

@section('title', 'Edit Material Issued')

@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    <style>
        .product-row {
            border: 1px solid #eee;
            margin-bottom: 2px;
            padding: 10px; /* Added padding for better UI */
        }
        .product-row.focused {
            background-color: #f0f0f0; /* Focused background color */
        }
        .remove-btn {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                                <i class="ik ik-truck bg-green"></i>
                                <div class="d-inline">
                                    <h5>Edit Issue Voucher No. {{ $materialissued->first()->issue_order_no }}</h5>
                                    <span>View, delete, and update Material Issue Voucher</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-right">
                            @include('include.backButtons') <!-- Include back button partial -->
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        
                        <p class="alert alert-info">Note! After Return Quantity can not be restored!</p>

                        <form method="POST" action="{{ route('project.material_issued.update', encrypt($data->id)) }}" id="updateForm">
                            @csrf
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea class="form-control" name="remarks" rows="1">{{ $materialissued->first()->remarks }}</textarea>
                            </div>
                            <h5>Products</h5>
                            <div id="productRows">
                                @foreach($materialissued as $index => $row)
                                    @if($row->quantity > 0)
                                        <div class="row product-row">
                                            <div class="form-group col-sm-3">
                                                <label>Product {{ $index + 1 }} <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="product_id[]" required>
                                                    <option value="{{ $row->product->id }}" selected>{{ $row->product->name }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label>Quantity <span class="text-red">*</span></label>
                                                <input type="text" readonly class="form-control" name="quantity[]" value="{{ $row->quantity }}" required>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label>Return QTY <span class="text-red">*</span></label>
                                                <input type="number" class="form-control" name="return_quantity[]" value="{{ $row->quantity }}" required>
                                            </div>
                                            <div class="form-group col-sm-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger remove-btn" onclick="deleteItem(this)">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="form-group mt-4 text-right">
                                <button type="submit" class="btn btn-primary">Confirm Return & Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script>
        // Initialize select2 for dynamic elements
        $('.select2').select2();

        // Function to delete a product row
        function deleteItem(el) {
            $(el).closest('.product-row').remove();
        }

        // Handle form submission
        function handleFormSubmit(formSelector, url, method, successCallback, errorCallback) {
            $(formSelector).submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        if (successCallback) successCallback(response);
                    },
                    error: function(error) {
                        if (errorCallback) errorCallback(error);
                    }
                });
            });
        }
    </script>
@endpush
