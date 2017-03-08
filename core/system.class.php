<?php

class system
{
  public static function exec($command)
  {
    if (file_exists(SCRIPTS.DIRECTORY_SEPARATOR.$command)) {
      $commands = file_get_contents(SCRIPTS.DIRECTORY_SEPARATOR.$command);
      return shell_exec($commands);
    } else {
      return false;
    }
  }

  public static function reboot()
  {
    return shell_exec('sudo reboot &');
  }

  public static function getCPUTemp()
  {
    return round(shell_exec('cat /sys/devices/virtual/thermal/thermal_zone0/temp')/1000, 2);
  }
          
  public static function getGPUTemp()
  {
    return round(str_replace(array('temp=', '\'C'), array('', ''), shell_exec('vcgencmd measure_temp')), 2);
  }
  
  public static function getUptime()
  {
    $input = explode(' ', shell_exec('cat /proc/uptime'));
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime('@'.round($input[0], 0));
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes,  %s seconds');
    //return shell_exec('uptime');
  }
  
  public static function getCPUFreq()
  {
    return round(shell_exec('sudo cat /sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_cur_freq')/1000, 0);
  }
  
  public static function getGPUVoltage()
  {
    return str_replace(array('volt=', 'V'), array('', ''), shell_exec('vcgencmd measure_volts core'));
  }
  
  public static function getLoadAverage()
  {
    $input = explode(' ', shell_exec('cat /proc/loadavg'));
    return array('1min' => $input[0], '5min' => $input[1], '15min' => $input[2]);
  }
  
  public static function getStorage()
  {
    $input = shell_exec('df');
    $output = array();
    $regexp = '/\/dev\/root\s([0-9]*)\s([0-9]*)\s([0-9]*)\s([0-9]*\%)\s\//';
    if (preg_match($regexp, preg_replace('/\s+/', ' ', $input), $match)) {
      $output['total'] = $match[1];
      $output['used'] = $match[2];
      $output['free'] = $match[3];
    }
    return $output;
  }
  
  public static function getMemory()
  {
    //$input = shell_exec('cat /proc/meminfo');
    $input = shell_exec('free');
    $output = array();
    $regexp = '/Mem:\s([0-9]*)\s([0-9]*)\s([0-9]*)\s([0-9]*)\s([0-9]*)\s([0-9]*)\s-\/\+\sbuffers\/cache:\s([0-9]*)\s([0-9]*)/';
    if (preg_match($regexp, preg_replace('/\s+/', ' ', $input), $match)) {
      $output['total'] = $match[1];
      $output['os_used'] = $match[2];
      $output['os_free'] = $match[3];
      $output['used'] = $match[7];
      $output['free'] = $match[8];
    }
    
    return $output;
  }
  
  public static function getSwap()
  {
    $input = shell_exec('free');
    $output = array();
    $regexp = '/Swap:\s([0-9]*)\s([0-9]*)\s([0-9]*)/';
    if (preg_match($regexp, preg_replace('/\s+/', ' ', $input), $match)) {
      $output['total'] = $match[1];
      $output['used'] = $match[2];
      $output['free'] = $match[3];
    }
    return $output;
  }
  
  public static function getLAN()
  {
    $output = array('rx_bytes' => shell_exec('cat /sys/class/net/eth0/statistics/rx_bytes'), 'tx_bytes' => shell_exec('cat /sys/class/net/eth0/statistics/tx_bytes'));
    return $output;
  }
  
  public static function getWLAN()
  {
    $output = array('rx_bytes' => shell_exec('cat /sys/class/net/wlan0/statistics/rx_bytes'), 'tx_bytes' => shell_exec('cat /sys/class/net/wlan0/statistics/tx_bytes'));
    return $output;
  }
}

?>