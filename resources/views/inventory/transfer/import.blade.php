@extends('inventory.layout')
@section('title', 'Create Material Request')
@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-body {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-top: none;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .page-header-title {
            display: flex;
            align-items: center;
        }

        .page-header-title i {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        .text-red {
            color: red;
        }

        .modal-content {
            border-radius: 0.375rem;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-4">
                            <div class="page-header-title">
                                <i class="ik ik-truck bg-green"></i>
                                <div class="d-inline">
                                    <h5>Import Material Request</h5>
                                    <span>Import material requests by copying them into a .csv file</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="text-right">
                                @include('include.backButtons')
                            </div>
                        </div>
                    </div>
                </div>
                <form class="forms-sample row" id="uploadForm" method="POST" enctype="multipart/form-data">
                    @csrf                
                    <div class="card col-lg-4 col-md-6 col-sm-12">
                        <div class="card-header">
                            <h3 class="card-title">Upload Material Requests (.CSV) file</h3>                    
                        </div>
                        <div class="card-body">
                            <div id="error-messages" class="error-message"></div>

                                <div class="row">
                                    @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="purchase_applicable">Store Name</label>
                                            <select name="store_id" id="store_id" class="form-control select2">
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                @endforeach                                            
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="invalid-feedback" role="alert" id="purchase_applicable_error"></span>
                                        </div>
                                    </div> 
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="transfer_no">Material Request No.</label>
                                            <input type="number" placeholder="123456" class="form-control" id="transfer_no" name="transfer_no" value="{{ old('transfer_no') }}">
                                            <span class="invalid-feedback" role="alert" id="transfer_no_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="material_request_date">Material Request Date <span class="text-red">*</span></label>
                                            <input type="date" class="form-control" name="date" id="date" value="{{ old('date') }}" required>
                                            <span class="invalid-feedback" role="alert" id="date_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control" name="remarks" rows="1">{{ old('remarks') }}</textarea>
                                            <span class="invalid-feedback" role="alert" id="remarks_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="file">Upload CSV File <span class="text-red">*</span></label>
                                        <input type="file" class="form-control" id="file" name="file" accept=".csv">
                                        <span class="invalid-feedback" role="alert" id="file_error"></span>
                                    </div>
                                    <div class="form-group text-left col-12 mt-2">
                                        <button type="button" class="btn btn-primary uploadBtn">Upload File</button>
                                    </div>  
                                    <a target="_blank" href="{{ route('transfer.downloadImportedFile') }}" class="text-green">
                                        <i class="fas fa-download"></i> Download Example File
                                    </a>                                  
                                </div>

                        </div>

                    </div>

                    <div class="card col-lg-8 col-md-6 col-sm-12">
                        <div class="card-header">
                            <h4>Imported Products</h4>
                        </div>
                        <div class="card-body">
                            <div id="uploaded-products" style="display: none;">                                
                                <div id="productTableBody"></div>
                            </div>                        
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="alert alert-success">Material Request Imported Successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>    
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.uploadBtn').click(function() {
                let formData = new FormData($('#uploadForm')[0]);
                $.ajax({
                    url: '{{ route('transfer.import.upload') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#uploaded-products').show();
                        $('#productTableBody').html(response);

                        $('.saveBtn').click(function() {
                            $.ajax({
                                url: '{{ route('transfer.import') }}',
                                type: 'POST',
                                data: new FormData($('#uploadForm')[0]), // Adjusted to exclude file input
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status === 'success') {
                                        $('.is-invalid').removeClass('is-invalid');
                                        $('.invalid-feedback').text('');                            
                                        $('#successModal').modal('show');
                                        setTimeout(function() {
                                            location.reload();
                                        }, 2000);
                                    } else {
                                        $('#error-messages').text(response.message);
                                        $.each(response.errors, function(key, value) {
                                            $('#' + key).addClass('is-invalid');
                                            $('#' + key + '_error').text(value);
                                        });                            
                                    }
                                },
                                error: function(xhr, status, error) {
                                    $('#error-messages').text('Error: ' + error);
                                }
                            });
                        });                        
                    },
                    error: function(xhr, status, error) {
                        $('#error-messages').text('Error: ' + error);
                    }
                });
            });                      
        });
    </script>
@endpush
