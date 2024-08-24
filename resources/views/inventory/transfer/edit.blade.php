@extends('inventory.layout')
@section('title', 'Edit Material Request')
@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    <style>
        .product-row{
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
                                			<div class="col-lg-8">
                                				<div class="page-header-title">
                                					<i class="ik ik-truck bg-green"></i>
                                					<div class="d-inline">
                                						<h5>Edit Material Request No. {{ $transfer_request->transfer_no }}</h5>
                                						<span>View, delete and update Material Requisition</span>
                                					</div>
                                				</div>
                                			</div>
                                			<div class="col-lg-4">
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
                                        <h5>MR Details</h5>
                                        <div class="row">
                                            <div class="form-group col-sm-4">
                                                <label>Material Required No. <span class="text-red">*</span></label>
                                                <input type="number" class="form-control" id="material_request_no" name="transfer_no" value="{{ $transfer_request->transfer_no }}">
                                            </div>                                             
                                            <div class="form-group col-sm-4">
                                                <label>Material Required Date <span class="text-red">*</span></label>
                                                <input type="date" class="form-control" name="date" id="material_request_date" value="{{ $transfer_request->date }}">
                                            </div>        
                                        
                                            <div class="form-group col-sm-4">
                                                <label>Remarks</label>
                                                <textarea class="form-control" name="remarks" rows="1"> {{ $transfer_request->remarks }}</textarea>
                                            </div>    
                                        </div>
                                        <hr>
                                        <h5>Products</h5>
                                        
                                        <div id="productRows">
                                            @php $i=1;@endphp
                                            @foreach($transfer_request->details as $row)
                                            <div class="row product-row">
                                                <input type="hidden" value="{{ $row->transfer_id }}" name="transfer_no[]">

                                                <div class="form-group col-sm-4 select_items">
                                                    <label><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a> {{ $i++ }}: Product <span class="text-red">*</span></label>
                                                    <select class="form-control select2 product-select" name="product_id[]" required>
                                                        <option value="{{ $row->product->id }}" selected>{{ $row->product->name }}</option>
                                                    </select>                                                    
                                                </div>

                                                <div class="form-group col-sm-4">
                                                    <label>Quantity <span class="text-red">*</span></label>
                                                    <input type="number" class="form-control quantity" name="quantity[]" value="{{ $row->requested_quantity }}" required>
                                                </div>
                                   
                                                <div class="form-group col-sm-4">
                                                    <label>Units <span class="text-red">*</span></label>
                                                    <select name="unit_id[]" class="form-control" required>
                                                        @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}" {{ $unit->id == $row->unit_id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>                                                
                                            </div>
                                            @endforeach
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
$(document).ready(function() {
    var productCounter = 2;

    $('.add-row').click(function() {
        var counter = {{ count($transfer_request->details) + 1 }};
        var newRow = $('<div class="row product-row">' +
            '<input type="hidden" name="transfer_no[]" value="{{ $transfer_request->id }}">' +
            '<div class="form-group col-sm-4">' +
            '<label><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a> '+ counter +': Product <span class="text-red">*</span></label>' +
            '<select class="form-control select2 product-select" name="product_id[]" required>' +
            '<option value="">Select Product</option>' +
            '</select>' +
            '</div>' +
            '<div class="form-group col-sm-4">' +
            '<label>Quantity <span class="text-red">*</span></label>' +
            '<input type="number" class="form-control" name="quantity[]" required>' +
            '</div>' +
            '<div class="form-group col-sm-4">' +
            '<label>Unit <span class="text-red">*</span></label>' +
            '<select name="unit_id[]" class="form-control" required>' +
            '@foreach($units as $unit)' +
            '<option value="{{ $unit->id }}">{{ $unit->name }}</option>' +
            '@endforeach' +
            '</select>' +
            '</div>' +
            '</div>');
    
        $('#productRows').append(newRow);
        initSelect2(newRow);
    });
    $('.product-select').each(function() {
        initSelect2($(this).closest('.product-row'));
    });    
    // Initialize select2 for the first product row
    initSelect2($('.product-row:first'));

    // Initialize select2 for a given row
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
        });
    }

});

    handleFormSubmit('#submitForm', '{{ route('transfer.update', encrypt($transfer_request->id)) }}','POST', function(response) {
    }, function(error) {
        console.error(error);
    }); 

$(document).ready(function() {
  handleSaveAction('.saveBtn', function(response) {
    // Handle success response here
  }, function(error) {
    // Handle error response here
  });
});

function handleSaveAction(saveSelector, successCallback, errorCallback) {
    $(document).on('click', saveSelector, function(event) {
        event.preventDefault();

        // Disable the button
        $(this).prop('disabled', true);

        // Check if any product or quantity fields are empty
        var emptyFields = false;
        $('.product-select, .quantity').each(function() {
            if ($(this).val() === '') {
                emptyFields = true;
                return false; // Exit the loop early
            }
        });

        // Check if any other required fields are empty
        if ($('#material_request_no').val() === '' || $('#material_request_date').val() === '') {
            emptyFields = true;
        }

        if (emptyFields) {
            Swal.fire('Error', 'Please fill in all required fields.', 'error');
            $(this).prop('disabled', false); // Re-enable the button
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
                $(this).prop('disabled', false);
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Re-enable the button if the user cancels
                $(this).prop('disabled', false);
            }
        });
    });
}




function validateQuantity(input, maxQuantity) {
    if(maxQuantity != 0) {
        if (input.value > maxQuantity) {
            input.value = maxQuantity;
        }
    }
}
    
</script>
@endpush