<?php 
  include "auxCacheFunctions.php";
  include "auxCotizacionesFunctions.php";
  header( "HTTP/1.1 200 OK" );
  header( 'Content-Type: application/json' );

  if( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    $baseUrl = 'https://api-dolar-argentina.herokuapp.com/api';
    $dolarPosiblesValues = array( "oficial", "blue", "liqui", "promedio", "turista" );
    $evolucionPosiblesValues = array( true, false );

    $dolar = isset($_GET['dolar']) && in_array($_GET['dolar'], $dolarPosiblesValues) ? $_GET['dolar'] : false;
    $evolucion = in_array($_GET['evolucion'], $evolucionPosiblesValues) ? filter_var($_GET['evolucion'], FILTER_VALIDATE_BOOL) : false;
    if( !$dolar && $evolucion ){
      return print 'The quote cannot be obtained if the type of dollar is not specified';
    }

    $cacheTimeInSeconds = 30;
    $cacheFile = getCacheFileName( $dolar, $evolucion );
    $cacheContent = getContentCache( $cacheFile, $cacheTimeInSeconds );
    if( $cacheContent ) {
      return print $cacheContent;
    }
    deleteAllCacheFilesIfExpiredTime( $cacheTimeInSeconds );

    $cotizacionesUrls = getCotizacionesUrl( $baseUrl, $dolar, $evolucion );
    foreach( $cotizacionesUrls as $url ){
        $response[] = getDataAPI( $url );
    }
    writeResponseInFile( $cacheFile, json_encode($response) );

    return print json_encode( $response, false );
  } else {
      return print 'Invalid Request';
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
