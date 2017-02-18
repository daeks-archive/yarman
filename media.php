<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  if(isset($_GET['sys']) && isset($_GET['file'])) {
    $_GET['file'] = rawurldecode($_GET['file']);
    $obj = db::read('config', 'media_path').DIRECTORY_SEPARATOR.$_GET['sys'].DIRECTORY_SEPARATOR.$_GET['file'];
    if (file_exists($obj) && is_readable($obj) && basename($_GET['sys']) == $_GET['sys'] && basename($_GET['file']) == $_GET['file']) {
      switch (pathinfo($obj, PATHINFO_EXTENSION)) {
        case 'jpg':
          $mime = 'image/jpeg';
          break;
        case 'png':
          $mime = 'image/png';
          break;
        case 'mp4':
          $mime = 'video/mp4';
          break;
        default:
          $mime = false;
      }
      
      if ($mime) {
        header('Content-type: '.$mime);
        header('Content-length: '.filesize($obj));
        $file = @ fopen($obj, 'rb');
        if ($file) {
          fpassthru($file);
          exit;
        }
      }
    }
  }

?>