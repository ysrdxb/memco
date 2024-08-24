@extends('inventory.layout')
@section('title', 'Products')
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
                            <h5>{{ __('Products')}}</h5>
                            <span>{{ __('List of products')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 text-right">
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
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="activity">Activity:</label>
                                <select id="activity" class="form-control">
                                    <option value="">All</option>
                                    @foreach($activities as $activity)
                                        <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="subactivity">Subactivity:</label>
                                <select id="subactivity" class="form-control">
                                    <option value="">All</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="">&nbsp;</label><br>
                                <button id="filterButton" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                        <table id="product_table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Activity</th>
                                    <th>Subactivity</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    @unless(request()->has('print'))
                                    <th>Action</th>
                                    @endunless
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
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
                    <div id="updateForm"></div>
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
            url: "{{ url('/stores/products/showUpdateForm') }}" + '/' + productId, 
            type: 'POST',
            success: function(data) {
                setTimeout(function() {
                    $('#updateForm').html(data);
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
        pagingType: "full_numbers",
        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        ajax: {
            url: '{{ route("store.getProductsList") }}',
            type: "get"
        },
        columns: [
            { data: 'id', orderable: false, searchable: true },
            { data: 'name', name: 'name', orderable: false, searchable: true },
            { data: 'code', name: 'code' },
            { 
                data: 'category_name', 
                name: 'category_name',
                render: function(data) {
                    return data ? data : '';
                }
            },
            { data: 'subcategory_name', name: 'subcategory_name' },                
            { 
                data: 'brand_name', 
                name: 'brand_name',
                render: function(data) {
                    return data ? data : '';
                }
            },                
            { data: 'price', name: 'price' },
            { data: 'uom', name: 'uom' },
            { 
                data: 'quantity', 
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
            var api = this.api();
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
        }
    });

    $('#activity').on('change', function() {
        var activityId = $(this).val();
        if (activityId) {
            $.ajax({
                url: "{{ route('getSubactivities') }}",
                type: "GET",
                data: {
                    activity_id: activityId
                },
                success: function(response) {
                    $('#subactivity').empty();
                    $('#subactivity').append($('<option>', {
                        value: '',
                        text: 'All'
                    }));
                    $.each(response, function(key, value) {
                        $('#subactivity').append($('<option>', {
                            value: value.id,
                            text: value.name
                        }));
                    });
                }
            });
        } else {
            $('#subactivity').empty();
            $('#subactivity').append($('<option>', {
                value: '',
                text: 'All'
            }));
        }
    });

    $('#filterButton').on('click', function() {
        var activityId = $('#activity').val();
        var subactivityId = $('#subactivity').val();

        dTable.columns(3).search(activityId).columns(4).search(subactivityId).draw();
    });  
});


$(document).ready(function() {
    handleFormSubmit('#submitForms', '{{ route('stores.updateProductStock', encrypt(Auth::user()->stores->first()->id )) }}','POST', function(response) {
    }, function(error) {
    });
  });
</script>
@endpush
