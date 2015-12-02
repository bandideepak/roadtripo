@extends('app')

@section('content')

<div class="main-container">	
	<div class="header-wrapper">
		<div class="row">			
			<div class="col-sm-12 np">
				<div class="city-bg">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-wrapper">
								<h4>Its time for a Trip</h4>
								<p>Make a iteneray with RoadTripo and share it with friends.</p>								
								<div class="form-section">
									<form class="form-inline" method="POST" action="plantrip">
									  <div class="form-group has-success has-feedback">							    
									    <div class="input-group">
									      <span class="input-group-addon">From</span>
									      <input type="text" class="form-control" id="startPoint" name="startPoint" placeholder="Start">
									    </div>							   					   
									  </div>
									  <div class="form-group has-success has-feedback">							    
									    <div class="input-group">
									      <span class="input-group-addon">To</span>
									      <input type="text" class="form-control" id="endPoint" name="endPoint" placeholder="Destination">
									    </div>							   					   
									  </div>									 
									  <button type="submit" class="btn-trip"> Plan Trip</button>
									</form>
								</div>
								<p class="strong error"><?php if(isset($errorMessage)) print($errorMessage); ?></p>
								<div class="form-group has-success has-feedback has-key">									
									<p>Confirm Key</p>							    									    									      
									<form action="removeTrip" method="POST">
									    <div class="input-group">
										    <span class="input-group-addon">Key</span>
										    <input type="text" class="form-control" id="userKey" name="userKey" placeholder="Insert Key" autocomplete="off">									      		
									    </div>	
									<div id="confirmForm"></div>							      	
									<button type="submit" class="btn-trip"> Confirm</button>										
									</form>									    						   					   
								</div>								
							</div>
						</div>						
					</div>
					<div class="content-wrapper">		
						<div class="row">			
							<div class="col-xl-offset-2 col-xl-8 col-lg-12 col-md-12 col-sm-12 np">
								<div class="palces-list">
									<div class="row">
										<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 np">
										<?php if (isset($trips)) : ?>											
											<?php $trips = (json_decode($trips)); ?>
											<div class="row">
												<?php for ($i=0; $i < sizeof($trips); $i++): ?>								
													<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 np-xs np-md np-lg">
														<div class="palces-panel">
																<h4><?php print $trips[$i]->from ?></h4>
																<p><?php print $trips[$i]->to ?></p>
																<div class="palces-img-wrapper bg-<?php print $i % 10;?>">
																	<div class="palces-img-inner-wrapper">														
																		<img src="{{$baseURL}}imgs/pitstopPlace.png" width="100%" data-pin-nopin="true">
																	</div>
																</div>
																<form action="viewtrip" method="POST">
																	<input type="hidden" name="itineraryKey" value="<?php print $trips[$i]->key ?>">
																	<input type="hidden" name="tripId" value="<?php print $trips[$i]->id ?>">
																	<button type="submit" class="btn-trip"> View Trip</button>
																</form>																		
															<span>																
																<a class="remove-pitstop-place" href="#" data-itinerarykey="<?php print $trips[$i]->key; ?>" data-tripid="<?php print $trips[$i]->id; ?>">Remove</a>
															</span>
														</div>
													</div>								
												<?php endfor; ?>
											</div>
										<?php endif; ?>																																
										</div>						
									</div>					
								</div>
							</div>
						</div>
					</div>					
				</div>							
			</div>			
		</div>
	</div>	
</div>

<div id="loginModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
  	<div class="modal-header np-bt">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <!--<h4 class="modal-title">Find Nearby Places</h4>-->                    
        <img src="{{$baseURL}}imgs/Sign_in.png">  
    </div>

  	<div class="modal-body">
  		<form class="form-inline">
  			<div class="sign-modal-form">  															
  				<div class="form-group has-success has-feedback">							    
					<div class="input-group">
						<span class="input-group-addon">Email</span>
						<input type="text" class="form-control" id="inputGroupSuccess3" aria-describedby="inputGroupSuccess3Status" placeholder="Email Address">
					</div>							   					   
				</div>
				<div class="form-group has-success has-feedback">							    
					<div class="input-group">
						<span class="input-group-addon">Pwd</span>
						<input type="password" class="form-control" id="inputGroupSuccess3" aria-describedby="inputGroupSuccess3Status" placeholder="Password">
					</div>							   					   
				</div>	
				<button type="button" class="btn-trip"> Register</button>
				
				<button type="button" class="join-btn fb"><i class="fa fa-facebook"> </i> Facebook</button>
				<button type="submit" class="join-btn g"><i class="fa fa-google"> </i> Google</button>
				<span class="new_here">New here? <a data-toggle="modal" href="#joinModal">Register </a></span>
  			</div>	  			 													 	
		</form>
  	</div>
  </div>  	
  </div>
