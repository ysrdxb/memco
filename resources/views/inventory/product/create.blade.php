@extends('inventory.layout')
@section('title', 'Add Product')
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
						<h5>{{ __('Products')}}</h5>
						<span>{{ __('Add products')}}</span>
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
                        <form class="forms-sample" id="submitForm" method="POST" action="{{route('products.store')}}">
                            @csrf                           
                            <div class="row">
                                <div class="col-sm-12 card new-cust-card">
                                    <div class="card-header">
                                        <h3>General</h3>
                                    </div>
                                    <div class="card-body">                                         
                                        <div class="row">
                                            <div class="form-group col-sm-2">
                                                <label>Status</label>
                                                <div class="border-checkbox-section ml-3">
                                                    <div class="border-checkbox-group border-checkbox-group-primary d-block">
                                                        <input class="border-checkbox" type="checkbox" id="checkbox1" value="1" name="status" checked>
                                                        <label class="border-checkbox-label" for="checkbox1">Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <div class="form-radio">
                                                    <label for="type">Type <span class="text-red">*</span></label>
                                                    <div class="radio radio-inline">
                                                        <label>
                                                            <input type="radio" name="type" value="product" checked>
                                                            <i class="helper"></i>Product
                                                        </label>
                                                    </div>
                                                    <div class="radio radio-inline">
                                                        <label>
                                                            <input type="radio" name="type" value="tool">
                                                            <i class="helper"></i>Tool
                                                        </label>
                                                    </div>                                                    
                                                </div>                                        
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <label for="title">Name<span class="text-red">*</span></label>
                                                <input id="name" type="text" class="form-control" name="name" value="" placeholder="Enter product name" required="">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="title">Code<span class="text-red">*</span></label>
                                                <input id="code" type="text" class="form-control" name="code" value="" placeholder="Enter product code" required="">
                                                <div class="help-block with-errors"></div>
                                            </div>                                             
                                        </div>

                                        <!-- <div class="row d-none" id="tool-fields" style="display: none;">
                                            <div class="form-group col-sm-3">
                                                <label for="warranty">Warranty (Years)<span class="text-red">*</span></label>
                                                <input type="number" class="form-control" name="warranty" placeholder="Enter tool warranty" value="">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="warranty">Serial No.<span class="text-red"></span></label>
                                                <input type="number" class="form-control" name="serial_no" placeholder="Enter tool serial no." value="">
                                            </div>                                            
                                            <div class="form-group col-sm-3">
                                                <label for="condition">Condition<span class="text-red">*</span></label>
                                                <select class="form-control select2" name="product_condition">
                                                    <option value="">Select</option>
                                                    <option value="new">New</option>
                                                    <option value="used">Used</option>
                                                    <option value="not_working">Not Working / Damaged</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="purchase_date">Purchase Date<span class="text-red">*</span></label>
                                                <input type="date" class="form-control" value="" name="purchase_date" placeholder="Enter tool purchase date">
                                            </div>
                                        </div> -->

                                    </div>
                                </div>                                    
                     
                                <div class="col-sm-12 card new-cust-card">
                                    <div class="card-header">
                                        <h3>Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-sm-3">
                                                <label for="type">U.O.M <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="unit_id" required>
                                                    @foreach($units as $unit)
                                                        <option value="{{$unit->id}}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>                                                    
                                            </div>   
                                            <div class="form-group col-sm-3">
                                                <label>Activity <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="category_id" id="activity" required>
                                                    <option value="">Select Activity</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->id}}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label>SubActivity <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="subcategory_id" id="subactivity" required>
                                                    <option value="">Select SubActivity</option>
                                                </select>
                                            </div>                                            
                                            <div class="form-group col-sm-3">
                                                <label>Brand <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="brand_id" required>
                                                    @foreach($brands as $brand)
                                                        <option value="{{$brand->id}}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="type">Pass Key <span class="text-red">*</span></label>
                                                <input type="password" name="passkey" class="form-control" placeholder="Enter Pass Key" required>
                                            </div>                                            
                                        </div>
                                        <div class="form-group mt-4">
                                            <button class="btn btn-primary" type="submit">Submit <span class="ik ik-save"></span></button>
                                            <button class="btn btn-warning" type="button" id="clearForm">Clear <span class="ik ik-delete"></span></button>
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
        $('input[type=radio][name=type]').change(function() {
            if (this.value === 'tool') {
                $('#tool-fields').show();
            } else {
                $('#tool-fields').hide();
            }
        });
    });


    $('#activity').on('change', function() {
        var activityId = $(this).val();
        if (activityId) {
            $.ajax({
                url: "{{ route('getSubactivities') }}",
                type: "GET",
                data: {
                    activity_id: activityId
                },
                success: function(response) {
                    $('#subactivity').empty();
                    $('#subactivity').append($('<option>', {
                        value: '',
                        text: 'All'
                    }));
                    $.each(response, function(key, value) {
                        $('#subactivity').append($('<option>', {
                            value: value.id,
                            text: value.name
                        }));
                    });
                }
            });
        } else {
            $('#subactivity').empty();
            $('#subactivity').append($('<option>', {
                value: '',
                text: 'All'
            }));
        }
    });    

document.getElementById('clearForm').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default form submission
    Swal.fire({
        title: 'Are you sure?',
        text: 'All form data will be reset!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.getElementById('submitForm');
            form.reset();
            Swal.fire({
                icon: 'success',
                title: 'Success',
                html: 'Form cleared successfully!',
            });
        }
    });
});
   
$(document).ready(function() {
    handleFormSubmit('#submitForm', '{{ route('products.store') }}','POST', function(response) {
        //console.log(response);
    }, function(error) {
        console.error(error);
    });
});
</script>

@endpush