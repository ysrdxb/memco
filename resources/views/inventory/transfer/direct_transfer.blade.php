@extends('inventory.layout')
@section('title', 'Direct Transfers')
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Direct Transfers')}}</h5>
						<span>{{ __('List of Direct Transfers')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
                <a href="#categoryAdd" data-toggle="modal" data-target="#categoryAdd" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Create</a>
                @include('include.backButtons')
            </div>
		</div>
	</div>    
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>{{ __('Direct Transfers')}}</h3>
                        </div>
                        <div class="card-body p-0 table-border-style">
                            <div class="table-responsive">
                                <table id="advanced_table" class="table">
                                    <thead>
                                        <tr>
                                            <th class="nosort" width="10">
                                                <label class="custom-control custom-checkbox m-0">
                                                    <input type="checkbox" class="custom-control-input" id="selectall" name="" value="option2">
                                                    <span class="custom-control-label">&nbsp;</span>
                                                </label>
                                            </th>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Store</th>
                                            <th>Sub Store</th>
                                            <th>Person</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $row)
                                        <tr>
                                            <td>
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
                                                    <span class="custom-control-label">&nbsp;</span>
                                                </label>
                                            </td>
                                            <td>#{{ $row->id }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            <td>{{ $row->quantity }}</td>
                                            <td>{{ $row->store->name }}</td>
                                            <td>{{ $row->subStore->name }}</td>
                                            <td>{{ $row->user->name }}</td>      
                                            <td>{{$row->updated_at->diffForHumans()}}</td>
                                            <td>
                                                @can('store_delete')
                                                <a href="#!" class="deleteBtn" item-id="{{encrypt($row->id)}}"><i class="ik ik-trash-2 f-16 text-red"></i></a>
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
 
        </div>
    </div>
</div>

@can('store_create')
<!-- category add modal-->
<div class="modal fade pr-0 edit-layout-modal" id="categoryAdd" tabindex="-1" role="dialog" aria-labelledby="categoryAddLabel" aria-hidden="true">
    <div class="modal-dialog " role="document" style="width:25% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Issue Material')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="submitForm">
                    <div class="form-group">
                        <label for="editName">Select Product</label>
                        <select id="productSearch" class="form-control product_ids" name="product_id"></select>
                    </div> 
                    <div class="form-group">
                        <label for="">Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>                
                    <div class="form-group">
                        <label for="editName">Select Store</label>
                        <select name="store_id" id="store_id" class="form-control select2" required>
                            <option value="">-Select-</option>
                            @foreach($stores as $row)
                                <option value="{{$row->id}}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>    
                    <div class="form-group">
                        <label class="d-block">Sub Store</label>
                        <select name="sub_store_id" id="sub_store_id" class="form-control select2" required>
                            <option value="">-Select-</option>
                        </select>        
                    </div>                                                                                                 

                    <div class="form-group">
                        <label for="editName">Select Person</label>
                        <select name="user_id" id="user_id" class="form-control select2" required>
                            <option value="">-Select-</option>
                            @foreach($users as $user)
                                @php
                                    $roles = $user->get_roles();
                                    $roleNames = implode(', ', $roles);
                                @endphp
                                <option value="{{$user->id}}">{{ $user->name }} ({{$roleNames}})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="d-block">Remarks</label>
                        <textarea name="remarks" class="form-control" id="" cols="30" rows="1"></textarea>
                    </div>                                                            
                    <div class="form-group mt-4">
                        <button class="btn btn-primary" type="submit" name="Save">Save <i class="ik ik-save"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
@push('script')
@can('store_create')
<script>
    $(document).ready(function() {
        $('#productSearch').select2({
            placeholder: 'Search for a product',
            minimumInputLength: 0, 
            ajax: {
                url: '{{ route('products.search') }}',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    });    

    $(document).ready(function() {
        $('#store_id').change(function() {
            var storeId = $(this).val();
            $('#sub_store_id').empty();
            if (storeId) {
                $.ajax({
                    url: "{{ url('stores/getSubStores') }}/" + storeId,
                    type: 'GET',
                    dataType: 'html',
                    success: function(data) {                        
                        $('#sub_store_id').append(data);                        
                    }
                });
            } else {
                $('#sub_store_id').empty();
            }
        });
    });

  $(document).ready(function() {
    handleFormSubmit('#submitForm', '{{ route('directTransfers.save') }}','POST', function(response) {
    }, function(error) {
    });
  });

</script>
@endcan
@can('store_delete')
<script>
  $(document).ready(function() {
    handleDeleteAction('.deleteBtn', '/directTransfers/delete', function(response) {
    }, function(error) {
    });    
  });
</script>
@endcan
@endpush