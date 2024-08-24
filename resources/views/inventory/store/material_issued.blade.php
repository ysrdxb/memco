@extends('inventory.layout')
@section('title', 'Material Issued')
@section('content')
@push('head')
    <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
@endpush

<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Material Issue Voucher')}}</h5>
						<span>{{ __('List of MIV')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
                <a href="{{ route('stores.getMaterialIssuedEmployeeRecord') }}" class="btn btn-sm btn-primary mr-4"><i class="ik ik-info"></i>Employee Record</a>
                <a href="{{ route('stores.createMaterialIssued') }}" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Create</a>
                @include('include.backButtons')
            </div>
		</div>
	</div>
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header"><h3>{{ __( 'Material Issued' )}}</h3></div>
                <p class="alert alert-info">Note! You can return material issued by the employee by clicking on Return Button!</p>
                <div class="card-body">
                    <table id="stocks_table" class="table table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Issue Voucher No</th>
                                <th>Issued By</th>
                                <th>Store Name</th>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Employee Position</th>
                                <th>Date</th>
                                <th>Actions</th>
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

<div class="modal fade edit-layout-modal pr-0 " id="categoryAdd" tabindex="-1" role="dialog" aria-labelledby="categoryAddLabel" aria-hidden="true">
    <div class="modal-dialog w-300" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Edit Remarks')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    
                    <div class="form-group">
                        <label for="">Remarks</label>
                        <input type="text" class="form-control" name="remarks" id="remarks" required>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary" type="submit" name="Save">Save <i class="ik ik-save"></i></button>
                    </div>
                </form>
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
        var dTable = $('#stocks_table').DataTable({

            order: [],
            lengthMenu: [[20, 40, 80, 150, -1], [20, 40, 80, 150, "All"]],
            processing: true,
            responsive: true,
            serverSide: true,
            processing: true,
            language: {
              processing: '<div class="loader"></div>'
            },
            scroller: {
                loadingIndicator: true
            },
            scroller: {
                loadingIndicator: true
            },
            pagingType: "full_numbers",
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
            ajax: {
                url: '{{ route("stores.getMaterialIssuedList" ) }}',
                type: "get"
            },
            columns: [
                { data: 'counter', orderable: false, searchable: true },
                { data: 'issue_order_no', name: 'issue_order_no' },
                { data: 'issued_by', name: 'issued_by' },
                { data: 'store_name', name: 'store_name' },
                { data: 'employee_code', name: 'employee_code' },
                { data: 'employee_name', name: 'employee_name' },
                { data: 'employee_department', name: 'employee_department' },
                { data: 'date', name: 'date', orderable: false, searchable: false },
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

            $('.editVoucher').click(function() {
                var id = $(this).data('id');
                var url = '{{ route("project.material_issued.edit", ":id") }}';
                url = url.replace(':id', id);
            
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        // Assuming response contains the HTML content to be injected
                        $('#updateForm').html(response);
                        // Display the modal after the form is populated
                        $('#categoryAdd').modal('show');
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error('Error:', error);
                    }
                });
            
              handleFormSubmit('#updateForm', url, 'POST', function(response) {
                  // Handle success response
              }, function(error) {
                  // Handle error response
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

</script>

<script>

$(document).ready(function() {

    $(document).ready(function() {
        handleDeleteAction('.deleteBtn', '/stores/material/issued/delete', function(response) {
        }, function(error) {
        });
    });

        handleFormSubmit(formId, url , method, function(response) {
        }, function(error) {
        });


});

</script>

@endpush
