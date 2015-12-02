<?php 

namespace App\Http\Controllers;

use App\Routes;
use App\Trip;
use Hash;
use App\Libraries\Pitstop;

class RestController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Rest Controller
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

        $baseURL = '../public/';
		return \View::make('index')->with('pitStops', $resultPitStops)->with('baseURL', $baseURL);
	}

	public function planTripLocations($tripStart, $tripEnd){
		
		$plantrip="https://maps.googleapis.com/maps/api/directions/json?origin=" . $tripStart . "&destination=" . $tripEnd . "&key=AIzaSyA6eYoKwKS4On8M1AkxhkBFYZUn08eDSFI";
		        
		$raw=@file_get_contents($plantrip);
		$json_data=json_decode($raw);
		        
		if ($json_data->status=="OK") {	

			$response = $json_data;	

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
			$getPolyline = [];      

			for($i = 0; $i < sizeof($intermediatePoints); $i++){
			    $indexPoint = array_search($intermediatePoints[$i], $travelLegs);	
			    if (isset($response->routes[0]->legs[0]->steps[$indexPoint + 1]->start_location)) {				    	

			    	$pitStops[$i] = [
			    	  'city' => null,
			    	  'formatted_address' => null,
				      'lat' => $response->routes[0]->legs[0]->steps[$indexPoint + 1]->start_location->lat,
				      'lng' => $response->routes[0]->legs[0]->steps[$indexPoint + 1]->start_location->lng,
				      'placeId' => null,
				      'places' => null			 
				    ];				   			      
			    }    
			}  			

			/* Once the wayPoint Locations are fetched, we get the place ids of all Pitstops. */
			for ($i=0; $i < sizeof($pitStops); $i++) { 
				
				$geocoding = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $pitStops[$i]['lat'] . "," . $pitStops[$i]['lng'] ."&key=AIzaSyA6eYoKwKS4On8M1AkxhkBFYZUn08eDSFI";

				$raw=@file_get_contents($geocoding);
				$geocode_json_data=json_decode($raw);
				        
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

					$pitStops[$i] = [
					  'city' => $city,
					  'formatted_address' => $geocoderesponse->results[0]->formatted_address,
				      'lat' => $pitStops[$i]['lat'],
				      'lng' => $pitStops[$i]['lng'],
				      'placeId' => $geocoderesponse->results[0]->place_id,
				      'places' => null
				    ];
				}
				else {
			          //if no result found, status would be ZERO_RESULTS
			          return $geocode_json_data->status;  
				}				
			}	
			
			$pitStops = str_replace("'", "", json_encode($pitStops));
			return $pitStops;		

		} else {
		    //if no result found, status would be ZERO_RESULTS
		    return $json_data->status;  
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
				
		$placesNearby="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=" . $latlng . "&radius=50000&types=" . $placetype . "&key=AIzaSyA6eYoKwKS4On8M1AkxhkBFYZUn08eDSFI";
		        
		$raw=@file_get_contents($placesNearby);
		$json_data=json_decode($raw);			

		if ($json_data->status=="OK") {		          
		    
		    for ($i=0; $i < sizeof($pitStops); $i++) { 		    	

		    	$pitStopObject[$i] = [
					  'city' => $pitStops[$i]->city,
					  'formatted_address' => $pitStops[$i]->formatted_address,
				      'lat' => $pitStops[$i]->lat,
				      'lng' => $pitStops[$i]->lng,
				      'placeId' => $pitStops[$i]->placeId,				      
				      'places' => null
				    ];
		    }
		    
		    $pitStopObject['nearbyPlaces'] = [
		    	'nearbyPlaces' => $json_data->results
		    ];
		    
		    $pitStops = $pitStopObject;		    		    
		    return $pitStops;		   

		} else {
		          //if no result found, status would be ZERO_RESULTS
		          return $json_data->status;  
		}

		return null;
	}

	public function placesNearbyCity($latlng, $placetype){

		$placesNearby="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=" . $latlng . "&radius=50000&types=" . $placetype . "&key=AIzaSyA6eYoKwKS4On8M1AkxhkBFYZUn08eDSFI";
	
		$raw=@file_get_contents($placesNearby);
		$json_data=json_decode($raw);

		$nearbyPlaces = $json_data->results;
		return json_encode($nearbyPlaces);		
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

		/* Check the key */		
		if($userKey == ''){
			$errorMessage = 'Key Required';
			return \View::make('viewtrip')->with('pitStops', $pitStops)->with('errorMessage', '')->with('baseURL', $baseURL);		
		} 
		else if (Hash::check($userKey, $itineraryKey))
		{
		    $errorMessage = '';
		    return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('baseURL', $baseURL);
		}	
		else{			
			$errorMessage = 'Invalid Key';
			return \View::make('viewtrip')->with('pitStops', $pitStops)->with('errorMessage', '')->with('baseURL', $baseURL);		
		}
		
		return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('baseURL', $baseURL);	    		    
		
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

			if($pitStops[$i]->placeId == $pitstopId){
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
				
				$newPlaceObject = [
					'lat' => $placeDetails->geometry->location->lat,
					'lng' => $placeDetails->geometry->location->lng,
					'name' => $placeDetails->name,
					'placeId' => $placeDetails->place_id,
					'priceLevel' => $price_level,
                    'rating' => $rating,
                    'open_now' => $open_now,
                    'vicinity' => $vicinity,
                    'types' => $placeDetails->types[0]
                    
				];
				
				if(isset($places)){
					$totalPlaces = sizeof($places);
				}
				else{
					$totalPlaces = 0;
				}
				
				if($totalPlaces > 0){
					$placeObject = $pitStops[$i]->places;
					$placeObject[$totalPlaces] = $newPlaceObject;
				}
				else{
					$placeObject[0] = $newPlaceObject;
				}
												
				$places = $placeObject;
			}
			else{

				$places = $pitStops[$i]->places;						
			}

		    $pitStopObject[$i] = [
				'city' => $pitStops[$i]->city,
				'formatted_address' => $pitStops[$i]->formatted_address,
				'lat' => $pitStops[$i]->lat,
				'lng' => $pitStops[$i]->lng,
				'placeId' => $pitStops[$i]->placeId,				      
				'places' => $places
			];
		}	

		$pitStops = $pitStopObject;
		$pitStops = str_replace("'", "", json_encode($pitStops));

		$itineraryKey = null;
		$tripId = null;

		if(isset($_POST['itineraryKey']) && isset($_POST['tripId'])){			
			$itineraryKey = $_POST['itineraryKey'];	
			$tripId = $_POST['tripId'];				
			Trip::updateTrip($tripId, $pitStops);								
		}			
		
		$baseURL = '../public/';			
		return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('errorMessage', '')->with('baseURL', $baseURL);
		/*return \View::make('index')->with('pitStops', $pitStops)->with('baseURL', $baseURL);*/
	}
	
	public function checkKey($itineraryKey){
		if(isset($_GET['itineraryKey'])){
			$itineraryKey = $_GET['itineraryKey'];				
		}

		$itineraryKey = Hash::make($itineraryKey);
		$checkItineraryKey = Trip::checkKey($itineraryKey);
		
		if($checkItineraryKey){
			return 0;
		}
		return "Valid Key";
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

	public function apiviewTrip($itineraryKey, $tripId){

		$tripKey = 	Trip::getTripKey($tripId);

		if(empty($tripKey)){
			return json_encode("Wrong Trip Id");
		}

		if (Hash::check($itineraryKey, $tripKey[0]->trip_key))
		{
		    $itineraryKey = $tripKey[0]->trip_key;
		}	
		else{
			return json_encode("Key did not match");
		}		

		$tripItinerary = Trip::getTrip($itineraryKey);			

		$pitStops = ($tripItinerary[0]->trip_object);
		$pitStops = json_decode($pitStops);
				
		for ($i=0; $i < sizeof($pitStops); $i++) { 		    	
			
			$places = $pitStops[$i]->places;

			if($pitStops[$i]->places == ''){
				$places = null;
			}							
		
		    $pitStopObject[$i] = [
				'city' => $pitStops[$i]->city,
				'formatted_address' => $pitStops[$i]->formatted_address,
				'lat' => $pitStops[$i]->lat,
				'lng' => $pitStops[$i]->lng,
				'placeId' => $pitStops[$i]->placeId,				      
				'places' => $places
			];
		}

		$pitStops = $pitStopObject;
		$pitStops = str_replace("'", "", json_encode($pitStops));

		return $pitStops;

		/*$baseURL = '../../../public/';	    		    
		return \View::make('viewtrip')->with('pitStops', $pitStops)->with('trip', 'trip')->with('baseURL', $baseURL);*/		
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

		var_dump($pitStops);

		for ($i=0; $i < sizeof($pitStops); $i++) { 		    	
			
			if(isset($pitStops[$i]->places)){
				$places = $pitStops[$i]->places;
			}
			else{
				$places = null;
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
		return \View::make('viewtrip')->with('pitStops', $pitStops)->with('baseURL', $baseURL);		
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

		return $trips;

		/*$baseURL = '../public/';	    		    
		return \View::make('trips')->with('trips', $trips)->with('baseURL', $baseURL);*/
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
			$baseURL = '../public/';	
			return \View::make('trips')->with('baseURL', $baseURL);
		}				

		$pitStops = ($tripItinerary[0]->trip_object);
		$pitStops = json_decode($pitStops);

		for ($i=0; $i < sizeof($pitStops); $i++) { 		    	
			
			$places = $pitStops[$i]->places;

			if($pitStops[$i]->places == ''){
				$places = null;
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
			$updateTrip = Trip::updateTrip($tripId, $pitStops);	
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
			$updateTrip = Trip::updateTrip($tripId, $pitStops);	

		}		

		return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('errorMessage', '')->with('baseURL', $baseURL);		
		
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

	public function tripById($tripId){
		
		$tripDetails = Trip::getTripbyId($tripId);
		$tripInfo = $tripDetails[0]->trip_object;
		return json_encode(json_decode($tripInfo));

	}
}
