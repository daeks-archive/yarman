<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
page::start();

$fieldset = array();
$modules = module::readAll();
foreach ($modules as $moduleconfig) {
  $tmp = json_decode(file_get_contents($moduleconfig));
  if (isset($tmp->dashboard)) {
    foreach ($tmp->dashboard as $item) {
      $target = $item->panel;
      if (strpos($target, URL_SEPARATOR) !== 0) {
        $target = str_replace(BASE, '', MODULES.URL_SEPARATOR.$tmp->id.URL_SEPARATOR.$target);
        $target = 'http://'.$_SERVER['HTTP_HOST'].$target;
      }
      
      $parts = explode(' ', $item->grid);
      if (sizeof($parts) == 5) {
        if (array_key_exists($parts[0], $fieldset)) {
          if ($parts[3] == 'left') {
            while (isset($fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[1]][$parts[4]])) {
              $parts[4] += 1;
            };
            $fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[1]][$parts[4]] = $target;
          } elseif ($parts[3] == 'right') {
            while (isset($fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[2]][$parts[4]])) {
              $parts[4] += 1;
            };
            $fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[2]][$parts[4]] = $target;
          }
        } else {
          if ($parts[1] == '12' && $parts[2] == '0') {
            $fieldset[$parts[0]] = array($parts[3].'-col-sm-'.$parts[1] => array($parts[4] => $target));
          } else {
            if ($parts[3] == 'left') {
              $fieldset[$parts[0]] = array('left-col-sm-'.$parts[1] => array($parts[4] => $target), 'right-col-sm-'.$parts[2] => array());
            } elseif ($parts[3] == 'right') {
              $fieldset[$parts[0]] = array('left-col-sm-'.$parts[1] => array(), 'right-col-sm-'.$parts[2] => array($parts[4] => $target));
            }
          }
        }
      }
    }
  }
}

ksort($fieldset);

foreach ($fieldset as $key => $row) {
  echo '<div class="row">';
  foreach ($row as $key => $column) {
    echo '<div class="'.str_replace(array('left-', 'right-'), array('',''), $key).'">';
    ksort($column);
    foreach ($column as $key => $panel) {
      echo network::getRemoteContent($panel);
      //echo cache::getRemoteCache(cache::setRemoteCache($panel, $panel, 300));
    }
    echo '</div>';
  }
  echo '</div>';
}

page::end();

?>