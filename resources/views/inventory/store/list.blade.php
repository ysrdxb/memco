@extends('inventory.layout')
@section('title', 'Manage Projects')
@push('head')

@endpush
@section('content')

<div class="container-fluid">
    <!-- Header Banner -->
    <div class="header-dashboard-clean">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box-clean">
                <i class="ik ik-list"></i>
            </div>
            <div>
                <h3 class="header-title-text-clean">{{ __('Projects')}}</h3>
                <p class="header-sub-text-clean">{{ __('Total '. count($stores) . ' Projects listed below') }}</p>
            </div>
        </div>
        <div class="d-flex align-items-center" style="gap: 12px;">
            <a href="#categoryAdd" data-toggle="modal" data-target="#categoryAdd" class="btn btn-sm btn-primary-memco-memco mr-4"><i class="ik ik-plus"></i>{{ __('Add Project')}}</a>
                @include('include.backButtons')
        </div>
    </div>
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="table-card">
                <div class="card-header d-block">
                    <h3>{{ __('Manage Projects')}}</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive bale-stripped table-bordered">
                        <table id="advanced_table" class="table table-custom">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{ __('Name')}}</th>
                                    <th>{{ __('Stocks')}}</th>
                                    <th>{{ __('Stock Lock')}}</th>
                                    <th>{{ __('Returnables')}}</th>
                                    <th>{{ __('Added')}}</th>
                                    <th>{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter=1;@endphp
                                @foreach($stores as $row)
                                <tr>
                                    <td>{{$counter++}}</td>
                                    <td>{{$row->name}}</td>
                                    <td>
                                        <a href="{{ route('stores.stock', encrypt($row->id)) }}" class="btn btn-outline-primary" title="{{ __('Manage Stock')}}">
                                            <span class="ik ik-list"></span> {{ __('Stock List') }}
                                        </a>                                                
                                    </td>
                                    <td>
                                        <a href="{{ route('stores.items.list', encrypt($row->id)) }}" class="btn btn-outline-secondary" title="{{ __('Manage Locked Stock')}}">
                                            <span class="ik ik-lock"></span> {{ __('Locked Stocks') }}
                                        </a>                                                
                                    </td>   
                                    <td>
                                        <a href="{{ route('returnables.list', encrypt($row->id)) }}" class="btn btn-outline-success" title="{{ __('Manage Material Returns')}}">
                                            <span class="ik ik-rotate-cw"></span> {{ __('Returnables') }}
                                        </a>                                                 
                                    </td>                                            
           
                                    <td>{{$row->updated_at->diffForHumans()}}</td>
                                    <td>
                                        @can('store_edit')
                                        <a href="#" class="editUnitBtn" data-id="{{ encrypt($row->id) }}" data-name="{{ $row->name }}" data-users="{{ json_encode($row->users->pluck('id')->toArray()) }}" data-toggle="modal" data-target="#editUnitModal">
                                            <i class="ik ik-edit f-16 mr-15 text-green"></i>
                                        </a>
                                        @endcan
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


@can('store_edit')
<!-- Edit modal -->
<div class="modal fade edit-layout-modal" id="editUnitModal" tabindex="-1" role="dialog" aria-labelledby="editUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog w-300" role="document">
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
                        <label for="editName">Store Incharge</label>
                        <select name="user_id[]" id="Euser_id" class="form-control select2" required multiple="multiple">
                            <option value="">-Select-</option>
                            @foreach($users as $row)
                                <option value="{{$row->id}}">{{$row->name}}</option>
                            @endforeach
                        </select>
                    </div>                     
                    <!-- Add other input fields for editing -->
                    <input type="hidden" id="editUnitId" name="id">
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

@can('store_create')
<!-- category add modal-->
<div class="modal fade edit-layout-modal pr-0 " id="categoryAdd" tabindex="-1" role="dialog" aria-labelledby="categoryAddLabel" aria-hidden="true">
    <div class="modal-dialog w-300" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Add Store')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="submitForm">
                    <div class="form-group">
                        <label class="d-block"> Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Name" required>
                    </div>   
                    <div class="form-group">
                        <label class="d-block"> Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Address">
                    </div> 
                    <div class="form-group">
                        <label class="d-block"> Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone">
                    </div> 
                    <div class="form-group">
                        <label class="d-block"> Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email">
                    </div>                                                                            
                    <div class="form-group">
                        <label for="editName">Store Incharge</label>
                        <select name="user_id[]" multiple id="user_id" class="form-control select2" required>
                            <option value="">-Select-</option>
                            @foreach($users as $row)
                                <option value="{{$row->id}}">{{$row->name}}</option>
                            @endforeach
                        </select>
                    </div>                                         
                    <div class="form-group">
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
<script>
    $(document).ready(function() {
        $('.editUnitBtn').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var url = '{{ route("stores.update", ":id") }}';
            url = url.replace(':id', id);
            // Populate modal fields
            $('#editUnitId').val(id);
            $('#editName').val(name);

            // Use AJAX to fetch and set the selected store incharges
            $.ajax({
                url: '{{ route("stores.getStoreIncharges") }}',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    // Assuming response.incharges is an array of user IDs
                    $('#Euser_id').val(response.incharges).trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching store incharges: ' + error);
                }
            });

            // Show the modal
            $('#editUnitModal').modal('show');
        });
    });
</script>
@can('store_create')
<script>
  $(document).ready(function() {
    handleFormSubmit('#submitForm', '{{ route('stores.store') }}','POST', function(response) {
    }, function(error) {
    });
  });

  $(document).ready(function() {
    handleFormSubmit('#submitForms', '{{ route('stores.saveSubStore') }}','POST', function(response) {
    }, function(error) {
    });
  });  
</script>
@endcan
@can('store_edit')
<script>
$(document).ready(function() {
    $('.editUnitBtn').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var user_id = $(this).data('euser_id');
        $('#editUnitId').val(id);
        $('#editName').val(name);
        $('#user_id').val(user_id);
        var url = '{{ route("stores.update", ":id") }}';
        url = url.replace(':id', id);
        
        handleFormSubmit('#updateForm', url, 'PUT', function(response) {
        }, function(error) {
        });
    });
});
$(document).ready(function() {
    $('.editUnitBtn2').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var store_id = $(this).data('estore_id');
        $('#editUnitId2').val(id);
        $('#editName2').val(name);
        $('#store_id').val(store_id);
        var url = '{{ route("stores.updateSubStore", ":id") }}';
        url = url.replace(':id', id);
        
        handleFormSubmit('#updateForm2', url, 'PUT', function(response) {
        }, function(error) {
        });
    });
});
</script>
@endcan
@can('store_delete')
<script>
//   $(document).ready(function() {
//     handleDeleteAction('.deleteBtn', '/stores/delete', function(response) {
//     }, function(error) {
//     });    
//   });
</script>
@endcan
@endpush