<?php 
public function removePitstop(){		

		if(isset($_POST['itineraryKey']) && isset($_POST['tripId']) && isset($_POST['placeId'])  && isset($_POST['userKey'])){
			$itineraryKey = $_POST['itineraryKey'];	
			$tripId = $_POST['tripId'];
			$placeId = $_POST['placeId'];
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

		/* Check the key */		
		if($userKey == ''){
			return \View::make('viewtrip')->with('pitStops', $pitStops)->with('errorMessage', 'Key Required')->with('baseURL', $baseURL);		
		} 
		else if (Hash::check($userKey, $itineraryKey))
		{
		    
		}	
		else{			
			return \View::make('viewtrip')->with('pitStops', $pitStops)->with('errorMessage', 'Invalid Key')->with('baseURL', $baseURL);
		}		

		/* If key is Valid */
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

		return \View::make('viewtrip')->with('pitStops', $pitStops)->with('errorMessage', '')->with('baseURL', $baseURL);		
		
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