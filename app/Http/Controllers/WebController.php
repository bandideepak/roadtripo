<?php 

namespace App\Http\Controllers;

use App\Routes;
use App\Trip;
use Hash;
use App\Libraries\Pitstop;
use App\Libraries\Place;
use App\PitstopAPI;

class WebController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Web Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

	public function __construct()
	{
		$this->middleware('auth');
	}

	
	
	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */	
	public function planTrip(){		
		
		$tripStart = htmlspecialchars($_POST['startPoint']);
        $tripEnd = htmlspecialchars($_POST['endPoint']);	

        $tripStart = preg_replace("/[^ \w]+/", "", $tripStart);
        $tripEnd = preg_replace("/[^ \w]+/", "", $tripEnd);

        $tripStart = str_replace(' ', '+', $tripStart);
        $tripEnd = str_replace(' ', '+', $tripEnd);

        $resultPitStops = $this->planTripLocations($tripStart, $tripEnd);	   

        if($resultPitStops == null){
        	$baseURL = '../public/';
			return \View::make('index')->with('tripError', 'Invalid Start and Destination points.')->with('baseURL', $baseURL);
		}     

        $baseURL = '../public/';
		return \View::make('index')->with('pitStops', $resultPitStops)->with('baseURL', $baseURL);
	}

	public function planTripLocations($tripStart, $tripEnd){		
				
		$tripRoute = PitstopAPI::getRoutes($tripStart, $tripEnd);
		
		if($tripRoute == null){

			return $tripRoute;
		}

		if($tripRoute->status !="OK"){
			return null;
		}

		if ($tripRoute->status=="OK") {	

			$response = $tripRoute;	

		    $totalDistance = $response->routes[0]->legs[0]->distance->value;
			$totalMilesDistance = $totalDistance*0.000621371192;

			$totalDuration = $response->routes[0]->legs[0]->duration->value;			      	
			$seconds = $totalDuration;
			$H = floor($seconds / 3600);
			$i = ($seconds / 60) % 60;
			$s = $seconds % 60;
			$totalHourDistance = $H . ':' . $i . ':' . $s;
			
			
			$travelLegs[0] = $response->routes[0]->legs[0]->steps[0]->distance->value;			
		    for ($i = 1; $i < sizeof($response->routes[0]->legs[0]->steps); $i++) {
		        $travelLegs[$i] = $travelLegs[$i-1] + $response->routes[0]->legs[0]->steps[$i]->distance->value;
		    }

		    /* Get starting point Place Id */
      		$allPitStopsPlaceID[0] = $response->geocoded_waypoints[0]->place_id;

      		/* Per day travel time = 8 Hrs = 28800 Seconds. */
		    /* Per day travel time = 480 Miles = 772485 Meters. */		    
		    $travelTime = 28800;
		    $travelDistance = 772485;
		    $totalBreaks = floor($totalDistance / $travelDistance);	   
			
			/* wayPointsWithDistance */
			if($totalBreaks == 0){
				$totalBreaks = 1;
			}

			$distancePerDay = $totalDistance / $totalBreaks;				

			$intermediatePoints[0] = $travelLegs[0];
			for ($i = 0; $i < $totalBreaks; $i++) {
			    $wayPoint = $this->getClosest($travelLegs, $distancePerDay*($i + 1));
			   	$indexPoint = array_search($wayPoint, $travelLegs);		
			   	
			    if(($travelLegs[$indexPoint] - $distancePerDay*($i + 1)) > ($travelLegs[$indexPoint - 1] - $distancePerDay*($i + 1))){
			      $intermediatePoints[$i+1] = $travelLegs[$indexPoint - 1];
			    }
			    else{
			      $intermediatePoints[$i+1] = $travelLegs[$indexPoint];
			    }   
			}			

			/* Once the intermediate points are generated, we get the wayPoint locations in Pitstops. */   
			$pitStops = PitstopAPI::getWayPoints($intermediatePoints, $travelLegs, $tripRoute);				


			/* Once the wayPoint Locations are fetched, we get the place ids of all Pitstops. */			
			for ($i=0; $i < sizeof($pitStops); $i++) { 				
				
				$geocode_json_data = PitstopAPI::getPlaceDetails($pitStops[$i]->getLat(), $pitStops[$i]->getLng());					
				        
				if ($geocode_json_data->status=="OK") {
					$geocoderesponse = $geocode_json_data;	
					
					if(isset($geocoderesponse->results[0]->address_components[4]) && $geocoderesponse->results[0]->address_components[4]->types[0] == "administrative_area_level_2"){
						$city = $geocoderesponse->results[0]->address_components[3]->long_name;
					}
					else if($geocoderesponse->results[0]->address_components[3]->types[0] == "administrative_area_level_2"){
						$city = $geocoderesponse->results[0]->address_components[2]->long_name;
					}
					else if($geocoderesponse->results[0]->address_components[2]->types[0] == "administrative_area_level_2"){
						$city = $geocoderesponse->results[0]->address_components[1]->long_name;
					}
					else if($geocoderesponse->results[0]->address_components[1]->types[0] == "administrative_area_level_2"){
						$city = $geocoderesponse->results[0]->address_components[0]->long_name;
					}
					else{
						$city = $geocoderesponse->results[0]->address_components[1]->long_name;
					}

					if($i == 0){
						if(isset($geocoderesponse->results[0]->address_components[4]) && $geocoderesponse->results[0]->address_components[4]->types[0] == "locality"){
							$city = $geocoderesponse->results[0]->address_components[4]->long_name;
						}
						else if($geocoderesponse->results[0]->address_components[3]->types[0] == "locality"){
							$city = $geocoderesponse->results[0]->address_components[3]->long_name;
						}
						else if($geocoderesponse->results[0]->address_components[2]->types[0] == "locality"){
							$city = $geocoderesponse->results[0]->address_components[2]->long_name;
						}
						else if($geocoderesponse->results[0]->address_components[1]->types[0] == "locality"){
							$city = $geocoderesponse->results[0]->address_components[1]->long_name;
						}
						else{
							$city = $geocoderesponse->results[0]->address_components[0]->long_name;
						}
					}			   

					$pitStop = new Pitstop();
					$pitStop->setCity($city);
					$pitStop->setFormattedAddress($geocoderesponse->results[0]->formatted_address);
					$pitStop->setLat($pitStops[$i]->getLat());
					$pitStop->setLng($pitStops[$i]->getLng());
					$pitStop->setPlaceId($geocoderesponse->results[0]->place_id);
					$pitStop->setPlaces(null); 	
					$pitStopObject[$i] = $pitStop;					
					/*$pitStops[$i] = [
					  'city' => $city,
					  'formatted_address' => $geocoderesponse->results[0]->formatted_address,
				      'lat' => $pitStops[$i]->getLat(),
				      'lng' => $pitStops[$i]->getLng(),
				      'placeId' => $geocoderesponse->results[0]->place_id,
				      'places' => null
				    ];*/				    				    

				}
				else {
			          //if no result found, status would be ZERO_RESULTS
			          return $geocode_json_data->status;  
				}				
			}	
			$pitStops = $pitStopObject;
			/*$pitStops = str_replace("'", "", json_encode($pitStops));*/					   
			return $pitStops;		

		} else {
		    //if no result found, status would be ZERO_RESULTS
		    return $tripRoute->status;  
		}
	}

	public function getClosest($arr, $search) {	   
	   	$closest = max($arr);
	   	
	   	for($j = 0; $j < sizeof($arr); $j++){ 
        	if($arr[$j] >= $search && $arr[$j] < $closest){
        		$closest = $arr[$j]; 
        	} 
	    }	   
	    return $closest;
	}

	public function placesNearby($latlng, $placetype, $pitstops){

		if(isset($_POST['latlng']) && isset($_POST['placetype'])){
			$latlng = $_POST['latlng'];	
			$placetype = $_POST['placetype'];
			$pitStops = $_POST['pitstops'];
		}

		$lat = explode(",", $latlng)[0];
		$lng = explode(",", $latlng)[1];
		$pitStops = json_decode(json_encode($pitStops));
						
		$placesNearby = PitstopAPI::placesNearby($latlng, $placetype);		       	

		if ($placesNearby->status=="OK") {	

			//Adding places to the pitstop
			$pitStops = PitstopAPI::addPlacestoPitstop($pitStops, $placesNearby);		    		   		   
		    return $pitStops;		   

		} else {
		    //if no result found, status would be ZERO_RESULTS
		    return $placesNearby->status;  
		}

		return null;
	}

	public function allTrips(){

		$allTrips = Trip::getAllTrips();		
		$trips = null;		

		for ($i=0; $i < sizeof($allTrips); $i++) { 			
			$tripObject = json_decode($allTrips[$i]->trip_object);	
			
			if(isset($tripObject[$i]->places)) {
				$tripPotstops = sizeof($tripObject[$i]->places);
			}
			else{
				$tripPotstops = null;
			}

			$trips[$i] = [
				'from' => $tripObject[0]->city,		
				'from_formatted_address' => $tripObject[0]->formatted_address,
				'from_lat' => $tripObject[0]->lat,	
				'from_lng' => $tripObject[0]->lng,	
				'to' => $tripObject[sizeof($tripObject) - 1]->city,
				'to_formatted_address' => $tripObject[sizeof($tripObject) - 1]->formatted_address,
				'to_lat' => $tripObject[sizeof($tripObject) - 1]->lat,	
				'to_lng' => $tripObject[sizeof($tripObject) - 1]->lng,
				'id' => $allTrips[$i]->trip_id,
				'key' => $allTrips[$i]->trip_key, 				
				'pitstops' => $tripPotstops
			];
		}

		$trips = str_replace("'", "", json_encode($trips));

		$baseURL = '../public/';	    		    
		return \View::make('trips')->with('trips', $trips)->with('baseURL', $baseURL);
	}

	public function addPlace(){		

		if(isset($_POST['placeDetails']) && isset($_POST['pitstopId'])){
			$placeDetails = $_POST['placeDetails'];	
			$pitstopId = $_POST['pitstopId'];
			$pitStops = $_POST['pitstops'];
		}		

		$placeDetails =json_decode($placeDetails);		
		$pitStops =json_decode($pitStops);					
		
		for ($i=0; $i < sizeof($pitStops); $i++) { 		    	

			$price_level = null;
			$open_now = null;
			$rating = 1;
			$vicinity = null;
			$places = [];
			$newPlaceObject = [];	
			$oldPlaceObject = [];		
			$placeObject = [];			

			if($pitStops[$i]->placeId == $pitstopId){

				$places = [];
				$places = $pitStops[$i]->places;

				if(isset($placeDetails->price_level)){
					$price_level = $placeDetails->price_level;
				}				

				if(isset($placeDetails->opening_hours)){
					if($placeDetails->opening_hours->open_now){
						$open_now = "open";
					}
					else{
						$open_now = "close";
					}
				}

				if(isset($placeDetails->rating)){
					$rating = $placeDetails->rating;
				}

				if(isset($placeDetails->vicinity)){
					$vicinity = $placeDetails->vicinity;
				}

				$newPlaceObject = new Place();
				$newPlaceObject->setLat($placeDetails->geometry->location->lat);
				$newPlaceObject->setLng($placeDetails->geometry->location->lng);
				$newPlaceObject->setName($placeDetails->name);
				$newPlaceObject->setPlaceId($placeDetails->place_id);
				$newPlaceObject->setPriceLevel($price_level);
				$newPlaceObject->setRating($rating);
				$newPlaceObject->setOpenNow($open_now);
				$newPlaceObject->setVicinity($vicinity);
				$newPlaceObject->setTypes($placeDetails->types[0]);												

				if(isset($places)){
					$totalPlaces = sizeof($places);

				}
				else{
					$totalPlaces = 0;
				}				

				if($totalPlaces > 0){

					$placeObject = [];
					
					for ($k=0; $k < sizeof($pitStops[$i]->places); $k++) { 
						
						$oldPlaceObject = new Place();
						$oldPlaceObject->setLat($pitStops[$i]->places[$k]->lat);
						$oldPlaceObject->setLng($pitStops[$i]->places[$k]->lng);
						$oldPlaceObject->setName($pitStops[$i]->places[$k]->name);
						$oldPlaceObject->setPlaceId($pitStops[$i]->places[$k]->placeId);
						$oldPlaceObject->setPriceLevel($pitStops[$i]->places[$k]->priceLevel);
						$oldPlaceObject->setRating($pitStops[$i]->places[$k]->rating);
						$oldPlaceObject->setOpenNow($pitStops[$i]->places[$k]->open_now);
						$oldPlaceObject->setVicinity($pitStops[$i]->places[$k]->vicinity);
						$oldPlaceObject->setTypes($pitStops[$i]->places[$k]->types);

						$placeObject[$k] = $oldPlaceObject;								
					}

					$placeObject[$totalPlaces] = $newPlaceObject;
				}
				else{
					$placeObject[0] = $newPlaceObject;
				}

				$places = $placeObject;							
			}
			else{	

				$places = [];							
				
				if(sizeof($pitStops[$i]->places) > 0){

					for ($j=0; $j < sizeof($pitStops[$i]->places); $j++) { 
						$prevPlaceObject = new Place();
						$prevPlaceObject->setLat($pitStops[$i]->places[$j]->lat);
						$prevPlaceObject->setLng($pitStops[$i]->places[$j]->lng);
						$prevPlaceObject->setName($pitStops[$i]->places[$j]->name);
						$prevPlaceObject->setPlaceId($pitStops[$i]->places[$j]->placeId);
						$prevPlaceObject->setPriceLevel($pitStops[$i]->places[$j]->priceLevel);
						$prevPlaceObject->setRating($pitStops[$i]->places[$j]->rating);
						$prevPlaceObject->setOpenNow($pitStops[$i]->places[$j]->open_now);
						$prevPlaceObject->setVicinity($pitStops[$i]->places[$j]->vicinity);
						$prevPlaceObject->setTypes($pitStops[$i]->places[$j]->types);

						$places[$j] = $prevPlaceObject;						
					}
				}
				else{
					$places = [];
				}			

				if(empty($places)){
					$places = null;
				}											
			}							
			$newPitStopObject = new Pitstop();
			$newPitStopObject->setCity($pitStops[$i]->city);
			$newPitStopObject->setFormattedAddress($pitStops[$i]->formatted_address);
			$newPitStopObject->setLat($pitStops[$i]->lat);
			$newPitStopObject->setLng($pitStops[$i]->lng);
			$newPitStopObject->setPlaceId($pitStops[$i]->placeId);
			$newPitStopObject->setPlaces($places); 			
			$pitStopObject[$i] = $newPitStopObject;		

		}	

		$pitStops = $pitStopObject;					

		$itineraryKey = null;
		$tripId = null;				

		$jsonPitStops = PitstopAPI::jsonPitstop($pitStops);

		if(isset($_POST['itineraryKey']) && isset($_POST['tripId'])){			
			$itineraryKey = $_POST['itineraryKey'];	
			$tripId = $_POST['tripId'];				
					
			Trip::updateTrip($tripId, $jsonPitStops);								
		}					

		$baseURL = '../public/';			
		return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('jsonPitStops', $jsonPitStops)->with('errorMessage', '')->with('baseURL', $baseURL);
		/*return \View::make('index')->with('pitStops', $pitStops)->with('baseURL', $baseURL);*/
	}

	public function editTrip(){
		
		if(isset($_POST['itineraryKey']) && isset($_POST['tripId']) && isset($_POST['userKey'])){
			$itineraryKey = $_POST['itineraryKey'];	
			$tripId = $_POST['tripId'];			
			$userKey = $_POST['userKey'];		
		}
		else{
			$baseURL = '../public/';	    	
			return \View::make('trips')->with('baseURL', $baseURL);
		}		

		$getTrip = Trip::getTripbyId($tripId);				

		$pitStops = $getTrip[0]->trip_object; 		
		$pitStops = str_replace("'", "", $pitStops);		

		$baseURL = '../public/';

		$jsonPitStops = $pitStops;
		$pitStops = json_decode($pitStops); 		

		$finalpitStopObject = null;

		for ($i=0; $i < sizeof($pitStops); $i++) { 

			$pitStopPlaceObject = null;

			if(isset($pitStops[$i]->places)){
				for ($j=0; $j < sizeof($pitStops[$i]->places); $j++) { 				
				
					$place = new Place();
					$place->setLat($pitStops[$i]->places[$j]->lat);
					$place->setLng($pitStops[$i]->places[$j]->lng);
					$place->setName($pitStops[$i]->places[$j]->name);
					$place->setPlaceId($pitStops[$i]->places[$j]->placeId);
					$place->setPriceLevel($pitStops[$i]->places[$j]->priceLevel);
					$place->setRating($pitStops[$i]->places[$j]->rating);
					$place->setOpenNow($pitStops[$i]->places[$j]->open_now);
					$place->setVicinity($pitStops[$i]->places[$j]->vicinity);
					$place->setTypes($pitStops[$i]->places[$j]->types);

					$pitStopPlaceObject[$j] = $place;
				}
			}			
			
			$pitStop = new Pitstop();
			$pitStop->setCity($pitStops[$i]->city);
			$pitStop->setFormattedAddress($pitStops[$i]->formatted_address);
			$pitStop->setLat($pitStops[$i]->lat);
			$pitStop->setLng($pitStops[$i]->lng);
			$pitStop->setPlaceId($pitStops[$i]->placeId);
			$pitStop->setPlaces($pitStopPlaceObject); 	
			$finalpitStopObject[$i] = $pitStop;			
		}	

		$pitStops = $finalpitStopObject;

		/* Check the key */		
		if($userKey == ''){
			$errorMessage = 'Key Required';
			return \View::make('viewtrip')->with('pitStops', $pitStops)->with('errorMessage', $errorMessage)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('jsonPitStops', $jsonPitStops)->with('baseURL', $baseURL);		
		} 
		else if (Hash::check($userKey, $itineraryKey))
		{
		    $errorMessage = '';
		    return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('jsonPitStops', $jsonPitStops)->with('baseURL', $baseURL);
		}	
		else{			
			$errorMessage = 'Invalid Key';
			return \View::make('viewtrip')->with('pitStops', $pitStops)->with('errorMessage', $errorMessage)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('jsonPitStops', $jsonPitStops)->with('baseURL', $baseURL);		
		}		
		
		return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('jsonPitStops', $jsonPitStops)->with('baseURL', $baseURL);	    		    
		
	}

	public function saveTrip($itineraryKey, $pitstops){		

		if(isset($_POST['itineraryKey']) && isset($_POST['pitstops'])){
			$itineraryKey = $_POST['itineraryKey'];	
			$pitstops = json_encode($_POST['pitstops']);			
		}

		$itineraryKey = Hash::make($itineraryKey);
		$checkItineraryKey = Trip::checkKey($itineraryKey);
		
		if($checkItineraryKey){
			return 0;
		}

		return Trip::saveTrip($itineraryKey, $pitstops);
	}

	public function removePitstop(){		

		if(isset($_POST['itineraryKey']) && isset($_POST['tripId']) && isset($_POST['placeId'])){
			$itineraryKey = $_POST['itineraryKey'];	
			$tripId = $_POST['tripId'];
			$placeId = $_POST['placeId'];			
		}
		else{
			$baseURL = '../public/';	    	
			return \View::make('trips')->with('baseURL', $baseURL);
		}				

		$tripItinerary = Trip::getTrip($itineraryKey);	
		
		if(empty($tripItinerary)){
			
			$jsonPitStops = $_POST['jsonPitStops'];
			$pitStops = json_decode($jsonPitStops);			

			/*$baseURL = '../public/';	
			return \View::make('trips')->with('baseURL', $baseURL);*/
		}
		else{
			
			$pitStops = ($tripItinerary[0]->trip_object);
			$pitStops = json_decode($pitStops);
		}				
		
		for ($i=0; $i < sizeof($pitStops); $i++) { 		    	
			
			if(isset($pitStops[$i]->places)){
				$places = $pitStops[$i]->places;
			}
			else{
				$places = [];
			}
										
		
		    $pitStopObject[$i] = [
				'city' => $pitStops[$i]->city,
				'formatted_address' => $pitStops[$i]->formatted_address,
				'lat' => $pitStops[$i]->lat,
				'lng' => $pitStops[$i]->lng,
				'placeId' => $pitStops[$i]->placeId,
				'id' => $tripId,
				'key' => $itineraryKey,				      
				'places' => $places
			];
		}

		$pitStops = $pitStopObject;
		$pitStops = str_replace("'", "", json_encode($pitStops));

		$baseURL = '../public/';		
		$pitStops = json_decode($pitStops);

		if(isset($_POST['placesplaceId'])){

			/* To Remove Place in Pitstop */
			$placesplaceId = $_POST['placesplaceId'];
									
			$removedId = null;
			$removedPlaceId = null;

			for ($i=0; $i < sizeof($pitStops); $i++) { 
				if(($pitStops[$i]->id == $tripId) && ($pitStops[$i]->placeId == $placeId)){					
					$removedId = $i;

					for ($j=0; $j < sizeof($pitStops[$i]->places); $j++) { 						
						if($pitStops[$i]->places[$j]->placeId == $placesplaceId){
							unset($pitStops[$i]->places[$j]);							
							$removedPlaceId = $j;
						}
					}
				}
			}

			
			$k = 0;
			$placesRemovedObject = null;
			for ($i=0; $i < sizeof($pitStops); $i++) { 		    	
				if($i == $removedId){
					
					for ($j=0; $j < sizeof($pitStops[$i]->places); $j++) { 
						if($j == $removedPlaceId){
							$k++;
						}						
					}	
				}				

				
				if($i == $removedId){					
					for ($m=0; $m < sizeof($pitStops[$removedId]->places); $m++) { 
						if($m >= $removedPlaceId){
							if(isset($pitStops[$removedId]->places[$m+1])){
								$placesRemovedObject[$m] = $pitStops[$removedId]->places[$m+1];
							}							
						}
						else{
							$placesRemovedObject[$m] = $pitStops[$removedId]->places[$m];
						}						
					}	
					$places = $placesRemovedObject;																							
					$pitStops[$removedId]->places = $places;							
				}								
														  
			}
			
			$pitStops = json_encode($pitStops);
			if($tripId != 0){			
				$updateTrip = Trip::updateTrip($tripId, $pitStops);	
			}			
			
		}
		else{

			/* To Remove PitStop */		
			$removedId = null;
			for ($i=0; $i < sizeof($pitStops); $i++) { 
				if(($pitStops[$i]->id == $tripId) && ($pitStops[$i]->placeId == $placeId)){
					unset($pitStops[$i]);
					$removedId = $i;
				}
			}

			$j = 0;			

			if(sizeof($pitStops) == 0){
				$baseURL = '../public/';	
				return \View::make('trips')->with('baseURL', $baseURL);
			}

			/* If only two pitstop present */
			if(sizeof($pitStops) == 1){

				Trip::deleteTrip($tripId);
				$baseURL = '../public/';	
				return \View::make('trips')->with('baseURL', $baseURL);
			}		

			for ($i=0; $i < sizeof($pitStops); $i++) { 		    	
				if($i == $removedId){
					$j++;
				}
				$places = $pitStops[$j]->places;

				if($pitStops[$j]->places == ''){
					$places = null;
				}							
			
			    $pitStopRemovedObject[$i] = [
					'city' => $pitStops[$j]->city,
					'formatted_address' => $pitStops[$j]->formatted_address,
					'lat' => $pitStops[$j]->lat,
					'lng' => $pitStops[$j]->lng,
					'placeId' => $pitStops[$j]->placeId,
					'id' => $tripId,
					'key' => $itineraryKey,				      
					'places' => $places
				];

				$j++;
			}

			$pitStops = $pitStopRemovedObject;		

			$pitStops = json_encode($pitStops);

			if($tripId != 0){
				$updateTrip = Trip::updateTrip($tripId, $pitStops);	
			}			

		}		

		$jsonPitStops = $pitStops;
		$pitStops = json_decode($pitStops); 		
		$finalpitStopObject = null;

		for ($i=0; $i < sizeof($pitStops); $i++) { 

			$pitStopPlaceObject = null;

			for ($j=0; $j < sizeof($pitStops[$i]->places); $j++) { 				
				
				$place = new Place();
				$place->setLat($pitStops[$i]->places[$j]->lat);
				$place->setLng($pitStops[$i]->places[$j]->lng);
				$place->setName($pitStops[$i]->places[$j]->name);
				$place->setPlaceId($pitStops[$i]->places[$j]->placeId);
				$place->setPriceLevel($pitStops[$i]->places[$j]->priceLevel);
				$place->setRating($pitStops[$i]->places[$j]->rating);
				$place->setOpenNow($pitStops[$i]->places[$j]->open_now);
				$place->setVicinity($pitStops[$i]->places[$j]->vicinity);
				$place->setTypes($pitStops[$i]->places[$j]->types);

				$pitStopPlaceObject[$j] = $place;
			}
			
			$pitStop = new Pitstop();
			$pitStop->setCity($pitStops[$i]->city);
			$pitStop->setFormattedAddress($pitStops[$i]->formatted_address);
			$pitStop->setLat($pitStops[$i]->lat);
			$pitStop->setLng($pitStops[$i]->lng);
			$pitStop->setPlaceId($pitStops[$i]->placeId);
			$pitStop->setPlaces($pitStopPlaceObject); 	
			$finalpitStopObject[$i] = $pitStop;			
		}	

		$pitStops = $finalpitStopObject;

		return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('jsonPitStops', $jsonPitStops)->with('errorMessage', '')->with('baseURL', $baseURL);		
		
	}

	public function deleteTrip(){

		if(isset($_POST['itineraryKey']) && isset($_POST['tripId'])){
			$itineraryKey = $_POST['itineraryKey'];	
			$tripId = $_POST['tripId'];						
		}
		else{
			$baseURL = '../public/';	    	
			return \View::make('trips')->with('baseURL', $baseURL);
		}		

		$tripItinerary = Trip::getTrip($itineraryKey);	
		
		if(empty($tripItinerary)){
			$baseURL = '../public/';	
			return \View::make('trips')->with('baseURL', $baseURL);
		}		
		
		$errorMessage = '';		
		Trip::deleteTrip($tripId);

		$allTrips = Trip::getAllTrips();		
		$trips = null;

		for ($i=0; $i < sizeof($allTrips); $i++) { 			
			$tripObject = json_decode($allTrips[$i]->trip_object);	
			
			if(isset($tripObject[$i]->places)) {
				$tripPotstops = sizeof($tripObject[$i]->places);
			}
			else{
				$tripPotstops = null;
			}

			$trips[$i] = [
				'from' => $tripObject[0]->city,		
				'from_formatted_address' => $tripObject[0]->formatted_address,
				'from_lat' => $tripObject[0]->lat,	
				'from_lng' => $tripObject[0]->lng,	
				'to' => $tripObject[sizeof($tripObject) - 1]->city,
				'to_formatted_address' => $tripObject[sizeof($tripObject) - 1]->formatted_address,
				'to_lat' => $tripObject[sizeof($tripObject) - 1]->lat,	
				'to_lng' => $tripObject[sizeof($tripObject) - 1]->lng,
				'id' => $allTrips[$i]->trip_id,
				'key' => $allTrips[$i]->trip_key, 				
				'pitstops' => $tripPotstops
			];
		}		

		$trips = str_replace("'", "", json_encode($trips));

		$baseURL = '../public/';	    		    
		
		if($errorMessage != ''){
			return \View::make('trips')->with('trips', $trips)->with('errorMessage', $errorMessage)->with('baseURL', $baseURL);		
		} 		
		else{			
			return \View::make('trips')->with('trips', $trips)->with('errorMessage', $errorMessage)->with('baseURL', $baseURL);
		}		
	}

	public function removeTrip(){

		if(isset($_POST['itineraryKey']) && isset($_POST['tripId']) && isset($_POST['userKey'])){
			$itineraryKey = $_POST['itineraryKey'];	
			$tripId = $_POST['tripId'];			
			$userKey = $_POST['userKey'];		
		}
		else{
			$baseURL = '../public/';	    	
			return \View::make('trips')->with('baseURL', $baseURL);
		}		

		$tripItinerary = Trip::getTrip($itineraryKey);	
		
		if(empty($tripItinerary)){
			$baseURL = '../public/';	
			return \View::make('trips')->with('baseURL', $baseURL);
		}

		/* Check the key */		
		if($userKey == ''){
			$errorMessage = 'Key Required';
		} 
		else if (Hash::check($userKey, $itineraryKey))
		{
		    $errorMessage = '';
		}	
		else{			
			$errorMessage = 'Invalid Key';
		}

		if($errorMessage == ''){
			Trip::deleteTrip($tripId);
		}

		$allTrips = Trip::getAllTrips();		
		$trips = null;

		for ($i=0; $i < sizeof($allTrips); $i++) { 			
			$tripObject = json_decode($allTrips[$i]->trip_object);	
			
			if(isset($tripObject[$i]->places)) {
				$tripPotstops = sizeof($tripObject[$i]->places);
			}
			else{
				$tripPotstops = null;
			}

			$trips[$i] = [
				'from' => $tripObject[0]->city,		
				'from_formatted_address' => $tripObject[0]->formatted_address,
				'from_lat' => $tripObject[0]->lat,	
				'from_lng' => $tripObject[0]->lng,	
				'to' => $tripObject[sizeof($tripObject) - 1]->city,
				'to_formatted_address' => $tripObject[sizeof($tripObject) - 1]->formatted_address,
				'to_lat' => $tripObject[sizeof($tripObject) - 1]->lat,	
				'to_lng' => $tripObject[sizeof($tripObject) - 1]->lng,
				'id' => $allTrips[$i]->trip_id,
				'key' => $allTrips[$i]->trip_key, 				
				'pitstops' => $tripPotstops
			];
		}		

		$trips = str_replace("'", "", json_encode($trips));

		$baseURL = '../public/';	    		    
		
		if($errorMessage != ''){
			return \View::make('trips')->with('trips', $trips)->with('errorMessage', $errorMessage)->with('baseURL', $baseURL);		
		} 		
		else{			
			return \View::make('trips')->with('trips', $trips)->with('errorMessage', $errorMessage)->with('baseURL', $baseURL);
		}		
	}

	public function viewTrip(){		

		if(isset($_POST['itineraryKey']) && isset($_POST['tripId'])){
			$itineraryKey = $_POST['itineraryKey'];	
			$tripId = $_POST['tripId'];		
		}
		else{
			$baseURL = '../public/';	    	
			return \View::make('index')->with('baseURL', $baseURL);
		}

		$tripItinerary = Trip::getTrip($itineraryKey);			

		$pitStops = ($tripItinerary[0]->trip_object);
		$pitStops = json_decode($pitStops);		

		for ($i=0; $i < sizeof($pitStops); $i++) { 

			$pitStopPlaceObject = null;

			if(isset($pitStops[$i]->places)){

				for ($j=0; $j < sizeof($pitStops[$i]->places); $j++) { 				
				
					$place = new Place();
					$place->setLat($pitStops[$i]->places[$j]->lat);
					$place->setLng($pitStops[$i]->places[$j]->lng);
					$place->setName($pitStops[$i]->places[$j]->name);
					$place->setPlaceId($pitStops[$i]->places[$j]->placeId);
					$place->setPriceLevel($pitStops[$i]->places[$j]->priceLevel);
					$place->setRating($pitStops[$i]->places[$j]->rating);
					$place->setOpenNow($pitStops[$i]->places[$j]->open_now);
					$place->setVicinity($pitStops[$i]->places[$j]->vicinity);
					$place->setTypes($pitStops[$i]->places[$j]->types);

					$pitStopPlaceObject[$j] = $place;
				}
			}
			else{
				$pitStopPlaceObject = null;
			}
			
			
			$pitStop = new Pitstop();
			$pitStop->setCity($pitStops[$i]->city);
			$pitStop->setFormattedAddress($pitStops[$i]->formatted_address);
			$pitStop->setLat($pitStops[$i]->lat);
			$pitStop->setLng($pitStops[$i]->lng);
			$pitStop->setPlaceId($pitStops[$i]->placeId);
			$pitStop->setPlaces($pitStopPlaceObject); 	
			$finalpitStopObject[$i] = $pitStop;	

			/*$pitStopObject[$i] = [
				'city' => $pitStops[$i]->city,
				'formatted_address' => $pitStops[$i]->formatted_address,
				'lat' => $pitStops[$i]->lat,
				'lng' => $pitStops[$i]->lng,
				'placeId' => $pitStops[$i]->placeId,
				'id' => $tripId,
				'key' => $itineraryKey,				      
				'places' => $places
			];*/		
		}			

		$pitStops = $finalpitStopObject;

		/*$pitStops = PitstopAPI::jsonPitstop($pitStops);*/
		/*$pitStops = str_replace("'", "", json_encode($pitStops));*/

		$baseURL = '../public/';	    		    
		return \View::make('viewtrip')->with('pitStops', $pitStops)->with('tripId', $tripId)->with('itineraryKey', $itineraryKey)->with('baseURL', $baseURL);		
	}
}
