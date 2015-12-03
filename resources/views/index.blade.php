@extends('app')

@section('content')
<?php use App\PitstopAPI; ?>
<div class="main-container">
	
	<?php if ((($_SERVER['REQUEST_METHOD'] != 'POST') && !(isset($trip))) || (isset($tripError))): ?>	

	<div class="header-wrapper">
		<div class="row">			
			<div class="col-sm-12 np">
				<div class="city-bg">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-wrapper">
								<h4>Its time for a Trip</h4>
								<p>Make a iteneray with RoadTripo and share it with friends.</p>
								<?php if(isset($tripError)): ?>
									<p class="center"><?php print $tripError?></p>
								<?php endif; ?>
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
							</div>
						</div>						
					</div>					
				</div>							
			</div>			
		</div>
	</div>
	<?php else: ?>
	<div class=""></div>

	<div class="content-wrapper">
		<div class="row content-header">
			<div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 np">	
				<div class="pitstop-header">
					<img src="{{$baseURL}}imgs/nearby.png">
					<h4>Your Pitstop's</h4>
					<!-- <a href="#" class="add-pitstop no-lg">Add</a> -->
				</div>				
			</div>
			<div class="col-xl-10 col-lg-9 col-md-9 col-sm-8 np">
				<div class="row">
					<div class="places-options">
						<div class="col-sm-12">
							<div class="pitstop-nearby">
								<ul>
									<li class="no-md" data-placeType="transit_station">
										<img src="{{$baseURL}}imgs/transit_station.png">
										<p>Transit</p>
									</li>
									<li class="no-sm" data-placeType="restaurant">
										<img src="{{$baseURL}}imgs/restaurant.png">
										<p>Restaurant</p>
									</li>
									<li data-placeType="lodging">
										<img src="{{$baseURL}}imgs/lodging.png">
										<p>Hotels</p>
									</li>
									<li class="no-sm" data-placeType="campground">
										<img src="{{$baseURL}}imgs/campground.png">
										<p>Campground</p>
									</li>									
									<li data-placeType="gas_station">
										<img src="{{$baseURL}}imgs/gas_station.png">
										<p>Gas Station</p>
									</li>									
									<li class="no-md" data-placeType="atm">
										<img src="{{$baseURL}}imgs/atm.png">
										<p>Atm Banks</p>
									</li>									
									<li class="no-sm no-lg" data-placeType="cafe">
										<img src="{{$baseURL}}imgs/cafe.png">
										<p>Cafe</p>
									</li>																																																	
								</ul>
							</div>
						</div>
					</div>					
					<div class="col-sm-2">
					</div>
				</div>
				
			</div>	
		</div>
		<div class="row">
			<div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 np">				
				<div class="pitstop-list">					
					<div class="pitstop-info new-pitstop hidden">
						<div class="thumb">
							<img src="{{$baseURL}}imgs/pitstop.png">
						</div>							
						<div class="form-group has-success has-feedback add-new-pitstop">							    
							<div class="input-group">
								<span class="input-group-addon">Place</span>
								<input type="text" class="form-control" id="inputGroupSuccess3" aria-describedby="inputGroupSuccess3Status">
							</div>							   					   
						</div>
						<div class="form-group has-success has-feedback add-new-pitstop">							    
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<input type="text" class="form-control" id="inputGroupSuccess3" aria-describedby="inputGroupSuccess3Status">
							</div>							   					   
						</div>																		
					</div>		
					<?php if (isset($pitStops)) : ?>														
						<!-- <button class="btn-trip save-itinerary"><i class="fa fa-download"> </i> Save Itinerary</button> -->																			
							<?php for ($i=0; $i < sizeof($pitStops); $i++): ?>														
								<div class="pitstop-info-wrapper">			
									<div class="pitstop-info" data-pitstopid="<?php echo $pitStops[$i]->getPlaceId(); ?>" data-pitstopplaceid="<?php print $pitStops[$i]->getLat(); ?>,<?php print $pitStops[$i]->getLng(); ?>">
										<form action="removePitstop" method="POST">
											<?php if(isset($itineraryKey) && isset($tripId)): ?>
												<input type="hidden" name="itineraryKey" value="<?php print $itineraryKey ?>">
												<input type="hidden" name="tripId" value="<?php print $tripId ?>">
												<input type="hidden" name="jsonPitStops" value='<?php print $jsonPitStops ?>'>
												<input type="hidden" name="placeId" value="<?php print $pitStops[$i]->getPlaceId(); ?>">
												<button type="submit" class="fa-close-button"><i class="fa fa-close"></button></i>
											<?php endif; ?>											
										</form>
										<div class="thumb">
											<img src="{{$baseURL}}imgs/location.png">
										</div>														
										<h4><?php print $pitStops[$i]->getCity(); ?></h4>
										<p data-toggle="tooltip" data-placement="bottom" title="Click to Add Places"> <?php print $pitStops[$i]->getFormattedAddress(); ?> </p>
									</div>
									<div class="place-info">
										<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">																				
											<?php if(($pitStops[$i]->getPlaces() != null) || (!empty($pitStops[$i]->getPlaces()))): ?>

												<?php for ($j=0; $j < sizeof($pitStops[$i]->getPlaces()); $j++): ?> 										
													<div class="panel panel-default to-do">
													    <div class="panel-heading accordion" role="tab" id="<?php print $pitStops[$i]->getPlaces()[$j]->getPlaceId() ?>">
													      <h4 class="panel-title">
													      	<div class="thumb accordion">
																<img src="{{$baseURL}}imgs/<?php print $pitStops[$i]->getPlaces()[$j]->getTypes() ?>.png">
															</div>
													        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
													          <?php print $pitStops[$i]->getPlaces()[$j]->getName() ?>
													          <span><?php if(($pitStops[$i]->getPlaces()[$j]->getVicinity()) != null) print $pitStops[$i]->getPlaces()[$j]->getVicinity() ?></span>
													        </a>
													      </h4>
													    </div>
													    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
													      <div class="panel-body">
													      	<span class="stars"><?php print $pitStops[$i]->getPlaces()[$j]->getRating() ?></span>
													        <span><i class="fa fa-map-marker"> </i> <?php print $pitStops[$i]->getPlaces()[$j]->getLat() ?>, <?php print $pitStops[$i]->getPlaces()[$j]->getLng() ?></span>													       
													        <?php if(($pitStops[$i]->getPlaces()[$j]->getOpenNow()) != null): ?>
													        	<span><a href="#" class="open-close <?php print $pitStops[$i]->getPlaces()[$j]->getOpenNow() ?>"><?php print $pitStops[$i]->getPlaces()[$j]->getOpenNow() ?></a></span>
													        <?php endif; ?>													        
													        <span>													        
													        <form action="removePitstop" method="POST">
													        	<input type="hidden" name="placeId" value="<?php print $pitStops[$i]->getPlaceId() ?>">
													        	<?php if(isset($itineraryKey) && isset($tripId)): ?>
													        		<input type="hidden" name="itineraryKey" value="<?php print $itineraryKey ?>">
													        		<input type="hidden" name="tripId" value="<?php print $tripId ?>">
													        		<input type="hidden" name="jsonPitStops" value='<?php print $jsonPitStops ?>'>
													        		<input type="hidden" name="placesplaceId" value="<?php print $pitStops[$i]->getPlaces()[$j]->getPlaceId() ?>">
													        		<button type="submit" class="btn-trip remove-pitstop"> Remove</button>
													        	<?php endif; ?>													        	
													        </form>
													        
													        </span>
													      </div>
													    </div>
													  </div>
												<?php endfor; ?>
											<?php else: ?>
												<p>No Places Found!</p>
											<?php endif; ?>											
										</div>
									</div>										
									</div>
							<?php endfor; ?>							
					<?php endif; ?>							
				</div>
			</div>
			<div class="col-xl-10 col-lg-9 col-md-9 col-sm-8 np">
				<div class="palces-list">
					<div class="row">
						<div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 np-xs">
							<div class="trip-controls">
								<?php if (isset($pitStops)) : ?>
									<?php if(sizeof($pitStops) < 3): ?>
										<p class="err-msg">Note : Removing any one pitstop will delete the Trip</p>
									<?php endif ?>
								<?php endif ?>
								<?php if(isset($itineraryKey) && isset($tripId)): ?>
									<button class="btn-trip save-itinerary"><i class="fa fa-download"> </i> Save as New Itinerary</button>
									<?php if($tripId != 0): ?>										
										<form action="deleteTrip" method="POST">
											<input type="hidden" name="itineraryKey" value="<?php print $itineraryKey ?>">
											<input type="hidden" name="tripId" value="<?php print $tripId ?>">
											<button type="submit" class="btn-trip delete-itinerary"><i class="fa fa-trash-o"> </i> Delete Itinerary</button>
										</form>	
									<?php endif ?>																	
								<?php else: ?>
									<button class="btn-trip save-itinerary"><i class="fa fa-download"> </i> Save Itinerary</button>
								<?php endif; ?>
							</div>
							<div class="places-visit row" id="places-nearby">														
							</div>
						</div>						
					</div>
					
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

