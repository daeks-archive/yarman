<?php
  
class session
{
  public static function construct()
  {
    if (defined('COOKIE_LIFETIME')) {
      ini_set('session.cookie_lifetime', COOKIE_LIFETIME);
    }
    session_name(md5(BASE));
    session_start();
  }
}
  
?>