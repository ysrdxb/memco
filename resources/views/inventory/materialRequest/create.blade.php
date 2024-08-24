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
                                <div class="col-md-8">
                                	<div class="page-header">
                                		<div class="row align-items-end">
                                			<div class="col-lg-8">
                                				<div class="page-header-title">
                                					<i class="ik ik-truck bg-green"></i>
                                					<div class="d-inline">
                                						<h5>Material Request</h5>
                                						<span>View, delete and update Material Requisition</span>
                                					</div>
                                				</div>
                                			</div>
                                			<div class="col-lg-4">
                                			    <div class="text-right">
                                                    <a href="{{ url()->previous() }}" class="btn btn-secondary"><span class="ik ik-arrow-left"></span> Back</a>
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
                                        <!--<div class="row">-->
                                         
                                        <!--    <div class="form-group col-sm-6">-->
                                        <!--        <label>To Warehouse <span class="text-red">*</span></label>-->
                                        <!--        <select class="form-control select2" name="warehouse_id" required>-->
                                        <!--            @foreach($warehouses as $warehouse)-->
                                        <!--                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>-->
                                        <!--            @endforeach-->
                                        <!--        </select>-->
                                        <!--    </div>                                                                                      -->
                                                
                                            
                                        <!--    <div class="form-group col-sm-6">-->
                                        <!--        <label>Document</label>-->
                                        <!--        <input type="file" class="form-control" name="file" id="file">-->
                                        <!--    </div>    -->
                                            
                                        <!--    <div class="form-group col-sm-4">-->
                                        <!--        <label>Old Material Required No. <span class="text-red">*</span></label>-->
                                        <!--        <input type="number" class="form-control" name="material_request_no" value="">-->
                                        <!--    </div>                                             -->
                                        
                                        <!--    <div class="form-group col-sm-4">-->
                                        <!--        <label>Material Required Date <span class="text-red">*</span></label>-->
                                        <!--        <input type="date" class="form-control" name="date" id="date" value="">-->
                                        <!--    </div>        -->

                                        <!--    <div class="form-group col-sm-4">-->
                                        <!--        <label>Remarks</label>-->
                                        <!--        <textarea class="form-control" name="remarks" rows="1"></textarea>-->
                                        <!--    </div>   -->
                                            
                                        <!--    <div class="col-sm-12">-->
                                        <!--        <div class="form-group"> -->
                                        <!--            <label for="product">Select Product(s)</label>-->
                                        <!--            <select id="productSearch" class="form-control product_ids" name="product_id[]" multiple="multiple"></select>                                               -->
                                        <!--        </div>-->
                                        <!--    </div>                                            -->
                                        <!--</div>      -->
                                        
                                        <div class="row d-none">
                                            <div class="form-group col-sm-12">
                                                <label>Document</label>
                                                <input type="file" class="form-control" name="file" id="file">
                                            </div>
                                        </div>
                                        <h5>MR Details</h5>
                                        <div class="row">
                                            <div class="form-group col-sm-4">
                                                <label>Material Required No. <span class="text-secondary">(Optional)</span></label>
                                                <input type="number" class="form-control" id="material_request_no" name="transfer_no" value="">
                                            </div>                                             
                                            <div class="form-group col-sm-4">
                                                <label>Material Required Date <span class="text-red">*</span></label>
                                                <input type="date" class="form-control" name="date" id="material_request_date" value="" required>
                                            </div>        
                                        
                                            <div class="form-group col-sm-4">
                                                <label>Remarks</label>
                                                <textarea class="form-control" name="remarks" rows="1"></textarea>
                                            </div>   
                                              
                                        </div>

                                        <h5>Products</h5>
                                        
                                        <div id="productRows">
                                            <div class="row product-row">
                                                <div class="form-group col-sm-6">
                                                    <label>1: Product <span class="text-red">*</span></label>
                                                    <select class="form-control select product-select" name="product_id[]" onfocus="search(this)" required>
                                                        <option value="">Select Product</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-2">
                                                    <label>MR Quantity <span class="text-red">*</span></label>
                                                    <input type="number" class="form-control quantity" name="quantity[]" required>
                                                </div>
                                                <div class="form-group col-sm-2">
                                                    <label>Make LPO <span class="text-red">*</span></label>
                                                    <select class="form-control" name="makeLPO[]" required>
                                                        <option value="1">Yes</option> 
                                                        <option value="0">No</option>
                                                    </select>
                                                </div>   
                                                <div class="form-group col-sm-2">
                                                    <label>LPO Quantity <span class="text-red">*</span></label>
                                                    <input type="number" class="form-control" name="lpo_qty[]" required>
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
}   
$(document).ready(function() {
        var productCounter = 2;

        $('.add-row').click(function() {
            var newRow = $('<div class="row product-row">' +
                '<div class="form-group col-sm-6">' +
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
                '<label>Make LPO <span class="text-red">*</span></label>' +
                '<select name="makeLPO[]" class="form-control" required><option value="1">Yes</option><option value="0">No</option></select>' +
                '</div>' +
                '<div class="form-group col-sm-2">' +
                '<label>LPO Quantity <span class="text-red">*</span></label>' +
                '<input type="number" class="form-control" name="lpo_qty[]" required>' +
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
        
        // Check if any other required fields are empty
        if ($('#purchase_no').val() === '' || $('#material_request_no').val() === '' || $('#material_request_date').val() === '' || $('#purchase_date').val() === '') {
            emptyFields = true;
        }

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