<!-- Modal -->
<div id="saveItinerary" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
  	<div class="modal-header np-bt">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <!--<h4 class="modal-title">Find Nearby Places</h4>-->        
        <h4 class="modal-title">Save Itinerary</h4>
        <p>Save and use your itinerary on the Go..!!</p>
    </div>
  	<div class="modal-body">
  		<div class="form-inline">
  			<div class="itinerary-modal-form">
  				<p>Choose your secret key Eg: @my_#trip</p>
  				<div class="form-group has-success has-feedback mb-15">							    
					<div class="input-group">
						<span class="input-group-addon custom-link">http://roadtripo.com/trips/</span>
						<input type="text" class="form-control custom-link-text" id="itineraryKey" aria-describedby="itineraryKey" placeholder="Key">						
					</div>							   					   
					<img class="keycheck" src="{{$baseURL}}imgs/cross.png">
				</div>									
  				<!-- <div class="form-group has-success has-feedback">							    
					<div class="input-group">
						<span class="input-group-addon">Email</span>
						<input type="text" class="form-control" id="inputGroupSuccess3" aria-describedby="inputGroupSuccess3Status" placeholder="Email Address">
					</div>							   					   
				</div> -->	
				<button type="button" class="btn-trip check-key"> Check Key</button>
				<button type="button" class="btn-trip save-trip"> Save Trip</button>
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

