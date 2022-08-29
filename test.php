<?php 
  header("HTTP/1.1 200 OK");
  header('Content-Type: application/json');

  function getDataAPI($url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        throw new Exception('Error al consumir la API: '.$err);
    }
    return json_decode($response);
  }

  function getTimeCacheFile($cacheFile){
    $file_created_time = strtotime(date("H:i:s",filemtime($cacheFile)));
    $current_time = strtotime(date("H:i:s"));
    $interval = abs($current_time - $file_created_time);
    $minutes = round($interval / 60);
    return $minutes;
  }

  function returnCacheIfExists(){
    
  }

  if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $dolar = $_GET['dolar']??false;
    $evolucion = $_GET['evolucion']??false;

    $cacheFile = 'temp.json';
    $filedeleted = false;

    if(file_exists($cacheFile) && !$dolar){
      $fh = fopen($cacheFile, 'r');
      $minutes = getTimeCacheFile($cacheFile);

      if($minutes > 1){
          fclose($fh);
          unlink($cacheFile);
          $filedeleted = true;
      } else {
          return print file_get_contents($cacheFile);
      }
  } do {
    if(!$dolar  && !$evolucion){
        $cotizaciones = [];
        $arrayCotizacionesUrls = [
            'https://api-dolar-argentina.herokuapp.com/api/dolaroficial',
            'https://api-dolar-argentina.herokuapp.com/api/dolarblue',
            'https://api-dolar-argentina.herokuapp.com/api/contadoliqui',
            'https://api-dolar-argentina.herokuapp.com/api/dolarpromedio',
            'https://api-dolar-argentina.herokuapp.com/api/dolarturista',
            'https://api-dolar-argentina.herokuapp.com/api/evolucion/dolaroficial'
        ];
        foreach ($arrayCotizacionesUrls as $url){
            $cotizaciones[] = getDataAPI($url);
        }
        $fh = fopen($cacheFile, 'w');
        fwrite($fh, json_encode($cotizaciones)); 
        fclose($fh);

        return print json_encode($cotizaciones, false);
    }
    $baseUrl = 'https://api-dolar-argentina.herokuapp.com/api';
    $cotizacionUrl = $evolucion ? "{$baseUrl}/{$evolucion}/{$dolar}" : "{$baseUrl}/{$dolar}";
    
    if(!$dolar  && $evolucion){
        $errorMensaje = new stdClass();
        $errorMensaje->error = 'No se puede obtener la cotización si no se especifica el tipo de dolar';
        return $errorMensaje;
    }
    
    $cotizacionElegida = getDataAPI($cotizacionUrl);
    return print json_encode($cotizacionElegida, false);

    } while($filedeleted);

  } else {
      return print 'Invalid Request';
  }

?>