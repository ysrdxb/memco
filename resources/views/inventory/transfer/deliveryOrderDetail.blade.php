@php
    if($deliveryOrders[0]->delivered_by == 'supplier'){
        $title = 'Delivery Order Details';
        $desc = 'Delivery Order';
        $type = 'Delivery Order';
        $short = 'DO';
    } else {
        $title = 'Material Transfer Voucher Details';
        $desc = 'Material Transfer Voucher';
        $type = 'M.T.V';
        $short = 'MTV';
    }
@endphp
@extends('inventory.layout')
@section('title', $title)
@section('content')
@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
@endpush
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-list bg-secondary"></i>
                    <div class="d-inline">
                        <h5>{{ $title }}</h5>
                        <span>{{ __($desc . ' No.'. $deliveryOrders[0]->delivery_order_no)}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 text-right">
                <a href="{{ route('deliveryOrder.print', encrypt($deliveryOrders[0]->delivery_order_no)) }}" target="_blank" class="btn btn-info"><span class="ik ik-printer"></span> Print</a>
                @include('include.backButtons')
            </div>
            <div class="col-md-12 mt-4">
                <div class="d-flex align-items-center">
                    <form id="uploadForm{{ $deliveryOrders[0]->id }}" class="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="delivery_order_id" value="{{ $deliveryOrders[0]->id }}">
                        <div class="form-group float-left">
                            <input type="file" name="file" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary ml-4 mr-4">Upload</button>
                        <div class="progress ml-2" style="display: none;">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="alert alert-success ml-2" role="alert" style="display: none;"></div>
                        <div class="alert alert-danger ml-2" role="alert" style="display: none;"></div>
                    </form>
                    @if($deliveryOrders[0]->file_path)
                        <a href="javascript:void(0)" onclick="downloadFile('{{ $deliveryOrders[0]->file_path }}')" class="btn btn-success ml-4 mb-3"><span class="ik ik-download"></span> Download File</a>
                        <div id="fileMessage"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-body">
                  <!--<form id="submitForm">-->
                    <table id="delivery_order_details_table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">MR No.</th>
                                <th class="text-center">{{$type}} No.</th>
                                <th class="text-center">MR QTY</th>
                                <th class="text-center">{{$short}} QTY</th>
                                <th class="text-center">Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliveryOrders as $key => $deliveryOrder)
                                <tr class="item-row">
                                    <td style="text-align:center !important">{{ $key + 1 }}</td>
                                    <td style="text-align:center !important">
                                      <input type="hidden" name="product_id[]" value="{{ $deliveryOrder->product->id }}">
                                      {{ $deliveryOrder->product->name }}
                                    </td>
                                    <td style="text-align:center !important">{{ $deliveryOrder->transfer->transfer_no }}</td>
                                    <td style="text-align:center !important">{{ $short == 'DO' ? $deliveryOrder->delivery_order_no : $deliveryOrder->transfer->voucher->voucher_no }}</td>
                                    <td style="text-align:center !important">{{ optional($deliveryOrder->transfer->details->where('product_id', $deliveryOrder->product_id)->first())->requested_quantity }}</td>
                                    <td style="text-align:center !important"><input type="number" name="quantities[]" class="form-control" value="{{ $deliveryOrder->quantity }}"></td>
                                    <td style="text-align:center !important">{{ $deliveryOrder->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td><a href="javascript:;" class="text-danger" onclick="deleteItem(this)"><i class="ik ik-trash"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="javascript:;" class="text-warning add-row" style="border:1px solid;border-radius:25px;padding:3px 21px;">+ Add More Products</a>
                            </div>
                        </div>
                    </div>
                    <!--<button class="float-right btn btn-primary">Update Record</button>-->
                  <!--</form>-->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('.uploadForm').submit(function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = new FormData(form[0]);
            var progressBar = form.find('.progress-bar');
            var progressDiv = form.find('.progress');
            var successAlert = form.find('.alert-success');
            var errorAlert = form.find('.alert-danger');

            $.ajax({
                type: 'POST',
                url: '{{ route('deliveryOrder.upload', encrypt($deliveryOrders[0]->id)) }}',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    progressBar.width('0%');
                    progressDiv.show();
                    successAlert.hide();
                    errorAlert.hide();
                },
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            progressBar.width(percent + '%');
                        }
                    });
                    return xhr;
                },
                success: function(response) {
                    successAlert.html(response.message).show();
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.responseJSON.message;
                    errorAlert.html(errorMessage).show();
                },
                complete: function() {
                    progressBar.width('0%');
                    progressDiv.hide();
                    form.trigger('reset');
                }
            });
        });
    });
function downloadFile(filePath) {
    window.location.href = '{{ route('deliveryOrder.download') }}?file_path=' + filePath;
}


function deleteItem(element) {
    $(element).closest('.item-row').remove();
}
function downloadFile(filePath) {
    window.location.href = '{{ route('purchases.download') }}?file_path=' + filePath;
}

