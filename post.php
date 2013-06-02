<?php
pg_connect( 'host=141.114.192.128 port=5432 dbname=matador user=will password=will' );

$postdata = file_get_contents("php://input");

$data = json_decode($postdata);

$q = "INSERT INTO telemetries (vehicle_id,lat,lon,time) VALUES ('". pg_escape_string($data->Truck) . "','". pg_escape_string($data->Lat) ."','". pg_escape_string($data->Lon) ."','". pg_escape_string($data->Time) ."')";

pg_query($q) or die(pg_last_error());
