<?php

class es
{
  public static function start($tty = 1)
  {
    $commands = array();
    array_push($commands, 'export HOME='.current(db::instance()->read('config', "id='user_path'"))['value']);
    array_push($commands, 'sudo openvt -c '.$tty.' -s -f clear');
    array_push($commands, 'sudo openvt -c '.$tty.' -s -f emulationstation 2>&1 &');
    return shell_exec(implode(' && ', $commands));
  }
  
  public static function restart()
  {
    echo self::stopGame();
    return shell_exec('touch /tmp/es-restart && killall emulationstation');
  }
  
  public static function stop()
  {
    return shell_exec('killall emulationstation');
  }
  
  public static function config()
  {
    $content = file_get_contents(current(db::instance()->read('config', "id='es_path'"))['value'].DIRECTORY_SEPARATOR.'es_settings.cfg');
    $content = str_replace('<?xml version="1.0"?>', '<?xml version="1.0"?><settings>', $content).'</settings>';
    $xml = json_decode(json_encode(simplexml_load_string($content)), true);
    $output = array();
    foreach ($xml as $type) {
      foreach ($type as $item) {
        $output[$item['@attributes']['name']] = $item['@attributes']['value'];
      }
    }
    return $output;
  }
  
  public static function startGame($system, $id)
  {
    return shell_exec('/opt/retropie/supplementary/runcommand/runcommand.sh 0 _SYS_ '.$system.' \''.current(db::instance()->read('config', "id='rom_path'"))['value'].DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.$id.'\'');
  }
  
  public static function stopGame()
  {
    return system::exec('killgame.sh');
  }
}

?>