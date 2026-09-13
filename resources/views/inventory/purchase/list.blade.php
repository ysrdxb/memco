@extends('inventory.layout')
@section('title', 'Purchases (LPO Records)')

@push('head')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">

    <style>
        .purchase-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            padding: 10px 5px;
        }

    <style>
        .purchase-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            padding: 10px 5px;
        }

        .project-pill-badge {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 6px 16px;
            color: var(--color-text-muted);
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            margin-top: 8px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid purchase-wrapper">
    <!-- Header Banner -->
    <div class="header-dashboard-clean">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box-clean">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div>
                <h3 class="header-title-text-clean">Purchases (L.P.O Records)</h3>
                <p class="header-sub-text-clean">Manage local purchase orders, track supplier deliveries, and view purchase history</p>
                @if(!empty($projectName))
                    <div class="project-pill-badge">
                        <i class="fas fa-building mr-2" style="color: var(--color-accent);"></i> {{ $projectName }}
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center" style="gap: 12px;">
            <a href="{{ route('purchases.create') }}" class="btn-primary-memco">
                <i class="fas fa-plus-circle mr-2"></i> Create L.P.O Order
            </a>
            @include('include.backButtons')
        </div>
    </div>    

    <!-- Data Table Card -->
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="table-card">
                <div class="table-responsive">
                    <table id="purchases_table" class="table table-custom">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="nosort text-center">Date</th>
                                <th class="text-center">LPO No.</th>
                                <th class="text-center">M.R No.</th>
                                <th class="text-center">Supplier</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table> 
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for MR Details -->
<div class="modal fade edit-layout-modal pr-0" id="InvoiceModal" role="dialog" aria-labelledby="InvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog mw-70" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #0f172a; color: #fff; border-radius: 16px 16px 0 0; padding: 18px 24px;">
                <h5 class="modal-title font-weight-bold" id="InvoiceModalLabel" style="font-size: 1.1rem; color: #ffffff;">M.R Details</h5>
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
        var dTable = $('#purchases_table').DataTable({
            order: [],
            lengthMenu: [[20, 40, 80, 150, -1], [20, 40, 80, 150, "All"]],
            processing: true,
            responsive: false,
            serverSide: true,
            language: {
              processing: '<div class="loader"></div>'
            },
            pagingType: "full_numbers",
            dom: "<'row mb-3'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>tipr",
            ajax: {
                url: '{{ route("purchases.getList", $projectId) }}',
                type: "get",
                data: { isWarehouse: '{{ $isWarehouse ?? null }}' }
            },
            columns: [
                { data: 'counter', name: 'counter', orderable: false, searchable: true },
                { data: 'date', name: 'date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'transfer_no', name: 'transfer_no' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn',
                    title: 'Purchases',
                    header: false,
                    footer: true
                },
                {
                    extend: 'csv',
                    className: 'btn',
                    title: 'Purchases',
                    header: false,
                    footer: true
                },
                {
                    extend: 'excel',
                    className: 'btn',
                    title: 'Purchases',
                    header: false,
                    footer: true
                },
                {
                    extend: 'pdf',
                    className: 'btn',
                    title: 'Purchases',
                    pageSize: 'A2',
                    header: false,
                    footer: true
                },
                {
                    extend: 'print',
                    className: 'btn',
                    title: 'Purchases',
                    pageSize: 'A2',
                    header: true,
                    footer: false,
                    orientation: 'landscape'
                }
            ]
        });          
    });

    $(document).ready(function() {
        $('.showRequest').on('click', function() {
          var tid = $(this).data('tid');
          $.ajax({
            url: "{{ url('/purchases/getDeliveryOrder/show') }}" + '/' + tid, 
            type: 'GET',
            success: function(data) {
              $('#showData').html(data);
            }
          });
        });

        handleDeleteAction('.deleteBtn', '/purchases/delete', function(response) {
        }, function(error) {
        });    
    }); 
</script>
@endpush