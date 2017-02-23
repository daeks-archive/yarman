<?php

class cache
{
  public static function setClientVariable($key, $val, $time = COOKIE_LIFETIME)
  {
    $_SESSION[$key] = $val;
    setcookie($key, rawurlencode($val), time() + $time, URL_SEPARATOR);
  }
  
  public static function unsetClientVariable($key)
  {
    unset($_SESSION[$key]);
    setcookie($key, '', -1, URL_SEPARATOR);
  }
  
  public static function getClientVariable($key)
  {
    if (isset($_COOKIE[$key])) {
      $_SESSION[$key] = rawurldecode($_COOKIE[$key]);
      return rawurldecode($_COOKIE[$key]);
    } else {
      return '';
    }
  }
  
  public static function setRemoteCache($key, $value, $cache = 24 * 3600)
  {
    $output = CACHE.DIRECTORY_SEPARATOR.md5($key);
    if (file_exists($output)) {
      if (time() - filemtime($output) > $cache) {
        if (network::pingRemoteUrl($value)) {
          file_put_contents($output, network::getRemoteContent($value));
        }
      }
    } else {
      if (network::pingRemoteUrl($value)) {
        file_put_contents($output, network::getRemoteContent($value));
      }
    }
    return $output;
  }
  
  public static function unsetRemoteCache($key)
  {
    $output = CACHE.DIRECTORY_SEPARATOR.md5($key);
    if (file_exists($output)) {
      unlink($output);
    }
  }
  
  public static function getRemoteCache($key)
  {
    $output = CACHE.DIRECTORY_SEPARATOR.md5($key);
    if (file_exists($output)) {
      return file_get_contents($output);
    } else {
      return '';
    }
  }
}

?>