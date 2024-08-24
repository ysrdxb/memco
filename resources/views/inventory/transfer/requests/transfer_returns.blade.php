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
                        <h5>{{ __('Return Materials') }}</h5>
                        <span>Add, remove or edit direct transfers</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 text-right">
                <a href="{{ route('transferReturns.create') }}" class="btn btn-sm btn-primary"><i class="ik ik-plus"></i> Create</a>
                @include('include.backButtons')
            </div>
        </div>
    </div>

    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-block">
                    <h3>{{ __('Direct Transfers') }}</h3>
                </div>
                <div class="card-body p-0 table-border-style">
                    <div class="table-responsive">
                        <table id="advanced_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Store</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ $row->product->name }}</td>
                                    <td>{{ $row->quantity }}</td>
                                    <td>{{ $row->store->name }}</td>
                                    <td>
                                        @if($row->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-success">Success</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="#" class="deleteBtn" data-id="{{ encrypt($row->id) }}"><i class="ik ik-trash-2 f-16 text-danger"></i></a>
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

<!-- Category Add Modal -->
<div class="modal fade" id="categoryAdd" tabindex="-1" role="dialog" aria-labelledby="categoryAddLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Material Return') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="submitForm">
                    <div class="form-group">
                        <label for="productSearch">Select Product</label>
                        <select class="form-control select2" name="product_id" required>
                            <option value="">Select</option>
                            @foreach($products as $product)
                                <option value="{{ $product['id'] }}">{{ $product['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group mt-4">
                        <button class="btn btn-primary" type="submit">Save <i class="ik ik-save"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Handle delete action
        $(document).on('click', '.deleteBtn', function(event) {
            event.preventDefault();
            let id = $(this).data('id');
            let url = '{{ url('stores/transferReturns/delete') }}/' + id;

            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            location.reload(); // Reload the page on success
                        } else {
                            alert('Error deleting data.');
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });
    });
</script>
@endpush
