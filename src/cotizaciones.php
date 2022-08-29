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

  function getCotizacionesUrl($baseUrl, $dolar, $evolucion){
    if(!$dolar  && !$evolucion){
      $CotizacionesUrls = [
        "{$baseUrl}/dolaroficial",
        "{$baseUrl}/dolarblue",
        "{$baseUrl}/contadoliqui",
        "{$baseUrl}/dolarpromedio",
        "{$baseUrl}/dolarturista",
        "{$baseUrl}/evolucion/dolaroficial"
      ];
      return $CotizacionesUrls;
    } 

    if($evolucion && $dolar == 'oficial'){
      $CotizacionesUrls[] = "{$baseUrl}/evolucion/dolaroficial";
      return $CotizacionesUrls;
    }

    switch($dolar){
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

  function getTimeCacheFile($cacheFile){
    $file_created_time = strtotime(date("H:i:s",filemtime($cacheFile)));
    $current_time = strtotime(date("H:i:s"));
    $interval = abs($current_time - $file_created_time);
    $minutes = round($interval / 60);
    return $minutes;
  }

  function getCacheIfExists($cacheFile, $dolar){

    if(file_exists($cacheFile) && !$dolar){
      $fh = fopen($cacheFile, 'r');
      $minutes = getTimeCacheFile($cacheFile);

      if($minutes > 1){
          fclose($fh);
          unlink($cacheFile);
      } else {
          return file_get_contents($cacheFile);
      }
    }
  }

  function writeResponseInCache($cacheFile, $response){
    $fh = fopen($cacheFile, 'w');
    fwrite($fh, $response);
    fclose($fh);
  }

  if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $baseUrl = 'https://api-dolar-argentina.herokuapp.com/api';


    $dolar = $_GET['dolar']??false;
    $evolucion = $_GET['evolucion']??false;

    $cacheFile = 'temp.json';

    if(!$dolar && $evolucion){
      return print 'The quote cannot be obtained if the type of dollar is not specified';
    }

    $cacheContent = getCacheIfExists($cacheFile, $dolar);
    if($cacheContent) {
      return print $cacheContent;
    }

    $CotizacionesUrls = getCotizacionesUrl($baseUrl, $dolar, $evolucion);

    foreach($CotizacionesUrls as $url){
        $response[] = getDataAPI($url);
    }
    

    if(!$dolar){
      //No almacena en cache si se especifica el tipo de dolar
      writeResponseInCache($cacheFile, json_encode($response));
    }
    return print json_encode($response, false);
    
  } else {
      return print 'Invalid Request';
  }

?>