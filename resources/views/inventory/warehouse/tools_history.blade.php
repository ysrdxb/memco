@extends('inventory.layout')
@section('title', 'Tools History')
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Stocks')}}</h5>
						<span>{{ __('List of Stocks')}}</span>
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
                <div class="card-header"><h3>{{ __( 'Tools History' )}}</h3></div>
                <div class="card-body">
                    <table id="stocks_table" class="table table-bordered table-stripped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Project Name</th>
                                <th>Tool Name</th>
                                <th>Serial No.</th>
                                <th>Serials QTY</th>
                                <th>Date Issued</th>
                                <th>M.R#</th>
                                <th>D.O#</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach($stockDetail as $row)
                                @if($row->deliveryOrderSerials->isNotEmpty())
                                    @php $serial = $row->deliveryOrderSerials->first(); @endphp
                                    @if($serial->deliveryOrder)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ optional(optional($serial->deliveryOrder)->transfer)->project->name ?? 'N/A' }}</td>
                                        <td>{{ $serial->serial->product->name }}</td>
                                        <td>{{ $serial->serial->serial_no }}</td>
                                        <td>1</td>
                                        <td>{{ $serial->created_at }}</td>
                                        <td>{{ $serial->deliveryOrder->transfer->transfer_no }}</td>
                                        <td>{{ $serial->deliveryOrder->delivery_order_no }}</td>
                                        <td>Not returned</td>
                                        <td></td>
                                    </tr>
                                    @endif
                                @endif
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
    $('.editUnitBtn').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var user_id = $(this).data('euser_id');
        var address = $(this).data('address');
        $('#editUnitId').val(id);
        $('#editName').val(name);
        $('#editAddress').val(address);
        $('#user_id').val(user_id);
        var url = '{{ route("warehouses.update", ":id") }}';
        url = url.replace(':id', id);
        
        handleFormSubmit('#updateForm', url, 'PUT', function(response) {
        }, function(error) {
        });
    });
});
  $(document).ready(function() {
    handleDeleteAction('.deleteBtn', '/warehouses/delete', function(response) {
    }, function(error) {
    });    
  });
</script>

@endpush