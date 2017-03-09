<?php

class network
{
  public static $agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36';

  public static function success($data = null, $event = null)
  {
    self::send(200, $data, $event);
  }

  public static function error($data, $event = null)
  {
    self::send(500, $data, $event);
  }

  public static function fatal($data, $event = null)
  {
    self::send(999, $data, $event);
  }

  public static function send($status, $data, $event)
  {
    $array = array();
    $array['status'] = $status;
    $array['data'] = (($data == null) ? '' : htmlentities($data));
    $array['event'] = (($event == null) ? '' : $event);
    echo json_encode($array);
  }
  

  public static function get($key)
  {
    if (isset($_GET[$key])) {
      return $_GET[$key];
    } else {
      return '';
    }
  }
  
  public static function post($key)
  {
    if (isset($_POST[$key])) {
      return $_POST[$key];
    } else {
      return '';
    }
  }

  public static function pingRemoteUrl($url)
  {
    $nurl = parse_url($url);
    $socket = @fsockopen($nurl['host'], (isset($nurl['port'])? $nurl['port'] : 80), $errno, $errstr, 5);
    if (!$socket) {
        return $errno."@".$errstr;
    } else {
        fclose($socket);
        return "OK";
    }
  }
  
  public static function getRemoteContentLength($url)
  {
    return (isset(get_headers($url, 1)['Content-Length']) ? get_headers($url, 1)['Content-Length'] : 0);
  }

  public static function getRemoteContent($url, $getstartbytes = false)
  {
    if (extension_loaded('curl')) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      if ($getstartbytes) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Range: bytes=0-32768'));
      }
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_USERAGENT, self::$agent);
      return curl_exec($ch);
    } else {
      $ctx = stream_context_create(array('http' => array('timeout' => 5)));
      return file_get_contents($url, false, $ctx);
    }
  }
}

?>