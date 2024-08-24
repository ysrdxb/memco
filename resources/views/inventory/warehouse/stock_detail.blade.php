@extends('inventory.layout')
@section('title', 'Tool Stock Detail')
@section('content')
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
    @endpush
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-list bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __($stock->product->name .' Stock Detail')}}</h5>
                        <span>Add, remove or edit Tools Stock</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 text-right">
                @include('include.backButtons')       
            </div>
        </div>
    </div>
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="card p-3">
                @if($stock->quantity > 0)
                <form id="submitForm" method="POST" action="{{ route('stock.saveDetail', encrypt($stock->id)) }}" enctype="multipart/form-data" novalidate>
                    @csrf                 
                    <div class="card-header d-block">
                        @if($stock->detail)
                        <p class="alert alert-warning"><strong>Note: </strong> Total <strong>{{ $stockDetails->sum('quantity') }}</strong> items with different <strong>SERIAL No. </strong>of '{{ $stock->product->name }}' are in stock.</p>
                        @else 
                        <p class="alert alert-danger"><strong>Note: </strong> No Item added to the stock detail for this product. Please add below</p>
                        @endif
                    </div>
                    <div class="card-body">  
                        <?php $i = 0; 
                        
                        ?>                                          
                        @foreach($stockDetails as $row)
                        @php $i++; @endphp
                        <div class="row product-row">
                            <div class="form-group col-sm-2 select_items">
                                <label>#{{ $i }}</label>
                                <input type="text" name="product_id[]" readonly value="{{ optional($stock->product)->name }}" class="form-control">                                                    
                            </div>

                            <div class="form-group col-sm-1">
                                <label>QTY <span class="text-red">*</span></label>
                                <input type="number" class="form-control quantity" name="quantity[]" value="{{ $row->quantity }}" required>
                            </div>

                            <div class="form-group col-sm-2">
                                <label>Serial # <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="serial_no[]" value="{{ $stock->detail[$i-1]->serial_no ?? $i }}" required>
                                @if($row->quantity == 0)
                                    @php $serial = $row->deliveryOrderSerials->first(); @endphp
                                    
                                    @if($serial && $serial->deliveryOrder)
                                        <small class="text-white" style="background: #007bff !important;padding: 1px 5px;border-radius: 25px;">{{ optional($serial->deliveryOrder->transfer->project)->name ?? 'Main Store' }}</small>
                                    @endif
                                @endif                                
                            </div>                        

                            <div class="form-group col-sm-1">
                                <label>Model</label>
                                <input type="text" class="form-control" name="model_no[]" value="{{ $stock->detail[$i-1]->model_no ?? $i }}" required>
                            </div>                        

                            <div class="form-group col-sm-1">
                                <label>Warranty<span class="text-red">*</span></label>
                                <input type="number" class="form-control" name="warranty[]" value="{{ $stock->detail[$i-1]->warranty ?? 2 }}" required>
                            </div> 

                            <div class="form-group col-sm-2">
                                <label>Condition <span class="text-red">*</span></label>
                                <select name="product_condition[]" id="" required class="form-control">
                                    <option value="new" {{ isset($stock->detail[$i-1]) && $stock->detail[$i-1]->product_condition == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="used" {{ isset($stock->detail[$i-1]) && $stock->detail[$i-1]->product_condition == 'used' ? 'selected' : '' }}>Used</option>
                                </select>
                            </div>                         

                            <div class="form-group col-sm-1">
                                <label><small>Purchase Date</small> <span class="text-red">*</span></label>
                                <input type="date" class="form-control" name="purchase_date[]" value="{{ $stock->detail[$i-1]->purchase_date ?? date('Y-m-d') }}" required>
                            </div>                        
                            <div class="form-group col-sm-2">
                                <label>Brand</label>
                                <select name="brand_id[]" id="" required class="form-control select2">
                                    @foreach($brands as $brand)
                                        <option {{ isset($stock->detail[$i-1]) && $brand->id == $stock->detail[$i-1]->brand_id ? 'selected' : '' }} value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>                            
                            </div>                        
                        </div>
                        @endforeach
                        <div class="row">
                            <div class="form-group col-6 text-left mt-4">
                                <button type="button" class="btn btn-outline-danger addRowBtn">+ Add More Serials</button>
                            </div>
                            <div class="form-group col-6 text-right mt-4">
                                <label class="form-label text-left">Secret Pass Key</label>
                                <input type="password" class="form-control" name="passkey" placeholder="Enter Secret Pass key" required>
                            </div>                             
                        </div>
                        <div class="form-group text-right mt-4" style="width:100%;float:right">
                            <button type="button" class="btn btn-primary saveBtn"><i class="fas fa-save"></i> Save Stock Detail</button>
                        </div>
                    </div>
                </form>
                @else 
                <p class="alert alert-danger">{{ $stock->product->name }} out of stock</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    $(document).on('click', '.addRowBtn', function(event) {
        event.preventDefault();

        let rowCount = $('.product-row').length;

        let lastRow = $('.product-row:last');
        let newRow = lastRow.clone();
        newRow.find('input, select').each(function() {
            $(this).val('');
        });

        let decodedProductName = $('<textarea />').html('{{ $stock->product->name }}').text();
        newRow.find('input[name="product_id[]"]').val(decodedProductName);

        newRow.find('.form-group').each(function(index) {
            let label = $(this).find('label');
            let labelText = label.text().split('#')[0];
            label.text(labelText);
        });

        newRow.find('input[name^="serial_no"]').attr('name', 'serial_no[]');
        newRow.find('input[name^="model_no"]').attr('name', 'model_no[]');
        newRow.find('input[name^="warranty"]').attr('name', 'warranty[]');
        newRow.find('select[name^="product_condition"]').attr('name', 'product_condition[]');
        newRow.find('input[name^="purchase_date"]').attr('name', 'purchase_date[]');
        newRow.find('select[name^="brand_id"]').attr('name', 'brand_id[]');

        lastRow.after(newRow);
    });
});


$(document).ready(function() {
    $(document).on('click', '.saveBtn', function(event) {
        event.preventDefault();

        if (validateForm()) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Your selected data will be saved!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Save it!',
                cancelButtonText: 'Cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: $('#submitForm').attr('action'),
                        type: 'POST',
                        data: $('#submitForm').serialize(),
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success'
                                }).then((result) => {
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An unexpected error occurred.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }
    });

    function validateForm() {
        let isValid = true;

        // Validate each input
        $('.product-row').each(function(index, element) {
            let serialNo = $(element).find('input[name="serial_no[]"]').val();
            let modelNo = $(element).find('input[name="model_no[]"]').val();
            let warranty = $(element).find('input[name="warranty[]"]').val();

            if (serialNo.trim() === '' || modelNo.trim() === '' || warranty.trim() === '') {
                isValid = false;
                return false; // Exit the loop early
            }
        });

        if (!isValid) {
            Swal.fire({
                title: 'Error!',
                text: 'Please fill in all required fields (Serial #, Model, Warranty)',
                icon: 'error'
            });
        }

        return isValid;
    }
});

  
</script>
@endpush