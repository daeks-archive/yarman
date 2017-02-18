<?php

  define('BASE', (($_SERVER['DOCUMENT_ROOT'] != '') ? $_SERVER['DOCUMENT_ROOT'] : dirname(realpath(__FILE__))));
  define('NAME', 'RetroPie');
  define('BRAND', 'core/img/site-logo.png');
  define('COOKIE_LIFETIME', 60*60*24*7*4*3);
  
  define('INC', BASE.DIRECTORY_SEPARATOR.'core');
  define('JS', BASE.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'js');
  define('CSS', BASE.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'css');
  define('DATA', BASE.DIRECTORY_SEPARATOR.'data');
  define('CACHE', DATA.DIRECTORY_SEPARATOR.'cache');
  define('DEFAULTS', BASE.DIRECTORY_SEPARATOR.'defaults');
  define('MODULES', BASE.DIRECTORY_SEPARATOR.'modules');

  define('MODULE', 'config.json');
  define('URL_SEPARATOR', '/');

  define('FILE_CACHE' , false);
  define('FILE_COMPRESS' , false);
  
  config::includes(INC);
  session::construct();
  
  class config {
  
    public static function includes($path) {
      foreach (scandir($path) as $include){
        if(is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($path.DIRECTORY_SEPARATOR.$include, '.inc.') !== false && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'PHP'){
          require_once($path.DIRECTORY_SEPARATOR.$include);
        }
      }
    }
    
    public static function libraries($path) {
      foreach (scandir($path) as $include){
        if(is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($path.DIRECTORY_SEPARATOR.$include, '.lib.') !== false && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'PHP'){
          require_once($path.DIRECTORY_SEPARATOR.$include);
          }
      }
    }

  }

?>