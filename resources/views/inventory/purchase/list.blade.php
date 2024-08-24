@extends('inventory.layout')
@section('title', 'Purchases')
@section('content')
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
    @endpush

<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Purchases')}}</h5>
						<span>{{ __('List of Purchases')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
				<a href="{{ route('purchases.create') }}" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Create</a>
                @include('include.backButtons')
            </div>
		</div>
	</div>    
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header"><h3 class="alert alert-secondary w-100 text-center">{{ $projectName }}</h3></div>
                <div class="card-body">
                    <table id="purchases_table" class="table table-bordered table-stripped">
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
                        <tbody>
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade edit-layout-modal pr-0 " id="InvoiceModal" role="dialog" aria-labelledby="InvoiceModalLabel" aria-hidden="true">
	<div class="modal-dialog mw-70" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="InvoiceModalLabel">M.R Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="card-body">
                    <div id="showData"></div>
				</div>
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
            processing: true,
            language: {
              processing: '<div class="loader"></div>'
            },
            scroller: {
                loadingIndicator: false
            },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
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
                    className: 'btn-sm btn-info',
                    title: 'Stocks',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Stocks',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Stocks',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Stocks',
                    pageSize: 'A2',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-default',
                    title: 'Stocks',
                    // orientation:'landscape',
                    pageSize: 'A2',
                    header: true,
                    footer: false,
                    orientation: 'landscape',
                    exportOptions: {
                        // columns: ':visible',
                        stripHtml: false
                    }
                }
            ],
            initComplete: function () {
                var api =  this.api();
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());
                    input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');
    
                    $(input).appendTo($(column.header()).empty())
                    .on('keyup', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });
    
                    $('input', this.column(column).header()).on('click', function(e) {
                        e.stopPropagation();
                    });
                });
    
                api.columns(selectable).every( function (i, x) {
                    var column = this;
    
                    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function(e){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? '^'+val+'$' : '', true, false ).draw();
                            e.stopPropagation();
                        });
    
                    $.each(dropdownList[i], function(j, v) {
                        select.append('<option value="'+v+'">'+v+'</option>')
                    });
                });
            }  
        });          
	});

</script>

<script>
  $(document).ready(function() {

    $('.showRequest').on('click', function() {
      var tid = $(this).data('tid');
      alert(tid);
      $.ajax({
        url: "{{ url('/purchases/getDeliveryOrder/show') }}" + '/' + tid, 
        type: 'GET',
        success: function(data) {
          $('#showData').html(data);
        }
      });
    });

    handleDeleteAction('.deleteBtn', '/purchases/delete', function(response) {
        //
    }, function(error) {
        //
    });    
  });	
</script>
@endpush