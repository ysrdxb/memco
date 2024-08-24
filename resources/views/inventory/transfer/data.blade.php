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
                        <h5>{{ __($transfer->transfer_no)}}</h5>
                        <span>{{ __('Details of for Material Request #'.$transfer->transfer_no) }}</span>
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
                        <div class="col-12">   
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <!-- <th>Delivered QTY</th>
                                        <th>Balance</th> -->
                                        <th>Unit</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @php $counter = 1; @endphp
                                    @foreach($transfer->details as $row)
                                        @php
                                           // $deliveredQuantity = $transfer->deliveryOrders->where('product_id', $row->product_id)->sum('quantity');

                                           // $balance = $row->requested_quantity - $deliveredQuantity;
                                        @endphp
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $row->product->code }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            <td>{{ $row->requested_quantity }}</td>
                                            <td>{{ $row->unit->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><h3>Remarks</h3></div>
                                <div class="card-body">
                                    <div class="alert alert-secondary fade show" role="alert">
                                        <strong>{{ $transfer->remarks }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>                    
                    </div>
                    @endif
                </div>
            </div>
        </div>      
    </div>
</div>

@endsection