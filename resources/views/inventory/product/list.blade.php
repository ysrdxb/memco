@extends('inventory.layout')
@section('title', 'Products')
@section('content')
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <style>
            /* HTML: <div class="loader"></div> */
            .loader {
              margin:auto;
              width: 32px;
              aspect-ratio: 1;
              display: grid;
              border: 4px solid #0000;
              border-radius: 50%;
              border-color: #226bc5 #0000;
              animation: l16 1s infinite linear;
            }
            .loader::before,
            .loader::after {    
              content: "";
              grid-area: 1/1;
              margin: 2px;
              border: inherit;
              border-radius: 50%;
            }
            .loader::before {
              border-color: #f03355 #0000;
              animation: inherit; 
              animation-duration: .5s;
              animation-direction: reverse;
            }
            .loader::after {
              margin: 8px;
            }
            @keyframes l16 { 
              100%{transform: rotate(1turn)}
            }         
        </style>
    @endpush
<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-end">
			<div class="col-lg-4">
				<div class="page-header-title">
					<i class="ik ik-list bg-secondary"></i>
					<div class="d-inline">
						<h5>{{ __('Products')}}</h5>
						<span>{{ __('List of products')}}</span>
					</div>
				</div>
			</div>
            <div class="col-lg-8 text-right">
                <button type="button" class="btn btn-sm btn-secondary mr-4" data-toggle="modal" data-target="#importModal"><i class="ik ik-upload"></i>Import</button>
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-danger"><i class="ik ik-plus"></i>Add</a>
                @include('include.backButtons')
            </div>
		</div>
	</div>
    <div class="row">
		@include('include.message')
		<div class="col-md-12">
			<div class="card p-3">
				<div class="card-header"><h3>{{ __('Products')}}</h3></div>
				<div class="card-body">
					<table id="product_table" class="table table-bordered table-stripped">
						<thead>
							<tr>
								<th class="text-center">#</th>
                                <th class="text-center">Name</th>                                
                                <th class="text-center">Code</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Activity</th>
								<th class="text-center">Subactivity</th>                                
                                <th class="text-center">Brand</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Quantity</th>
								@unless(request()->has('print'))
                                <th class="text-center">Action</th>
								@endunless
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

<!-- Import modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Products</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">Choose Excel file:</label>
                        <input type="file" name="file" id="file" class="form-control-file" required accept=".xls,.csv,.xlsx">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="importForm" class="btn btn-primary" id="importBtn">Import</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade edit-layout-modal pr-0 " id="categoryAdd" tabindex="-1" role="dialog" aria-labelledby="categoryAddLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:25% !important;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Update Product')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="updateForm">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
<script>
    $(document).on('click', '.add-stock-link', function() {
        var productId = $(this).data('product-id');
        var html = '<div class="loader"></div>';
        $('#updateForm').html(html);
        $.ajax({
            url: "{{ url('/products/showUpdateForm') }}" + '/' + productId, 
            type: 'POST',
            success: function(data) {
                setTimeout(function() {
                    $('#updateForm').html(data);
                    $('.select2').select2();
                }, 10);
            }
        });
        
    });
    
    $(document).ready(function() {
        var searchable = [];
        var selectable = []; 
        var dTable = $('#product_table').DataTable({
    
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
                loadingIndicator: false
            },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
            ajax: {
                url: '{{ route("products.getList") }}',
                type: "get",
                data: {
                    type: '{{ $type }}'
                }                
            },
            columns: [
                { data: 'id', orderable: false, searchable: true },
                { data: 'name', name: 'name' },                
                { data: 'code', name: 'code' },
                { data: 'type', name: 'type' },
                { 
                    data: 'category_name', 
                    name: 'category_name',
                    render: function(data) {
                        return data ? data : '';
                    }
                },
                { data: 'subcategory', name: 'subcategory' },                
                { 
                    data: 'brand_name', 
                    name: 'brand_name',
                    render: function(data) {
                        return data ? data : '';
                    }
                },                
                { data: 'uom', name: 'uom' },
                //{ data: 'quantity', name: 'quantity', orderable: true, searchable: true},      
                {   data: 'quantity', 
                    name: 'quantity',
                    render: function(data) {
                        return data ? data : '';
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Products',
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
    $('#importForm').submit(function(e) {
        e.preventDefault(); 

        // Disable the button
        $('#importBtn').prop('disabled', true);
        $('#importBtn').text('Wait...');

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                $('#importModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    html: response.message,
                });                
                window.location.href = response.redirect;
            },
            error: function(xhr, status, error) {
                // Enable the button if there is an error
                $('#importBtn').prop('disabled', false);
                $('#importBtn').text('Import');
                Swal.fire({
                    icon: 'eror',
                    title: 'Sorry !!',
                    html: error,
                });                
            }
        });
    });

    $('#importModalBtn').click(function() {
        $('#importModal').modal('show');
    }); 

    handleDeleteAction('.deleteBtn', '/products/destroy', function(response) {
        //
    }, function(error) {
        //
    });    
});

$(document).ready(function() {
    handleFormSubmit('#submitForms', '{{ route('products.updateProduct') }}','POST', function(response) {
    }, function(error) {
    });
  });
</script>
@endpush
