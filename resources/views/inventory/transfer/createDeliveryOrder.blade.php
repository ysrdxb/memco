@extends('inventory.layout')
@push('head')
<style>
.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    color: #e53935;
    border-color: #dee2e6 #dee2e6 #fff;
}

/* Hide the browser's default checkbox */
.select-box input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
    position: absolute;
    left: -27px;
    height: 24px;
    width: 25px;
    background-color: #eee;
}

/* Style the checkmark when the checkbox is checked */
.select-box input:checked + .checkmark {
  background-color: #2196F3; /* Change this to the desired color */
}

/* Create the checkmark icon */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark icon when the checkbox is checked */
.select-box input:checked + .checkmark:after {
  display: block;
}

/* Style the checkmark icon */
.select-box .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  transform: rotate(45deg);
}

.multiselect-container{
    max-height:200px;
    overflow-y:scroll;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" />
@endpush
@section('title', 'Create Delivery Order')
@section('content')
<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Delivery Orders')}}</h5>
						<span>{{ __('Create delivery order')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
            @include('include.backButtons')
            </div>
		</div>
	</div>
	
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="lpo-tab" data-toggle="tab" href="#lpo" role="tab" aria-controls="lpo" aria-selected="true"><strong>Create Delivery Order</strong></a>
                        </li>
                    @if(Auth::user()->type != 'data_entry' && !Auth::user()->hasRole('Store Incharge'))
                        <li class="nav-item">
                            <a class="nav-link" id="no-lpo-tab" data-toggle="tab" href="#no-lpo" role="tab" aria-controls="no-lpo" aria-selected="false"><strong>Create Transfer Voucher</strong></a>
                        </li>
                    @endif
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="lpo" role="tabpanel" aria-labelledby="lpo-tab">
                            <!-- First section -->
                            <form id="submitForm">
                                <div class="col-md-12">
                                    <!-- First section -->
                                    <div class="row">
                                        <div class="form-group col-sm-2">
                                            <label for="datepicker">Delivery Order No.</label>
                                            <input autocomplete="off" type="number" name="delivery_order_no" class="form-control" id="delivery_order_no" placeholder="D.O Number">
                                        </div>                                        
                                        <div class="form-group col-sm-2">
                                            <label for="datepicker">Date</label>
                                            <input autocomplete="off" type="text" name="date" class="form-control datetimepicker-input" id="datepicker" data-toggle="datetimepicker" data-target="#datepicker" placeholder="Select Date">
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label for="material_request_id">MR No.</label>
                                            <select name="material_request_id" id="material_request_id" class="form-control select2">
                                                <option value="">Select</option>
                                                @foreach($purchases as $request)
                                                    @if($request->transfer->status !== 'delivered')
                                                        <option value="{{ encrypt($request->transfer->id) }}">{{ $request->transfer->transfer_no }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="supplier_id">Purchase Order</label>
                                            <select class="form-control select2" name="purchase_id">
                                                <option value="">Select L.P.O</option>
                                                @foreach($purchases as $purchase)
                                                    <option value="{{ $purchase->id }}">{{ $purchase->purchase_no }}</option>
                                                @endforeach
                                            </select>
                                        </div>                                
                                        <div class="form-group col-sm-3">
                                            <label for="notes">Delivery Note</label>
                                            <textarea class="form-control" name="delivery_note" rows="1" placeholder="Enter Note"></textarea>
                                        </div>                                        
                                    </div>
                                    <div id="productRows"></div>                                   
                                </div>                                
                            </form>
                        </div>
                        @if(Auth::user()->type !='data_entry' && !Auth::user()->hasRole('Store Incharge'))
                        <div class="tab-pane fade" id="no-lpo" role="tabpanel" aria-labelledby="no-lpo-tab">
                            <!-- Second section -->
                            <form id="submitForm2">
                                <div class="col-md-12">
                                    <div id="tab2Products">
                                        <div class="row mt-2">
                                        <div class="form-group col-md-4">
                                            <label for="supplier_id">Select Project</label>
                                            <select class="form-control select2" name="project_id" required>
                                                <option value="">Select</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                        <div class="form-group col-md-4">
                                            <label for="supplier_id">Material Request No</label>
                                            <select class="form-control select2 transfer_request_id" onchange="updateProductRows()" name="via_transfer_id">
                                                <option value="">Select</option>
                                                @foreach($transfers as $row)
                                                    <option value="{{ $row->id }}">{{ $row->transfer_no }}</option>
                                                @endforeach
                                            </select>
                                        </div>    
                                        <div class="form-group col-md-4">
                                            <label>Requested By</label>
                                            <select class="form-control" name="requested_by"><option value="">Select User</option>@foreach($users as $user)<option value="{{$user->id}}">{{$user->name}}@endforeach</select>
                                        </div>  
                                        </div>

                                        <div id="addFields"></div>
                                        <div class="form-group mt-4 add_products">
                                            <a href="javascript:;" class="text-warning add-row" style="border:1px solid;border-radius:25px;padding:3px 21px;">+ Add More Products</a>
                                        </div>  
                                        <div class="form-group text-right mt-4">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>                                                                             
                                    </div>
                                </div>                                
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<script>


    function confirmDeleteItem(element) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Item will be removed from the list!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteItem(element);
            }
        });
    }
    function deleteItem(element) {
        $(element).closest('.product-row').remove();
    }  
    function submitForm(input) {
        var form = input.closest('form');
        form.submit();
    }    
    @if(Auth::user()->type !='data_entry' && !Auth::user()->hasRole('Store Incharge')) 
        function resetProject() {
            $('#tab2Products').empty();
            var newRow = $('<div class="row">' +
                '<div class="col-md-4">' +
                '<div class="form-group productRow">' +
                '<label for="supplier_id">Select Project</label>' +
                '<select class="form-control select2" name="project_id" required>' +
                '<option value="">Select</option>@foreach($projects as $row)<option value="{{ $row->id }}"> {{ $row->name }} </option>@endforeach' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-4">' +
                '<div class="form-group">' +
                '<label for="supplier_id">Material Request No</label>' +
                '<select class="form-control select2 transfer_request_id" onchange="updateProductRows()" name="via_transfer_id">' +
                '<option value="">Select</option>@foreach($transfers as $row)<option value="{{ $row->id }}"> {{ $row->transfer_no }} </option>@endforeach' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-4">' +
                '<div class="form-group">' +
                '<label>Requested By</label>' +
                '<select class="form-control" name="requested_by"><option value="">Select User</option>@foreach($users as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach</select>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-md-12">' +
                '<div id="addFields"></div>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-md-12">' +
                '<div class="form-group mt-4">' +
                '<a href="javascript:;" class="text-warning add-row" style="border:1px solid;border-radius:25px;padding:3px 21px;">+ Add More Products</a>' +
                '</div><div class="form-group text-right mt-4"><button type="submit" class="btn btn-success">Save</button></div>' +
                '</div>' +
                '</div>' +
                '</div>');
            $('#tab2Products').append(newRow);
            $('#submitForm2')[0].reset();
            initSelect2(newRow);
        }
        
        var productCounter = 2;

        // Event delegation for the "Add More Products" button
        $(document).on('click', '.add-row', function() {
            var newRow = $('<div class="row product-row">' +
                '<div class="form-group col-sm-4">' +
                '<label><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a>Select Product <span class="text-red">*</span></label>' +
                '<select class="form-control select2 product-select2" name="product_id[]" onfocus="searchByStock(this)" required>' +
                '<option value="">Select Product</option>' +
                '</select>' +
                '</div>' +
                '<div class="form-group col-sm-1">' +
                '<label>Select Unit <span class="text-red">*</span></label>' +
                '<select class="form-control select2" name="unit_id[]">' +
                '<option value="">Unit</option>' +
                '@foreach($units as $unit)' +
                '<option value="{{ $unit->id }}">{{ $unit->name }}</option>' +
                '@endforeach' +
                '</select>' +
                '</div>' +
                '<div class="form-group col-sm-1">' +
                '<label>Type <span class="text-red">*</span></label>' +
                '<input type="text type" readonly class="form-control type" name="type[]" required>' +
                '</div>' +       
                '<div class="form-group col-sm-2 fetchToolSerial"></div>' +
                '<div class="form-group col-sm-1">' +
                '<label>Quantity <span class="text-red">*</span></label>' +
                '<input type="number" class="form-control quantity" name="quantity[]" required>' +
                '</div>' +
                '<div class="form-group col-sm-2"><label>Current Stock <span class="text-secondary"></span></label><div class="w-100 text-center bg-info"><span  class="current-stock text-white">N/A</span></div></div>' +                
                '</div>');
        
            // Append the new row to the #addFields container
            $('#addFields').append(newRow);
            // Initialize select2 for the new row
            initSelectStock(newRow);
        });
        
        function updateProductRows() {
            var via_transfer_id = $('select[name="via_transfer_id"]').val();
            $('select[name="product_id[]"]').empty();
            $('#tab2Products').html('');
            if (via_transfer_id) {
                // Fetch purchases based on the selected material_request_id
                $.ajax({
                    url: '{{ route('transfer.getProductsForTransfer') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        via_transfer_id: via_transfer_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Replace the content of #productRows with the rendered HTML
                        $('#tab2Products').html(response.html);
                        
                        $('select[name="tool_serial[]"]').multiselect({
                            nonSelectedText: 'Select Serials',
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true,
                            buttonWidth: '100%'
                        });   

                        $(document).on('change', '#tool_serial', function() {
                            var selectedSerials = $(this).val() || [];
                            var totalSerials = selectedSerials.length;
                            $(this).closest('.quantity-row').find('.quantity').val(totalSerials);
                        });
                        
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(error);
                    }
                });
            } else {
                // Clear the purchase selection dropdown if no material request is selected
                $('select[name="purchase_id"]').empty().trigger('change');
            }
        }        

        @endif

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

        function initSelectStock(row) {
            row.find('.product-select2').select2({
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
                    url: '{{ route('products.current_stock_warehouse') }}',
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
        
    function searchByStock(field) {
        $(field).select2({
            placeholder: 'Search for a product',
            minimumInputLength: 0,
            ajax: {
                url: '{{ route('products.searchByStock') }}',
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
    
    // Function to fetch purchases based on the selected material request
    $('select[name="material_request_id"]').on('change', function() {
        var materialRequestId = $(this).val();
        $('select[name="product_id[]"]').empty();
        $('#tableData').html(''); // Clear existing product rows
        if (materialRequestId) {
            // Fetch purchases based on the selected material_request_id
            $.ajax({
                url: '{{ route('transfer.getPurchasesForTransfer') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Add CSRF token for Laravel POST requests
                    material_request_id: materialRequestId
                },
                dataType: 'json',
                success: function(response) {
                    // Update the purchase selection dropdown with the fetched purchases
                    var select = $('select[name="purchase_id"]');
                    select.empty();
                    $.each(response, function(index, purchase) {
                        select.append('<option value="' + purchase.id + '">' + purchase.purchase_no + '</option>');
                    });
                    select.trigger('change'); // Trigger change event to update select2
                    selectAll();
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(error);
                }
            });
        } else {
            // Clear the purchase selection dropdown if no material request is selected
            $('select[name="purchase_id"]').empty().trigger('change');
        }
    });

    // Function to fetch products based on the selected purchase
    $('select[name="purchase_id"]').on('change', function() {
        var purchaseId = $(this).val();
        $('#productRows').html(''); // Clear existing product rows
        if (purchaseId) {
            // Fetch products based on the selected purchase_id
            $.ajax({
                url: '{{ route('transfer.getProductsForPurchase') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Add CSRF token for Laravel POST requests
                    purchase_id: purchaseId
                },
                success: function(response) {
                    $('#productRows').html(response); // Replace the content of #productRows with the rendered HTML        
                    selectAll();                               
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(error);
                }
            });
        }
    });

    function selectAll()
    {
        document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('click', function() {
                    var selectAll = document.getElementById('select-all');
                    var allChecked = true;
                    document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
                        if (!checkbox.checked) {
                            allChecked = false;
                        }
                    });
                    selectAll.checked = allChecked;
                });
            });

            document.getElementById('select-all').addEventListener('click', function() {
                var checkboxes = document.querySelectorAll('.product-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = document.getElementById('select-all').checked;
                });
            });        
    }    

    $(document).ready(function() {

        function handleFormSubmitWithConfirmation(formSelector, url, method, successCallback, errorCallback) {
            $(document).on('submit', formSelector, function(event) {
                event.preventDefault(); // Prevent default form submission

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Your selected data will be saved!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Save it!',
                    cancelButtonText: 'Cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with form submission
                        $.ajax({
                            url: url,
                            method: method,
                            data: $(this).serialize(),
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        html: response.message,
                                    });
                                    if (response.redirect && response.redirect !== '') {
                                        window.location.href = response.redirect;
                                    }
                                } else if (response.status === 'error') {                                    
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Success',
                                        html: response.message,
                                    });                                    
                                } else {
                                    // Error message
                                    swal.fire({
                                        title: 'Error',
                                        text: "Invalid response",
                                        icon: 'error',
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire('Error', 'An error occurred while processing your request.', 'error');
                                if (errorCallback) {
                                    errorCallback(error);
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Swal.fire('Cancelled', 'Your data is safe :)', 'error');
                    }
                });
            });
        }

        handleFormSubmitWithConfirmation('#submitForm', '{{ route('deliveryOrder.save') }}','POST', function(response) {
            //
        }, function(error) {
            //
        });

        @if(Auth::user()->type != 'data_entry')
        handleFormSubmitWithConfirmation('#submitForm2', '{{ route('deliveryOrderWithoutLPO.save') }}','POST', function(response) {
            //
        }, function(error) {
            //
        });  
        @endif
    
        
        $('select[name="product_id[]"]').change(function() {
            var selectedProducts = $(this).val();

            if (selectedProducts.length > 0) {
                $.ajax({
                    url: '{{route('transfer.getProductTable')}}',
                    type: 'POST',
                    data: { products: selectedProducts, material_request_id: $('select[name="material_request_id"]').val() },
                    dataType: 'text',
                    success: function(response) {
                        //console.log(response);
                        $('#tableData').html(response);
                    },
                    error: function(xhr, status, error) {
                        //console.log(error);
                    }
                });
            }
        });

        $('#warehouse_id').change(function() {
            $('select[name="product_id[]"]').trigger('change');
        });    
        
    });

    function updateSubtotalAndGrandTotal(val) {
        var subtotal = 0;
        $('.purchasesTable tbody tr').each(function() {
            var quantity = val;
            var price = parseFloat($(this).find('td:eq(3)').text().replace('AED ', ''));
            var rowSubtotal = quantity * price;
            subtotal += rowSubtotal;
            $(this).find('.subtotal').text('AED ' + rowSubtotal.toFixed(2));
        });

        $('.grandTotal').text('AED ' + subtotal.toFixed(2));
    }    

    $(document).on('change', 'select[name="tool_serial[]"]', function() {
        var selectedCount = $(this).val().length;
        $(this).closest('.product-row').find('.quantity').val(selectedCount);
    });

    function fetchToolSerials(productId, container) {
        $.ajax({ 
            url: '{{ route('fetchToolSerials') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var selectField = '<div class="form-group">' +
                        '<label for="tool_serial">Select Serials</label>' +
                        '<select class="form-control" name="tool_serial[]" id="select_items" multiple="multiple" required>';
                    $.each(response.serials, function(id, serial) {
                        selectField += '<option value="' + id + '">' + serial + '</option>';
                    });
                    selectField += '</select></div>';
                    container.html(selectField);
                    // $('#select_items').select2();
                    $('#select_items').multiselect({
                        nonSelectedText: 'Select Serials',
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        buttonWidth:'100%'
                    });               
                
                } else {
                    var selectField = $('<div>', {class: 'form-group'})
                        .append($('<label>').text('Select Tools Serials'))
                        .append($('<input>', {
                            type: 'text',
                            readonly: 'readonly',
                            class: 'form-control text-red',
                            value: response.message
                        }));
                    container.html(selectField);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + error);
            }
        });
    }

    // Event listener for product selection
    $(document).on('change', 'select[name="product_id[]"]', function() {
        var productId = $(this).val();
        var container = $(this).closest('.product-row').find('.fetchToolSerial');
        var quantityField = $(this).closest('.product-row').find('.quantity');
        var typeField = $(this).closest('.product-row').find('.type');
        // Check if the selected product is a tool
        $.ajax({
            url: '{{ route('checkProductType') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response && response.productType === 'tool') {
                    fetchToolSerials(productId, container);
                    quantityField.prop('readonly', true); // Make the quantity field readonly
                } else {
                    container.empty(); // Clear the tool serial select field
                    quantityField.prop('readonly', false); // Make the quantity field editable
                }
                typeField.val(response.productType);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });

</script>

@endpush