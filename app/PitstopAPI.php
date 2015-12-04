<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\Pitstop;

class PitstopAPI extends Model {	

	/**
	 * The Google API used by the model.
	 *
	 * @var string
	 */
	public static function placesNearby($latlng, $placetype)
	{
		$placesNearbyAPI="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=" . $latlng . "&radius=50000&types=" . $placetype . "&key=AIzaSyA6eYoKwKS4On8M1AkxhkBFYZUn08eDSFI";

		$raw=@file_get_contents($placesNearbyAPI);
		$placesNearby=json_decode($raw);

		return $placesNearby;
	}

	public static function addPlacestoPitstop($pitStops, $placesNearby)
	{
		for ($i=0; $i < sizeof($pitStops); $i++) { 	

			$pitStop = new Pitstop();
			$pitStop->setCity($pitStops[$i]->city);
			$pitStop->setFormattedAddress($pitStops[$i]->formatted_address);
			$pitStop->setLat($pitStops[$i]->lat);
			$pitStop->setLng($pitStops[$i]->lng);
			$pitStop->setPlaceId($pitStops[$i]->placeId);
			$pitStop->setPlaces(null); 	

			$pitStopObject[$i] = $pitStop;		    	
		}
		    
		$pitStopObject['nearbyPlaces'] = [
		    'nearbyPlaces' => $placesNearby->results
		];
		    
		$pitStops = $pitStopObject;		    		    
		return $pitStops;
	}

	public static function getRoutes($tripStart, $tripEnd)
	{
		$getRoute="https://maps.googleapis.com/maps/api/directions/json?origin=" . $tripStart . "&destination=" . $tripEnd . "&key=AIzaSyA6eYoKwKS4On8M1AkxhkBFYZUn08eDSFI";

		$raw=@file_get_contents($getRoute);
		$tripRoute=json_decode($raw);

		return $tripRoute;
	}

	public static function getWayPoints($intermediatePoints, $travelLegs, $tripRoute)
	{				
		$response = $tripRoute;

		for($i = 0; $i < sizeof($intermediatePoints); $i++){			
			$indexPoint = array_search($intermediatePoints[$i], $travelLegs);	
			if (isset($response->routes[0]->legs[0]->steps[$indexPoint + 1]->start_location)) {				    

			    $pitStop = new Pitstop();
				$pitStop->setCity(null);
				$pitStop->setFormattedAddress(null);
				$pitStop->setLat($response->routes[0]->legs[0]->steps[$indexPoint + 1]->start_location->lat);
				$pitStop->setLng($response->routes[0]->legs[0]->steps[$indexPoint + 1]->start_location->lng);
				$pitStop->setPlaceId(null);
				$pitStop->setPlaces(null);				

			    $pitStopObject[$i] = $pitStop; 			    		     
			}    
		}

		$pitStops = $pitStopObject;
		return $pitStops;
	}

	public static function getPlaceDetails($lat, $lng){
		$geocoding = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng ."&key=AIzaSyA6eYoKwKS4On8M1AkxhkBFYZUn08eDSFI";

		$raw=@file_get_contents($geocoding);
		$geocode_json_data=json_decode($raw);

		return $geocode_json_data;
	}

	public static function jsonPitstop($pitStops){		

		$pitStopObject = [];
		for ($i=0; $i < sizeof($pitStops); $i++) { 

			$placeObject = [];
			if(($pitStops[$i]->getPlaces()) != null){

				for ($j=0; $j < sizeof($pitStops[$i]->getPlaces()); $j++) { 					
					$placeObject[$j] = [
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
			}			

			$places = $placeObject;

			$pitStopObject[$i] = [
				'city' => $pitStops[$i]->getCity(),
				'formatted_address' => $pitStops[$i]->getFormattedAddress(),
				'lat' => $pitStops[$i]->getLat(),
				'lng' => $pitStops[$i]->getLng(),
				'placeId' => $pitStops[$i]->getPlaceId(),				      
				'places' => $places
			];
		}

		return json_encode($pitStopObject);
	}

}
