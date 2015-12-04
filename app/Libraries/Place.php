<?php 

namespace App\Libraries;


class Place {	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */	
	private $lat;
	private $lng;
	private $name;
	private $placeId;
	private $priceLevel;
	private $rating;
	private $open_now;
	private $vicinity;
	private $types;

	/**
	 * Pitstop constructor.	
	 */
	public function __construct()
	{
		
	}
	
	/**
	 * @return string
	 */
	public function getLat()
	{
		return $this->lat;
	}

	/**
	 * @param string $lat
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
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getOpenNow()
	{
		return $this->open_now;
	}

	/**
	 * @param mixed $open_now
	 */
	public function setOpenNow($open_now)
	{
		$this->open_now = $open_now;
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
	public function getPriceLevel()
	{
		return $this->priceLevel;
	}

	/**
	 * @param mixed $priceLevel
	 */
	public function setPriceLevel($priceLevel)
	{
		$this->priceLevel = $priceLevel;
	}

	/**
	 * @return mixed
	 */
	public function getRating()
	{
		return $this->rating;
	}

	/**
	 * @param mixed $rating
	 */
	public function setRating($rating)
	{
		$this->rating = $rating;
	}

	/**
	 * @return mixed
	 */
	public function getTypes()
	{
		return $this->types;
	}

	/**
	 * @param mixed $types
	 */
	public function setTypes($types)
	{
		$this->types = $types;
	}

	/**
	 * @return mixed
	 */
	public function getVicinity()
	{
		return $this->vicinity;
	}

	/**
	 * @param mixed $vicinity
	 */
	public function setVicinity($vicinity)
	{
		$this->vicinity = $vicinity;
	}

}
