<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model {	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'trips';	

	public static function checkKey($itineraryKey){		
		return \DB::table('trips')		        		        
		        ->where('trip_key', '=', $itineraryKey)
		        ->select('trip_key')	        
		        ->get();
	}

	public static function getTripKey($tripId){
		return \DB::table('trips')		        		        
		        ->where('trip_id', '=', $tripId)
		        ->select('trip_key')	        
		        ->get();
	}

	public static function saveTrip($itineraryKey, $pitstops){
		return \DB::table('trips')				
				->insertGetId(['trip_key' => $itineraryKey, 'trip_object' => $pitstops]);
	}

	public static function getTrip($itineraryKey){
		return \DB::table('trips')		        		        
		        ->where('trip_key', '=', $itineraryKey)
		        ->select('trips.*')	        
		        ->get();
	}

	public static function getTripbyId($tripId){
		return \DB::table('trips')		        		        
		        ->where('trip_id', '=', $tripId)
		        ->select('trips.*')	        
		        ->get();
	}

	public static function getAllTrips(){
		return \DB::table('trips')
		        ->select('trips.*')
		        ->get();
	}	

	public static function updateTrip($tripId, $pitstops){
		return \DB::table('trips')
				->where('trip_id', '=', $tripId)
				->update(['trip_object' => $pitstops]);
	}

	public static function deleteTrip($tripId){
		return \DB::table('trips')
				->where('trip_id', '=', $tripId)
				->delete();
	}
}
