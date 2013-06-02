<?php
header( 'Access-Control-Allow-Origin: *', true );
header( 'Content-Type: application/json; charset=UTF-8', true );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', 0 ) . ' GMT', true );
header( 'Cache-Control: public, max-age=0', true );
header( 'Pragma: public', true );

$dbconn = pg_connect( 'host=141.114.192.128 port=5432 dbname=matador user=will password=will' );

$q = 'SELECT * from telemetries';
if( !empty( $_GET[ 'lastid' ] ) )
	$q .= ' WHERE _id > ' . $_GET[ 'lastid' ];
$q .= ' ORDER BY time ASC';

$result = pg_fetch_all( pg_query( $dbconn, $q ) );

$array = array();
if( !empty( $result ) ) { 
	foreach( $result as $r ) {
		$array[] = array(
			'truckid' => $r[ 'vehicle_id' ],
			'lat' => $r[ 'lat' ],
			'lon' => $r[ 'lon' ],
			'lastid' => $r[ '_id' ],
		);
	}
}

if( empty( $_GET[ 'jsonp' ] ) ) {
	echo json_encode( $output );
} else {
	echo $_GET[ 'jsonp' ] . '( ' . json_encode( $array ) . ' )';
}
