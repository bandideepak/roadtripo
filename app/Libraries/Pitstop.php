<?php 

namespace App\Libraries;


class Pitstop {	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	private $city;
	private $formatted_address;
	private $lat;
	private $lng;
	private $placeId;
	private $places;

	/**
	 * Pitstop constructor.	
	 */
	public function __construct()
	{
		
	}

	/**
	 * @return string
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @param string $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}

	/**
	 * @return mixed
	 */
	public function getFormattedAddress()
	{
		return $this->formatted_address;
	}

	/**
	 * @param mixed $formatted_address
	 */
	public function setFormattedAddress($formatted_address)
	{
		$this->formatted_address = $formatted_address;
	}

	/**
	 * @return mixed
	 */
	public function getLat()
	{
		return $this->lat;
	}

	/**
	 * @param mixed $lat
	 */
	public function setLat($lat)
	{
		$this->lat = $lat;
	}

	/**
	 * @return mixed
	 */
	public function getLng()
	{
		return $this->lng;
	}

	/**
	 * @param mixed $lng
	 */
	public function setLng($lng)
	{
		$this->lng = $lng;
	}

	/**
	 * @return mixed
	 */
	public function getPlaceId()
	{
		return $this->placeId;
	}

	/**
	 * @param mixed $placeId
	 */
	public function setPlaceId($placeId)
	{
		$this->placeId = $placeId;
	}

	/**
	 * @return mixed
	 */
	public function getPlaces()
	{
		return $this->places;
	}

	/**
	 * @param mixed $places
	 */
	public function setPlaces($places)
	{
		$this->places = $places;
	}



	

}
