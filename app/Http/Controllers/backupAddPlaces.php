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
		
		$baseURL = '../public/';			
		return \View::make('index')->with('pitStops', $pitStops)->with('itineraryKey', $itineraryKey)->with('tripId', $tripId)->with('errorMessage', '')->with('baseURL', $baseURL);
		/*return \View::make('index')->with('pitStops', $pitStops)->with('baseURL', $baseURL);*/
	}