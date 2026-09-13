@extends('inventory.layout')
@section('title', 'Material Requisition')
@section('content')
<div class="container-fluid">
	<!-- Header Banner -->
    <div class="header-dashboard-clean">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box-clean">
                <i class="ik ik-list"></i>
            </div>
            <div>
                <h3 class="header-title-text-clean">{{ __('Material Requests from Store')}}</h3>
                <p class="header-sub-text-clean">{{ __('Material Requests from store')}}</p>
            </div>
        </div>
        <div class="d-flex align-items-center" style="gap: 12px;">
            <a href="{{ url()->previous() }}" class="btn btn-secondary-memco ml-4"><span class="ik ik-arrow-left"></span> Back</a>
        </div>
    </div> 
	<div class="row">
		<div class="col-md-12">		
			<div class="table-card">
				<div class="card-body">
					<table id="advanced_table" class="table table-custom">
						<thead>
							<tr>
								<th class="nosort">Date</th>
								<th>Requisition Code</th>
								<th>Store Name</th>
								<th>Requested By</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($transfers as $transfer)
							<tr>
								<td>{{ $transfer->date }}</td>
								<td>{{ $transfer->ref_no }}</td>
								<td>{{ $transfer->store->name }}</td>
								<td>{{ $transfer->user->name }}</td>
								<td><span class="badge badge-pill badge-{{$transfer->status=='received' || $transfer->status == 'delivered' ? 'success' : 'danger' }} mb-1">{{ $transfer->status }}</span></td>
								<td>
									<button class="showRequest btn btn-secondary btn-rounded" data-tid="{{ encrypt($transfer->id) }}" data-toggle="modal" data-target="#InvoiceModal">Show</button>
									@if($transfer->status === 'delivered')
										<a href="{{ route('transfer.delivery-order', encrypt($transfer->id)) }}" target="_blank"  class="showRequest btn btn-info btn-rounded ml-4">Delivery Order</a>
									@endif

									@can('deleteTransfer')
										<a href="#!" class="deleteBtn ml-4 float-right" item-id="{{encrypt($transfer->id)}}"><i class="ik ik-trash-2 f-16 text-red"></i></a>
									@endcan	
																
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
	</div>
</div>
<div class="modal fade edit-layout-modal pr-0 " id="InvoiceModal" role="dialog" aria-labelledby="InvoiceModalLabel" aria-hidden="true">
	<div class="modal-dialog mw-70" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="InvoiceModalLabel">Transfer Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="card-body">
                    <div id="showData"></div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('script')
<script>
  $(document).ready(function() {

    handleDeleteAction('.deleteBtn', '/transfer/stores/delete', function(response) {
    }, function(error) {
    });    

    $('.showRequest').on('click', function() {
      var tid = $(this).data('tid');
      $.ajax({
        url: "{{ url('/transfers/warehouses/show') }}" + '/' + tid, 
        type: 'GET',
        success: function(data) {
          $('#showData').html(data);
        }
      });
    });
  });  
</script>
@endpush