<div id="nearbyPlace" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <!--<h4 class="modal-title">Find Nearby Places</h4>-->
        <img src="{{$baseURL}}imgs/Travel.png">
        <h4 class="modal-title">Find Nearby</h4>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard</p>
      </div>
      <div class="modal-body">
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="atm" name="atm" checked />
			    <label for="atm"></label>
			    <span class="place-text">Atm</span>
			</span>
      	</div>
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="lodging" name="lodging" checked />
			    <label for="lodging"></label>
			    <span class="place-text">Hotels</span>
			</span>
      	</div>
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="restaurant" name="restaurant" checked />
			    <label for="restaurant"></label>
			    <span class="place-text">Restaurants</span>
			</span>
      	</div>      	      	
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="campground" name="campground" checked />
			    <label for="campground"></label>
			    <span class="place-text">Campgrounds</span>
			</span>
      	</div>
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="cafe" name="cafe" checked />
			    <label for="cafe"></label>
			    <span class="place-text">Cafe</span>
			</span>
      	</div>
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="gas_station" name="gas_station" checked />
			    <label for="gas_station"></label>
			    <span class="place-text">Gas Station</span>
			</span>
      	</div>
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="bar" name="bar" checked />
			    <label for="bar"></label>
			    <span class="place-text">Bar</span>
			</span>
      	</div>
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="car_rental" name="car_rental" checked />
			    <label for="car_rental"></label>
			    <span class="place-text">Car Rentals</span>
			</span>
      	</div>
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="transit" name="transit" checked />
			    <label for="transit"></label>
			    <span class="place-text">Transit</span>
			</span>
      	</div>
      	<div class="nearby-place-block">
      		 <span class="field-checkbox is-fancy">
			    <input type="checkbox" value="None" id="point_of_interest" name="point_of_interest" checked />
			    <label for="point_of_interest"></label>
			    <span class="place-text">Places</span>
			</span>
      	</div>     	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Find Nearby</button>
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

