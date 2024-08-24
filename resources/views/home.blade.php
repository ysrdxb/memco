<!doctype html>
<html class="no-js" lang="en">
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Memco</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="icon" href="{{ asset('public/favicon.png') }}" type="image/x-icon" />

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
        
        <script src="{{ asset('js/app.js') }}"></script>

        <!-- themekit admin template asstes -->
        <link rel="stylesheet" href="{{ asset('public/all.css') }}">
        <link rel="stylesheet" href="{{ asset('public/dist/css/theme.css') }}">
        <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/plugins/icon-kit/dist/css/iconkit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/plugins/ionicons/dist/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    </head>

    <body class="home-gradient-bg">
		<div class="container ">
			<div class="d-flex justify-content-between my-5">
				<div>
					<a href="{{url('/')}}">
		            	<img width="150" src="{{ asset('img/logo1.png') }}">
		            </a>
				</div>
				<div>
					<a class="btn btn-primary btn-rounded mr-3" href="{{url('login')}}/">Login</a>
				</div>
			</div>
	    	<div class="banner-text m-4 d-relative">
	    		<img height="50" class="d-absolute left-0"  src="{{asset('/img/p1.png')}}">
	    		<img height="300" class="d-absolute"  src="{{asset('/img/s1-2.png')}}">
	    		<img height="50" class="d-absolute right-0"  src="{{asset('/img/s2-2.png')}}">
	    		Let us access <br> the system!
	    	</div>

	    	<div class="radmin-bannner text-center">
	    		<img  src="{{asset('/img/radmin.jpg')}}">
	    	</div>
		    <div class="row justify-content-center">
		        <div class="my-5">
		        	<p class="text-center">Need Help?</p>
		        	<div class="card-body template-demo text-center">
                        <a href="https://themicly.com" class="btn social-btn text-white btn-google"><i class="ik ik-globe"></i></a>
                        <a href="https://github.com/themicly" class="btn social-btn text-white btn-facebook "><i class="fab fa-github"></i></a>
                        <a href="https://twitter.com/themiclyBd" class="btn social-btn text-white btn-twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/rakibhstu/" class="btn social-btn text-white btn-linkedin"><i class="fab fa-linkedin"></i></a>
                    </div>
		        	
		        </div>
		    </div>
		</div>
		<script src="{{ asset('all.js') }}"></script>
        
    </body>
</html>

