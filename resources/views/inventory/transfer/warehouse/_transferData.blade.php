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
                        <small class="float-left">Transfer #{{ $transfer->ref_no }}</small>
                        <a href="{{ route('transfer.print', encrypt($transfer->id) ) }}" class="float-right btn btn-warning small text-white btn-rounded" target="_blank">                        
                            <i class="fa fa-print"></i> Print
                        </a>                                                 
                        </h3>                       
                    </div>
				</div>                    
                <div class="row invoice-info">
                    <div class="col-sm-3  invoice-col">
                        From
                        <address>
                            <strong>{{ $transfer->store->name }}</strong>
                        </address>
                    </div>
                    <div class="col-sm-3 invoice-col">
                        To
                        <address>
                            <strong>{{ $transfer->warehouse->name }}</strong>
                        </address>
                    </div>
                    <div class="col-sm-3 invoice-col text-right">
                        Transfer Date 
                        <address>
                            <strong>{{ $transfer->date }}</strong>
                        </address>
                    </div>
                    <div class="col-sm-3 invoice-col text-right">
                        Status
                        <address>
                            <span class="badge badge-pill badge-{{$transfer->status=='received' || $transfer->status == 'delivered' ? 'success' : 'danger' }} mb-1">{{ $transfer->status }}</span>
                        </address>
                    </div>                                    

                    <div class="col-12">   
                    <h3 class="d-block w-100">Transfer Details</h3>   
                    <form class="forms-sample" id="submitForm" method="POST" action="javascript:;" data-trid="{{ encrypt($transfer->id) }}">                  
                        <table class="table table-hover table-responsive table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th class="wp-20">Item Code</th>
                                    <th class="w-40">Item Name</th>
                                    <th class="w-10">Requested QTY</th>
                                    <th class="w-10">Available QTY</th>
                                    <th class="w-15">Deliver QTY</th>
                                    <th class="w-10">U.O.M</th>
                                    <th class="w-10">Brand</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfer->details as $row)
                                <tr>
                                    <td>{{ $row->product->code }}</td>
                                    <td>{{ $row->product->name }}</td>
                                    <td>{{ $row->requested_quantity }}</td>
                                    <td>
                                        @if($row->product->stockRecords->isNotEmpty())
                                        <span class="badge badge-info">{{ $row->product->stockRecords[0]->quantity }} {{ $row->unit->name }}</span>
                                        @else
                                        <span class="badge badge-warning">Out of stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="hidden" name="product_id[]" value="{{ $row->product_id }}">
                                        <input type="number" class="form-control" value="{{ $row->delivered_quantity > 0 ? $row->delivered_quantity : $row->requested_quantity }}" name="delivered_quantity[{{ $row->product_id }}]" {{ $transfer->status === 'delivered' ? 'readonly' : '' }}>
                                    </td>
                                    <td>{{ $row->unit->name }}</td>
                                    <td>{{ $row->brand->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-md-6 text-right">

                            </div>
                            <div class="col-md-6">

                            <div class="progress-task">
                                <div class="dd" data-plugin="nestable">
                                    <ol class="dd-list">                                   
                                        <li class="dd-item" data-id="1">
                                            <div class="dd-handle">                                        
                                                <h6><strong>{{ $transfer->user->name }} ({{ $transfer->store->name }})</strong> created the request <small class="float-right date">{{ $transfer->created_at->diffForHumans() }}</small></h6>
                                                <p>{{ $transfer->remarks }}</p>
                                            </div>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                            
                            @if($transfer->status === 'delivered' && $transfer->delivery)
                                <div class="completed-task">
                                    <div class="dd" data-plugin="nestable">
                                        <ol class="dd-list">                                   
                                            <li class="dd-item" data-id="1">
                                                <div class="dd-handle">                                        
                                                    <h6><strong>{{ $transfer->delivery->user->name }} ({{$transfer->warehouse->name}})</strong> delivered the request <small class="float-right date">{{ $transfer->updated_at->diffForHumans() }}</small></h6>
                                                    <p>{{ $transfer->delivery->comments }}</p>
                                                </div>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            
                                <div class="form-group">
                                    <label for="driver_name">Driver Name:</label>
                                    <input type="text" readonly class="form-control" value="{{ $transfer->delivery->driver->name }}">
                                </div>                            
                                <div class="form-group">
                                    <label for="comment">Comment:</label>
                                    <textarea class="form-control" id="comment" name="comments" rows="1" readonly>{{ $transfer->delivery ? $transfer->delivery->comments : '' }}</textarea>
                                </div>
                            @else 
                            
                                <div class="form-group">
                                    <label for="driver_name">Driver Name:</label>
                                    <select name="driver_id" id="driver_id" class="form-control">
                                        <option value="">Select</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>

                                </div>                            
                                <div class="form-group">
                                    <label for="comment">Comment:</label>
                                    <textarea class="form-control" id="comment" name="comments" rows="1"></textarea>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="form-group text-right">
                                    <button class="btn btn-primary" type="submit">Deliver Now</button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </form>
                    </div>
                </div>
            @endif     

<script>
$(document).ready(function() {

    $('#submitForm').submit(function(event) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });        
        event.preventDefault();
        
        var tid = $(this).data('tid');
        var form = $(this);
        var url = "{{ route('transfer.warehouses.update', encrypt($transfer->id)) }}";

        // Perform AJAX request
        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(), // Serialize form data
                success: function(response) {
                if (response.redirect && response.redirect !== '' && response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        html: response.message,
                    });          
                    window.location.href = response.redirect;
                    } else {
                    Swal.fire({
                        icon: 'error',  
                        title: 'Sorry!!',
                        html: response.message,
                    });          
                    }
                    if (typeof successCallback === 'function') {
                    successCallback(response);
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = Object.values(errors).flat().join('<br>');
                    Swal.fire({
                        icon: 'error',  
                        title: 'Sorry!!',
                        html: errorMessage,
                    });
                    } else {
                    Swal.fire({
                        icon: 'error',  
                        title: 'Sorry!!',
                        html: 'An Error Occured',
                    });
                    }
                    if (typeof errorCallback === 'function') {
                    errorCallback(xhr.responseText);
                    }
                }
        });
    });
});                
</script>
            