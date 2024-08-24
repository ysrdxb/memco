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
						<h5>{{ __('Material Issue')}}</h5>
						<span>{{ __('Issue Material within main store')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
                <a href="{{ route('warehouses.createMaterialIssued') }}" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Create</a>
                @include('include.backButtons')
            </div>
		</div>
	</div>    
    <div class="row">
        @include('include.message')
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header"><h3>{{ __( 'Material Issued' )}}</h3></div>
                <div class="card-body">
                    <table id="stocks_table" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Quantity Issued</th>
                                <th>Current Stock</th>
                                <th>Warehouse Name</th>
                                <th>Worker Name</th>
                                <th>Worker Department</th>
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
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Create Material Issued')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="submitForm">                 
                    <div class="form-group">
                        <label for="editName">Select Item</label>
                        <select name="product_id" class="form-control select2" required>
                            @foreach($stockRecord as $row)
                                <option value="{{$row->product->id}}">{{$row->product->name}}</option>
                            @endforeach
                        </select>
                    </div>  
                    <div class="form-group">
                        <label class="d-block"> Quantity </label>
                        <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
                    </div>                     
                    <div class="form-group">
                        <label for="">Worker Name</label>
                        <input type="text" class="form-control" name="worker_name" required>
                    </div>                                       
                    <div class="form-group">
                        <label for="">Worker Department</label>
                        <input type="text" class="form-control" name="worker_department" required>
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
                url: '{{ route("warehouses.getMaterialIssuedList" ) }}',
                type: "get"
            },
            columns: [
                { data: 'id', orderable: false, searchable: true },
                { data: 'product_code', name: 'product_code' },
                { data: 'product_name', name: 'product_name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'current_stock', name: 'current_stock' },
                { data: 'warehouse_name', name: 'warehouse_name' },
                { data: 'worker_name', name: 'worker_name' },
                { data: 'worker_department', name: 'worker_department' },
                { data: 'updated_date', name: 'updated_date', orderable: false, searchable: false },
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
    $('.editUnitBtn').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var user_id = $(this).data('euser_id');
        var address = $(this).data('address');
        $('#editUnitId').val(id);
        $('#editName').val(name);
        $('#editAddress').val(address);
        $('#user_id').val(user_id);
        var url = '{{ route("warehouses.update", ":id") }}';
        url = url.replace(':id', id);
        
        handleFormSubmit('#updateForm', url, 'PUT', function(response) {
        }, function(error) {
        });
    });

    $(document).ready(function() {
        handleDeleteAction('.deleteBtn', '/warehouses/material/issued/delete', function(response) {
        }, function(error) {
        });    
    });

    $(document).ready(function() {
        handleFormSubmit('#submitForm', '{{ route('warehouses.saveMaterialIssued') }}','POST', function(response) {
        }, function(error) {
        });
    }); 

});
 
</script>

@endpush