@extends('inventory.layout')
@section('title', 'Add Purchase')
@section('content')
	<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Purchases')}}</h5>
						<span>{{ __('Add L.P.O')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
            @include('include.backButtons')
            </div>
		</div>
	</div>
        <div class="row">
        <div class="card mb-3">
            <div class="card-body">
                <form id="submitForm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-group col-sm-2">
                                    <label for="transfer_id">Material Request No.</label>
                                    <select name="transfer_id" id="transfer_id" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach($requests as $request)
                                        <option value="{{ encrypt($request->id) }}">{{ $request->transfer_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="datepicker">L.P.O Number</label>
                                    <input required autocomplete="off" type="number" name="purchase_no" class="form-control" id="purchase_no" placeholder="L.P.O No.">
                                </div>                                
                                <div class="form-group col-sm-2">
                                    <label for="datepicker">Purchase Date</label>
                                    <input required autocomplete="off" type="text" name="date" class="form-control datetimepicker-input" id="datepicker" data-toggle="datetimepicker" data-target="#datepicker" placeholder="Select Date">
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="supplier_id">Supplier</label>
                                    <select class="form-control select2" name="supplier_id" required>
                                        <option value="">Add Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="notes">Purchase Note</label>
                                    <textarea class="form-control" name="notes" rows="1" placeholder="Enter Note"></textarea>
                                </div>
                            </div>  
                            <div id="products"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function deleteItem(element) {
        $(element).closest('.product').remove();
    }     
    $(document).ready(function() {
        $('select[name="transfer_id"]').on('change', function() {
            $('#products').empty();
            var transferId = $(this).val();
            if (transferId) {
                $.ajax({
                    url: '{{ route('transfer.getProductsForTransferRequest') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        transfer_id: transferId
                    },
                    dataType: 'html',
                    success: function(response) {
                        $('#products').html(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(error);
                    }
                });
            } else {
                // Clear the product selection dropdown if no material request is selected
                $('select[name="product_id[]"]').empty().trigger('change');
            }
        });

    });
  $(document).ready(function() {
    handleFormSubmit('#submitForm', '{{ route('purchases.store') }}','POST', function(response) {
        //
    }, function(error) {
        //
    });

    $('#warehouse_id').change(function() {
        $('select[name="product_id[]"]').trigger('change');
    });    
    
});

</script>

@endpush