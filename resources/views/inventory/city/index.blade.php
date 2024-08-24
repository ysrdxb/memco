@extends('inventory.layout')
@section('title', 'Cities')
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-8">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Cities')}}</h5>
						<span>{{ __('List of cities')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-4 text-right">
                <a href="#categoryAdd" data-toggle="modal" data-target="#categoryAdd" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Add</a>
                @include('include.backButtons')
            </div>
		</div>
	</div>
    <div class="row">
        <!-- start message area-->
        @include('include.message')
        <!-- end message area-->
        <div class="col-md-12">
            <div class="mb-2 clearfix">
                <div class="d-block mb-3 text-right">
                    <button class="btn btn-danger" href="#categoryAdd" data-toggle="modal" data-target="#categoryAdd">
                        <i class="ik ik-plus"></i> Add City
                    </button>
                </div>
            </div>
            <div class="separator mb-20"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>{{ __('Cities')}}</h3>
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
                                        @foreach($cities as $row)
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
                                                <a href="#" class="editUnitBtn" data-id="{{ encrypt($row->id) }}" data-name="{{ $row->name }}" data-toggle="modal" data-target="#editUnitModal">
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
                    {{ $cities->links() }}
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
                <h5 class="modal-title" id="editUnitModalLabel">Edit City</h5>
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
                    <!-- Add other input fields for editing -->
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
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Add City')}}</h5>
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
    handleFormSubmit('#submitForm', '{{ route('cities.store') }}','POST', function(response) {
      //console.log(response);
    }, function(error) {
      console.error(error);
    });
  });
$(document).ready(function() {
    $('.editUnitBtn').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#editUnitId').val(id);
        $('#editName').val(name);

        var url = '{{ route("cities.update", ":id") }}';
        url = url.replace(':id', id);
        
        handleFormSubmit('#updateForm', url, 'PUT', function(response) {
            // Handle success response
            console.log(response);
        }, function(error) {
            // Handle error response
            console.error(error);
        });
    });
});
  $(document).ready(function() {
    handleDeleteAction('.deleteBtn', '/settings/cities/delete', function(response) {
      console.log(response);
    }, function(error) {
      //console.error(error);
    });    
  });
</script>

@endpush