@extends('inventory.layout')
@section('title', 'Positions')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="ik ik-list bg-secondary"></i>
                    <div class="d-inline">
                        <h5>{{ __('Positions')}}</h5>
                        <span>{{ __('List of positions')}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-right">
                <a href="#positionAdd" data-toggle="modal" data-target="#positionAdd" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Add</a>
                @include('include.backButtons')
            </div>
        </div>
    </div>    
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header"><h3>{{ __('Positions')}}</h3></div>
                <div class="card-body">
                    <table id="position_table" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Action</th>
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

<!-- Edit modal -->
<div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-labelledby="editPositionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPositionModalLabel">Edit Position</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    <!-- Include input fields for editing position details -->
                    <div class="form-group">
                        <label for="editName">Name</label> 
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="">Department</label>
                        <select name="department_id" id="edit_department_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" id="editPositionId" name="id">
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Position add modal-->
<div class="modal fade edit-layout-modal pr-0 " id="positionAdd" tabindex="-1" role="dialog" aria-labelledby="positionAddLabel" aria-hidden="true">
    <div class="modal-dialog w-300" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="positionAddLabel">{{ __('Add Position')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="submitForm">
                    <div class="form-group">
                        <label class="d-block">Position Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <label for="">Department</label>
                        <select name="department_id" id="department_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
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
    var dTable = $('#position_table').DataTable({
        order: [],
        lengthMenu: [[20, 40, 80, 150, -1], [20, 40, 80, 150, "All"]],
        processing: true,
        responsive: false,
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
            url: '{{ route("positions.getList") }}',
            type: "get"
        },
        columns: [
            { data: 'id', orderable: false, searchable: true },
            { data: 'name', name: 'name' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        buttons: [
            {
                extend: 'copy',
                className: 'btn-sm btn-info',
                title: 'Positions',
                header: false,
                footer: true,
                exportOptions: {
                    // columns: ':visible'
                }
            },
            {
                extend: 'csv',
                className: 'btn-sm btn-success',
                title: 'Positions',
                header: false,
                footer: true,
                exportOptions: {
                    // columns: ':visible'
                }
            },
            {
                extend: 'excel',
                className: 'btn-sm btn-warning',
                title: 'Positions',
                header: false,
                footer: true,
                exportOptions: {
                    // columns: ':visible',
                }
            },
            {
                extend: 'pdf',
                className: 'btn-sm btn-primary',
                title: 'Positions',
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
                title: 'Positions',
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

            $('.editBtn').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#editPositionId').val(id);
                $('#editName').val(name);

                // Show the edit modal
                $('#editPositionModal').modal('show');
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
    handleFormSubmit('#submitForm', '{{ route('positions.store') }}','POST', function(response) {
      //console.log(response);
    }, function(error) {
      console.error(error);
    });
});

$(document).ready(function() {
    handleDeleteAction('.deleteBtn', '/positions/delete', function(response) {
      console.log(response);
    }, function(error) {
      //console.error(error);
    });    
});


</script>

@endpush