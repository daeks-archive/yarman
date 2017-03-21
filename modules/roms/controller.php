<?php

set_time_limit(600);
require(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>