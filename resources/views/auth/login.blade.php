@extends('app')

@section('content')

<?php 
if(!(isset($baseURL))){
	$baseURL = "../../public/";
}
?>

<div class="login-bg">
<div class="container">
	<div class="sign-screen">
		<div class="row sing-screen-wrapper">
			<div class="col-sm-6">
				<div class="screen-logo-section">
					
					<h2>roadtripo</h2>
					<div class="screen-title-section">
						<h1>Welcome to the <br>Roadtripo.</h1>						
					</div>	
					<p>Its time for a Trip.</p>	
					<p>Make a iteneray with RoadTripo and share it with friends.</p>			
				</div>
			</div>
			<div class="col-sm-6">
				<div class="screen-login-section">
					<div class="panel panel-default">						
						<div class="panel-body">
							@if (count($errors) > 0)
								<div class="alert alert-danger">
									<strong>Whoops!</strong> There were some problems with your input.<br><br>
									<ul>
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							@endif

							<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}" autocomplete="off">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<div class="form-group">									
									<div class="col-md-12">
										<input type="email" class="form-control" name="email" value="{{ old('email') }}" autocomplete="off" placeholder="Email Address">
									</div>
								</div>

								<div class="form-group">									
									<div class="col-md-12">
										<input type="password" class="form-control" name="password" autocomplete="off" placeholder="Password">
									</div>
								</div>								

								<div class="form-group">
									<div class="col-md-6 padding-0">
										<button type="submit" class="btn sign-button">Login</button>
										<!-- <a class="btn btn-link" href="{{ url('/password/email') }}">Forgot Your Password?</a> -->
									</div>
								</div>

								<p class="text-white">If you are a New User, please register.</p>
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>
	</div>	
</div>
</div>

@endsection
