@extends('app')

@section('content')
<?php use App\PitstopAPI; ?>

<div class="main-container">
	<div class="header-wrapper">
		<div class="row">			
			<div class="col-sm-12 np">
				<div class="city-bg height-adjust">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-wrapper">
							<?php if (isset($pitStops)) : ?>
																								
								<h4><?php print $pitStops[0]->getCity() ?> <i class="fa fa-hand-o-right"></i> <?php print $pitStops[sizeof($pitStops) - 1]->getCity() ?></h4>										
									<button type="button" class="btn-trip center edit-trip"> Edit Trip</button>					
										
									<p class="strong error"><?php if(isset($errorMessage)) print($errorMessage); ?></p>
									<div class="form-group has-success has-feedback has-key">	
									<p class="strong"><?php if(sizeof($pitStops) == 2) print "Removing a pitstop will remove the trip."?></p>										
										<p>Confirm Key</p>							    									    									      
									    <form action="editTrip" method="POST">
									      	<div class="input-group">
										      	<span class="input-group-addon">Key</span>
										      	<input type="text" class="form-control" id="userKey" name="userKey" placeholder="Insert Key" autocomplete="off">									      		
									      	</div>	

									      	<div id="confirmForm">
									      		<input type="hidden" name="tripId" value='<?php print ($tripId); ?>'>
									      		<input type="hidden" name="itineraryKey" value='<?php print ($itineraryKey); ?>'>
									      	</div>							      	
									      	<button type="submit" class="btn-trip"> Confirm</button>										
										</form>									    						   					   
									</div>
							<?php endif; ?>														
							</div>
						</div>						
					</div>					
				</div>							
			</div>			
		</div>
	</div>

	<div class="content-wrapper">		
		<div class="row">			
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 np">
				<div class="palces-list">
					<div class="row">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 np">
						<?php if (isset($pitStops)) : ?>							
							<?php for ($i=0; $i < sizeof($pitStops); $i++): ?>
								<div class="row bg-<?php print (10 + ($i % 10)) ?>">
									<div class="places-planned col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-12">
										<h3>Pitstop <span><?php print $i ?></span></h3>
											<div class="pitstop-info">																	
												<div class="thumb">
													<img src="{{$baseURL}}imgs/pitstopPlace.png">
												</div>						
												<h4><?php print $pitStops[$i]->getCity(); ?></h4>
												<p><?php print $pitStops[$i]->getFormattedAddress(); ?></p>
											</div>																						
									</div>									
									<?php if(($pitStops[$i]->getPlaces() != null)): ?>	
										<?php if($pitStops[$i]->getPlaces() != ''): ?>									
										<div class="pitstop-places col-xl-10 col-lg-9 col-md-9 col-sm-8 col-xs-12">	
											
											<?php for ($j=0; $j < sizeof($pitStops[$i]->getPlaces()); $j++): ?> 										
													<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 np-xs np-md np-lg">
														<div class="palces-panel">
															<h4><?php print $pitStops[$i]->getPlaces()[$j]->getName() ?></h4>
															<p><?php if($pitStops[$i]->getPlaces()[$j]->getVicinity() != null) print $pitStops[$i]->getPlaces()[$j]->getVicinity() ?></p>
															<div class="palces-img-wrapper bg-<?php print $j % 10;?>">
																<div class="palces-img-inner-wrapper">														
																	<img src="{{$baseURL}}imgs/<?php print $pitStops[$i]->getPlaces()[$j]->getTypes()?>.png" width="100%" data-pin-nopin="true">
																</div>
															</div>
															<span class="stars"><?php print $pitStops[$i]->getPlaces()[$j]->getRating() ?></span>																									
														</div>
													</div>										
											<?php endfor; ?>																																							
										</div>
										<?php else: ?>
											<div class="col-xl-9 col-lg-8 col-md-8 col-sm-6 col-xs-12">											
													<p class="no-place">No Places Found!</p>											
											</div>	
										<?php endif; ?>
									<?php else: ?>
										<div class="col-xl-9 col-lg-8 col-md-8 col-sm-6 col-xs-12">											
												<p class="no-place">No Places Found!</p>											
										</div>	
									<?php endif; ?>
								</div>
							<?php endfor; ?>
						<?php endif; ?>																																
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
	$('.edit-trip').click(function(){
		$('.has-key').show();
	});
</script>

<!--
<script type="text/javascript">
	$('.has-key').hide();
	$('.remove-pitstop').click(function(){
		var itineraryKey = $(this).data('itinerarykey');
		var tripId = $(this).data('tripid');
		var placeId = $(this).data('placeid');

		/*confirmForm*/
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

		var panelInput = document.createElement("input");
		panelInput.type = "hidden";
		panelInput.name = "placeId";
		panelInput.value = placeId;
		confirmForm.appendChild(panelInput);		

		$('.has-key').show();
		$("html, body").animate({ scrollTop: 0 }, 500);
	});
</script>

<script type="text/javascript">	
	$('.remove-pitstop-place').click(function(){
		var itineraryKey = $(this).data('itinerarykey');
		var tripId = $(this).data('tripid');
		var placeId = $(this).data('placeid');
		var placesplaceId = $(this).data('placesplaceid');

		/*confirmForm*/
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

		var panelInput = document.createElement("input");
		panelInput.type = "hidden";
		panelInput.name = "placeId";
		panelInput.value = placeId;
		confirmForm.appendChild(panelInput);

		var panelInput = document.createElement("input");
		panelInput.type = "hidden";
		panelInput.name = "placesplaceId";
		panelInput.value = placesplaceId;
		confirmForm.appendChild(panelInput);		

		$('.has-key').show();
		$("html, body").animate({ scrollTop: 0 }, 500);
	});
</script>
-->
@endsection