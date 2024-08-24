@php 
$type = isset($type) ? $type : 'do';
@endphp
@extends('inventory.layout')
@section('title', 'Material Request Status')
@push('head')

<style>
.elevation-2 {
    box-shadow: 2px 2px 4px #a7a7a7;
}

.info-box {
    box-shadow: 0 0 1px rgba(42, 74, 200, 0.13), 0 1px 3px rgba(21, 145, 255, 0.2);
    border-radius: 0.25rem;
    background-color: #fbfbfb;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 1rem;
    min-height: 120px;
    padding: 1.5rem;
    position: relative;
    width: 100%;
}
.info-box .info-box-icon {
    border-radius: 0.25rem;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    text-align: center;
    width: 70px;
}
.info-box-icon:hover{
    background: red;
}
.bg-gradient-purple {
    background: #6f42c1 linear-gradient(180deg,#855eca,#6f42c1) repeat-x!important;
    color: #fff;
}
.bg-gradient-olive {
    background: #3d9970 linear-gradient(180deg,#5aa885,#3d9970) repeat-x!important;
    color: #fff;
}
.bg-gradient-danger {
    background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x!important;
    color: #fff;
}
.bg-gradient-warning {
    background: #ffc107 linear-gradient(180deg,#ffca2c,#ffc107) repeat-x!important;
    color: #1f2d3d;
}
.bg-gradient-secondary {
    background: #6c757d linear-gradient(180deg,#828a91,#6c757d) repeat-x!important;
    color: #fff;
}
.bg-gradient-orange {
    background: #fd7e14 linear-gradient(180deg,#fd9137,#fd7e14) repeat-x!important;
    color: #fff;
}
.bg-gradient-dark {
    background: #343a40 linear-gradient(180deg,#52585d,#343a40) repeat-x!important;
    color: #fff;
}
.bg-gradient-pink {
    background: #e83e8c linear-gradient(180deg,#eb5b9d,#e83e8c) repeat-x!important;
    color: #fff;
}
.bg-gradient-primary {
    background: #007bff linear-gradient(180deg,#268fff,#007bff) repeat-x!important;
    color: #fff;
}
.bg-gradient-info {
    background: #17a2b8 linear-gradient(180deg,#3ab0c3,#17a2b8) repeat-x!important;
    color: #fff;
}
.bg-gradient-success {
    background: #28a745 linear-gradient(180deg,#48b461,#28a745) repeat-x!important;
    color: #fff;
}
.info-box .info-box-content {
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    line-height: 1.8;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    padding: 0 10px;
}

.info-box .info-box-number {
    display: block;
    margin-top: 0.25rem;
    font-weight: 700;
}
.info-box:hover {
    box-shadow: 0 3px 6px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.23)!important;
}    
.text-bold{font-weight:bold;font-size:16px}     
.card {
    background-color: #fff;
    border-radius: 10px;
    border: none;
    position: relative;
    margin-bottom: 30px;
    box-shadow: 0 0.46875rem 2.1875rem rgba(90,97,105,0.1), 0 0.9375rem 1.40625rem rgba(90,97,105,0.1), 0 0.25rem 0.53125rem rgba(90,97,105,0.12), 0 0.125rem 0.1875rem rgba(90,97,105,0.1);
}


.card .card-statistic-3 .card-icon-large .fas, .card .card-statistic-3 .card-icon-large .far, .card .card-statistic-3 .card-icon-large .fab, .card .card-statistic-3 .card-icon-large .fal {
    font-size: 110px;
}

.card .card-statistic-3 .card-icon {
    text-align: center;
    line-height: 50px;
    margin-left: 15px;
    color: #000;
    position: absolute;
    right: -5px;
    top: 20px;
    opacity: 0.1;
}

