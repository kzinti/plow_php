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

      #info-panel{
        background-color: white;
        border: 2px dotted #E9E9E9;
        /*height: 370px;*/
        height: auto;
      }

      .panel{
        width: 296px;
        height: 50px;
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
        margin-left: 20%;
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
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>
 
function get_random_color() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.round(Math.random() * 15)];
    }
    return color;
}

function getTruck(truck){
          var value = truck.value;
          var id = truck.id;
          alert(id);
        }
 
function initialize() {
        var mapOptions = {
                zoom: 12,
                center: new google.maps.LatLng(44.801207, -68.777817),
                disableDefaultUI: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
        };
 
        window.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}
 
window.lastid = 0;
 
var markers = new Array();
var flightpaths = new Array();
var flightpathcoords = new Array();
function map_points() {
        jQuery.getJSON( 'http://141.114.192.128/ajax.php?lastid=' + window.lastid + '&jsonp=?', function( data ) {
                if( data ) {
                        jQuery.each( data, function( i, d ) {
                                if( markers[d.truckid] ) {
                                        markers[d.truckid].setPosition( new google.maps.LatLng( d.lat,d.lon ) );
                                        flightpathcoords[d.truckid].push( new google.maps.LatLng( d.lat,d.lon) );
                                        flightpaths[d.truckid].setPath( flightpathcoords[d.truckid] );
                                        flightpaths[d.truckid].setMap( window.map );
                                } else {
                                        markers[d.truckid] = new google.maps.Marker({
                                                position: new google.maps.LatLng( d.lat,d.lon),
                                                title:"Snow Plow",
                                        });
                                        flightpathcoords[d.truckid] = new Array();
                                        flightpathcoords[d.truckid].push( new google.maps.LatLng( d.lat,d.lon) );
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

        
        function popToolbar(typeArray){

        }
       
        

        setTimeout( function() { map_points(); }, 3000 );
}
map_points();
 
 
window.onload = initialize;
 
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

        

      });
    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
    <div id="info-panel-border" class="glass">
      <div id="info-panel" class="glass">
        <dl class="accordion">
          <dt class="panel"><a href="">Snowplows</a></dt>
          <dd>
            <ul class = "panel-content">
              <?php
                $dbconn = pg_connect( 'host=141.114.192.128 port=5432 dbname=matador user=will password=will' );
                $q = "SELECT *
                      FROM vehicles;";
                $result = pg_fetch_all( pg_query( $dbconn, $q ) );
                $i = 1;
                foreach($result as $r){
                  if ($r['type']==='plow') {
                    echo '<li id='.$i.'> Snowplow '.$i.'</li>';
                    $i = $i + 1;
                  }
                }

              ?>
            </ul>
          </dd>
          <dt class = "panel"><a href="">Sandsweepers</a></dt>
          <dd>
            <ul class = "panel-content">
          </ul>
          </dd>
          <dt class = "panel"><a href="">Garbage Trucks</a></dt>
          <dd>
            <ul class = "panel-content">
          </ul>
          </dd>
        </dl>
      </div>
    </div>
  </body>
</html>