/*$('.place-info').hide();*/
$('.pitstop-info').click(function(){
	$('.pitstop-info').removeClass('active');
	$(this).addClass('active');
	$(this).next('.place-info').slideToggle();
	$(this).next('.place-info').removeClass('hidden');
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
 $(function(){
 	$('.plan-trip').click(function() { 		
       	var travelFrom = $('#startPoint').val();
        var travelTo = $( "#endPoint" ).val();  

           $.ajax({
                url: 'plantrip/{tripStart}/{tripEnd}',
                type: 'GET',
                data: { tripStart:travelFrom, tripEnd:travelTo },
                success: function(data)
                {                                     	      
                	alert(data);
                }
        });
    });
 });
</script>

<script type="text/javascript">
var pitStopId = ""
var placeLatLng = "";
var placeSearchType = "";
	$('.pitstop-info').click(function(){
		placeLatLng = $(this).data("pitstopplaceid");
		pitStopId = $(this).data("pitstopid");	

		placeSearchType = "point_of_interest,lodging";				

		if(placeLatLng != ""){
			getPlaces(pitStopId, placeLatLng, placeSearchType);			
		}
	});
	$('.pitstop-nearby li').click(function(){
		placeSearchType = $(this).data("placetype");
		
		if(placeLatLng != ""){
			getPlaces(pitStopId, placeLatLng, placeSearchType);			
		}		
	});	

function getPlaces(pitStopId, placeLatLng, placeSearchType){

		$.ajax({
                url: 'places/{latlng}/{placetype}/{pitstops}',
                type: 'POST',
                data: { latlng:placeLatLng, placetype:placeSearchType, pitstops:pitStops },
                success: function(data)
                {                         
                	var response = data['nearbyPlaces'].nearbyPlaces;            	      
                	console.log(data);
                	document.getElementById("places-nearby").innerHTML = "";

                	for (var i = 0; i < response.length; i++) {
                		var placesPlanel = document.createElement("div"); 
                		placesPlanel.className = "palces-panel";

                		var panelTitle = document.createElement("h4");
                		panelTitle.innerHTML = response[i].name;
                		placesPlanel.appendChild(panelTitle);  

                		var panelInfo = document.createElement("p");
                		panelInfo.innerHTML = response[i].vicinity;
                		placesPlanel.appendChild(panelInfo);  

                		var placeImgWrapper = document.createElement("div"); 
                		var setBg = i % 10;               	
                		placeImgWrapper.className = "palces-img-wrapper bg-"+setBg;                		

                		var placeImgInnerWrapper = document.createElement("div");
                		placeImgInnerWrapper.className = "palces-img-inner-wrapper";

                		var placeImg = document.createElement("img");
                		placeImg.src = "<?php print $baseURL; ?>imgs/" + response[i].types[0]+'.png';
                		placeImg.setAttribute('width', '100%');

                		placeImgInnerWrapper.appendChild(placeImg);
                		placeImgWrapper.appendChild(placeImgInnerWrapper);
                		placesPlanel.appendChild(placeImgWrapper);

                		/* Creating a form */
                		var panelForm = document.createElement("form");
						panelForm.setAttribute('method',"POST");
						panelForm.setAttribute('action',"addPlace");	

						var panelInput = document.createElement("input");
						panelInput.type = "hidden";
						panelInput.name = "itineraryKey";
						panelInput.value = '<?php if(isset($itineraryKey)){ print $itineraryKey; } else { print 0; }?>';
						panelForm.appendChild(panelInput);	

						var panelInput = document.createElement("input");
						panelInput.type = "hidden";
						panelInput.name = "tripId";
						panelInput.value = '<?php if(isset($tripId)){ print $tripId; } else{ print 0; }  ?>';
						panelForm.appendChild(panelInput);				

						var panelInput = document.createElement("input");
						panelInput.type = "hidden";
						panelInput.name = "placeDetails";
						panelInput.value = JSON.stringify(response[i]);
						panelForm.appendChild(panelInput);

						var panelInput = document.createElement("input");
						panelInput.type = "hidden";
						panelInput.name = "pitstopId";
						panelInput.value = pitStopId;
						panelForm.appendChild(panelInput);

						var panelInput = document.createElement("input");
						panelInput.type = "hidden";
						panelInput.name = "pitstops";
						panelInput.value = JSON.stringify(pitStops);
						panelForm.appendChild(panelInput);										
						
						/* Creating a button */
                		var panelButton = document.createElement("button");
                		var panelButtonText = document.createTextNode("Add to Trip"); 
                		panelButton.className = "btn-trip";
                		panelButton.appendChild(panelButtonText); 
                		
                		panelForm.appendChild(panelButton);  
                		placesPlanel.appendChild(panelForm);  

                		var outerPanel = document.createElement("div");
                		outerPanel.className = "col-xl-3 col-lg-4 col-md-4 col-sm-4 col-xs-6 np-xs np-md np-lg";
                		outerPanel.appendChild(placesPlanel);
                		document.getElementById("places-nearby").appendChild(outerPanel);    
                	}
                }
        	});
}

</script>

<?php if(isset($pitStops)): 

	for ($i=0; $i < sizeof($pitStops); $i++) { 	
		$placesJsonObject = [];
		for ($j=0; $j < sizeof($pitStops[$i]->getPlaces()); $j++) { 

				$placesJsonObject[$j] = [
					'lat' => $pitStops[$i]->getPlaces()[$j]->getLat(),
					'lng' => $pitStops[$i]->getPlaces()[$j]->getLng(),
					'name' => $pitStops[$i]->getPlaces()[$j]->getName(),
					'placeId' => $pitStops[$i]->getPlaces()[$j]->getPlaceId(),
					'priceLevel' => $pitStops[$i]->getPlaces()[$j]->getPriceLevel(),
                    'rating' => $pitStops[$i]->getPlaces()[$j]->getRating(),
                    'open_now' => $pitStops[$i]->getPlaces()[$j]->getOpenNow(),
                    'vicinity' => $pitStops[$i]->getPlaces()[$j]->getVicinity(),
                    'types' => $pitStops[$i]->getPlaces()[$j]->getTypes()
				];
				
			}

		if(empty($placesJsonObject)){
			$placesJsonObject = [];
		}
		
		$pitStopsJsonObject[$i] = [
					  'city' => $pitStops[$i]->getCity(),
					  'formatted_address' => $pitStops[$i]->getFormattedAddress(),
				      'lat' => $pitStops[$i]->getLat(),
				      'lng' => $pitStops[$i]->getLng(),
				      'placeId' => $pitStops[$i]->getPlaceId(),
				      'places' => $placesJsonObject
		];
	}	
	$pitStopsJsonObject = str_replace("'", "", json_encode($pitStopsJsonObject));
	?>
	<script type="text/javascript">

		var pitStops = JSON.parse('<?php if(isset($pitStopsJsonObject)){ print ($pitStopsJsonObject); } ?>');
		var lastStop = pitStops.length - 1;
		var pitStopId = pitStops[lastStop].placeId;
		var placeLatLng = pitStops[lastStop].lat + ',' + pitStops[lastStop].lng;
		var placeSearchType = "point_of_interest,lodging";						
		getPlaces(pitStopId, placeLatLng, placeSearchType);	

	</script>
<?php endif; ?>

<script type="text/javascript">

$('.save-trip').hide();
	$('.check-key').click(function(){
		
		var	itineraryKey = $('#itineraryKey').val();
		itineraryKey = $.trim(itineraryKey);
		if(itineraryKey.length > 0){
			$.ajax({
	            url: 'checkKey/{itineraryKey}',
	            type: 'GET',
	            data: { itineraryKey:itineraryKey },
	            success: function(data)
	            {
	            	alert(data);
	            	if(data == 'Valid Key'){
	            		$('.check-key').hide();
	            		$('.save-trip').show();
	            		$("#itineraryKey").prop('disabled', true);
	            		$(".keycheck").attr("src", "{{$baseURL}}imgs/tick.png");
	            	}
	            	else{
	            		$('.check-key').show();
	            		$('.save-trip').hide();
	            		$(".keycheck").attr("src", "{{$baseURL}}imgs/cross.png");
	            	}
	            }
	        });
		}		
	});
</script>

<script type="text/javascript">
	$('.save-trip').click(function(){
		var	itineraryKey = $('#itineraryKey').val();
		
		$.ajax({
            url: 'saveTrip/{itineraryKey}/{pitstops}',
            type: 'POST',
            data: { itineraryKey:itineraryKey, pitstops:pitStops },
            success: function(data)
            {
            	if(data != 0){
            		$('#saveItinerary').modal('hide');
            		window.location.replace("trips");            		            		            	
            	}
            }
        });
	});
</script>

@endsection