.l-bg-cyan {
    background: linear-gradient(135deg, #289cf5, #84c0ec) !important;
    color: #fff;
}

.l-bg-green {
    background: linear-gradient(135deg, #23bdb8 0%, #43e794 100%) !important;
    color: #fff;
}

.l-bg-orange {
    background: linear-gradient(to right, #f9900e, #ffba56) !important;
    color: #fff;
}

.l-bg-cyan {
    background: linear-gradient(135deg, #289cf5, #84c0ec) !important;
    color: #fff;
}

</style>
@endpush
@section('content')   
<div class="container-fluid"> 
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-4">
            <div class="page-header-title">
                
            </div>
        </div>
        <div class="col-lg-8">
            <div class="text-right">
            @include('include.backButtons')                                                  
            </div>
        </div>
    </div>
</div>     
<div class="card">
<div class="card-header"><h3>{{ $pageName }}</h3></div>
<div class="card-body">
    <div class="row">    
        @if($page == 'transfers')
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                <div class="card l-bg-blue-dark" style="background: linear-gradient(170deg, #44AAB3 0%, #191C2C 100%)">
                    <div class="card-statistic-3 p-4">
                        <a href="{{ route('transfer.getStatus', encrypt(\App\Models\Warehouse::first()->id)) }}">
                            <div class="mb-0">
                                <h6 class="card-title mb-0 text-white" style="min-height: 2.3em; overflow: hidden; text-overflow: ellipsis;">Main Store Sajaa</h6>                            
                            </div>
                            <div class="row align-items-center mb-2 d-flex">
                                <div class="col-8">
                                    <span class="text-white">Pending: {{ $warehouse_pending_transfers }}</span>
                                </div>
                                <div class="col-4 text-right">
                                    <span class="text-white">Total: {{ $warehouse_total_transfers }}</span>
                                </div>
                            </div>
                            @php
                                $pendingPercentage = ($warehouse_pending_transfers / $warehouse_total_transfers) * 100;
                                $completedPercentage = 100 - $pendingPercentage;
                            @endphp
                            <div class="progress mt-1" data-height="8" style="height: 8px;">
                                <div class="progress-bar progress-bar-striped bg-{{ $pendingPercentage == 0 ? 'success' : 'primary' }} progress-bar-animated" role="progressbar" data-width="{{ $completedPercentage }}%" aria-valuenow="{{ $completedPercentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $completedPercentage }}%;"></div>
                                <div class="progress-bar progress-bar-striped bg-{{ $pendingPercentage == 100 ? 'danger' : 'yellow' }}" role="progressbar" data-width="{{ $pendingPercentage }}%" aria-valuenow="{{ $pendingPercentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $pendingPercentage }}%;"></div>
                            </div>
                            <div class="mt-2">
                                <span class="text-white">Completed: {{ (number_format($completedPercentage, 2)) }}%</span>
                                <span class="float-right text-white">Pending: {{ (number_format($pendingPercentage, 2)) }}%</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>      
            @foreach($projectsWithTransferStatus as $item)
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                <div class="card l-bg-blue-dark">
                    <div class="card-statistic-3 p-4">
                        <a href="{{ route('transfer.getStatus', encrypt($item['project']->id)) }}">
                            <div class="mb-0">
                                <h6 class="card-title mb-0" style="min-height: 2.3em; overflow: hidden; text-overflow: ellipsis;">{{ $item['project']->name }}</h6>                            
                            </div>
                            <div class="row align-items-center mb-2 d-flex">
                                <div class="col-8">
                                    <span>Pending: {{ $item['pending_transfers'] }}</span>
                                </div>
                                <div class="col-4 text-right">
                                    <span>Total: {{ $item['total_transfers'] }}</span>
                                </div>
                            </div>
                            @php
                                $pendingPercentage = ($item['pending_transfers'] / $item['total_transfers']) * 100;
                                $completedPercentage = 100 - $pendingPercentage;
                            @endphp
                            <div class="progress mt-1" data-height="8" style="height: 8px;">
                                <div class="progress-bar progress-bar-striped bg-{{ $pendingPercentage == 0 ? 'success' : 'primary' }} progress-bar-animated" role="progressbar" data-width="{{ $completedPercentage }}%" aria-valuenow="{{ $completedPercentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $completedPercentage }}%;"></div>
                                <div class="progress-bar progress-bar-striped bg-{{ $pendingPercentage == 100 ? 'danger' : 'yellow' }}" role="progressbar" data-width="{{ $pendingPercentage }}%" aria-valuenow="{{ $pendingPercentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $pendingPercentage }}%;"></div>
                            </div>
                            <div class="mt-2">
                                <span>Completed: {{ (number_format($completedPercentage, 2)) }}%</span>
                                <span class="float-right">Pending: {{ (number_format($pendingPercentage, 2)) }}%</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
        
        @elseif($page == 'deliveryOrders')
            @foreach($warehouses as $row)
                <div class="col-lg-3 col-12">
                    <div class="info-box elevation-2">
                        <span class="info-box-icon bg-gradient-olive elevation-2">
                            <span class="" style="font-size:18px">
                                {{ optional($row->transfers)->flatMap->deliveryOrders->where('delivered_by', 'supplier')->pluck('delivery_order_no')->unique()->count() }}
                            </span>
                        </span>
                        <div class="info-box-content">
                            <a href="{{ route('deliveryOrder.list', ['id' => encrypt($row->id), 'type' => $type, 'isWarehouse' => 'main']) }}">
                                <span class="info-box-text text-bold">{{ $row->name }}</span>
                                <span class="info-box-number">
                                    <h2></h2>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach($data as $row)
                <div class="col-lg-3 col-12">
                    <div class="info-box elevation-2">
                        <span class="info-box-icon bg-gradient-dark elevation-2">
                            <span class="" style="font-size:18px">
                                @if($type && $type == 'mtv')
                                    {{ $row->transfers->flatMap->deliveryOrders->where('delivered_by', 'store')->pluck('delivery_order_no')->unique()->count() }}
                                @else
                                    {{ $row->transfers->flatMap->deliveryOrders->where('delivered_by', 'supplier')->pluck('delivery_order_no')->unique()->count() }}
                                @endif
                            </span>
                        </span>
                        <div class="info-box-content">
                            <a href="{{ route('deliveryOrder.list', encrypt($row->id)) }}{{ $type ? '?type='.$type : '' }}">
                                <span class="info-box-text text-bold">{{ $row->name }}</span>
                                <span class="info-box-number">
                                    <h2></h2>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

        @elseif($page == 'purchases')
            @foreach($warehouses as $row)
                <div class="col-lg-3 col-12">
                    <div class="info-box elevation-2">
                        <span class="info-box-icon bg-gradient-olive elevation-2">
                            <span class="" style="font-size:18px">
                                {{ $row->transfers->flatMap->purchases->pluck('transfer_id')->unique()->count() }}
                            </span>
                        </span>
                        <div class="info-box-content">
                            <a href="{{ route('purchases.list', encrypt($row->id)) }}?isWarehouse=main">
                                <span class="info-box-text text-bold">{{ $row->name }}</span>
                                <span class="info-box-number">
                                    <h2></h2>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach        
            @foreach($data as $row)
                <div class="col-lg-3 col-12">
                    <div class="info-box elevation-2">
                        <span class="info-box-icon bg-gradient-dark elevation-2">
                            <span class="" style="font-size:18px">
                                {{ $row->transfers->flatMap->purchases->pluck('transfer_id')->unique()->count() }}
                            </span>
                        </span>
                        <div class="info-box-content">
                            <a href="{{ route('purchases.list', encrypt($row->id)) }}">
                                <span class="info-box-text text-bold">{{ $row->name }}</span>
                                <span class="info-box-number">
                                    
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach            
        @endif
    </div>
</div>
</div>
</div>
@endsection