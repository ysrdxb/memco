@extends('inventory.layout')
@section('title', 'Manage Stores')
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
                        <h5>{{ __('Return Materials')}}</h5>
                        <span>Add, remove or edit direct transfers</span>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>{{ __('Returnables')}}</h3>
                        </div>
                        <div class="card-body p-0 table-border-style">
                            @if(count($data))
                            <div class="table-responsive">
                                
                                <table id="advanced_table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Product Type</th>
                                            <th>Quantity</th>
                                            <th>Store</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $row)
                                        <tr>
                                            <td>#{{ $row->id }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            <td>{{ $row->product->type }}</td>
                                            <td>{{ $row->quantity }}</td>
                                            <td>{{ $row->store->name }}</td>
                                            <td>
                                                @if($row->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-success">Success</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->updated_at->diffForHumans() }}</td>
                                            <td>
                                                @if($row->status == 'pending')
                                                    <a href="#categoryAdd" data-toggle="modal" data-target="#categoryAdd" data-tid="{{ encrypt($row->id) }}" class="btn btn-sm btn-primary showData"><i class="ik ik-info"></i> Receive</a>
                                                @else
                                                    Approved
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="card-body">
                                <p class="alert alert-danger">No Returnable Item available</p>
                            </div>
                            @endif                            
                        </div>
                    </div>
                </div>
            </div>               
 
        </div>
    </div>
</div>

<!-- category add modal-->
<div class="modal fade pr-0 edit-layout-modal" id="categoryAdd" tabindex="-1" role="dialog" aria-labelledby="categoryAddLabel" aria-hidden="true">
    <div class="modal-dialog " role="document" style="width:25% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryAddLabel">{{ __('Return Material')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="returnForm">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>
</div>      
@endsection
@push('script')
<script>
    $('.showData').on('click', function() {
      var tid = $(this).data('tid');
      var html = '<div class="loader"></div>';
      $.ajax({
        url: "{{ url('/stores/transferReturns/show') }}" + '/' + tid, 
        type: 'GET',
        success: function(data) {
            setTimeout(function() {
                $('#returnForm').html(data);
            }, 10);          
        }
      });
    });
    
    $(document).on('submit', '#approveQuantity', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('transferReturns.approve') }}",
                type: 'post',
                data: formData,
                success: function(response) {
                    if (response.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            html: response.message,
                        });
                        if (response.redirect && response.redirect !== '') {
                            window.location.href = response.redirect;
                        }
                    } else if (response.status === 'error') {
                        console.log(response.message); // Log the error message to the console
                        swal.fire({
                            title: 'Error',
                            text: response.message.join('<br>'), // Display the error messages as a single string
                            icon: 'error',
                        });
                    } else {
                        // Error message
                        swal.fire({
                            title: 'Error',
                            text: "Invalid response",
                            icon: 'error',
                        });
                    }
                    if (typeof successCallback === 'function') {
                        successCallback(response);
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = Object.values(errors).flat().join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Sorry!!',
                            html: errorMessage,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sorry!!',
                            html: 'An Error Occured',
                        });
                    }
                    if (typeof errorCallback === 'function') {
                        errorCallback(xhr.responseText);
                    }
                }
            });
    });
    


</script>
<script>
  $(document).ready(function() {
    handleDeleteAction('.deleteBtn', '/stores/transferReturns/delete', function(response) {
    }, function(error) {
    });    
  });
</script>
@endpush