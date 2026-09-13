@extends('inventory.layout')
@section('title', 'Create Material Request')

@push('head')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">

    <style>
        .create-mr-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            padding: 10px 5px;
        }

        .header-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 18px;
            padding: 24px 28px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .header-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(99, 102, 241, 0.35);
            margin-right: 18px;
            flex-shrink: 0;
        }

        .header-title-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 2px;
            letter-spacing: -0.4px;
        }

        .header-sub-text {
            color: #94a3b8;
            font-size: 0.88rem;
            margin-bottom: 0;
            font-weight: 500;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            padding: 11px 22px;
            font-weight: 700;
            font-size: 0.88rem;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(99, 102, 241, 0.5);
        }

        .form-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .form-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-card .card-header h5 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .form-card .card-body {
            padding: 24px;
        }

        .form-group label {
            font-size: 0.84rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.88rem;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* PRODUCT ROW CARD STYLING */
        .product-row-card {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.2s ease;
            position: relative;
        }

        .product-row-card:hover {
            border-color: #cbd5e1;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
        }

        .stock-badge {
            background: #e2e8f0;
            color: #334155;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 8px;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .btn-add-product {
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5 !important;
            border: 1.5px dashed rgba(99, 102, 241, 0.4);
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.9rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none !important;
        }

        .btn-add-product:hover {
            background: rgba(99, 102, 241, 0.18);
            border-color: #6366f1;
        }

        .btn-remove-row {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-remove-row:hover {
            background: #ef4444;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid create-mr-wrapper">
    <!-- Header Banner -->
    <div class="header-banner d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div>
                <h3 class="header-title-text">Create Material Request</h3>
                <p class="header-sub-text">Submit a new material requisition order with item quantities, units, and brand specifications</p>
            </div>
        </div>

        <div class="d-flex align-items-center" style="gap: 12px;">
            <button type="button" class="btn-hero-primary saveBtn">
                <i class="fas fa-floppy-disk mr-2"></i> Submit Request
            </button>
            @include('include.backButtons')
        </div>
    </div>

    <!-- Submit Form Container -->
    <form class="forms-sample" id="submitForm" method="POST" action="javascript:;">
        @csrf

        <!-- General Details Card -->
        <div class="form-card">
            <div class="card-header">
                <h5><i class="fas fa-sliders text-indigo mr-2"></i> General Requisition Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-lg-3 col-md-6">
                        <label for="purchase_applicable">L.P.O Applicable</label>
                        <select name="purchase_applicable" id="purchase_applicable" class="form-control">
                            <option value="1">Yes, can create L.P.O</option>
                            <option value="0">No, Just for Main Store M.T.V</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-3 col-md-6">
                        <label for="material_request_no">Material Request No.</label>
                        <input type="number" class="form-control" id="material_request_no" name="transfer_no" placeholder="Auto / Custom No.">
                    </div>

                    <div class="form-group col-lg-3 col-md-6">
                        <label for="material_request_date">Request Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date" id="material_request_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group col-lg-3 col-md-6">
                        <label for="remarks">Remarks / Notes</label>
                        <textarea class="form-control" name="remarks" rows="1" placeholder="Optional notes"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Requisition Card -->
        <div class="form-card">
            <div class="card-header">
                <h5><i class="fas fa-boxes-stacked text-indigo mr-2"></i> Product Items Requisition</h5>
            </div>
            <div class="card-body">
                <div id="productRows">
                    <div class="product-row-card product-row">
                        <div class="row align-items-center">
                            <div class="form-group col-lg-4 col-md-6">
                                <label>1. Select Product <span class="text-danger">*</span></label>
                                <select class="form-control select product-select" name="product_id[]" onfocus="search(this)" required>
                                    <option value="">Search & Select Product</option>
                                </select>
                            </div>

                            <div class="form-group col-lg-2 col-md-6">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control quantity" name="quantity[]" placeholder="Qty" required>
                            </div>

                            <div class="form-group col-lg-2 col-md-4">
                                <label class="d-block">Current Stock</label>
                                <div class="stock-badge current-stock">0</div>
                            </div>

                            <div class="form-group col-lg-2 col-md-4">
                                <label>Unit <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="unit_id[]" required>
                                    <option value="">Default Unit</option> 
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>                                                    

                            <div class="form-group col-lg-2 col-md-4">
                                <label>Brand <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="brand_id[]" required>
                                    <option value="">Default Brand</option> 
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>                                                     

                            @if(Auth::user()->type == 'data_entry')
                            <div class="form-group col-lg-2 col-md-4">
                                <label>Make LPO</label>
                                <select class="form-control" name="makeLPO[]" required>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            
                            <div class="form-group col-lg-2 col-md-4">
                                <label>LPO Quantity</label>
                                <input type="number" class="form-control" name="lpo_qty[]" required>
                            </div>
                            @else 
                                <input type="hidden" name="makeLPO[]" value="0">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="javascript:;" class="btn-add-product add-row">
                        <i class="fas fa-plus-circle mr-2"></i> Add Another Product Item
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Submit Footer Action Bar -->
        <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn-hero-primary saveBtn px-5 py-3" style="font-size: 1rem;">
                <i class="fas fa-floppy-disk mr-2"></i> Submit Material Request
            </button>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
function deleteItem(element) {
    $(element).closest('.product-row-card').remove();
}

function initSelect2(row) {
    row.find('.product-select').select2({
        ajax: {
            url: '{{ route('products.search') }}',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return { results: data };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        var product_id = e.params.data.id;
        var currentStockInput = $(this).closest('.product-row-card').find('.current-stock');

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

function initAllSelect2() {
    $('.product-row').each(function() {
        initSelect2($(this));
    });
}

function addProductRow() {
    var productCounter = $('.product-row-card').length + 1;
    var newRow = $('<div class="product-row-card product-row">' +
    '<div class="d-flex justify-content-between align-items-center mb-2">' +
    '<strong class="text-dark font-weight-bold" style="font-size: 0.9rem;">Item #' + productCounter + '</strong>' +
    '<button type="button" onclick="deleteItem(this)" class="btn-remove-row"><i class="fas fa-trash-can mr-1"></i> Remove Item</button>' +
    '</div>' +
    '<div class="row align-items-center">' +
    '<div class="form-group col-lg-4 col-md-6">' +
    '<label>Select Product <span class="text-danger">*</span></label>' +
    '<select class="form-control select product-select" name="product_id[]" required>' +
    '<option value="">Search & Select Product</option>' +
    '</select>' +
    '</div>' +
    '<div class="form-group col-lg-2 col-md-6">' +
    '<label>Quantity <span class="text-danger">*</span></label>' +
    '<input type="number" class="form-control quantity" name="quantity[]" placeholder="Qty" required>' +
    '</div>' +
    '<div class="form-group col-lg-2 col-md-4">' +
    '<label class="d-block">Current Stock</label>' +
    '<div class="stock-badge current-stock">0</div>' +
    '</div>' +
    '<div class="form-group col-lg-2 col-md-4">' +
    '<label>Unit <span class="text-danger">*</span></label>' +
    '<select class="form-control" name="unit_id[]" required>' +
    '<option value="">Default Unit</option>'+
    @foreach($units as $unit)
    '<option value="{{ $unit->id }}">{{ $unit->name }}</option>' +
    @endforeach
    '</select>' +
    '</div>' +
    '<div class="form-group col-lg-2 col-md-4">' +
    '<label>Brand <span class="text-danger">*</span></label>' +
    '<select class="form-control" name="brand_id[]" required>' +
    '<option value="">Default Brand</option>'+
    @foreach($brands as $brand)
    '<option value="{{ $brand->id }}">{{ $brand->name }}</option>' +
    @endforeach
    '</select>' +
    '</div>' +
    '</div></div>');

    if ('{{ Auth::user()->type }}' === 'data_entry') {
        newRow.find('.row').append('<div class="form-group col-lg-2 col-md-4">' +
            '<label>Make LPO</label>' +
            '<select name="makeLPO[]" class="form-control" required><option value="0">No</option><option value="1">Yes</option></select>' +
            '</div>' +
            '<div class="form-group col-lg-2 col-md-4">' +
            '<label>LPO Quantity</label>' +
            '<input type="number" class="form-control" name="lpo_qty[]" required>' +
            '</div>');
    } else {
        newRow.append('<input type="hidden" name="makeLPO[]" value="0"><input type="hidden" name="lpo_qty[]" value="0">');
    }

    $('#productRows').append(newRow);
    initSelect2(newRow);
}

$('.add-row').click(function() {
    addProductRow();
});

$(document).ready(function() {
    initAllSelect2();

    handleFormSubmit('#submitForm', '{{ route('transfer.save') }}', 'POST', function(response) {
    }, function(error) {
        console.error(error);
    });

    handleSaveAction('.saveBtn', function(response) {
    }, function(error) {
    });
});

function handleSaveAction(saveSelector, successCallback, errorCallback) {
    $(document).on('click', saveSelector, function(event) {
        event.preventDefault();

        var emptyFields = false;
        $('.product-select, .quantity').each(function() {
            if ($(this).val() === '') {
                emptyFields = true;
                return false;
            }
        });

        if (emptyFields) {
            Swal.fire('Error', 'Please fill in all required product fields.', 'error');
            return false;
        }

        Swal.fire({
            title: 'Submit Material Request?',
            text: 'Are you sure you want to submit this requisition order?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Submit Request',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#submitForm').submit();
            }
        });
    });
}
</script>
@endpush