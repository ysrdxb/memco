@extends('inventory.layout')
@section('title', 'Tracking Page')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <div class="page-header-title">
                    <i class="ik ik-list bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('Material Issued Tracking')}}</h5>
                        <span>Filter and get results Employee Material Issued Records</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 text-right">
                @include('include.backButtons')      
            </div>
        </div>
    </div>
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" action="{{ route('material_issued.filter') }}" method="GET">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="employeeId">Employee</label>
                                <select class="form-control select2" id="employeeId" name="employeeId">
                                    <option value="">Select Employee</option>
                                    @foreach($materialIssued as $row)
                                        <option value="{{ $row->employee_id }}" {{ $row->employee_id == request()->get('employeeId') ? 'selected' : '' }}>{{ $row->employee->name }} ({{ $row->employee->employee_no }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if($records)
            <div class="card">
                <div class="card-header">
                    <h3>Store Name: {{ $records->first()->store->name }}</h3><Br>
                </div>   
                <div class="card-header">
                    <p>Material Issued History for Employee: {{ $records->first()->employee->name }}</p>          
                </div>   
            </div>            
            <div class="card"> 
                <div class="card-header">
                    <h3>Products Issued</h3>
                </div>                 
                <div class="card-body">

                    <table class="table table-bordered table-stripped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Issue Voucher NO</th>
                                <th>Issued By</th>
                                <th>Employee Name</th>
                                <th>Employee Code</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Brand</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach($records as $record)
                                @if($record->product->type == 'product')
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $record->issue_order_no }}</td>
                                    <td>{{ $record->user->name }}</td>
                                    <td>
                                        {{ $record->employee->name }}
                                    </td>      
                                    <td>{{ $record->employee->employee_no }}</td>                              
                                    <td>{{ $record->product->name }}</td>
                                    <td>{{ $record->quantity }}</td>
                                    <td>{{ $record->brand->name }}</td>
                                    <td>{{ $record->updated_at }} </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>   
                    
                </div>
            </div>   
            
            <div class="card"> 
                <div class="card-header">
                    <h3>Tools Issued</h3>
                </div>                  
                <div class="card-body">

                    <table class="table table-bordered table-stripped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Issue Voucher NO</th>
                                <th>Issued By</th>
                                <th>Employee Name</th>
                                <th>Employee Code</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Brand</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach($records as $record)
                                @if($record->product->type == 'tool')
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $record->issue_order_no }}</td>
                                    <td>{{ $record->user->name }}</td>
                                    <td>
                                        {{ $record->employee->name }}
                                    </td>      
                                    <td>{{ $record->employee->employee_no }}</td>                              
                                    <td>{{ $record->product->name }}</td>
                                    <td>{{ $record->quantity }}</td>
                                    <td>{{ $record->brand->name }}</td>
                                    <td>{{ $record->updated_at }} </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>   
                    
                </div>
            </div>

            @endif
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#filterForm select').on('change', function() {
            $('#filterForm').submit();
        });
    });
</script>
@endpush
