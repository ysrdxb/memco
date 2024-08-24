@extends('inventory.layout')
@section('title', 'Manage Suppliers')
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Suppliers')}}</h5>
						<span>{{ __('List of Suppliers')}}</span>
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
                            <h3>{{ __('Supplier')}}</h3>
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
                                            <th>Name</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($suppliers as $row)
                                        <tr>
                                            <td>
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
                                                    <span class="custom-control-label">&nbsp;</span>
                                                </label>
                                            </td>
                                            <td>#{{$row->id}}</td>
                                            <td>{{$row->name}}</td>
                                            <td>{{$row->updated_at->diffForHumans()}}</td>
                                            <td>
                                                <a href="#" class="editUnitBtn" data-id="{{ encrypt($row->id) }}" data-name="{{ $row->name }}" data-email="{{ $row->email }}" data-phone="{{ $row->phone }}" data-country="{{ $row->country }}" data-city="{{ $row->city }}" data-address="{{ $row->address }}" data-details="{{ $row->details }}" data-toggle="modal" data-target="#editUnitModal">
                                                    <i class="ik ik-edit f-16 mr-15 text-green"></i>
                                                </a>
                                                <a href="#!" class="deleteBtn" item-id="{{encrypt($row->id)}}"><i class="ik ik-trash-2 f-16 text-red"></i></a>
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

<!-- Edit modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" role="dialog" aria-labelledby="editUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUnitModalLabel">Edit Store</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    <!-- Include input fields for editing unit details -->
                    <div class="form-group">
                        <label for="editName">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>                    
                    <div class="form-group">
                        <label for="editName">Email</label>
                        <input type="text" class="form-control" id="editEmail" name="email" required>
                    </div>                    
                    <div class="form-group">
                        <label for="editPhone">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone" required>
                    </div>                    
                    <div class="form-group">
                        <label for="editCountry">Country</label>
                        <input type="text" class="form-control" id="editCountry" name="country" required>
                    </div>                    
                    <div class="form-group">
                        <label for="editCity">City</label>
                        <input type="text" class="form-control" id="editCity" name="city" required>
                    </div>                    
                    <div class="form-group">
                        <label for="editAddress">Address</label>
                        <input type="text" class="form-control" id="editAddress" name="address" required>
                    </div>                    
                    <div class="form-group">
                        <label for="editDetails">Details</label>
                        <input type="text" class="form-control" id="editDetails" name="details" required>
                    </div>                    
                     
                    <input type="hidden" id="editUnitId" name="id">
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- category add modal-->
<div class="modal fade edit-layout-modal pr-0 " id="categoryAdd" tabindex="-1" role="dialog" aria-labelledby="categoryAddLabel" aria-hidden="true">
    <div class="modal-dialog w-300" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Add Supplier')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="submitForm">
                    <div class="form-group">
                        <label class="d-block"> Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Name" required>
                    </div>
                                                                                                                       
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit" name="Save">Save <i class="ik ik-save"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
@push('script')
<script>
  $(document).ready(function() {
    handleFormSubmit('#submitForm', '{{ route('suppliers.store') }}','POST', function(response) {
    }, function(error) {
    });
  });
$(document).ready(function() {
    $('.editUnitBtn').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var phone = $(this).data('phone');
        var country = $(this).data('country');
        var city = $(this).data('city');
        var address = $(this).data('address');
        var details = $(this).data('details');

        $('#editUnitId').val(id);
        $('#editName').val(name);
        $('#editEmail').val(email);
        $('#editPhone').val(phone);
        $('#editCountry').val(country);
        $('#editCity').val(city);
        $('#editAddress').val(address);
        $('#editDetails').val(details);

        var url = '{{ route("suppliers.update", ":id") }}';
        url = url.replace(':id', id);
        
        handleFormSubmit('#updateForm', url, 'PUT', function(response) {
        }, function(error) {
        });
    });
});
  $(document).ready(function() {
    handleDeleteAction('.deleteBtn', '/supplier/delete', function(response) {
    }, function(error) {
    });    
  });
</script>

@endpush