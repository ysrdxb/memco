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
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Edit User')}}</h5>
                            <span>{{ __('Create new user, assign roles & permissions')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('/')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('User')}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <!-- clean unescaped data is to avoid potential XSS risk -->
                                {{ clean($user->name, 'titles')}}
                            </li>

                        </ol>
                    </nav>
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
                        <form class="forms-sample" method="POST" action="{{ url('user/update') }}" >
                        @csrf
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <div class="row">
                                <div class="col-sm-6">

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
                                        <label for="email">{{ __('Email')}}<span class="text-red">*</span></label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ clean($user->email, 'titles')}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">{{ __('Phone')}}<span class="text-red">*</span></label>
                                        <input id="phone" type="number" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ clean($user->phone, 'titles')}}" placeholder="Enter phone" required>
                                        <div class="help-block with-errors" ></div>

                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div> 

                                    <div class="form-group">
                                        <label for="email">{{ __('Employee No')}}</label>
                                        <input id="employee_no" type="number" class="form-control @error('employee_no') is-invalid @enderror" name="employee_no" value="{{ clean($user->employee_no, 'titles')}}" placeholder="Enter Employee No">
                                        <div class="help-block with-errors" ></div>

                                        @error('employee_no')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>                                     

                                    <!--<div class="row">-->
                                    <!--    <div class="form-group col-sm-6">-->
                                    <!--        <label for="department_id">{{ __('Department')}}<span class="text-red">*</span></label>-->
                                    <!--        <select id="department_id" name="department_id" class="form-control select2" required>-->
                                    <!--            <option value="">Select Department</option>-->
                                    <!--            @foreach($departments as $department)-->
                                    <!--                <option value="{{ $department->id }}" {{ $department->id == $user->department_id ? 'selected' : ''}}>{{ $department->name }}</option>-->
                                    <!--            @endforeach-->
                                    <!--        </select>-->
                                    <!--    </div>-->

                                    <!--    <div class="form-group col-sm-6">-->
                                    <!--        <label for="position_id">{{ __('Position')}}<span class="text-red">*</span></label>-->
                                    <!--        <select id="position_id" name="position_id" class="form-control select2" required>-->
                                    <!--            @if($position)-->
                                    <!--                <option value="{{ $position->id }}">{{ $position->name }}</option>-->
                                    <!--            @else-->
                                    <!--            <option value="">Select Position</option>-->
                                    <!--            @endif-->
                                    <!--        </select>-->
                                    <!--    </div>-->
                                    <!--</div>                                     -->

                                    <div class="form-group">
                                        <label for="password">{{ __('Password')}} <span class="badge badge-info"></span></label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  >
                                        <div class="help-block with-errors"></div>

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password-confirm">{{ __('Confirm Password')}}</label>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    
                                    
                                    
                                    
                                
                                </div>
                                <div class="col-md-6">
                                    <!-- Assign role & view role permisions -->
                                    <div class="form-group">
                                        <label for="role">{{ __('Assign Role')}}<span class="text-red">*</span></label>
                                        {!! Form::select('role', $roles, $user_role->id??'' ,[ 'class'=>'form-control select2', 'placeholder' => 'Select Role','id'=> 'role', 'required'=>'required']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="role">{{ __('Permissions')}}</label>
                                        <div id="permission" class="form-group">
                                            @foreach($user->getAllPermissions() as $key => $permission) 
                                            <span class="badge badge-dark m-1">
                                                <!-- clean unescaped data is to avoid potential XSS risk -->
                                                {{ clean($permission->name, 'titles')}}
                                            </span>
                                            @endforeach
                                        </div>
                                        <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
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
