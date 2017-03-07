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
      if (strpos($target, DIRECTORY_SEPARATOR) !== 0) {
        $target = MODULES.DIRECTORY_SEPARATOR.$tmp->id.DIRECTORY_SEPARATOR.$target;
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
      $offset = strpos($panel, '?');
      if ($offset !== false) {
        $params = substr($panel, $offset+1);
        $include = substr($panel, 0, $offset);
        foreach (explode('&', $params) as $value) {
          $parts = explode('=', $value);
          if (sizeof($parts) == 2) {
            $_GET[$parts[0]] = $parts[1];
          } else {
            $_GET[$parts[0]] = '';
          }
        }
        include($include);
      } else {
        include($panel);
      }
    }
    echo '</div>';
  }
  echo '</div>';
}

page::end();

?>