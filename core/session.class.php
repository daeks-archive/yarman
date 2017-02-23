<?php
    
  class session
  {
    public static function construct()
    {
      if (defined('COOKIE_LIFETIME')) {
        ini_set('session.cookie_lifetime', COOKIE_LIFETIME);
      }
      if (!session_id()) {
        session_start();
      }
    }
  }
    
?>