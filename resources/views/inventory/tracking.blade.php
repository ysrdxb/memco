@extends('inventory.layout')
@section('title', 'Tracking Page')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-list bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('Tracking Page')}}</h5>
                        <span>Filter and get results for M.R/D.O/MTV/LPO</span>
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
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" action="{{ route('track.filter') }}" method="GET">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="transferId">Track Material Request</label>
                                <select class="form-control select2" id="transferId" name="transferId">
                                    <option value="">Select Material Request</option>
                                    @foreach($transfers as $transfer)
                                        <option value="{{ $transfer->id }}">{{ $transfer->transfer_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="productId">Track Products / Tools</label>
                                <select class="form-control select2" id="productId" name="productId">
                                    <option value="">Select Products</option>
                                    @foreach($products as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="deliveryOrderId">Track Pending Material Requests</label>
                                <select class="form-control select2" id="projectId" name="projectId">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--<div class="form-group col-md-3">-->
                            <!--    <label for="transferVoucherId">Transfer Voucher</label>-->
                            <!--    <select class="form-control select2" id="transferVoucherId" name="transferVoucherId">-->
                            <!--        <option value="">Select Transfer Voucher No.</option>-->
                            <!--        {{-- Add options dynamically if needed --}}-->
                            <!--    </select>-->
                            <!--</div>                            -->
                        </div>
                    </form>
                </div>
                </div>
                
                @if($transfer_records)
                <div class="card">
                    <div class="card-header">
                        <h3>Material Request Details for M.R No. {{ $transfer_records->transfer_no }}</h3>
                    </div> 
                    <div class="card-body">
                        <table class="table table-bordered table-stripped">
                            <thead>
                                <tr>
                                    <th>Created By</th>
                                    <th>Project Name</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ optional($transfer_records->user)->name }}</td>
                                    <td>
                                        {{ $transfer_records->requestable_type == \App\Models\Project::class  ?
                                            (isset($transfer_records->project) ? $transfer_records->project->name : '')
                                            : (isset($transfer->warehouse) ? $transfer_records->warehouse->name : $transfer_records->requestable->name )
                                        }}
                                    </td>                                    
                                    <td>{{ $transfer_records->created_at }}</td>
                                    <td><a href="{{ route('transfer.detail', encrypt($transfer_records->id)) }}" class="btn btn-primary" target="_blank">Detail <i class="fas fa-external-link-alt"></i></a></td>
                                </tr>
                            </tbody>
                        </table>   
                    </div>
                </div>
                
                    @if($transfer_records->purchases->isNotEmpty())
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ count($transfer_records->purchases) }} L.P.O Records found for M.R# {{ $transfer_records->transfer_no }}</h3>
                        </div>
                        <div class="card-body">
                            @foreach($transfer_records->purchases->groupBy('purchase_no') as $purchaseNo => $purchases)
                            <h6>
                                <a target="_blank" href="{{ route('purchases.detail', encrypt($purchases->first()->id)) }}" class="text-primary">Purchase Records # {{ $purchaseNo }} <i class="fas fa-external-link-alt"></i></a>
                            </h6>
                            <table class="table table-bordered table-stripped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Qty</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>    
                                @foreach($purchases as $purchase)
                                    @foreach($purchase->details as $detail)
                                    <tr>
                                        <td>{{ $detail->product->name }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ $purchase->created_at }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                            @endforeach
                        </div>
                    </div>
                    @endif                
                
                    @if($transfer_records->deliveryOrders->isNotEmpty())
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ count($transfer_records->deliveryOrders->groupBy('delivery_order_no')) }} Delivery Orders / M.T.V created for M.R# {{ $transfer_records->transfer_no }}</h3>
                        </div>
                        <div class="card-body">
                            @foreach($transfer_records->deliveryOrders->groupBy('delivery_order_no') as $deliveryOrderNo => $deliveryOrdersGroup)
                            @php 
                            $do_no = $deliveryOrdersGroup->first()->delivered_by == 'supplier' ? $deliveryOrdersGroup->first()->delivery_order_no : \App\Models\TransferVoucher::find($deliveryOrdersGroup->first()->transfer_voucher_id)->voucher_no;
                            @endphp
                                <h6><a class="text-primary" href="{{ route('deliveryOrder.detail', encrypt($deliveryOrderNo)) }}" target="_blank">
                                    {{ $deliveryOrdersGroup->first()->delivered_by == 'supplier' ? 'Delivery Order #' : 'Material Transfer Voucher#' }}: {{ $do_no }}
                                    <i class="fas fa-external-link-alt"></i></a> 
                                </h6>
                                <table class="table table-bordered table-stripped">
                                    <thead>
                                        <tr>
                                            <th>{{ $deliveryOrdersGroup->first()->delivered_by == 'supplier' ? 'L.P.O#' : 'Type'  }}</th>
                                            <th>Project Name</th>
                                            <th>Product/Serial</th>
                                            <th>Qty</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deliveryOrdersGroup as $deliveryOrder)
                                            <tr>
                                                <td>{{ $deliveryOrder->purchase ? $deliveryOrder->purchase->purchase_no : 'M.T.V' }}</td>
                                                <td>
                                                    {{ $transfer_records->requestable_type == \App\Models\Project::class  ?
                                                        (isset($transfer_records->project) ? $transfer_records->project->name : '')
                                                        : (isset($transfer->warehouse) ? $transfer_records->warehouse->name : $transfer_records->requestable->name )
                                                    }}
                                                </td>
                                                <td>{{ $deliveryOrder->product->name }}</td>
                                                <td>{{ $deliveryOrder->quantity }}</td>
                                                <td>{{ $deliveryOrder->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                @endif
                
                
                @if($product)
                
                <div class="card">
                    <div class="card-header">
                        <h3>Stock Details for <strong>{{ $product->name }}</strong></h3>
                    </div> 
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Store Name</th>
                                        <th>Available Stock</th>
                                        @if(auth()->user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge']))
                                            <th>Total Transferred</th>
                                            <th>Total Received</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1; 
                                        $stocks = \App\Models\StockRecord::where('product_id', $product->id)->get();
                                        $total_transferred = 0; // Initialize total transferred
                                        if(auth()->user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge'])){
                                            $total_transferred = \App\Models\DeliveryOrder::where('product_id', $product->id)
                                                                        ->where('delivered_by', 'store')
                                                                        ->sum('quantity');
                                        }                                   
                                    @endphp
                            
                                    @if($stocks->isNotEmpty())
                                        @foreach($stocks as $row)
                                            @if(isset($row->stockable))
                                                <tr class="{{ optional($row->stockable)->name === 'Main Warehouse Sajaa' ? 'alert alert-primary fw-600' : ''}}">
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ optional($row->stockable)->name }}</td>
                                                    <td>{{ $row->quantity < 1 ? 'Out of Stock' : $row->quantity }}</td>
                                                    @if(optional($row->stockable)->name === 'Main Warehouse Sajaa' && auth()->user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge']))
                                                        <td>{{ $total_transferred }}</td>
                                                        <td>{{ $total_transferred + $row->quantity }}</td>
                                                    @elseif(auth()->user()->hasRole(['Admin', 'Super Admin', 'Warehouse Incharge']))
                                                        <td></td>
                                                        <td></td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                           
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Material Request Details for <strong>{{ $product->name }}</strong></h3>
                    </div> 
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>M.R #</th>
                                        <th>Project</th>
                                        <th>Request Date</th>
                                        <th>Requested Quantity</th>
                                        <th>Delivered Quantity</th>
                                        <th>Product</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @foreach($product->materialRequestDetails as $detail)
                                    @php 
                                    $transfer = \App\Models\Transfer::find($detail->transfer_id);
                                    $delivered_quantity = \App\Models\DeliveryOrder::where('transfer_id', $transfer->id)->where('product_id', $detail->product_id)->sum('quantity');
                                    @endphp
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $transfer->transfer_no }}</td>
                                                <td>{{ optional($transfer->requestable)->name }}</td>
                                                <td>{{ $detail->created_at }}</td>
                                                <td>{{ $detail->requested_quantity }}</td>
                                                <td>{{ $delivered_quantity }}</td>
                                                <td>{{ $detail->product->name }}</td>
                                            </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Purchase Details for <strong>{{ $product->name }}</strong></h3>
                    </div> 
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>M.R #</th>
                                        <th>Project</th>
                                        <th>Request Date</th>
                                        <th>Requested Quantity</th>
                                        <th>Delivery Order Quantity</th>
                                        <th>Product</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @foreach($product->purchaseDetails as $detail)
                                        @if($detail && $detail->purchase)
                                            @php
                                                $purchase = $detail->purchase;
                                                $delivered_quantity = \App\Models\DeliveryOrder::where('purchase_id', $purchase->id)
                                                    ->where('product_id', $detail->product_id)
                                                    ->sum('quantity');
                                            @endphp
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $purchase->purchase_no }}</td>
                                                <td>{{ optional(optional(optional($purchase->transfer)->requestable))->name }}</td>
                                                <td>{{ $detail->created_at }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ $delivered_quantity }}</td>
                                                <td>{{ $detail->product->name }}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Transfer Vouchers Detail for <strong>{{ $product->name }}</strong></h3>
                    </div> 
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Project</th>
                                        <th>M.R #</th>
                                        <th>Request Date</th>
                                        <th>Requested Quantity</th>
                                        <th>Transfer Voucher Quantity</th>
                                        <th>Product</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @foreach($product->transferVouchers as $detail)
                                        @if($detail && $detail->delivered_by === 'store')
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ optional($detail->transfer->project)->name }}</td>
                                                <td>{{ $detail->transfer_no }}</td>
                                                <td>{{ $detail->created_at }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ $detail->product->name }}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>                
                
                @endif
                
                @if(!empty($project_transfers))
                    <div class="card">
                        <div class="card-header">
                            <h3>Pending Material Requests for <strong>{{ $project_transfers->first()->requestable->name }}</strong></h3>
                        </div> 
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>M.R #</th>
                                            <th>Project</th>
                                            <th>Request Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach($project_transfers as $transfer)
                              
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $transfer->transfer_no }}</td>
                                                    <td>{{ optional($transfer->requestable)->name }}</td>
                                                    <td>{{ $transfer->created_at }}</td>
                                                    <td><span class="badge badge-pill badge-danger w-100">Pending</span></td>
                                                    <td><a href="{{ route('transfer.detail', encrypt($transfer->id)) }}" class="ml-4 text-primary">View Details</a></td>
                                                </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>                
                @endif
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#filterForm select').on('change', function() {
            $('#filterForm').submit();
        });
    });
</script>
@endpush
