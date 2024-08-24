@extends('inventory.layout')
@section('title', 'Material Requisition')

@section('content')
<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-8">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Material Requisitions')}}</h5>
						<span>{{ __('List of Material Requisitions from Main Store')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-4 text-right">
                <a href="{{ route('materialRequest.create') }}" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Create</a>
                <a href="{{ url()->previous() }}" class="btn btn-secondary ml-4"><span class="ik ik-arrow-left"></span> Back</a>
            </div>
		</div>
	</div>

    <div class="row">
		@include('include.message')
		<div class="col-md-12">
			<div class="card p-3">
				<div class="card-header"><h3>{{ __('Material Requisitions')}}</h3></div>
				<div class="card-body">
					<table id="_dataTable" class="table table-bordered">
						<thead>
							<tr>
								<th class="nosort text-center">#</th>
								<th class="text-center">Created Date</th>
								<th class="text-center">REF No.</th>
								<th class="text-center">Requestable</th>
								<th class="text-center">Requested By</th>
								<th class="text-center">Status</th>
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
				<h5 class="modal-title" id="InvoiceModalLabel">Transfer Details</h5>
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
        var dTable = $('#_dataTable').DataTable({
    
            order: [],
            lengthMenu: [[20, 40, 80, 150, -1], [20, 40, 80, 150, "All"]],
            processing: true,
            responsive: true,
            serverSide: true,
            processing: true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
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
                url: '{{ route("materialRequest.getList") }}',
                type: "post"
            },
            columns: [
                { data: 'id', orderable: false, searchable: false },
				{ data: 'date', name: 'date' },
				{ data: 'ref_no', name: 'ref_no' },
				{ data: 'requestable', name: 'requestable' },
				{ data: 'requested_by', name: 'requested_by' },
                { data: 'status', name: 'status'},
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Material Requests',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Products',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Products',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Products',
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
                    title: 'Products',
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

				$('.showRequest').on('click', function() {
					var tid = $(this).data('tid');
					$.ajax({
						url: "{{ url('/materialRequests/show') }}" + '/' + tid, 
						type: 'GET',
						success: function(data) {
						$('#showData').html(data);
						}
					});
				});	

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

  $(document).ready(function() {
    handleDeleteAction('.deleteBtn', '/materialRequests/delete', function(response) {
    }, function(error) {
    });    
  });  
  
</script>
@endpush
