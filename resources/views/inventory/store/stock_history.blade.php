@extends('inventory.layout')
@section('title', 'Stock History of '.$product->name)
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
                            <h5>{{ __($product->name)}}</h5>
                            <span>{{ __('Stock Record History of '.$product->name) }}</span>
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
                    <div class="card-header"><h3>{{ __( $project->name )}}</h3></div>
                    <div class="card-body">
                        <h2>Material Received History</h2>
                        @if($stock && count($stockDetails))
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Material Request No.</th>
                                        <th>Product Name</th>
                                        <th>Requested Quantity</th>
                                        <th>Received Quantity</th>
                                        <th>Balance Quantity</th>
                                        <th>Unit</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_delivered = 0;
                                            $deliveryOrders = \App\Models\DeliveryOrder::whereIn('transfer_id', $stockDetails->pluck('transfer_id'))
                                                ->whereIn('product_id', $stockDetails->pluck('product_id'))
                                                ->get();
                                            
                                            $total_delivered = $deliveryOrders->sum('quantity');                                        
                                    @endphp
                                    @foreach($stockDetails as $detail)
                                        @php
                                            
                                            $total_do = $deliveryOrders->where('transfer_id', $detail['transfer_id'])
                                                ->where('product_id', $detail['product_id'])
                                                ->sum('quantity');
                                        @endphp
                                        <tr>
                                            <td>{{ $detail['transfer_no'] }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $detail['requested_quantity'] }}</td>
                                            <td>{{ $total_do }}</td>
                                            <td>
                                                @php
                                                    $difference = $detail['requested_quantity'] - $total_do;
                                                @endphp
                                                
                                                @if($difference < 0)
                                                    +{{ abs($difference) }}
                                                @else
                                                    {{ $difference }}
                                                @endif
                                            </td>
                                            <td>{{ $detail['unit'] }}</td>
                                            <td>{{ $detail['date'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="p-4">
                                    <tr class="mt-4">
                                        <td colspan="6"></td>
                                        <td>
                                            <strong>Total Received:</strong> {{ $total_delivered }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td><strong>Total Issued:</strong> {{ $materialIssued->sum('quantity') }} </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td>
                                            <strong>Total In Stock:</strong> {{ $stock->quantity }}
                                        </td>
                                    </tr>
                                    
                                </tfoot>
                            </table>
                        @else
                            <div class="alert alert-danger">No stock history found for {{ $product->name }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header"><h2>{{ __( 'Material Issued History' )}}</h2></div>
                    <div class="card-body">
                        @if(count($materialIssued))
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Brand</th>
                                        <th>Issued By</th>
                                        <th>Person HR Code</th>
                                        <th>Person Name</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($materialIssued as $key => $row)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            <td>{{ $row->quantity }}</td>
                                            <td>{{ $row->brand->name }}</td>
                                            <td>{{ optional($row->user)->name }}</td>
                                            <td>{{ $row->employee->employee_no }}</td>
                                            <td>{{ $row->employee->name }}</td>
                                            <td>{{ $row->updated_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="p-4">
                                    <tr class="mt-4">
                                        <td colspan="7"></td>
                                        <td>
                                            <strong>Total Issued:</strong> {{ $materialIssued->sum('quantity') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <div class="alert alert-danger">No material issued history found for {{ $product->name }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
