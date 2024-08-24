@extends('inventory.layout')
@section('title', 'Create Material Request')
@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-truck bg-green"></i>
					<div class="d-inline">
						<h5>Material Issue Voucher</h5>
						<span>View, delete and update Material Issue Voucher</span>
					</div>
				</div>
			</div>
			<div class="col-lg-8">
			    <div class="text-right">
                    <a href="{{ url()->previous() }}" class="btn btn-warning"><span class="ik ik-arrow-left"></span> Back</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary ml-4"><span class="ik ik-home"></span> Dashboard</a>
                </div>
			</div>
		</div>
	</div>
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="">
                        
                        <form id="submitForm" method="POST" action="{{ route('stores.saveMaterialIssued') }}" enctype="multipart/form-data">
                            @csrf                           
                            <div class="card new-cust-card">
                                <div class="card-header">
                                    <h3>Create Material Issue Voucher</h3>                                        
                                </div>                                    
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="document">Attachment</label>
                                            <input type="file" class="form-control-file" name="document" id="document" accept="image/*">
                                        </div>  
                                        <div class="form-group col-md-4">
                                            <label for="address">Area / Building</label>
                                            <input type="text" class="form-control" name="address" id="address">
                                        </div>                                                                                         
                                        <div class="form-group col-md-4">
                                            <label for="date">Issue Date<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date" id="date" required>
                                        </div>     
                                    </div>   
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="userSearch">Employee Name<span class="text-danger">*</span></label>
                                            <select id="userSearch" class="form-control select2" name="user_id" required>
                                                <option value="">Select User</option>
                                                <!-- Populate with user options -->
                                            </select>
                                        </div>  
                                        <div class="form-group col-md-8">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control" name="remarks" id="remarks" rows="1" style="height:38px"></textarea>
                                        </div>                                             
                                    </div>  
                                    <div id="productRows">
                                        <div class="form-row product-row">
                                            <div class="form-group col-md-4">
                                                <label>Product <span class="text-danger">*</span></label>
                                                <select class="form-control select2 product-select" name="product_id[]" required>
                                                    <option value="">Select Product</option>
                                                    <!-- Populate with product options -->
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="quantity[]" required>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Current Stock</label>
                                                <p class="alert alert-secondary current-stock p-2">N/A</p>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="unit_id">Unit <span class="text-danger">*</span></label>
                                                <select name="unit_id[]" class="form-control" required>
                                                    @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="brand_id">Brand</label>
                                                <select name="brand_id[]" class="form-control" required>
                                                    @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>                                                
                                        </div>                                           
                                    </div>
                                    <div class="form-group text-left">
                                        <button type="button" class="btn btn-outline-danger btn-round add-row">+ Add More Products</button>
                                    </div>
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary saveBtn"><i class="fa fa-save"></i> Save Voucher</button>
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
        if ($('#productRows .product-row').length === 0) {
            $('.noproductsselected').show();
        } else {
            $('.noproductsselected').hide();
        }        
    }
    $(document).ready(function() {

        var counter = 1;

        $('.add-row').click(function() {
            counter++;
            var newRow = $('<div class="row product-row">' +
                '<div class="form-group col-sm-4">' +
                '<label><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a> '+ counter +': Product <span class="text-red">*</span></label>' +
                '<select class="form-control select2 product-select" name="product_id[]" required>' +
                '<option value="">Select Product</option>' +
                '</select>' +
                '</div>' +
                '<div class="form-group col-sm-2">' +
                '<label>Quantity <span class="text-red">*</span></label>' +
                '<input type="number" class="form-control" name="quantity[]" required>' +
                '</div>' +
                '<div class="form-group col-sm-2">' +
                '<label>Current Stock</label>' +
                '<p class="alert alert-secondary current-stock p-2">N/A</p>' +
                '</div>' +
                '<div class="form-group col-sm-2">' +
                '<label>Unit</label>' +
                '<select name="unit_id[]" class="form-control" required>' +
                '@foreach($units as $unit)' +
                '<option value="{{ $unit->id }}">{{ $unit->name }}</option>' +
                '@endforeach' +
                '</select>' +
                '</div>' +
                '<div class="form-group col-sm-2">' +
                '<label>Brand</label>' +
                '<select name="brand_id[]" class="form-control" required>' +
                '@foreach($brands as $brand)' +
                '<option value="{{ $brand->id }}">{{ $brand->name }}</option>' +
                '@endforeach' +
                '</select>' +
                '</div>' +                
                '</div>');

            $('#productRows').append(newRow);
            initSelect2(newRow);

            if ($('#productRows .product-row').length === 0) {
                $('.noproductsselected').show();
            } else {
                $('.noproductsselected').hide();
            }            
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
            }).on('select2:select', function(e) {
                var selectedProductId = e.params.data.id;
                var currentStockInput = $(this).closest('.product-row').find('.current-stock');

                // Check if the selected product ID is already selected in another row
                if ($('.product-select').filter(function() { return $(this).val() === selectedProductId; }).length > 1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Product already selected. Please select a different product.',
                    });
                    $(this).val('').trigger('change'); // Reset the select2 value
                    return false;
                }

                $.ajax({
                    url: '{{ route('products.current_stock') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: selectedProductId
                    },
                    success: function(response) {
                        var currentStock = response.current_stock;
                        currentStockInput.text(currentStock);
                        currentStockInput.removeClass('alert-danger');

                        if (currentStock === 0) {
                            currentStockInput.addClass('alert-danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        }

    });

    $(document).ready(function() {
        $('#productSearch').select2({
            placeholder: 'Search for a product',
            minimumInputLength: 0, 
            ajax: {
                url: '{{ route('stores.stocklist') }}',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    });

$(document).ready(function() {
    $('#userSearch').select2({
        placeholder: 'Search for a user',
        minimumInputLength: 0,
        ajax: {
            url: '{{ route('users.search') }}', 
            dataType: 'json',
            type: 'post',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });
});    

$(document).ready(function() {
    $('select[name="product_id[]"]').change(function() {
        var selectedProducts = $(this).val();
        if (selectedProducts.length > 0) {
            $.ajax({
                url: '{{url('/')}}/stores/material/issued/getProducts',
                type: 'get',
                data: { products: selectedProducts },
                dataType: 'html', // Change to 'html' as you expect HTML response
                success: function(response) {
                    $('#tableData').html(response);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }
    });
});

function validateQuantity(input, maxQuantity) {
    if (maxQuantity != 0) {
        if (input.value > maxQuantity) {
            input.value = maxQuantity;
        }
    }
}

function handleFormSubmit(formId, url, method, successCallback, errorCallback) {
    $(document).on('submit', formId, function (e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var errors = false;
        if ($('.product-select').filter(function() { return $(this).val() !== ''; }).length === 0) {
            errors = true;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select at least one product.',
            });
            return false;
        }        
        $('.product-row').each(function() {
            var productName = $(this).find('.product-select option:selected').text();
            var quantityInput = $(this).find('input[name="quantity[]"]');
            var currentStock = parseInt($(this).find('.current-stock').text());
            
            if (parseInt(quantityInput.val()) === 0 || quantityInput.val() === '') {
                errors = true;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Quantity of ' + productName + ' is 0 or empty. Please add more quantity or remove it.',
                });
                return false;
            } else if (parseInt(quantityInput.val()) > currentStock) {
                errors = true;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Quantity of ' + productName + ' exceeds current stock',
                });
                return false;
            }
        });
        
        if (errors) {           
            return false;
        }      

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.redirect && response.redirect !== '' && response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        html: response.message,
                    }).then(function () {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sorry!!',
                        html: response.message,
                    });
                }
                if (typeof successCallback === 'function') {
                    successCallback(response);
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = Object.values(errors).flat().join('<br>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessage,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sorry!!',
                        html: 'An Error Occurred',
                    });
                }
                if (typeof errorCallback === 'function') {
                    errorCallback(xhr.responseText);
                }
            }
        });
    });

    // Handle click on dynamically appended save button
    $(document).on('click', '.saveBtn', function(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: 'Your selected data will be saved!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Save it!',
            cancelButtonText: 'Cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $(formId).submit(); // Trigger form submission
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Swal.fire('Cancelled', 'Your data is safe :)', 'error');
            }
        });
    });
} 

$(document).ready(function() {
    handleFormSubmit('#submitForm', '{{ route('stores.saveMaterialIssued') }}', 'POST', function(response) {
        console.log(response);
        // Handle success response here
        Swal.fire({
            title: 'Success!',
            text: response.message,
            icon: 'success'
        }).then((result) => {
            // Redirect to a new page if necessary
            if (response.redirect) {
                window.location.href = response.redirect;
            }
        });
    }, function(error) {
        console.error(error);
        // Handle error response here
        Swal.fire({
            title: 'Error!',
            text: error.responseJSON.message,
            icon: 'error'
        });
    });
});

</script>

@endpush