</div>

<div id="joinModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
  	<div class="modal-header np-bt">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <!--<h4 class="modal-title">Find Nearby Places</h4>-->                    
        <img src="{{$baseURL}}imgs/Sign_in.png">  
    </div>

  	<div class="modal-body">
  		<form class="form-inline">
  			<div class="sign-modal-form">  															
  				<div class="form-group has-success has-feedback">							    
					<div class="input-group">
						<span class="input-group-addon">Email</span>
						<input type="text" class="form-control" id="inputGroupSuccess3" aria-describedby="inputGroupSuccess3Status" placeholder="Email Address">
					</div>							   					   
				</div>
				<div class="form-group has-success has-feedback">							    
					<div class="input-group">
						<span class="input-group-addon">Pwd</span>
						<input type="password" class="form-control" id="inputGroupSuccess3" aria-describedby="inputGroupSuccess3Status" placeholder="Password">
					</div>							   					   
				</div>	
				<button type="button" class="btn-trip"> Login</button>
				
				<button type="button" class="join-btn fb"><i class="fa fa-facebook"> </i> Facebook</button>
				<button type="submit" class="join-btn g"><i class="fa fa-google"> </i> Google</button>
				<span class="new_here">Already have account? <a data-toggle="modal" href="#loginModal">Log in </a></span>
  			</div>	  			 													 	
		</form>
  	</div>
  </div>  	
  </div>
</div>


<div id="map"></div>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<script type="text/javascript" src="{{$baseURL}}js/ployline.js"></script>
<script type="text/javascript" src="{{$baseURL}}js/maps-api.js"></script>



<script type="text/javascript">
$('.datepicker').datepicker();

$('.save-itinerary').click(function(){
	$("#saveItinerary").modal('show');
});
$('.modal').hide();
$('.nearby-btn').click(function(){
	$("#nearbyPlace").modal('show');
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});

/* ======== NEW PITSTOP ========= */
$('.new-pitstop').hide();

$('.add-pitstop').click(function(){
	$('.new-pitstop').removeClass('hidden')
	$('.new-pitstop').slideToggle( "slow", function() {});
});

/* ======== STARS DISPLAY ========= */
$.fn.stars = function() {
    return $(this).each(function() {
        // Get the value
          val = parseFloat($(this).html());
        // Make sure that the value is in 0 - 5 range, multiply to get width
        var size = Math.max(0, (Math.min(5, val))) * 16;
        // Create stars holder
        var $span = $('<span />').width(size);
        // Replace the numerical value with stars
        $(this).html($span);
    });
}
$(function() {
    $('span.stars').stars();
});

$('.new_here').click(function(){
	$(this).closest('.modal').modal('toggle');
});

</script>

<script type="text/javascript">
$('.has-key').hide();
	$('.remove-pitstop-place').click(function(){
		$('.form-section').hide();		

		var itineraryKey = $(this).data('itinerarykey');
		var tripId = $(this).data('tripid');

		document.getElementById("confirmForm").innerHTML = "";
		var confirmForm = document.getElementById("confirmForm");
		
		var panelInput = document.createElement("input");
		panelInput.type = "hidden";
		panelInput.name = "itineraryKey";
		panelInput.value = itineraryKey;
		confirmForm.appendChild(panelInput);

		var panelInput = document.createElement("input");
		panelInput.type = "hidden";
		panelInput.name = "tripId";
		panelInput.value = tripId;
		confirmForm.appendChild(panelInput);

		$('.has-key').show();
		$("html, body").animate({ scrollTop: 0 }, 500);
	});
</script>

@endsection