// $(document).ready(function() {
//     handleFormSubmitWithConfirmation('#submitForm', '{{ route('deliveryOrder.update', encrypt($deliveryOrders[0]->id)) }}','POST', function(response) {
//         //
//     }, function(error) {
//         //
//     });

//     function handleFormSubmitWithConfirmation(formSelector, url, method, successCallback, errorCallback) {
//         $(document).on('submit', formSelector, function(event) {
//             event.preventDefault(); // Prevent default form submission

//             Swal.fire({
//                 title: 'Are you sure?',
//                 text: 'Your selected data will be saved!',
//                 icon: 'warning',
//                 showCancelButton: true,
//                 confirmButtonText: 'Save it!',
//                 cancelButtonText: 'Cancel it!'
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     // Proceed with form submission
//                     $.ajax({
//                         url: url,
//                         method: method,
//                         data: $(this).serialize(),
//                         success: function(response) {
//                             if (response.status == 'success') {
//                                 Swal.fire({
//                                     icon: 'success',
//                                     title: 'Success',
//                                     html: response.message,
//                                 });
//                                 if (response.redirect && response.redirect !== '') {
//                                     window.location.href = response.redirect;
//                                 }
//                             } else if (response.status === 'error') {
//                                 Swal.fire({
//                                     title: 'Error',
//                                     html: response.message.join('<br>'), // Display the error messages as separate lines
//                                     icon: 'error',
//                                 });
//                             } else {
//                                 // Error message
//                                 Swal.fire({
//                                     title: 'Error',
//                                     html: response.message,
//                                     icon: 'error',
//                                 });
//                             }
//                         },
//                         error: function(xhr, status, error) {
//                             if (xhr.status === 422) {
//                                 var errors = xhr.responseJSON.errors;
//                                 var errorMessage = Object.values(errors).flat().join('<br>');
//                                 Swal.fire({
//                                     title: 'Validation Error',
//                                     html: errorMessage,
//                                     icon: 'error',
//                                 });
//                             } else {
//                                 Swal.fire({
//                                     title: 'Error',
//                                     html: 'An error occurred while processing your request.',
//                                     icon: 'error',
//                                 });
//                             }
//                             if (errorCallback) {
//                                 errorCallback(error);
//                             }
//                         }
//                     });
//                 } else if (result.dismiss === Swal.DismissReason.cancel) {
//                     // Swal.fire('Cancelled', 'Your data is safe :)', 'error');
//                 }
//             });
//         });
//     }


// });


// Event delegation for the "Add More Products" button
$(document).on('click', '.add-row', function() {
    var newRow = $('<tr class="item-row">' +
        '<td style="text-align:center !important"></td>' + // Placeholder for row number
        '<td style="text-align:center !important">'+
        '<select class="form-control product-select" name="product_id[]" required onchange="getRequestedQuantity(this)">' + // Product select dropdown
        '<option value="">Select Product</option>' +
        '</select>' +
        '</td>' + // Placeholder for purchase number
        '<td style="text-align:center !important"></td>' + // Placeholder for delivery orders
        '<td style="text-align:center !important"></td>' + // Placeholder for material request number
        '<td style="text-align:center !important" id="requested_quantity"></td>' +
        '<td style="text-align:center !important"><input type="number" required name="quantities[]" class="form-control" value=""></td>' + // Input for quantity
        '<td style="text-align:center !important">{{ $deliveryOrder->created_at->format('Y-m-d H:i:s') }}</td>' + // Placeholder for balance
        '<td><a href="javascript:;" class="text-danger" onclick="deleteItem(this)"><i class="ik ik-trash"></i></a></td>' + // Delete button
        '</tr>');

    // Update the row number
    var rowNumber = $('#delivery_order_details_table tbody tr').length + 1;
    newRow.find('td:first').text(rowNumber);

    // Update the material request number and delivery order number based on the first row
    var firstRow = $('#delivery_order_details_table tbody tr:first');
    newRow.find('td:nth-child(4)').text(firstRow.find('td:nth-child(4)').text());
    newRow.find('td:nth-child(3)').text(firstRow.find('td:nth-child(3)').text());

    // Clear input values
    newRow.find('input, select').val('');
    $('#delivery_order_details_table tbody').append(newRow);

    // Initialize select2 for the new row
    initSelect2(newRow);
});

function initSelect2(row) {
    row.find('.product-select').select2({
        ajax: {
            url: '{{ route('deliveryOrder.getAvailableProducts') }}',
            dataType: 'json',
            type: 'post',
            data: {delivery_order_id:'{{$deliveryOrders[0]->id}}'},
            delay: 0,
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });
}

function getRequestedQuantity(selectElement) {
    var productId = selectElement.value;
    var requestedQuantityCell = $(selectElement).closest('tr').find('#requested_quantity');

    // AJAX request to get the requested quantity
    $.ajax({
        type: 'POST',
        url: '{{ route('deliveryOrder.getRequestedQuantity') }}',
        data: {product_id: productId, delivery_order_id : '{{ $deliveryOrders[0]->id}}'},
        success: function(response) {
            requestedQuantityCell.text(response.requested_quantity);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching requested quantity: ' + error);
        }
    });
}
</script>

@endpush
