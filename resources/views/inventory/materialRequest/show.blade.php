            @if(empty($data))
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
                        <small class="float-left">Material Request No. {{ $data->ref_no }}</small>
                        <a href="{{ route('materialRequest.print', encrypt($data->id) ) }}" class="float-right btn btn-warning small text-white btn-rounded" target="_blank">                        
                            <i class="fa fa-print"></i> Print
                        </a>                                                 
                        </h3>                       
                    </div>
				</div>                    
                <div class="row invoice-info">
                    <div class="col-md-12 row">
                        <div class="col-sm-3  invoice-col border">
                            From
                            <address>
                                <strong>{{ $data->warehouse->name }}</strong>
                            </address>
                        </div>
                        <div class="col-sm-3 invoice-col border">
                            To
                            <address>
                                <strong>{{ __('Procurement Department') }}</strong>
                            </address>
                        </div>
                        <div class="col-sm-3 invoice-col border">
                            Date 
                            <address>
                                <strong>{{ $data->created_at }}</strong>
                            </address>
                        </div>
                        <div class="col-sm-3 invoice-col border">
                            Status
                            <address>
                                <span class="badge badge-pill badge-{{$data->status=='received' || $data->status == 'delivered' ? 'success' : 'danger' }} mb-1">{{ $data->status }}</span>
                            </address>
                        </div>
                    </div>                                    
                    <div class="col-12">   
                        <h3 class="d-block w-100">Details</h3>                     
                        <table class="table table-hover table-stripped">
                            <thead>
                                <tr>
                                    <th class="wp-10">Item Code</th>
                                    <th class="w-35">Item Name</th>
                                    <th class="w-15">Requested QTY</th>
                                    <th class="w-15">Delivered QTY</th>
                                    <th class="w-15">Balance QTY</th>
                                    <th class="w-10">U.O.M</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->details as $row)
                                <tr>
                                    <td>{{ $row->product->code }}</td>
                                    <td>{{ $row->product->name }}</td>
                                    <td>{{ $row->requested_quantity }}</td>
                                    <td>{{ $row->delivered_quantity }}</td>
                                    <td>{{ $row->requested_quantity - $row->delivered_quantity }}</td>
                                    <td>{{ $row->unit->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 row">
                        <div class="card">
                            <div class="card-header"><h3>Remarks</h3></div>
                            <div class="card-body">
                                <div class="alert alert-secondary fade show" role="alert">
                                    <strong>{{ $data->remarks }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            @endif