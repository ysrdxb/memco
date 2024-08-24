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
                        <form class="forms-sample" id="submitForm" method="POST" action="#">
                            @csrf                           
                            <div class="row">
                                <div class="col-sm-12 card new-cust-card">
                                    <div class="card-header">
                                        <h3>General</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-sm-3">
                                                <label>From Store <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="store_id" required>
                                                    <option selected="selected" value="{{ Auth::user()->stores->first()->id }}" >{{ Auth::user()->stores->first()->name }}</option>
                                                </select>
                                            </div>    
                                            <div class="form-group col-sm-3">
                                                <label>To Warehouse <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="warehouse_id" required>
                                                    @foreach($warehouses as $warehouse)
                                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>                                                                                      
                                            <div class="form-group col-sm-3">
                                                <label>Main Activity <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="category_id" required>
                                                    <option selected="selected" value="" >-Select-</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>                                            
                                            <div class="form-group col-sm-3">
                                                <label>Brand <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="brand_id" required>
                                                    <option selected="selected" value="" >-Select-</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-3">
                                                <label>Product <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="product_id" required>
                                                    <option selected="selected" value="" >-Select-</option>
                                                    @foreach($products as $product)
                                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>                                            
                                            <div class="form-group col-sm-3">
                                                <label>Qty <span class="text-red">*</span></label>
                                                <input id="requested_quantity" value="1" type="number" class="form-control" name="requested_quantity" placeholder="Qty" required="">
                                            </div>        
                                            <div class="form-group col-sm-3">
                                                <label for="type">Unit Of Measurement</label>
                                                <select class="form-control select2" name="unit_id" required>
                                                    <option selected="selected" value="" >-Select-</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                    @endforeach
                                                </select>                                                    
                                            </div>                                                                                                                            
                                            <div class="form-group col-sm-3">
                                                <label>Priority <span class="text-red">*</span></label>
                                                <select class="form-control select2" name="priority" required>
                                                    <option value="normal">Normal</option>
                                                    <option value="urgent">Urgent</option>
                                                    <option value="topUrgent">Top Urgent</option>                                                    
                                                </select>
                                            </div>                                                                                                                                                                                                        
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-3">
                                                <label>Date <span class="text-red">*</span></label>
                                                <input type="date" class="form-control" name="date" id="date">
                                            </div>        
                                            <div class="form-group col-sm-3">
                                                <label for="title">Requested By <span class="text-red">*</span></label>
                                                <select name="user_id" id="user_id" class="form-control" readonly>
                                                    <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div> 
                                            <div class="form-group col-sm-6">
                                                <label>Requisition Remarks <span class="text-red">*</span></label>
                                                <textarea class="form-control" name="remarks" rows="1"></textarea>
                                            </div>                                                                                                                                                                                                        
                                        </div>                                        

                                    </div>
                                </div>
                                <div class="">
                                    <div class="form-group mt-4">
                                        <button class="btn btn-primary" type="submit">Save <span class="ik ik-save"></span></button>
                                        <button class="btn btn-warning" type="button" id="clearForm">Clear <span class="ik ik-delete"></span></button>
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
    handleFormSubmit('#submitForm', '{{ route('materialRequest.store') }}','POST', function(response) {
        //console.log(response);
    }, function(error) {
        console.error(error);
    });
});
</script>

@endpush