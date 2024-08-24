@extends('inventory.layout')
@section('title', 'Create Material Request')
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
                                        <h5>Details</h5>
                                        <div class="row">
                                                                                       
                                            <div class="form-group col-sm-6">
                                                <label>Date <span class="text-red">*</span></label>
                                                <input type="date" class="form-control" name="date" id="material_request_date" value="" required>
                                            </div>        
                                        
                                            <div class="form-group col-sm-6">
                                                <label>Remarks</label>
                                                <textarea class="form-control" name="remarks" rows="1"></textarea>
                                            </div>   
                                              
                                        </div>

                                        <h5>Products</h5>
                                        
                                        <div id="productRows">
                                            <div class="row product-row">
                                                <div class="form-group col-sm-6">
                                                    <label><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a> 1: Product <span class="text-red">*</span></label>
                                                    <select class="form-control select product-select" name="product_id[]" onfocus="search(this)" required>
                                                        <option value="">Select Product</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-2">
                                                    <label>Quantity <span class="text-red">*</span></label>
                                                    <input type="number" class="form-control quantity" name="quantity[]" required>
                                                </div>                                                
                                                <div class="form-group col-sm-2">
                                                    <label>Unit <span class="text-red">*</span></label>
                                                    <select class="form-control" name="unit_id[]" required>
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
    updateCounters();
}
$(document).ready(function() {
    var productCounter = $('.product-row').length + 1;

    $('.add-row').click(function() {
        var newRow = $('<div class="row product-row">' +
            '<div class="form-group col-sm-6">' +
            '<label><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a> <span id="counter_' + productCounter + '">' + productCounter + '</span> Product <span class="text-red">*</span></label>' +
            '<select class="form-control select product-select" name="product_id[]" onfocus="search(this)" required>' +
            '<option value="">Select Product</option>' +
            '</select>' +
            '</div>' +
            '<div class="form-group col-sm-2">' +
            '<label>Quantity <span class="text-red">*</span></label>' +
            '<input type="number" class="form-control quantity" name="quantity[]" required>' +
            '</div>' +
            '<div class="form-group col-sm-2">' +
            '<label>Unit <span class="text-red">*</span></label>' +
            '<select name="unit_id[]" class="form-control" required><option value="">Default</option>@foreach($units as $unit)<option value="{{ $unit->id }}">{{ $unit->name }}</option>@endforeach</select>' +
            '</div>' +
            '<div class="form-group col-sm-2">' +
            '<label>Brand <span class="text-red">*</span></label>' +
            '<select name="brand_id[]" class="form-control" required><option value="">Default</option>@foreach($brands as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach</select>' +
            '</div></div>');

        $('#productRows').append(newRow);
        productCounter++;
        initSelect2(newRow);
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

    function updateCounters() {
        $('.product-row').each(function(index) {
            $(this).find('span[id^="counter_"]').text(index + 1);
        });
        productCounter = $('.product-row').length + 1; // Update productCounter
    }

    // Initialize counters on page load
    updateCounters();
});



    function search(field){
        $(field).select2({
            placeholder: 'Search for a product',
            minimumInputLength: 0, 
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
   
    // $(document).ready(function() {
    //     $('.product-select').select2({
    //         placeholder: 'Search for a product',
    //         minimumInputLength: 0, 
    //         ajax: {
    //             url: '{{ route('products.search') }}',
    //             dataType: 'json',
    //             delay: 0,
    //             processResults: function(data) {
    //                 return {
    //                     results: data
    //                 };
    //             },
    //             cache: true
    //         }
    //     });
    // });

    $(document).ready(function() {

        $('select[name="product_id[]"]').change(function() {
            var selectedProducts = $(this).val();
            var warehouse_id = $('#warehouse_id').val();

            if (warehouse_id === '' || warehouse_id < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: 'Select warehouse first.',
                });
                $(this).val([]); 
                //$('.product_ids').trigger('change');
                return false;
            }

            // if (selectedProducts.length > 0) {
            //     $.ajax({
            //         url: '{{url('/')}}/transfers/getTransferProducts?page=transfer.store._get_transfer_products',
            //         type: 'POST',
            //         data: { products: selectedProducts, warehouse_id: warehouse_id },
            //         dataType: 'text',
            //         success: function(response) {
            //             console.log(response);
            //             $('#tableData').html(response);
            //         },
            //         error: function(xhr, status, error) {
            //             console.log(error);
            //         }
            //     });
            // }
        });

        // $('#warehouse_id').change(function() {
        //     $('select[name="product_id[]"]').trigger('change');
        // }); 

    });

    handleFormSubmit('#submitForm', '{{ route('transfer.store') }}','POST', function(response) {
    }, function(error) {
        // 
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



function validateQuantity(input, maxQuantity) {
    if(maxQuantity != 0) {
        if (input.value > maxQuantity) {
            input.value = maxQuantity;
        }
    }
}
    
</script>
@endpush