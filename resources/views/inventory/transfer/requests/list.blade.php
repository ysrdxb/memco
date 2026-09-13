@extends('inventory.layout')
@section('title', 'Material Requisition')

@section('content')
@push('head')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">

    <style>
        .mr-list-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            padding: 10px 5px;
        }

        /* HEADER (CLEAN) */
        .header-dashboard-clean {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--color-border);
        }

        .header-icon-box-clean {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--color-card);
            border: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--color-primary);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-right: 16px;
            flex-shrink: 0;
        }

        .header-title-text-clean {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--color-text-primary);
            margin-bottom: 2px;
            letter-spacing: -0.4px;
        }

        .header-sub-text-clean {
            color: var(--color-text-muted);
            font-size: 0.85rem;
            margin-bottom: 0;
            font-weight: 500;
        }

        /* TABLE CONTAINER CARD */
        .table-card {
            background: var(--color-card);
            border-radius: 12px;
            border: 1px solid var(--color-border);
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .table-custom {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .table-custom thead th {
            background: var(--color-surface) !important;
            color: var(--color-text-muted) !important;
            font-size: 0.78rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            padding: 14px 16px !important;
            border-bottom: 2px solid var(--color-border) !important;
            border-top: none !important;
        }

        .table-custom tbody td {
            padding: 14px 16px !important;
            font-size: 0.88rem !important;
            color: var(--color-text-primary) !important;
            vertical-align: middle !important;
            border-bottom: 1px solid var(--color-border) !important;
        }

        .table-custom tbody tr:hover td {
            background-color: var(--color-surface) !important;
        }

        /* DATATABLES OVERRIDES */
        .dt-buttons .btn {
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            padding: 6px 12px !important;
            margin-right: 6px !important;
            border: 1px solid var(--color-primary) !important;
            background: transparent !important;
            color: var(--color-primary) !important;
            transition: all 0.2s;
        }

        .dt-buttons .btn:hover {
            background: var(--color-primary) !important;
            color: #ffffff !important;
        }

        .dataTables_filter input {
            border: 1.5px solid var(--color-border) !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
            outline: none !important;
            font-size: 0.88rem !important;
            color: var(--color-text-primary) !important;
            background: var(--color-card) !important;
        }

        .dataTables_filter input:focus {
            border-color: var(--color-accent) !important;
            box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.15) !important;
        }

        .dataTables_length select {
            border: 1.5px solid var(--color-border) !important;
            border-radius: 8px !important;
            padding: 4px 8px !important;
            outline: none !important;
            color: var(--color-text-primary) !important;
            background: var(--color-card) !important;
        }
    </style>
@endpush

<div class="container-fluid mr-list-wrapper">
    <!-- Header Banner -->
    <div class="header-dashboard-clean">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box-clean">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div>
                <h3 class="header-title-text-clean">Material Requisitions</h3>
                <p class="header-sub-text-clean">View, manage, import, and process site material requisition orders</p>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
            <a class="btn-secondary-memco" href="{{ route('transfer.import.create') }}">
                <i class="fas fa-file-import mr-2"></i> Import M.R
            </a>
            <a class="btn-primary-memco" href="{{ route('transfer.create') }}">
                <i class="fas fa-plus-circle mr-2"></i> Create New M.R
            </a>
            @include('include.backButtons')
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-card">
                <div class="table-responsive">
                    <table id="_dataTable" class="table table-custom">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>MR No.</th>
                                <th>Requestable</th>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Transfer Details -->
<div class="modal fade edit-layout-modal pr-0" id="InvoiceModal" role="dialog" aria-labelledby="InvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog mw-70" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #0f172a; color: #fff; border-radius: 16px 16px 0 0; padding: 18px 24px;">
                <h5 class="modal-title font-weight-bold" id="InvoiceModalLabel" style="font-size: 1.1rem; color: #ffffff;">Transfer Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div id="showData"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
<script>
$(document).ready(function() {
        var searchable = [];
        var selectable = []; 
        var dTable = $('#_dataTable').DataTable({
            order: [],
            lengthMenu: [[20, 40, 80, 150, -1], [20, 40, 80, 150, "All"]],
            processing: true,
            responsive: true,
            serverSide: true,
            language: {
              processing: '<div class="loader"></div>'
            },
            pagingType: "full_numbers",
            dom: "<'row mb-3'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>tipr",
            ajax: {
                url: '{{ route("transfer.getList") }}',
                type: "post",
                data: { _token: '{{ csrf_token() }}' }
            },
            columns: [
                { data: 'number', orderable: false, searchable: false },
                { data: 'material_request_no', name: 'material_request_no', searchable: true },
                { data: 'requestable', name: 'requestable' },
                { data: 'requested_by', name: 'requested_by' },
                { data: 'status', name: 'status'},
                { data: 'date', name: 'date' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn',
                    title: 'Material Requests',
                    header: false,
                    footer: true
                },
                {
                    extend: 'csv',
                    className: 'btn',
                    title: 'Material Requests',
                    header: false,
                    footer: true
                },
                {
                    extend: 'excel',
                    className: 'btn',
                    title: 'Material Requests',
                    header: false,
                    footer: true
                },
                {
                    extend: 'pdf',
                    className: 'btn',
                    title: 'Material Requests',
                    pageSize: 'A2',
                    header: false,
                    footer: true
                },
                {
                    extend: 'print',
                    className: 'btn',
                    title: 'Material Requests',
                    pageSize: 'A2',
                    header: true,
                    footer: false,
                    orientation: 'landscape'
                }
            ],
            initComplete: function () {
                $('.showRequest').on('click', function() {
                  var tid = $(this).data('tid');
                  $.ajax({
                    url: "{{ url('/transfers/show') }}" + '/' + tid, 
                    type: 'GET',
                    success: function(data) {
                      $('#showData').html(data);
                    }
                  });
                }); 
            }  
        });          
    });

    $(document).ready(function() {
        handleDeleteAction('.deleteBtn', '/transfers/delete', function(response) {
        }, function(error) {
        });    
    });  
</script>
@endpush
