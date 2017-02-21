<?php

  require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.'config.php');
  if(isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'render':
        if(isset($_GET['system']) && isset($_GET['id'])) {
          $_GET['id'] = rawurldecode($_GET['id']);
          $obj = db::read('config', 'media_path').DIRECTORY_SEPARATOR.$_GET['system'].DIRECTORY_SEPARATOR.$_GET['id'];
          if (file_exists($obj) && is_readable($obj) && basename($_GET['system']) == $_GET['system'] && basename($_GET['id']) == $_GET['id']) {
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
      break;
      case 'dialog':
        echo '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
        echo '<h4 class="modal-title" id="modallabel">Upload Dialog</h4>';
        echo '</div>';
        echo '<div class="modal-body" id="modal-body">';
        echo '<div class="dropzone">Drop files here or click to upload</span></div>';
        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
        echo '</div>';
      break;
      case 'upload':
        if(isset($_GET['system']) && isset($_GET['type']) && isset($_FILES['object']) && $_FILES['object']['error'][0] == 0) {
          $output = $_FILES['object']['name'][0];
          if(isset($_POST['id'])) {
            $output = pathinfo($_POST['id'], PATHINFO_FILENAME).'-'.$_GET['type'].'.'.pathinfo($_FILES['object']['name'][0], PATHINFO_EXTENSION);
          } else {
            $output = pathinfo($_FILES['object']['name'][0], PATHINFO_FILENAME).'-'.$_GET['type'].'.'.pathinfo($_FILES['object']['name'][0], PATHINFO_EXTENSION);
          }
        
          if(move_uploaded_file($_FILES['object']['tmp_name'][0], db::read('config', 'media_path').DIRECTORY_SEPARATOR.$_GET['system'].DIRECTORY_SEPARATOR.$output)) {
              utils::ajax(200, db::read('config', 'media_path').DIRECTORY_SEPARATOR.$_GET['system'].DIRECTORY_SEPARATOR.$output);
          } else {
            utils::ajax(500, 'Object could not be uploaded', 'true');
          }
        } else {
          utils::ajax(500, 'General Error', 'true');
        }
      break;
      default:
      break;
    }
  }

?>