<?php
    
  class module {

    public static function readAll() {
      $tmp = array();
      foreach (scandir(MODULES) as $include){
        if(is_dir(MODULES.DIRECTORY_SEPARATOR.$include) && is_file(MODULES.DIRECTORY_SEPARATOR.$include.DIRECTORY_SEPARATOR.MODULE)){
          array_push($tmp, MODULES.DIRECTORY_SEPARATOR.$include.DIRECTORY_SEPARATOR.MODULE);
        }
      }
      return $tmp;
    }
        
    public static function read($module = null) {
      if($module == null) {
        if(isset($_SERVER['REQUEST_URI'])) {
          $location = explode(URL_SEPARATOR, $_SERVER['REQUEST_URI']);
          if(sizeof($location) > 2) {
            if (file_exists(MODULES.DIRECTORY_SEPARATOR.$location[2].DIRECTORY_SEPARATOR.MODULE)) {
               return json_decode(file_get_contents(MODULES.DIRECTORY_SEPARATOR.$location[2].DIRECTORY_SEPARATOR.MODULE));
            } else {
              return null;
            }
          } else {
            return null;
          }
        } else {
          return null;
        }
      } else {
        if (file_exists(MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.MODULE)) {
             return json_decode(file_get_contents(MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.MODULE));
          } else {
            return null;
          }
      }
    }
    
  }
    
?>