<?php

function getTimeFile( $cacheFile ){
  $file_created_time = strtotime( date("H:i:s", filemtime( $cacheFile )));
  $current_time = strtotime( date ("H:i:s") );
  $fileTimeInSeconds = abs( $current_time - $file_created_time );
  return $fileTimeInSeconds;
}

function getContentCache( $cacheFile, $cacheTimeInSeconds ){
  if( file_exists( $cacheFile )){
    $fh = fopen( $cacheFile, 'r' );
    $fileTimeInSeconds = getTimeFile( $cacheFile );

    if( $fileTimeInSeconds > $cacheTimeInSeconds ){
        fclose( $fh );
        unlink( $cacheFile );
    } else {
        return file_get_contents( $cacheFile );
    }
  }
}

function writeResponseInFile( $cacheFile, $response ){
  $fh = fopen( $cacheFile, 'w' );
  fwrite( $fh, $response );
  fclose( $fh );
}

function deleteAllCacheFilesIfExpiredTime( $cacheTimeInSeconds ){
  $files = glob( "*temp.json" );
  foreach( $files as $file ){
    $fileTimeInSeconds = getTimeFile( $file );
    if( $fileTimeInSeconds > $cacheTimeInSeconds ){
      unlink( $file );
    }
  }
}

function getCacheFileName( $dolar, $evolucion ){
  if( !$dolar ){
    $cacheFile = "cotizaciones.temp.json";
  } else if ( $dolar && !$evolucion ){
    $cacheFile = "{$dolar}.temp.json";
  } else if ( $dolar == 'oficial' && $evolucion ){
    $cacheFile = "{$dolar}-evolucion.temp.json";
  }
  return $cacheFile;
}

?>