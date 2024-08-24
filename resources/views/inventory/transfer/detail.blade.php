@extends('inventory.layout')
@section('title', 'Material Requisition')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-list bg-secondary"></i>
                    <div class="d-inline">
                        <h5>{{ $transfer->transfer_no }}</h5>
                        <span>{{ __('Details for Material Request #' . $transfer->transfer_no) }}</span>
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
                <div class="card-body">
                    @if(empty($transfer))
                        <div class="row">
                            <div class="col-12">
                                <p class="lead">Sorry !!</p>
                                <div class="alert alert-danger mt-10">
                                    <i class="ik ik-info"></i> No data found
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row mb-4">
                            <div class="col-12">
                                <h3 class="d-block w-100">
                                    <small class="float-left">MR# {{ $transfer->transfer_no }}</small>
                                    <a href="{{ route('transfer.print', encrypt($transfer->id) ) }}" class="float-right btn btn-warning small text-white btn-rounded" target="_blank">
                                        <i class="fa fa-print"></i> Print
                                    </a>
                                </h3>
                            </div>
                        </div>
                        <div class="row invoice-info">
                            <div class="col-sm-8 invoice-col">
                                Date
                                <address>
                                    <strong>{{ $transfer->date }}</strong>
                                </address>
                            </div>
                            <div class="row mt-3 mb-3">
                                <div class="col-lg-12">
                                    <div class="d-inline-block">
                                        <span class="badge badge-primary p-2">M.T.V QTY</span>
                                        <span class="badge badge-info p-2">L.P.O QTY</span>
                                        <span class="badge badge-success p-2">D.O QTY</span>
                                        <span class="badge badge-secondary p-2">Balance == 0</span>
                                        <span class="badge badge-danger bg-orange p-2">Balance > 0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>M.R QTY</th>
                                            <th>M.T.V QTY</th>
                                            <th>L.P.O QTY</th>
                                            <th>D.O QTY</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                            <th>Unit</th>
                                            @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php 
                                        $counter = 1;
                                        $do = 0;
                                        $lpo = 0;
                                    @endphp

                                    @foreach($transfer->details as $row)
                                    @php
                                        $do = 0;
                                        $lpo = 0;

                                        // Calculate the sum of quantities for delivery orders
                                        foreach($transfer->purchases as $purchase) {
                                            $do += $purchase->deliveryOrders
                                                ->where('product_id', $row->product_id)
                                                ->where('delivered_by', 'supplier')
                                                ->sum('quantity');
                                        }

                                        // Calculate the sum of quantities for purchases
                                        $lpo = $transfer->purchases->flatMap->details
                                            ->where('product_id', $row->product_id)
                                            ->sum('quantity');

                                        $mtv = $transfer->deliveryOrders->where('product_id', $row->product_id)
                                            ->where('delivered_by', 'store')
                                            ->sum('quantity');
                                        $bal = $row->requested_quantity - $mtv - $do;
                                    @endphp

                                        <tr id="row-{{ $row->id }}">
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $row->product->code }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            <td class="requested_quantity">{{ $row->requested_quantity }}</td>
                                            <td><span class="badge badge-primary text-white p-2">{{ $mtv }}</span></td>
                                            <td><span class="badge badge-info text-white p-2">{{ $lpo }}</span></td>
                                            <td><span class="badge badge-success text-white p-2">{{ $do }}</span></td>
                                            <td class="balance">
                                                @if($row->status === 'closed')
                                                <span class="badge badge-secondary text-white p-2">Closed</span>
                                                @else
                                                <span class="badge badge-danger bg-{{ $bal > 0 ? 'orange' : 'secondary' }}">{{ $bal }}</span>
                                                @endif
                                            </td>
                                            <td class="status">
                                                @if($row->status === 'closed')
                                                <span class="badge badge-success">Completed</span>
                                                @else
                                                @if($bal > 0)
                                                <span class="badge badge-danger">Pending</span>
                                                @else
                                                <span class="badge badge-success">Completed</span>
                                                @endif
                                                @endif
                                            </td>
                                            <td>{{ $row->unit->name }}</td>
                                            @if($bal > 0)
                                            <td class="action">
                                                @if($row->status === 'closed')
                                                <button class="btn btn-secondary updateQuantityBtn" data-toggle="modal" data-target="#quantityModal" data-transfer-id="{{ $transfer->id }}" data-product-id="{{ $row->product_id }}" data-row-id="{{ $row->id }}" data-balance="{{ $bal }}">Unlock & Open</button>
                                                @else
                                                <button class="btn btn-primary updateQuantityBtn" data-toggle="modal" data-target="#quantityModal" data-transfer-id="{{ $transfer->id }}" data-product-id="{{ $row->product_id }}" data-row-id="{{ $row->id }}" data-balance="{{ $bal }}">Lock & Close</button>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="quantityModal" tabindex="-1" role="dialog" aria-labelledby="quantityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quantityModalLabel">Lock/Unlock & Close/Unclose the Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="quantityForm">
                    @csrf
                    <input type="hidden" name="transfer_id" id="transfer_id" value="">
                    <input type="hidden" name="product_id" id="product_id" value="">
                    <input type="hidden" name="balance" id="balance" value="">
                    <input type="hidden" name="row_id" id="row_id" value="">
                    <div class="form-group">
                        <input type="hidden" name="quantity" id="quantity" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    $('.updateQuantityBtn').on('click', function() {
        var transferId = $(this).data('transfer-id');
        var productId = $(this).data('product-id');
        var rowId = $(this).data('row-id');
        var balance = $(this).data('balance');

        $('#transfer_id').val(transferId);
        $('#product_id').val(productId);
        $('#row_id').val(rowId);
        $('#quantity').val(balance);
        $('#balance').val(balance);
    });

    $('#quantityForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route('update.quantity') }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    var row = $('#row-' + response.row_id);
                    var statusCell = row.find('.status');
                    var btnCell = row.find('.action');

                    btnCell.html('<button class="btn ' + (response.status === 'active' ? 'btn-primary' : 'btn-secondary') + ' updateQuantityBtn" data-toggle="modal" data-target="#quantityModal" data-transfer-id="' + response.transfer_id + '" data-product-id="' + response.product_id + '" data-row-id="' + response.row_id + '" data-balance="' + response.balance + '">' + (response.status === 'active' ? 'Lock & Close' : 'Unlock & Open') + '</button>');

                    statusCell.html('<span class="badge badge-' + (response.status === 'active' ? 'danger' : 'success') + '">' + (response.status === 'active' ? 'Pending' : 'Completed') + '</span>');

                    var balanceCell = row.find('.balance');
                    var balanceBadge = '<span class="badge badge-' + (response.balance === 'Closed' ? 'secondary' : 'orange text-white orange') + '">' + response.balance + '</span>';
                    balanceCell.html(balanceBadge);

                    $('#quantityModal').modal('hide');
                } else {
                    alert('Error updating quantity');
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    });
});
</script>
@endpush
