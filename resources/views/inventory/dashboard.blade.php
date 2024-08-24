@extends('inventory.layout')
@section('title', 'Dashboard')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <style>
.elevation-2 {
    box-shadow: 2px 2px 4px #a7a7a7;
}

.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
    background-color: #fff;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 1rem;
    min-height: 120px;
    padding: 1.5rem;
    position: relative;
    width: 100%;
}
.info-box .info-box-icon {
    border-radius: 0.25rem;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    font-size: 1.875rem;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    text-align: center;
    width: 70px;
}
.info-box-icon:hover{
    background: red;
}
.bg-gradient-purple {
    background: #6f42c1 linear-gradient(180deg,#855eca,#6f42c1) repeat-x!important;
    color: #fff;
}
.bg-gradient-olive {
    background: #3d9970 linear-gradient(180deg,#5aa885,#3d9970) repeat-x!important;
    color: #fff;
}
.bg-gradient-danger {
    background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x!important;
    color: #fff;
}
.bg-gradient-warning {
    background: #ffc107 linear-gradient(180deg,#ffca2c,#ffc107) repeat-x!important;
    color: #1f2d3d;
}
.bg-gradient-secondary {
    background: #6c757d linear-gradient(180deg,#828a91,#6c757d) repeat-x!important;
    color: #fff;
}
.bg-gradient-orange {
    background: #fd7e14 linear-gradient(180deg,#fd9137,#fd7e14) repeat-x!important;
    color: #fff;
}
.bg-gradient-dark {
    background: #343a40 linear-gradient(180deg,#52585d,#343a40) repeat-x!important;
    color: #fff;
}
.bg-gradient-pink {
    background: #e83e8c linear-gradient(180deg,#eb5b9d,#e83e8c) repeat-x!important;
    color: #fff;
}
.bg-gradient-primary {
    background: #007bff linear-gradient(180deg,#268fff,#007bff) repeat-x!important;
    color: #fff;
}
.bg-gradient-info {
    background: #17a2b8 linear-gradient(180deg,#3ab0c3,#17a2b8) repeat-x!important;
    color: #fff;
}
.bg-gradient-success {
    background: #28a745 linear-gradient(180deg,#48b461,#28a745) repeat-x!important;
    color: #fff;
}
.info-box .info-box-content {
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    line-height: 1.8;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    padding: 0 10px;
}
.info-box .info-box-text, .info-box .progress-description {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.info-box .info-box-number {
    display: block;
    margin-top: 0.25rem;
    font-weight: 700;
}
.info-box:hover {
    box-shadow: 0 3px 6px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.23)!important;
}            
        </style>
    @endpush
<div class="container-fluid">
    <div id="allCounters"></div>
</div>


    @push('script')
        <script>
            $(document).ready(function() {
                var html = '<div class="loader"></div>';
                $('#allCounters').html(html);                
                $.ajax({
                    url: "{{ route('dashboard.data') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#allCounters').html(response.html);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                    }
                });
            });
        </script>
    @endpush
@endsection
