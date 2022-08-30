<?php 
  include "auxCacheFunctions.php";
  include "auxCotizacionesFunctions.php";
  header( "HTTP/1.1 200 OK" );
  header( 'Content-Type: application/json' );

  if( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    $baseUrl = 'https://api-dolar-argentina.herokuapp.com/api';
    $dollarPosiblesValues = array( "oficial", "blue", "liqui", "promedio", "turista" );
    $evolutionPosiblesValues = array( true, false );

    $dollar = isset($_GET['dolar']) && in_array($_GET['dolar'], $dollarPosiblesValues) ? $_GET['dolar'] : false;
    $evolution = in_array($_GET['evolucion'], $evolutionPosiblesValues) ? filter_var($_GET['evolucion'], FILTER_VALIDATE_BOOL) : false;
    if( !$dollar && $evolution ){
      return print 'The price history cannot be obtained if the official dollar is not selected';
    }

    $cacheTimeInSeconds = 30;
    $cacheFile = getCacheFileName( $dollar, $evolution );
    $cacheContent = getContentCache( $cacheFile, $cacheTimeInSeconds );
    if( $cacheContent ) {
      return print $cacheContent;
    }
    deleteAllCacheFilesIfExpiredTime( $cacheTimeInSeconds );

    $cotizacionesUrls = getCotizacionesUrl( $baseUrl, $dollar, $evolution );
    foreach( $cotizacionesUrls as $url ){
        $response[] = getDataAPI( $url );
    }
    writeResponseInFile( $cacheFile, json_encode($response) );

    return print json_encode( $response, false );
  } else {
      return print 'Invalid Request, please use GET method';
  }

  function getDataAPI( $url ) {
    $curl = curl_init();
    curl_setopt_array( $curl , array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));
    $response = curl_exec( $curl );
    $err = curl_error( $curl );
    curl_close( $curl );
    if ( $err ) {
        throw new Exception('Error consuming API: '.$err );
    }
    return json_decode( $response );
  }
