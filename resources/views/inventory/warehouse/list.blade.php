@extends('inventory.layout')
@section('title', 'Manage Warehouse')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-list bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('Warehouse') }}</h5>
                        <span>Manage warehouse</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 text-right">
                <a href="#categoryAdd" data-toggle="modal" data-target="#categoryAdd" class="btn btn-sm btn-danger">
                    <i class="ik ik-plus"></i>Add
                </a>
                @include('include.backButtons')      
            </div>
        </div>
    </div>
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ $warehouses->first()->name }}</h3>
                        </div>
                    </div>
                </div>
                @foreach($warehouses as $row)
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Warehouse Stocks</h5>
                            <p class="card-text">You can check all details about the current stock of the warehouse.</p>
                            <a href="{{ route('warehouses.stock', ['id' => encrypt($row->id), 'type' => 'product']) }}" class="btn btn-primary">Stocks Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Warehouse Tools</h5>
                            <p class="card-text">You can check all about the tools, serials, and availability for this warehouse.</p>
                            <a href="{{ route('warehouses.stock', ['id' => encrypt($row->id), 'type' => 'tool']) }}" class="btn btn-primary">Tools Detail</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


@endsection
@push('script')
<script>
  $(document).ready(function() {
    handleFormSubmit('#submitForm', '{{ route('warehouses.store') }}','POST', function(response) {
    }, function(error) {
    });
  });
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