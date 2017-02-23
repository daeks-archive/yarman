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
}

?>
