@extends('inventory.layout')
@section('title', 'Material Requisition Detail')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-truck bg-green"></i>
                    <div class="d-inline">
                        <h5>Material Requisition Detail</h5>
                        <span>View details of Material Requisition</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <nav class="breadcrumb-container" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/dashboard"><i class="ik ik-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#">Material Requisition Detail</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">        
            <div class="card">
                <div class="card-body">
                    <h5>Material Requisition Details</h5>
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <td>{{ $request->date }}</td>
                            </tr>
                            <tr>
                                <th>REF No.</th>
                                <td>{{ $request->ref_no }}</td>
                            </tr>
                            <tr>
                                <th>Store</th>
                                <td>{{ $request->store->name }}</td>
                            </tr>
                            <tr>
                                <th>Requested By</th>
                                <td>{{ $request->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Product</th>
                                <td>{{ $request->product->name }}</td>
                            </tr>
                            <tr>
                                <th>QTY</th>
                                <td>{{ $request->requested_quantity }}</td>
                            </tr>
                            <tr>
                                <th>Current Stock</th>
                                <td>
                                    @if(count($request->warehouseStockRecords))
                                        <span class="badge badge-{{$request->warehouseStockRecords[0]->quantity < $request->requested_quantity ? 'warning' : 'success' }} mb-1">{{ $request->warehouseStockRecords[0]->quantity }}</span>
                                    @else
                                        <span class="badge badge-warning mb-1"><i class="ik ik-info"></i> {{ __('Out of Stock') }}</span>
                                    @endif
                                </td>
                            </tr>                            
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge badge-pill badge-{{ $request->status == 'received' || $request->status == 'delivered' ? 'primary' : 'danger' }} mb-1">{{ $request->status }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12 card new-cust-card mb-4">
                            <div class="card-header bg-secondary">
                                <h3 class="text-white">Change Request Status</h3>
                            </div>
                            <div class="card-body">
                                <form class="forms-sample" id="submitForm" method="POST" action="#">
                                @csrf 
                                    <div class="form-group">
                                        <label for="action" class="">Remarks</label>
                                        <textarea name="action" id="action" class="form-control" rows="1">{{$request->statuses[0]->user_id==Auth::user()->id ? $request->statuses[0]->action : ''}}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status" class="">Quantity</label>
                                                <input type="number" name="quantity" id="quantity" value="{{ $request->requested_quantity }}" class="form-control" required>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status" class="">Status</label>
                                                <select name="status" id="status" class="form-control select2">
                                                    <option value="">Select</option>
                                                    <option value="delivered" {{$request->statuses[0]->user_id==Auth::user()->id && $request->statuses[0]->status == 'delivered' ? 'selected' : ''}}>Delivered</option>
                                                    <option value="pending" {{$request->statuses[0]->user_id==Auth::user()->id && $request->statuses[0]->status == 'pending' ? 'selected' : ''}}>Pending</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group"><br>
                                                <button type="submit" class="btn btn-secondary">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <h5>Request Status</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>By</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($request->statuses as $log)
                            <tr>
                                <td>{{ $log->created_at }}</td>
                                <td>{{ $log->user->name }}</td>
                                <td>{{ $log->quantity }}</td>
                                <td><span class="badge badge-pill badge-{{ $log->status == 'received' || $log->status == 'delivered' ? 'primary' : 'danger' }} mb-1">{{ $log->status }}</span></td>
                                <td>{{ $log->action }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
@push('script')
<script>
$(document).ready(function() {
    handleFormSubmit('#submitForm', '{{ route('materialRequest.changeStatus', encrypt($request->id)) }}','POST', function(response) {
        //console.log(response);
    }, function(error) {
        console.error(error);
    });
});
</script>
@endpush