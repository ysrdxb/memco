@extends('inventory.layout')
@section('title', 'Manage Stores')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-list bg-secondary"></i>
                    <div class="d-inline">
                        <h5>{{ __('Stores Items List')}} </h5>
                        <span>Add, remove or edit stores</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 text-right">
                <a href="{{ route('stores.items.create', encrypt($store->id)) }}" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Add</a>
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
                            <h3>{{ __($store->name)}}</h3>
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
                                            <th>Store Name</th>
                                            <th>Item Name</th>
                                            <th>Max Quantity</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $row)
                                        <tr>
                                            <td>
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
                                                    <span class="custom-control-label">&nbsp;</span>
                                                </label>
                                            </td>
                                            <td>#{{$row->id}}</td>
                                            <td>{{ $row->store->name }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            <td>{{ $row->max_quantity }}</td>
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

@endsection
@push('script')

@can('store_delete')
<script>
  $(document).ready(function() {
    handleDeleteAction('.deleteBtn', '/stores/items/delete', function(response) {
    }, function(error) {
    });    
  });
</script>
@endcan
@endpush