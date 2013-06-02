<?php error_reporting(E_ALL); 
ini_set('display_errors',"On");
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Truck tracker</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
        font-weight: 300;
      }

      ul, li, dd{
        margin: 0;
        padding: 0;
      }

      #map-canvas{
        height: 100%;
        width: 100%;
        float: left;
      }

      .glass{
        filter: alpha(opacity=92);
        -moz-opacity: 0.92;
        -khtml-opacity: 0.92;
        opacity: 0.92;
      }

      #info-panel-border{
        border: 1px solid white;
        right: 3%;
        float: right;
        /*height: 374px;*/
        height: auto;
        width: 300px;
        margin: 5% 0 0 0;
        position: absolute;
      }

      #info-panel-border.hidden {
        -webkit-transform: translate(300px,0px);
      }
      
      #info-panel{
        background-color: white;
        border: 2px dotted #E9E9E9;
        /*height: 370px;*/
        height: auto;
      }

      .panel:after {
          width: 260;
          clear: both;
          display:inline;
      }

      .panel{
        width: 286px;
        height: 30px;
        padding-left: 10px;
        background-color: #E9E9E9;
        list-style-type: none;

        /* Internet Explorer 10 */
        display:-ms-flexbox;
        -ms-flex-align:center;

        /* Firefox */
        display:-moz-box;
        -moz-box-align:center;

        /* Safari, Opera, and Chrome */
        display:-webkit-box;
        -webkit-box-align:center;

        /* W3C */
        display:box;
        box-align:center;
      }
      .panel-content li{
        list-style-type: none;
        padding-left: 20px;
        background-color: #E9E9E9;
      }

      .accordion{
        margin: 0px;

      }

      .accordion a{
        display: block;
        color: black;
        font-weight: bold;
        text-decoration: none;
      }

    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=geometry"></script>
    <script>
 
 function get_random_color() {
     var letters = '0123456789ABCDEF'.split('');
     var color = '#';
     for (var i = 0; i < 6; i++) {
         color += letters[Math.round(Math.random() * 15)];
     }
     return color;
 }

 function vehiclePopup(vehicle) {
     var vehicleId = vehicle.id;
     var popupWindow = new google.maps.InfoWindow();
     jQuery.getJSON("http://141.114.192.128/ajax.php", {
             vehicleid: vehicleId
         })
         .done(function (data) {
             if (data == null) return;
             posish = new google.maps.LatLng(data[0].lat, data[0].lon);
             var place = latLongAddress(posish, vehicleId);
             var info = "ID: " + vehicleId +
                 "\nCurrent Approximate Location: " + place +
                 "\nSalt Rate: " +
                 "\nSand Rate: ";
             popupWindow.setContent(info);
             popupWindow.open(map, markers[vehicleId - 1]);
             window.map.setCenter(posish);
         });
 }

 function getTruck(truck) {
     var value = truck.value;
     var id = truck.id;
     alert(id);
 }

 var geocoder = null;
 var infowindow = new google.maps.InfoWindow();
 //var marker = null;
 function initialize() {
  geocoder = new google.maps.Geocoder();
  var mapOptions = {
    zoom: 12,
    center: new google.maps.LatLng(44.801207, -68.777817),
    disableDefaultUI: true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  window.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  markers.push = new google.maps.Marker({
    /*icon: new google.maps.MarkerImage('noun_project_4247.svg',
      null, null, null, new google.maps.Size(64,64)),*/
    map: map
  });
 }
 function in_circle(center_x, center_y, radius, x, y){
  square_dist = (center_x - x) ^ 2 + (center_y - y) ^ 2
  return (square_dist <= (radius ^ 2))
 }

 console.log(in_circle(44.801207,-68.777817,0.000100,0,0));

 function latLongAddress(latLng, id) {
     geocoder.geocode({
             'latLng': latLng
         }, function (results, status) {
             if (status == google.maps.GeocoderStatus.OK) {
                 if (results[0]) {
                      //map.setZoom(11);
                   markers[id].setPosition(latLng);
                     infowindow.setContent(results[0].formatted_address);
                     infowindow.open(map, markers[id]);
                     return results[0].formatted_address;
                 }
             } else {
                 alert("GeoCoder failed due to: " + status);
             }
         });
 }
 window.lastid = 0;

 var markers = new Array();
 var flightpaths = new Array();
 var flightpathcoords = new Array();

 function map_points() {
     jQuery.getJSON('http://141.114.192.128/ajax.php?lastid=' + window.lastid + '&jsonp=?', function (data) {
       if (data) {
         var PreviousLatLong = null;
                 jQuery.each(data, function (i, d) {
                   if (markers[d.truckid]) {
                     var ItemLatLong = new google.maps.LatLng(d.lat, d.lon);
                     if(PreviousLatLong !== null && google.maps.geometry.spherical.computeDistanceBetween(PreviousLatLong,ItemLatLong) < 15) return true;
                     PreviousLatLong = ItemLatLong;
                             markers[d.truckid].setPosition(ItemLatLong);
                             flightpathcoords[d.truckid].push(ItemLatLong);
                             flightpaths[d.truckid].setPath(flightpathcoords[d.truckid]);
                             flightpaths[d.truckid].setMap(window.map);
                   } else {
                     var ItemLatLong = new google.maps.LatLng(d.lat, d.lon);
                     if(PreviousLatLong !== null && google.maps.geometry.spherical.computeDistanceBetween(PreviousLatLong,ItemLatLong) < 15) return true;
                     PreviousLatLong = ItemLatLong;
                             markers[d.truckid] = new google.maps.Marker({
                                     position: ItemLatLong,
                                       title: "Snow Plow",
                                       
    /*icon: new google.maps.MarkerImage('noun_project_4247.svg',
    null, null, null, new google.maps.Size(64,64)),*/
                                 });
                             flightpathcoords[d.truckid] = new Array();
                             flightpathcoords[d.truckid].push(ItemLatLong);
                             flightpaths[d.truckid] = new google.maps.Polyline({
                                     path: flightpathcoords[d.truckid],
                                     strokeColor: get_random_color(),
                                     strokeOpacity: 1.0,
                                     strokeWeight: 2
                                 });
                             flightpaths[d.truckid].setMap(window.map);
                             markers[d.truckid].setMap(window.map);
                         }
                         window.lastid = d.lastid;
                     });
             }
         });


     function popToolbar(typeArray) {

     }



     setTimeout(function () {
             map_points();
         }, 3000);
 }
 //map_points();


 window.onload = function(){
   //while(google.maps.geometry){
    initialize();
    map_points();
  }
 
    </script>

    <script type="text/javascript">
      $(document).ready(function(){
        var allPanels = $('.accordion > dd').hide();

        $('.accordion > dt > a').click(function() {
          allPanels.slideUp();
          
          if($(this).parent().next().is(':hidden'))
          {
              $(this).parent().next().slideDown();
          }
          
          return false;
        });
        jQuery('#info-panel').click(function(){
          jQuery("#info-panel-border").toggleClass("hidden");
        });
      });
    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
    <div id="info-panel-border" style="display: none;" class="glass">
      <div id="info-panel" class="glass">
        <dl class="accordion">
<?php 
$dbconn = pg_connect( 'host=141.114.192.128 port=5432 dbname=matador user=will password=will' );
$q = "SELECT type FROM vehicles GROUP BY type";
$result = pg_fetch_all( pg_query( $dbconn, $q ) );
foreach($result as $r){
?>
    
    <dt class="panel"><input onclick="checkEvent();" type='checkbox' class="<?php echo $r['type']; ?>" id="<?php echo $r['type'] . $i; ?>"><a href=""><?php echo $r['type']; ?></a></dt>
            <dd>
              <ul class = "panel-content">
<?php
  $i=1;
  $q = "SELECT * FROM vehicles WHERE type='". $r['type'] ."';";
  $vehiclesResult = pg_fetch_all(pg_query($q));
  foreach($vehiclesResult as $vehicle){
                echo '<li id='.$i.' onclick=\'vehiclePopup(this)\'> ' . $r['type'] .$i.'</li>';
               $i++;
  }
              ?>
            </ul>
          </dd>
<?php } ?>
        </dl>
      </div>
    </div>
  <script>
    function checkEvent(){
      if(document.getElementByClassName(this.class).checked){
        uncheck(this);
      } else {
        check(this);
      }
    }
    function check(item)
    {

    }
    function uncheck(item)
    {
      for(var i = 0; i<markers.length; i++) {
       //if(markers[i] = 
      console.log(markers);
      }
    }
  </script>
  </body>
</html>
