@extends('inventory.layout') 
@section('title', $user->name)
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('public/plugins/select2/dist/css/select2.min.css') }}">
    @endpush

    
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-4">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Edit Employee')}}</h5>
                            <span>{{ __('Edit Employee')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 text-right">
                    @include('include.backButtons')
                </div>
            </div>
        </div>
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{ url('employee/update') }}" >
                        @csrf
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="form-group">
                                        <label for="name">{{ __('Username')}}<span class="text-red">*</span></label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ clean($user->name, 'titles')}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="ec">{{ __('Employee Code')}}<span class="text-red">*</span></label>
                                        <input id="employee_no" type="text" class="form-control @error('employee_no') is-invalid @enderror" name="employee_no" value="{{ clean($user->employee_no, 'titles')}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('employee_no')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone')}}<span class="text-red">*</span></label>
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ clean($user->phone, 'titles')}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                                                        
                                    <div class="form-group">
                                        <label for="email">{{ __('Email')}}<span class="text-red">*</span></label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ clean($user->email, 'titles')}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                 

                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label for="department_id">{{ __('Department')}}<span class="text-red">*</span></label>
                                            <select id="department_id" name="department_id" class="form-control select2" required>
                                                <option value="">Select Department</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}" {{ $department->id == $user->department_id ? 'selected' : ''}}>{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <label for="position_id">{{ __('Position')}}<span class="text-red">*</span></label>
                                            <select id="position_id" name="position_id" class="form-control select2" required>
                                                @if($position)
                                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                                @else
                                                <option value="">Select Position</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>                                     
                                
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary form-control-right">{{ __('Update')}}</button>
                                    </div>
                                </div>
                            </div>
                        
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script') 
        <script src="{{ asset('public/plugins/select2/dist/js/select2.min.js') }}"></script>
         <!--get role wise permissiom ajax script-->
        <script src="{{ asset('public/js/get-role.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#department_id').change(function(){
                    var department_id = $(this).val();
                    if(department_id){
                        $.ajax({
                            url: '{{ route("get-positions") }}',
                            type: "POST",
                            data: {"department_id" : department_id},
                            dataType: "json",
                            success:function(data) {
                                $('#position_id').empty();
                                $.each(data, function(index, position) {
                                    $('#position_id').append('<option value="'+ position.id +'">'+ position.name +'</option>');
                                });
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
