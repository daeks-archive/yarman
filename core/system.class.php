<?php

  class system {

    public static function getCPUTemp() {
      return round(shell_exec('cat /sys/class/thermal/thermal_zone0/temp')/1000, 2);
    }
            
    public static function getGPUTemp() {
      return str_replace(array('temp=', '\'C'), array('', ''), shell_exec('vcgencmd measure_temp'));
    }
    
    public static function getUptime() {
      return shell_exec('uptime');
    }
    
    public static function getLoadAverage() {
      $input = shell_exec('uptime');
      $output = array();
      $regexp = '/load average:\s([0-9.]*),\s([0-9.]*),\s([0-9.]*)/';
      if(preg_match($regexp, preg_replace('/\s+/', ' ', $input), $match)) {
        $output['1min'] = $match[1];
        $output['5min'] = $match[2];
        $output['15min'] = $match[3];
      }
      return $output;
    }
    
    public static function getStorage() {
      $input = shell_exec('df');
      $output = array();
      $regexp = '/\/dev\/root\s([0-9]*)\s([0-9]*)\s([0-9]*)\s([0-9]*\%)\s\//';
      if(preg_match($regexp, preg_replace('/\s+/', ' ', $input), $match)) {
        $output['total'] = $match[1];
        $output['used'] = $match[2];
        $output['free'] = $match[3];
      }
      return $output;
    }
    
    public static function getMemory() {
      $input = shell_exec('free');
      $output = array();
      $regexp = '/Mem:\s([0-9]*)\s([0-9]*)\s([0-9]*)\s([0-9]*)\s([0-9]*)\s([0-9]*)\s-\/\+/';
      if(preg_match($regexp, preg_replace('/\s+/', ' ', $input), $match)) {
        $output['total'] = $match[1];
        $output['used'] = $match[2];
        $output['free'] = $match[3];
      }
      
      return $output;
    }
    
    public static function getSwap() {
      $input = shell_exec('free');
      $output = array();
      $regexp = '/Swap:\s([0-9]*)\s([0-9]*)\s([0-9]*)/';
      if(preg_match($regexp, preg_replace('/\s+/', ' ', $input), $match)) {
        $output['total'] = $match[1];
        $output['used'] = $match[2];
        $output['free'] = $match[3];
      }
      
      return $output;
    }
    
  }

?>