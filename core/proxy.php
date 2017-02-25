<?php

require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.'config.php');
if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'render':
      if (network::get('emulator') != '' && network::get('id') != '') {
        $id = rawurldecode(network::get('id'));
        $obj = db::read('fields', network::get('type'), 'path').DIRECTORY_SEPARATOR.network::get('emulator').DIRECTORY_SEPARATOR.$id;
        if (file_exists($obj) && is_readable($obj) && basename(network::get('emulator')) == network::get('emulator') && basename($id) == $id) {
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
              break;
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
      if (network::get('emulator') != '' && network::get('type') != '' && isset($_FILES['object']) && $_FILES['object']['error'][0] == 0) {
        $output = $_FILES['object']['name'][0];
        if (network::post('id') != '') {
          $output = pathinfo(network::post('id'), PATHINFO_FILENAME).'-'.network::get('type').'.'.pathinfo($_FILES['object']['name'][0], PATHINFO_EXTENSION);
        } else {
          $output = pathinfo($_FILES['object']['name'][0], PATHINFO_FILENAME).'-'.network::get('type').'.'.pathinfo($_FILES['object']['name'][0], PATHINFO_EXTENSION);
        }
      
        if (move_uploaded_file($_FILES['object']['tmp_name'][0], db::read('fields', network::get('type'), 'path').DIRECTORY_SEPARATOR.network::get('emulator').DIRECTORY_SEPARATOR.$output)) {
            network::success(db::read('fields', network::get('type'), 'path').DIRECTORY_SEPARATOR.network::get('emulator').DIRECTORY_SEPARATOR.$output);
        } else {
          network::error('Object could not be uploaded', 'true');
        }
      } else {
        network::error('General Error', 'true');
      }
      break;
    default:
      break;
  }
}

?>