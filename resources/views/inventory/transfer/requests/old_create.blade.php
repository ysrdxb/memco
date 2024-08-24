@extends('inventory.layout')
@section('title', 'Create Material Request')
@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    <style>
        .product-row {
            border: 1px solid #eee;
            padding-bottom: 0px;
            margin-bottom: 2px;
        }

        .product-row.focused {
            background-color: #f0f0f0; /* Change to your desired background color */
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="">
                        <form class="forms-sample" id="submitForm" method="POST" action="javascript:;">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="page-header">
                                        <div class="row align-items-end">
                                            <div class="col-lg-4">
                                                <div class="page-header-title">
                                                    <i class="ik ik-truck bg-green"></i>
                                                    <div class="d-inline">
                                                        <h5>Material Request</h5>
                                                        <span>View, delete and update Material Requisition</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="text-right">
                                                @include('include.backButtons') 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3>General</h3>
                                            <div class="form-group text-right" style="width:100%;float:right">
                                                <button type="button" class="btn btn-primary saveBtn">Save</button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row d-none">
                                                <div class="form-group col-sm-12">
                                                    <label>Document</label>
                                                    <input type="file" class="form-control" name="file" id="file">
                                                </div>
                                            </div>
                                            <h5>MR Details</h5>
                                            <div class="row">
                                                <div class="form-group col-sm-3">
                                                    <label for="">L.P.O Applicable</label>
                                                    <select name="purchase_applicable" id="purchase_applicable" class="form-control">
                                                        <option value="1">Yes can create L.P.O</option>
                                                        <option value="0">No, Just for Main Store M.T.V</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-3">
                                                    <label>Material Request No.</label>
                                                    <input type="number" class="form-control" id="material_request_no"
                                                        name="transfer_no" value="">
                                                </div>
                                                <div class="form-group col-sm-3">
                                                    <label>Material Request Date <span class="text-red">*</span></label>
                                                    <input type="date" class="form-control" name="date"
                                                        id="material_request_date" value="" required>
                                                </div>

                                                <div class="form-group col-sm-3">
                                                    <label>Remarks</label>
                                                    <textarea class="form-control" name="remarks"
                                                        rows="1"></textarea>
                                                </div>
                                            </div>
                                            @if(1==2 && !Auth::user()->hasRole('Store Incharge'))
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button class="btn btn-primary" type="button" data-toggle="collapse"
                                                        data-target="#lpoDetails" aria-expanded="false"
                                                        aria-controls="lpoDetails">
                                                        Add Purchase Details
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="collapse" id="lpoDetails">
                                                <hr>
                                                <h5>LPO Details</h5>
                                                <div class="row">
                                                    <div class="form-group col-sm-4">
                                                        <label>Supplier <span class="text-red"></span></label>
                                                        <select class="form-control select2" name="supplier_id">
                                                            <option value="">Select</option>
                                                            @foreach($suppliers as $supplier)
                                                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-4">
                                                        <label>LPO No. <span class="text-red"></span></label>
                                                        <input type="number" class="form-control" id="purchase_no"
                                                            name="purchase_no" value="">
                                                    </div>
                                                    <div class="form-group col-sm-4">
                                                        <label>LPO Date <span class="text-red"></span></label>
                                                        <input type="date" class="form-control" name="purchase_date"
                                                            id="purchase_date" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            @endif
                                            <h5>Products</h5>

                                            <div id="productRows">
                                                <div class="row product-row">
                                                    <div class="form-group col-sm-4">
                                                        <label>1: Product <span class="text-red">*</span></label>
                                                        <select class="form-control select product-select"
                                                            name="product_id[]" onfocus="search(this)" required>
                                                            <option value="">Select Product</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label>MR Quantity <span class="text-red">*</span></label>
                                                        <input type="number" class="form-control quantity"
                                                            name="quantity[]" required>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label>Current Stock <span class="text-secondary"></span></label>
                                                        <div class="current-stock"></div>
                                                    </div>
                                                <div class="form-group col-sm-2">
                                                    <label>Unit <span class="text-red">*</span></label>
                                                    <select class="form-control select2" name="unit_id[]" required>
                                                        <option value="">Default</option> 
                                                        @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>                                                    
                                                    <div class="form-group col-sm-2">
                                                        <label>Brand <span class="text-red">*</span></label>
                                                        <select class="form-control select2" name="brand_id[]" required>
                                                            <option value="">Default</option> 
                                                            @foreach($brands as $brand)
                                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>                                                     
                                                    @if(Auth::user()->type=='data_entry')
                                                    <div class="form-group col-sm-2">
                                                        <label>Make LPO <span class="text-secondary">(Optional)</span></label>
                                                        <select class="form-control" name="makeLPO[]" required>
                                                            <option value="0">No</option>
                                                            <option value="1">Yes</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-group col-sm-2">
                                                        <label>LPO Quantity <span class="text-secondary">(Optional)</span></label>
                                                        <input type="number" class="form-control" name="lpo_qty[]" required>
                                                    </div>
                                                    @else 
                                                        <input type="hidden" name="makeLPO[]" value="0">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group mt-2">
                                                <a href="javascript:;" class="text-warning add-row" style="border:1px solid;border-radius:25px;padding:3px 21px;">+ Add More Products</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script>
function deleteItem(element) {
    $(element).closest('.product-row').remove();
}

// Initialize select2 for the first product row
function initSelect2(row) {
    row.find('.product-select').select2({
        ajax: {
            url: '{{ route('products.search') }}',
            dataType: 'json',
            delay: 0,
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        var product_id = e.params.data.id;
        var currentStockInput = $(this).closest('.product-row').find('.current-stock');

        $.ajax({
            url: '{{ route('products.current_stock') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: product_id
            },
            success: function(response) {
                currentStockInput.text(response.current_stock);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

}



// Initialize select2 for all product rows
function initAllSelect2() {
    $('.product-row').each(function() {
        initSelect2($(this));
    });
}

// Add a new product row
function addProductRow() {
    var productCounter = $('.product-row').length + 1;
    var newRow = $('<div class="row product-row">' +
    '<div class="form-group col-sm-4">' +
    '<label><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a>' + productCounter + ': Product <span class="text-red">*</span></label>' +
    '<select class="form-control select product-select" name="product_id[]" onfocus="search(this)" required>' +
    '<option value="">Select Product</option>' +
    '</select>' +
    '</div>' +
    '<div class="form-group col-sm-2">' +
    '<label>Quantity <span class="text-red">*</span></label>' +
    '<input type="number" class="form-control quantity" name="quantity[]" required>' +
    '</div>' +
    '<div class="form-group col-sm-2">' +
    '<label>Current Stock <span class="text-secondary"></span></label>' +
    '<div class="current-stock"></div>' +
    '</div>' +
    '<div class="form-group col-sm-2">' +
    '<label>Unit <span class="text-red">*</span></label>' +
    '<select class="form-control" name="unit_id[]" required>' +
    '<option value="">Default</option>'+
    @foreach($units as $unit)
    '<option value="{{ $unit->id }}">{{ $unit->name }}</option>' +
    @endforeach
    '</select>' +
    '</div>' +
    '<div class="form-group col-sm-2">' +
    '<label>Brand <span class="text-red">*</span></label>' +
    '<select class="form-control" name="brand_id[]" required>' +
    '<option value="">Default</option>'+
    @foreach($brands as $brand)
    '<option value="{{ $brand->id }}">{{ $brand->name }}</option>' +
    @endforeach
    '</select>' +
    '</div>' +
    '</div>');


    if ('{{ Auth::user()->type }}' === 'data_entry') {
        newRow.append('<div class="form-group col-sm-2">' +
            '<label>Make LPO <span class="text-secondary">(Optional)</span></label>' +
            '<select name="makeLPO[]" class="form-control" required><option value="0">No</option><option value="1">Yes</option></select>' +
            '</div>' +
            '<div class="form-group col-sm-2">' +
            '<label>LPO Quantity <span class="text-secondary">(Optional)</span></label>' +
            '<input type="number" class="form-control" name="lpo_qty[]" required>' +
            '</div>');
    } else {
        newRow.append('<input type="hidden" name="makeLPO[]" value="0"><input type="hidden" name="lpo_qty[]" value="0">');
    }

    newRow.append('</div>');

    $('#productRows').append(newRow);
    initSelect2(newRow);
}

// Event handler for adding a new product row
$('.add-row').click(function() {
    addProductRow();
});

// Initialize select2 for existing rows and add event handlers
$(document).ready(function() {
    initAllSelect2();

    handleFormSubmit('#submitForm', '{{ route('transfer.save') }}', 'POST', function(response) {
    }, function(error) {
        console.error(error);
    });

    handleSaveAction('.saveBtn', function(response) {
        // Handle success response here
    }, function(error) {
        // Handle error response here
    });
});

// Toggle LPO details section
$('#toggleLpoDetails').click(function() {
    $('#lpoDetails').collapse('toggle');
});

// Form submission confirmation
function handleSaveAction(saveSelector, successCallback, errorCallback) {
    $(document).on('click', saveSelector, function(event) {
        event.preventDefault();

        // Check if any product or quantity fields are empty
        var emptyFields = false;
        $('.product-select, .quantity').each(function() {
            if ($(this).val() === '') {
                emptyFields = true;
                return false; // Exit the loop early
            }
        });

        if (emptyFields) {
            Swal.fire('Error', 'Please fill in all required fields.', 'error');
            return false;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'Your selected data will be saved!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Save it!',
            cancelButtonText: 'Cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#submitForm').submit(); // Trigger form submission
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Swal.fire('Cancelled', 'Your data is safe :)', 'error');
            }
        });
    });
}
 
</script>
@endpush