@extends('inventory.layout')
@section('title', 'Import Material Request')

@push('head')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">

    <style>
        .import-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            padding: 10px 5px;
        }

        .header-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 18px;
            padding: 24px 28px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .header-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(99, 102, 241, 0.35);
            margin-right: 18px;
            flex-shrink: 0;
        }

        .header-title-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 2px;
            letter-spacing: -0.4px;
        }

        .header-sub-text {
            color: #94a3b8;
            font-size: 0.88rem;
            margin-bottom: 0;
            font-weight: 500;
        }

        .form-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .form-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 18px 24px;
        }

        .form-card .card-header h5 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .form-card .card-body {
            padding: 24px;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.88rem;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .btn-upload-action {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            padding: 11px 22px;
            font-weight: 700;
            font-size: 0.88rem;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }

        .btn-upload-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(99, 102, 241, 0.5);
        }

        .btn-download-sample {
            background: rgba(16, 185, 129, 0.1);
            color: #059669 !important;
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .btn-download-sample:hover {
            background: rgba(16, 185, 129, 0.2);
            color: #047857 !important;
        }

        .empty-preview-state {
            padding: 60px 20px;
            text-align: center;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid import-wrapper">
    <!-- Header Banner -->
    <div class="header-banner d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box">
                <i class="fas fa-file-import"></i>
            </div>
            <div>
                <h3 class="header-title-text">Import Material Request</h3>
                <p class="header-sub-text">Upload a CSV file to bulk import material requisition orders into your store</p>
            </div>
        </div>

        <div>
            @include('include.backButtons')
        </div>
    </div>

    <!-- Main Content Form Row -->
    <form class="forms-sample row" id="uploadForm" method="POST" enctype="multipart/form-data">
        @csrf                
        
        <!-- Left Configuration Card -->
        <div class="col-lg-5 col-md-12">
            <div class="form-card">
                <div class="card-header">
                    <h5><i class="fas fa-cloud-arrow-up text-indigo mr-2"></i> Upload CSV Configuration</h5>                    
                </div>
                <div class="card-body">
                    <div id="error-messages" class="alert alert-danger" style="display: none;"></div>

                    <div class="row">
                        @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
                        <div class="col-md-12 form-group">
                            <label for="store_id">Store / Project Name</label>
                            <select name="store_id" id="store_id" class="form-control select2">
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }} (Warehouse)</option>
                                @endforeach                                            
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }} (Project)</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback" role="alert" id="store_id_error"></span>
                        </div> 
                        @endif

                        <div class="col-md-6 form-group">
                            <label for="transfer_no">Material Request No.</label>
                            <input type="number" placeholder="e.g. 123456" class="form-control" id="transfer_no" name="transfer_no" value="{{ old('transfer_no') }}">
                            <span class="invalid-feedback" role="alert" id="transfer_no_error"></span>
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="date">Request Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            <span class="invalid-feedback" role="alert" id="date_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="remarks">Remarks / Notes</label>
                            <textarea class="form-control" name="remarks" rows="2" placeholder="Optional notes for this import">{{ old('remarks') }}</textarea>
                            <span class="invalid-feedback" role="alert" id="remarks_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="file">Upload CSV File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
                            <span class="invalid-feedback" role="alert" id="file_error"></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap" style="gap: 10px;">
                        <button type="button" class="btn-upload-action uploadBtn">
                            <i class="fas fa-upload mr-2"></i> Process CSV File
                        </button>

                        <a target="_blank" href="{{ route('transfer.downloadImportedFile') }}" class="btn-download-sample">
                            <i class="fas fa-file-csv mr-2"></i> Download Sample CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Preview Card -->
        <div class="col-lg-7 col-md-12">
            <div class="form-card">
                <div class="card-header">
                    <h5><i class="fas fa-table-list text-indigo mr-2"></i> Imported Products Preview</h5>
                </div>
                <div class="card-body">
                    <div id="uploaded-products" style="display: none;">                                
                        <div id="productTableBody"></div>
                    </div>

                    <div id="empty-preview-placeholder" class="empty-preview-state">
                        <i class="fas fa-file-csv text-muted mb-3" style="font-size: 3.5rem; opacity: 0.4;"></i>
                        <h6 class="font-weight-bold text-dark mb-1">No File Uploaded Yet</h6>
                        <p class="text-muted small mb-0">Select a valid CSV file on the left and click "Process CSV File" to preview imported products.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-success">
                    <i class="fas fa-circle-check" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="font-weight-bold text-dark mb-2">Import Successful!</h5>
                <p class="text-muted mb-4">Material Request orders have been successfully imported into the system.</p>
                <button type="button" class="btn btn-secondary px-4 rounded-10" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>    
@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('.uploadBtn').click(function() {
            let $btn = $(this);
            let originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...');

            let formData = new FormData($('#uploadForm')[0]);
            $.ajax({
                url: '{{ route('transfer.import.upload') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $btn.prop('disabled', false).html(originalHtml);
                    $('#empty-preview-placeholder').hide();
                    $('#uploaded-products').show();
                    $('#productTableBody').html(response);

                    $('.saveBtn').click(function() {
                        let $saveBtn = $(this);
                        $saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');
                        
                        $.ajax({
                            url: '{{ route('transfer.import') }}',
                            type: 'POST',
                            data: new FormData($('#uploadForm')[0]),
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $saveBtn.prop('disabled', false);
                                if (response.status === 'success') {
                                    $('.is-invalid').removeClass('is-invalid');
                                    $('.invalid-feedback').text('');                            
                                    $('#successModal').modal('show');
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    $('#error-messages').show().text(response.message);
                                    $.each(response.errors, function(key, value) {
                                        $('#' + key).addClass('is-invalid');
                                        $('#' + key + '_error').text(value);
                                    });                            
                                }
                            },
                            error: function(xhr, status, error) {
                                $saveBtn.prop('disabled', false);
                                $('#error-messages').show().text('Error: ' + error);
                            }
                        });
                    });                        
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).html(originalHtml);
                    $('#error-messages').show().text('Error processing CSV file. Please verify file format.');
                }
            });
        });                      
    });
</script>
@endpush
