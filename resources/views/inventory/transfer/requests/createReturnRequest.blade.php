@extends('inventory.layout')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-list bg-secondary"></i>
                    <div class="d-inline">
                        <h5>{{ __('Create Return Request') }}</h5>
                        <span>Create material return request</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 text-right">
                @include('include.backButtons')
            </div>
        </div>
    </div>
    @include('include.message')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-block">
                <h3>{{ __('Direct Transfers') }}</h3>
            </div>    
            <div class="card-body">
                <form id="submitForm">
                    <div class="form-group">
                        <label for="productSearch">Select Product</label>
                        <select id="productSearch" class="form-control select2" name="product_id" required>
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
<script>
    $(document).ready(function() {
        // Initialize Select2 for product search
        $('#productSearch').select2({
            placeholder: 'Search for a product',
            minimumInputLength: 0,  // Trigger search after 2 characters
            ajax: {
                url: '{{ route('transferReturns.getProducts') }}',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                },
                cache: false
            }
        });
    });
    
    $('#submitForm').on('submit', function(event) {
        event.preventDefault();
        let form = $(this);
        let url = '{{ route('transferReturns.save') }}';

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#categoryAdd').modal('hide');
                    location.reload(); // Reload the page on success
                } else {
                    alert('Error saving data.');
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    });    
</script>
@endpush