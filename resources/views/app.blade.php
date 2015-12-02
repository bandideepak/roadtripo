<?php 
if(!(isset($baseURL))){
	$baseURL = "../../public/";
}
?>
<!DOCTYPE html>
<html  lang="en">
<head>
	<title>Road Tripo: Road Trip | Itinerary planner for trips</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta property="og:locale" content="en_US">
	<meta property="og:type" content="website">
	<meta property="og:title" content="Road Tripo: Road Trip &amp; Itinerary planner for trips">
	<meta property="og:description" content="Road Tripo is #1 free Itinerary planner. Get Started for Planning your trips and share Itinerary with your friends">
	<meta property="og:url" content="http://roadtripo.com/">
	<meta property="og:site_name" content="Road Tripo">

	<!-- Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Raleway:500,600,400,300' rel='stylesheet' type='text/css'>

	<!-- Prettify -->
    <link href="{{$baseURL}}css/prettify.css" rel="stylesheet">

	<!-- CSS Stylesheet -->
	<link rel="stylesheet" type="text/css" href="{{$baseURL}}css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="{{$baseURL}}css/BootstrapXL.css">
	<link rel="stylesheet" type="text/css" href="{{$baseURL}}css/font-awesome.min.css">	
	<link href="{{$baseURL}}css/owl.carousel.css" rel="stylesheet">
    <link href="{{$baseURL}}css/owl.theme.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="{{$baseURL}}css/style.css">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	<!-- Javascript -->
	<script type="text/javascript" src="{{$baseURL}}js/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="{{$baseURL}}js/jquery-migrate-1.2.1.min.js"></script>	
	<script type="text/javascript" src="{{$baseURL}}js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{{$baseURL}}js/owl.carousel.min.js"></script>
	<script type="text/javascript" src="{{$baseURL}}js/bootstrap-datepicker.js"></script>	
	<script type="text/javascript" src="{{$baseURL}}js/moment.js"></script>
</head>
<body>

<div class="main-container">
	<div class="nav-wrapper">
		<div class="container">
			<div class="row navigation-section">
				<nav class="navbar navbar-default">
				  <div class="container-fluid">
				    <!-- Brand and toggle get grouped for better mobile display -->
				    <div class="navbar-header">
				      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				        <span class="sr-only">Toggle navigation</span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				      </button>
				      <a class="navbar-brand" href="{{ url('/index') }}"><img src="{{$baseURL}}imgs/logo.png" alt="logo"></a>
				    </div>

				    <!-- Collect the nav links, forms, and other content for toggling -->
				    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				      <ul class="nav navbar-nav">		        
				        <!-- <li><a href="trips">Trips</a></li>		 -->		       
				      </ul>		    
				      <ul class="nav navbar-nav navbar-right">				      	
				      	<li><a href="{{ url('/trips') }}">Trips</a></li>	
				      	@if (Auth::guest())
							<li><a href="{{ url('/auth/login') }}">Login</a></li>
							<li><a href="{{ url('/auth/register') }}">Register</a></li>
						@else
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="{{ url('/auth/logout') }}">Logout</a></li>
								</ul>
							</li>
						@endif				       
				        <!-- <li class="dropdown">
				          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Account <span class="caret"></span></a>
				          <ul class="dropdown-menu" role="menu">
				            <li><a data-toggle="modal" href="#joinModal" >Login</a></li>
				            <li><a data-toggle="modal" href="#loginModal">Register</a></li>				            
				            <li class="divider"></li>
				            <li><a href="#">Separated link</a></li>
				          </ul>
				        </li> -->
				      </ul>
				    </div><!-- /.navbar-collapse -->
				  </div><!-- /.container-fluid -->
				</nav>
			</div>
		</div>
	</div>
	@yield('content')
</div>
</body>
</html>
