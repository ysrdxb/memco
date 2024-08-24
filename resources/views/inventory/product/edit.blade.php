@extends('inventory.layout')
@section('title', 'Edit Product | '.$product->name)
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
						<h5>{{ __('Edit Products')}}</h5>
						<span>{{ __($product->name )}}</span>
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
                        <form class="forms-sample" id="submitForm" method="POST" action="{{route('products.store', encrypt($product->id))}}">
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
                                                    <div class="border-checkbox-group border-checkbox-group-{{$product->status=='1'?'success':'primary'}} d-block">
                                                        <input class="border-checkbox" type="checkbox" id="checkbox1" value="1" {{$product->status=='1'?'checked':''}} name="status">
                                                        <label class="border-checkbox-label" for="checkbox1">Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="id" value="{{encrypt($product->id)}}">
                                            <div class="form-group col-sm-3">
                                                <div class="form-radio">
                                                    <label for="type">Type <span class="text-red">*</span></label>
                                                    <div class="radio radio-inline">
                                                        <label>
                                                            <input type="radio" {{ $product->type =='product' ? 'checked' : ''}} name="type" value="product" checked>
                                                            <i class="helper"></i>Product
                                                        </label>
                                                    </div>
                                                    <div class="radio radio-inline">
                                                        <label>
                                                            <input type="radio" {{ $product->type =='tool' ? 'checked' : ''}} name="type" value="tool">
                                                            <i class="helper"></i>Tool
                                                        </label>
                                                    </div>                                                    
                                                </div>                                        
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <label for="title">Name<span class="text-red">*</span></label>
                                                <input id="name" type="text" class="form-control" name="name" value="{{$product->name}}" placeholder="Enter product name" required="">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="title">Code<span class="text-red">*</span></label>
                                                <input id="code" type="text" class="form-control" name="code" value="{{$product->code}}" placeholder="Enter product code" required="">
                                                <div class="help-block with-errors"></div>
                                            </div>                                            
                                        </div>
                                        <!-- <div class="row d-none" id="tool-fields" style="display: {{ $product->type == 'tool' ? 'flex' : 'none' }};">
                                            <div class="form-group col-sm-3">
                                                <label for="warranty">Warranty (Years)<span class="text-red">*</span></label>
                                                <input type="number" class="form-control" name="warranty" placeholder="Enter tool warranty" value="{{$product->warranty}}">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="warranty">Serial No.<span class="text-red"></span></label>
                                                <input type="number" class="form-control" name="serial_no" placeholder="Enter tool serial no." value="{{$product->serial_no}}">
                                            </div>                                            
                                            <div class="form-group col-sm-3">
                                                <label for="condition">Condition<span class="text-red">*</span></label>
                                                <select class="form-control select2" name="product_condition">
                                                    <option value="">Select</option>
                                                    <option value="new" {{ $product->product_condition == 'new' ? 'selected' : ''}}>New</option>
                                                    <option value="used" {{ $product->product_condition == 'used' ? 'selected' : ''}}>Used</option>
                                                    <option value="not_working" {{ $product->product_condition == 'not_working' ? 'selected' : ''}}>Not Working / Damaged</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="purchase_date">Purchase Date<span class="text-red">*</span></label>
                                                <input type="date" class="form-control" value="{{$product->purchase_date}}" name="purchase_date" placeholder="Enter tool purchase date">
                                            </div>
                                        </div>                                         -->
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
                                                    <option selected="selected" value="" >-Select-</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{$unit->id}}" {{$unit->id==$product->unit_id?'selected' : ''}}>{{$unit->name}}</option>
                                                    @endforeach
                                                </select>                                                    
                                            </div>    
                                            <div class="form-group col-sm-3">
                                                <label>Activity <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="category_id" id="activity" required>
                                                    <option selected="selected" value="" >-Select-</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->id}}" {{$category->id==$product->category_id?'selected' : ''}}>{{$category->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label>SubActivity <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="subcategory_id" id="subactivity" required>
                                                    <option value="">Select SubActivity</option>
                                                    @foreach($subcategories as $subcategory)
                                                        <option value="{{$subcategory->id}}" {{$subcategory->id==$product->subcategory_id?'selected' : ''}}>{{$subcategory->name}}</option>
                                                    @endforeach                                                    
                                                </select>
                                            </div>                                            
                                            <div class="form-group col-sm-3">
                                                <label>Brand <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="brand_id" required>
                                                    <option selected="selected" value="" >-Select-</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{$brand->id}}" {{$brand->id==$product->brand_id?'selected' : ''}}>{{$brand->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="type">Pass Key <span class="text-red">*</span></label>
                                                <input type="password" name="passkey" class="form-control" placeholder="Enter Pass Key" required>
                                            </div>                                             
                                        </div>
                                        <div class="form-group mt-4">                                            
                                            <button class="btn btn-primary" type="submit"> 
                                                Submit 
                                                <div class="spinner-grow d-none" id="loader" style="width: 3rem; height: 3rem;" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <span class="ik ik-save"></span></button>
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
    handleFormSubmit('#submitForm', '{{ route('products.store') }}','POST', function(response) {
      //console.log(response);
    }, function(error) {
      console.error(error);
    });
  });
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
</script>

@endpush