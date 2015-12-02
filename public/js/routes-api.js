var totalDistance = 0, totalDuration = 0; 
var startLat, startLong, endLat, endLong;
var totalMilesDistance, totalHourDistance;
var travelLegs = [];
var intermediatePoints = [];
var pitStops = new Array();
var allPitStopsPlaceID = new Array();
var allPitStops = new Array();
var totalBreaks = 0;
var map, pyrmont;
var asyncgeocodeLatLng, asyncpitshopPlaceDetails;

function init() 
{

    pyrmont = new google.maps.LatLng(40.714224,-73.961452);

    map = new google.maps.Map(document.getElementById('map'), {
      center: pyrmont,
      zoom: 15,
      scrollwheel: false
    });        

    calcRoute();
}

google.maps.event.addDomListener(window, 'load', init);

var directionsService = new google.maps.DirectionsService();


function calcRoute() 
{  
  /*var start = document.getElementById("startPoint").value;
  var end = document.getElementById("endPoint").value;*/

  var start = "Chicago, IL, United States";
  var end = "Las Vegas, NV, United States";
  
  var request = {
    origin:start,
    destination:end,   
    travelMode: google.maps.DirectionsTravelMode.DRIVING
  };
  
  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {     

      totalDistance = response.routes[0].legs[0].distance.value;
      totalMilesDistance = totalDistance*0.000621371192;
      
      totalDuration = response.routes[0].legs[0].duration.value;
      totalHourDistance = moment().startOf('day').seconds(totalDuration).format('H:mm');

      travelLegs[0] = response.routes[0].legs[0].steps[0].distance.value;
      for (var i = 1; i < response.routes[0].legs[0].steps.length; i++) {
        travelLegs[i] = travelLegs[i-1] + response.routes[0].legs[0].steps[i].distance.value
      }     
      
      /* Get starting point Place Id */
      allPitStopsPlaceID.push(response.geocoded_waypoints[0].place_id);

      /* Per day travel time = 8 Hrs = 28800 Seconds. */
      /* Per day travel time = 480 Miles = 772485 Meters. */
      perDayTravel(28800, 772485);      

      /* Once the intermediate points are generated, we get the wayPoint locations in Pitstops. */
      wayPointLocations(response);  

      /* Once the wayPoint Locations are fetched, we get the place ids of all Pitstops. */
      getPitstopPlaceId(response, map);           

      $(function () {

        var myChecker = setInterval(function () {
          if (asyncpitshopPlaceDetails == 0) {            
            
            /*console.log(allPitStops);*/
            clearInterval(myChecker);
                              
          } 
        }, 500);
      });     

      /* Once the intermediate locations are fetched with latitude and longitude, places api will be called. */      
      placesAPI(map);
    }
    else{
      /* Display Error : Direction service not responding. */
      console.log("Direction Service Error");
    }
  });
}

function perDayTravel(travelTime, travelDistance)
{  
  if(typeof travelDistance === 'undefined')
  {
    totalBreaks = Math.floor(totalDuration / travelTime);
    wayPointsWithTime(totalBreaks);
  }
  else{
    totalBreaks = Math.floor(totalDistance / travelDistance);
    wayPointsWithDistance(totalBreaks);
  }  
}

function wayPointsWithDistance(totalBreaks)
{  
  var distancePerDay = totalDistance / totalBreaks;

  for (var i = 0; i < totalBreaks; i++) {
    var wayPoint = closest(travelLegs, distancePerDay*(i + 1));
    var indexPoint = travelLegs.indexOf(wayPoint);
    
    if((travelLegs[indexPoint] - distancePerDay*(i + 1)) > (travelLegs[indexPoint - 1] - distancePerDay*(i + 1))){
      intermediatePoints[i] = travelLegs[indexPoint - 1];
    }
    else{
      intermediatePoints[i] = travelLegs[indexPoint];
    }   
  }  
}

function closest(arr, closestTo)
{
    var closest = Math.max.apply(null, arr); 
   
    for(var i = 0; i < arr.length; i++){ 
        if(arr[i] >= closestTo && arr[i] < closest) closest = arr[i]; 
    }

  return closest; 
}

function wayPointLocations(response)
{    
  var getPolyline = [];      

  for(var i = 0; i < intermediatePoints.length; i++){
    var indexPoint = travelLegs.indexOf(intermediatePoints[i]);
    if (!(typeof response.routes[0].legs[0].steps[indexPoint + 1] === "undefined")) {
      getPolyline[i] = polyline.decode(response.routes[0].legs[0].steps[indexPoint + 1].encoded_lat_lngs);
    }    
  }  

  for(var i = 0; i < getPolyline.length; i++){    
    pitStops[i+1] = {
      lat:getPolyline[i][0][0],
      lng:getPolyline[i][0][1]     
    }      
  }   
}


function getPitstopPlaceId(response, map) {
  var geocoder = new google.maps.Geocoder;
  var infowindow = new google.maps.InfoWindow;  
  asyncgeocodeLatLng = pitStops.length;

  for (var i = 1; i < pitStops.length; i++) {
    var latlng = pitStops[i].lat + ',' + pitStops[i].lng;
    var input = latlng;
    var latlngStr = input.split(',', 2);
    var latlng = {lat: parseFloat(latlngStr[0]), lng: parseFloat(latlngStr[1])};
    geocoder.geocode({'location': latlng}, function(results, status) {
      if (status === google.maps.GeocoderStatus.OK) {
        allPitStopsPlaceID.push(results[1].place_id);
        
        asyncgeocodeLatLng--;  
        if(asyncgeocodeLatLng == 1){
          /* Perform Next Steps */
          allPitStopsPlaceID.push(response.geocoded_waypoints[1].place_id);          

          /* Getting place details */
          getPitshopPlaceDetails(response, map);
        } 
      } 
    });    
  }    
}

function getPitshopPlaceDetails(response, map){
  var infowindow = new google.maps.InfoWindow();
  var service = new google.maps.places.PlacesService(map);
  asyncpitshopPlaceDetails = allPitStopsPlaceID.length;

  for (var i = 0; i < allPitStopsPlaceID.length; i++) {
    service.getDetails({
      placeId: allPitStopsPlaceID[i]
      }, function(place, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
          allPitStops.push(place);
          asyncpitshopPlaceDetails--;          
        }
      });
  }
}

function placesAPI(map){    

  var request = {
    location: pyrmont,
    radius: '500',
    types: ['cafe']
  };

  var placesService = new google.maps.places.PlacesService(map);
 
  placesService.nearbySearch(request, function(results, status) {
    if (status == google.maps.places.PlacesServiceStatus.OK) {      
      for (var i = 0; i < results.length; i++) {        
        var place = results[i]; 
        
      }
    }
  });
}