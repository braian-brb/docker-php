<?php

function getCotizacionesUrl( $baseUrl, $dolar, $evolucion ){
  if( !$dolar  && !$evolucion ){
    $CotizacionesUrls = [
      "{$baseUrl}/dolaroficial",
      "{$baseUrl}/dolarblue",
      "{$baseUrl}/contadoliqui",
      "{$baseUrl}/dolarpromedio",
      "{$baseUrl}/dolarturista",
      "{$baseUrl}/evolucion/dolaroficial"
    ];
  } 

  if( $evolucion && $dolar == 'oficial' ){
    $CotizacionesUrls[] = "{$baseUrl}/evolucion/dolaroficial";
  }

  switch( $dolar ){
    case 'oficial':
      $CotizacionesUrls[] = "{$baseUrl}/dolaroficial";
      break;
    case 'blue':
      $CotizacionesUrls[] = "{$baseUrl}/dolarblue";
      break;
    case 'liqui':
      $CotizacionesUrls[] = "{$baseUrl}/contadoliqui";
      break;
    case 'promedio':
      $CotizacionesUrls[] = "{$baseUrl}/dolarpromedio";
      break;
    case 'turista':
      $CotizacionesUrls[] = "{$baseUrl}/dolarturista";
      break;
  }
  return $CotizacionesUrls;
}