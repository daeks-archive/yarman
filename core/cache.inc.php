<?php

	class cache {
	
		public static function setClientVariable($key, $val, $time = COOKIE_LIFETIME) {
      $_SESSION[$key] = $val;
			setcookie ($key, $val, time() + $time, URL_SEPARATOR);
		}
		
		public static function deleteClientVariable($key) {
      unset($_SESSION[$key]);
			setcookie ($key, '', -1);
		}
		
		public static function getClientVariable($key) {
			if(isset($_COOKIE[$key])) {
        $_SESSION[$key] = $_COOKIE[$key];
				return $_COOKIE[$key];
			} else {
				return '';
			}
		}
		
	}

?>