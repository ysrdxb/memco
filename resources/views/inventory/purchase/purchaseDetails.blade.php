@extends('inventory.layout')
@section('title', 'Purchases')
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
						<h5>{{ __('Purchases')}}</h5>
						<span>{{ __('List of Purchases')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
				<a href="{{ route('purchases.create') }}" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Create</a>
                @include('include.backButtons')
            </div>
            <div class="col-md-12 mt-4">
                <div class="d-flex align-items-center">
                    <form id="uploadForm{{ $purchases[0]->id }}" class="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="purchase_id" value="{{ $purchases[0]->id }}">
                            <div class="form-group float-left">
                                <input type="file" name="file" class="form-control-file">
                            </div>
                            <button type="submit" class="btn btn-primary ml-4 mr-4">Upload</button>
                        </div>
                        <div class="progress ml-2" style="display: none;">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="alert alert-success ml-2" role="alert" style="display: none;"></div>
                        <div class="alert alert-danger ml-2" role="alert" style="display: none;"></div>
                    </form>
                    @if($purchases[0]->file_path)
                        <a href="javascript:void(0)" onclick="downloadFile('{{ $purchases[0]->file_path }}')" class="btn btn-success ml-4 mb-3"><span class="ik ik-download"></span> Download File</a>
                        <div id="fileMessage"></div>
                    @endif
                </div>
            </div>            
		</div>
	</div>    
    <div class="row">
        @include('include.message')
        <div class="col-md-12">

                            @php
                                $transferProductIds = $purchases[0]->transfer->details->pluck('product_id');
                            @endphp

                            @foreach($purchases as $purchase)
                                @foreach($purchase->details as $key => $detail)
                                    @php
                                        $highlightClass = $transferProductIds->contains($detail->product_id) ? false : true;
                                        if ($highlightClass) {
                                            $transferProducts = $purchase->transfer->details
                                                ->where('product_id', $detail->product->id);
                                        }
                                    if($highlightClass)
                                    {
                                      $deliveryOrder = \App\Models\DeliveryOrder::where('purchase_id', $purchase->id)->first();
                                      if($deliveryOrder) {
                                        $stockRecord = \App\Models\StockRecord::where('product_id', $detail->product_id)->where('stockable_type', \App\Models\Project::class)
                                        ->where('stockable_id', $purchase->transfer->requestable_id)
                                        ->first();
                                        if($stockRecord) {
                                            $stockRecord->quantity = $stockRecord->quantity >= $detail->quantity ? $stockRecord->quantity - $detail->quantity : $stockRecord->quantity;
                                            $stockRecord->save();
                                        }
                                        $deliveryOrder->delete();
                                      }
                                      $purchase_d = \App\Models\PurchaseDetail::where('purchase_id', $purchase->id)
                                      ->where('product_id', $detail->product_id)
                                      ->first();
                                      $purchase_d->delete();
                                    } 
                                    @endphp

                                @endforeach
                            @endforeach



               <div class="card p-3">
                <div class="card-header"><h3>{{ __( 'Purchase No. '. $purchases[0]->purchase_no )}}</h3></div>
                <div class="card-body">
                    {{-- <form id="submitForm"> --}}
                        <table id="purchases_table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Purchase No.</th>
                                    <th class="text-center">Delivery Orders</th>
                                    <th class="text-center">Material Request No.</th>
                                    <th class="text-center">Item Name</th>
                                    <th class="text-center">MR QTY</th>
                                    <th class="text-center">L.P.O QTY</th>
                                    <th class="text-center">M.T.V QTY</th>
                                    <th class="text-center">D.O QTY</th>
                                    <th class="text-center">L.P.O Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $transferProductIds = $purchases[0]->details->pluck('product_id');
                                    $purchaseProductIds = collect([]);
                                @endphp

                                @foreach($purchases as $purchase)
                                    @foreach($purchase->transfer->details as $key => $detail)
                                        @php
                                            $purchase_detail = $purchase->details->where('product_id', $detail->product_id)->first();
                                            $purchaseProductIds->push($detail->product_id);
                                            $highlightClass = $purchase_detail ? '' : 'bg-yellow';
                                            $replaceOptions = [];
                                            if ($highlightClass) {
                                                $transferProducts = $purchase->transfer->details->where('product_id', $detail->product->id);
                                                if ($transferProducts->isNotEmpty()) {
                                                    $replaceOptions = $transferProducts->pluck('product.name', 'product_id')->toArray();
                                                }
                                            }
                                        @endphp
                                        @if($purchase_detail)
                                        <tr class="{{ $highlightClass }} item-row">
                                            <td id="rowno">{{ $key ++ }}</td>
                                            <td>{{ $purchase->purchase_no }} </td>
                                            <td id="dono">
                                                @if ($purchase->deliveryOrders->isNotEmpty())
                                                    <a href="{{ route('deliveryOrder.detail', encrypt($purchase->deliveryOrders->first()->delivery_order_no)) }}" target="_blank" class="text-primary">{{ $purchase->deliveryOrders->first()->delivery_order_no }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>            
                                            <td id="transferno"><a href="{{ route('transfer.detail', encrypt($purchase->transfer->id)) }}" target="_blank" class="text-primary">{{ $purchase->transfer->transfer_no }}</a></td>
                                            <td>
                                                <input type="hidden" name="product_id[]" value="{{ $detail->product->id }}">
                                                @if ($highlightClass)
                                                    <select class="form-control replaceSelect">
                                                        <option value="{{ $detail->product_id }}" selected>{{ $detail->product->name }}</option>
                                                        @foreach ($replaceOptions as $productId => $productName)
                                                            <option value="{{ $productId }}">{{ $productName }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    {{ $detail->product->name }}
                                                @endif
                                            </td>
                                            <td>{{ $detail->requested_quantity }}</td>
                                            <td><input type="number" readonly name="quantities[]" class="form-control" value="{{ $purchase_detail ? $purchase_detail->quantity : '' }}"></td>
                                            <td>{{ $purchase->deliveryOrders->where('product_id', $detail->product_id)->where('delivered_by', 'store')->sum('quantity') }}</td>
                                            <td>{{ $purchase->transfer->deliveryOrders->where('product_id', $detail->product_id)->where('delivered_by', 'supplier')->sum('quantity') }}</td>
                                            <td>{{ $detail->requested_quantity - $purchase_detail->quantity }}</td>
                                            <td><a href="javascript:;" class="text-danger" onclick="deleteItem(this)"><i class="ik ik-trash"></i></a></td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            </tbody>                                               
                        </table> 
                        <div class="container-fluid">
                            <!-- Your existing content -->

                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <a href="javascript:;" class="text-warning add-row" style="border:1px solid;border-radius:25px;padding:3px 21px;">+ Add More Products</a>
                                </div>
                            </div> --}}
                        </div>                        
                        {{-- <button class="float-right btn btn-primary">Update Purchase Record</button> --}}
                    </form>
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
                url: '{{ route('purchases.upload', encrypt($purchases[0]->id)) }}',
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
    function deleteItem(element) {
        $(element).closest('.item-row').remove();
    }    
function downloadFile(filePath) {
    window.location.href = '{{ route('purchases.download') }}?file_path=' + filePath;
}

// $(document).ready(function() {
//     handleFormSubmitWithConfirmation('#submitForm', '{{ route('purchases.update', encrypt($purchases[0]->id)) }}','POST', function(response) {
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
        '<td></td>' + // Placeholder for row number
        '<td>{{ $purchases[0]->purchase_no }}</td>' + // Placeholder for purchase number
        '<td></td>' + // Placeholder for delivery orders
        '<td></td>' + // Placeholder for material request number
        '<td>' +
        '<select class="form-control product-select" name="product_id[]" required onchange="getRequestedQuantity(this)">' + // Product select dropdown
        '<option value="">Select Product</option>' +
        '</select>' +
        '</td>' +
        '<td id="requested_quantity"></td>' +
        '<td><input type="number" name="quantities[]" class="form-control" value=""></td>' + // Input for quantity
        '<td>0</td>' + // Placeholder for store quantity
        '<td>0</td>' + // Placeholder for supplier quantity
        '<td>0</td>' + // Placeholder for balance
        '<td><a href="javascript:;" class="text-danger" onclick="deleteItem(this)"><i class="ik ik-trash"></i></a></td>' + // Delete button
        '</tr>');

    // Update the row number
    var rowNumber = $('#purchases_table tbody tr').length + 1;
    newRow.find('td:first').text(rowNumber);

    // Update the material request number and delivery order number based on the first row
    var firstRow = $('#purchases_table tbody tr:first');
    newRow.find('td:nth-child(4)').text(firstRow.find('td:nth-child(4)').text());
    newRow.find('td:nth-child(3)').text(firstRow.find('td:nth-child(3)').text());

    // Clear input values
    newRow.find('input, select').val('');
    $('#purchases_table tbody').append(newRow);

    // Initialize select2 for the new row
    initSelect2(newRow);
});

function initSelect2(row) {
    row.find('.product-select').select2({
        ajax: {
            url: '{{ route('purchases.getAvailableProducts') }}',
            dataType: 'json',
            type: 'post',
            data: {purchase_id:'{{$purchases[0]->id}}'},
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
        url: '{{ route('purchases.getRequestedQuantity') }}',
        data: {product_id: productId, purchase_id : '{{ $purchases[0]->id}}'},
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