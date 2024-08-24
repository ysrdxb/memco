@extends('inventory.layout')
@section('title', 'Create Material Request')
@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-4">
                    <div class="page-header-title">
                        <div class="d-inline">
                            <h5>Items</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 text-right">
                    <!--<button class="btn btn-success" type="submit">Save <span class="ik ik-save"></span></button>
                    <button class="btn btn-warning" type="button" id="clearForm">Cancel <span class="ik ik-delete"></span></button>-->                 
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="">
                        <form class="forms-sample" id="submitForm" method="POST" action="javascript:;">
                            @csrf                           
                            <input type="hidden" name="store_id" value="{{ encrypt($store->id) }}">
                            <div class="row">
                                <div class="col-sm-12 card new-cust-card">
                                    <div class="card-header">
                                        <h3>General</h3>
                                    </div>
                                    <div class="card-body">                                                                                   
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <div class="form-group"> 
                                                    <label for="product">Select Product(s)</label>                                                    
                                                    <select id="productSearch" class="form-control product_ids" name="product_id[]" multiple="multiple"></select>                                               
                                                </div>
                                            </div>                      
                                        </div>
                                        <div class="form-group mt-4 col-sm-10">
                                            <div id="tableData"></div>
                                        </div>                                                                                     
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
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

$('select[name="product_id[]"]').change(function() {
    var selectedProducts = $(this).val();
    var warehouse_id = $('#warehouse_id').val();

    if (warehouse_id === '' || warehouse_id < 1) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Select warehouse first.',
        });
        $(this).val([]); 
        //$('.product_ids').trigger('change');
        return false;
    }

    if (selectedProducts.length > 0) {
        $.ajax({
            url: '{{url('/')}}/transfers/getProducts?page=transfer.store._get_products',
            type: 'POST',
            data: { products: selectedProducts, warehouse_id: warehouse_id },
            dataType: 'text',
            success: function(response) {
                console.log(response);
                $('#tableData').html(response);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }
});

$('#warehouse_id').change(function() {
    $('select[name="product_id[]"]').trigger('change');
}); 

});



handleFormSubmit('#submitForm', '{{ route('stores.items.save') }}','POST', function(response) {
}, function(error) {
    console.error(error);
}); 

$(document).ready(function() {
  handleSaveAction('.saveBtn', function(response) {
    // Handle success response here
  }, function(error) {
    // Handle error response here
  });
});

function handleSaveAction(saveSelector, successCallback, errorCallback) {
  $(document).on('click', saveSelector, function(event) {
    event.preventDefault();

    Swal.fire({
      title: 'Are you sure?',
      text: 'Your selected data will be saved!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Save it!',
      cancelButtonText: 'Cancel it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $('#submitForm').submit(); // Trigger form submission
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        // Swal.fire('Cancelled', 'Your data is safe :)', 'error');
      }
    });
  });
}

</script>

@endpush