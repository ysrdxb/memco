@extends('inventory.layout') 
@section('title', 'Users')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('public/plugins/DataTables/datatables.min.css') }}">
    @endpush

    
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Users')}}</h5>
                            <span>{{ __('List of users')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <button type="button" class="btn btn-sm btn-secondary mr-4" data-toggle="modal" data-target="#importModal"><i class="ik ik-upload"></i>Import</button>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header"><h3>{{ __('Users')}}</h3></div>
                    <div class="card-body">
                        <table id="user_table" class="table table-bordered table-stripped">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name')}}</th>
                                    <th>{{ __('Email')}}</th>
                                    <th>{{ __('Role')}}</th>
                                    <th>{{ __('Permissions')}}</th>
                                    <th>{{ __('Action')}}</th>
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

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Users</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
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
    <!-- push external js -->
    @push('script')
    <script src="{{ asset('public/plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('public/plugins/select2/dist/js/select2.min.js') }}"></script>
    <!--server side users table script-->
    <script src="{{ asset('public/js/custom.js') }}"></script>

    <script>
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

        handleDeleteAction('.deleteBtn', '/users/destroy', function(response) {
            //
        }, function(error) {
            //
        });    
    });
        
    </script>
    @endpush
@endsection
