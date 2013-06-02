<?php
pg_connect( 'host=141.114.192.128 port=5432 dbname=matador user=will password=will' );

$postdata = file_get_contents("php://input");

$data = json_decode($postdata);

print_r($data);


