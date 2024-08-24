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
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Issue Material to Main Store')}}</h5>
						<span>{{ __('Issue material within main store')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
                 @include('include.backButtons')      
            </div>
		</div>
	</div> 
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="">
                        <form class="forms-sample" id="submitForm" method="POST" action="javascript:;">
                            @csrf                           
                            <div class="row">
                                <div class="col-sm-12 card new-cust-card">
                                    <div class="card-header">
                                        <h3>General</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-sm-4">
                                                <label>Attachment <span class="text-red">*</span></label>
                                                <input type="file" class="form-control" name="file" id="file">
                                            </div>  
                                            <div class="form-group col-sm-4">
                                                <label>Area / Building<span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="address" id="address" value="" required>
                                            </div>                                                                                         
                                            <div class="form-group col-sm-4">
                                                <label>Issue Date<span class="text-red">*</span></label>
                                                <input type="date" class="form-control" name="date" id="date" value="" required>
                                            </div>     
                                        </div>   
                                        <div class="row">
                                            <div class="form-group col-sm-4">
                                                <label>Worker Name<span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="worker_name" id="worker_name" value="" required>
                                            </div>                                            
                                            <div class="form-group col-sm-4">
                                                <label>Worker Department<span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="worker_department" id="worker_department" value="">
                                            </div>   
                                            <div class="form-group col-sm-4">
                                                <label>Remarks</label>
                                                <textarea class="form-control" name="remarks" rows="1" style="height:17px"></textarea>
                                            </div>                                             
                                        </div>                                                                                    
                                        <div class="row">                                           
                                            <div class="col-sm-12">
                                                <div class="form-group"> 
                                                    <label for="product">Select Product(s)</label>
                                                    <select id="productSearch" class="form-control product_ids" name="product_id[]" multiple="multiple"></select>                                               
                                                </div>
                                            </div>                       
                                        </div>
                                        <div class="form-group mt-4 col-sm-12">
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
                url: '{{ route('warehouses.stocklist') }}',
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
        //alert($(this).val());
        var selectedProducts = $(this).val();
        var warehouse_id = $('#warehouse_id').val();

        if (warehouse_id === '' || warehouse_id < 1) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: 'Select warehouse first.',
            });
            $(this).val([]); 
            return false;
        }

        if (selectedProducts.length > 0) {
            $.ajax({
                url: '{{url('/')}}/warehouses/material/issued/getProducts',
                type: 'get',
                data: { products: selectedProducts },
                dataType: 'html', // Change to 'html' as you expect HTML response
                success: function(response) {
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


    handleFormSubmit('#submitForm', '{{ route('warehouses.saveMaterialIssued') }}','POST', function(response) {
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
function validateQuantity(input, maxQuantity) {
    if(maxQuantity != 0) {
        if (input.value > maxQuantity) {
            input.value = maxQuantity;
        }
    }
}
</script>

@endpush