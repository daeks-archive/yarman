<?php
  
  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  page::start();
  
  panel::start('Documentation', 'warning');
  echo '<p>Need some more help? Checkout the <a href="https://retropie.org.uk/docs/" target="_blank">documentation</a>.</p>';
  panel::end();
  
  page::end();
  
?>