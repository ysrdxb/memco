<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class=" js flexbox canvas canvastext webgl no-touch geolocation postmessage no-websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers no-applicationcache svg inlinesvg smil svgclippaths">
<head>
	<title>@yield('title','') | MEMCO</title>
	<!-- initiate head with meta tags, css and script -->
	@include('include.head')
	<style>
table.table-bordered.dataTable th:last-child, table.table-bordered.dataTable th:last-child, table.table-bordered.dataTable td:last-child, table.table-bordered.dataTable td:last-child{
    text-align:left !important;
}    
.table tbody td{text-align:left !important}
.wrapper .page-wrap .app-sidebar{width:auto}
.wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item a span {
    font-size: 14px;
}
.wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item a {
	font-weight: 400; 
}
.wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item.has-sub .submenu-content .menu-item {
    font-size: 14px;
}
</style>
</head>
<body id="app" class="sidebar-mini">	
    <div class="wrapper">			
    	<!-- initiate header-->
    	@include('include.header')
    	<div class="page-wrap">
	    	<!-- initiate sidebar-->
			@if(Auth::user()->getRoleNames()->first()=='Store Incharge')
		    	@include('inventory.store_sidebar')
			@elseif(Auth::user()->getRoleNames()->first()=='Super Admin' || Auth::user()->getRoleNames()->first()=='Admin')
				@include('inventory.admin_sidebar')
			@elseif(Auth::user()->getRoleNames()->first()=='Warehouse Incharge')
				@include('inventory.warehouse_sidebar')
			@endif
	    	<div class="main-content">
	    		@yield('content')
	    	</div>
	    	@include('include.chat')

	    	<!-- initiate footer section-->
	    	@include('include.footer')

    	</div>
    </div>

    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="notificationMessage">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
						<span data-dismiss="modal" aria-label="Close">OK</span>
					</button>
                </div>
            </div>
        </div>
    </div>

	<!-- initiate scripts-->
	@include('include.script')	

</body>
</html>