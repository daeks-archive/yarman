<?php

require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.'config.php');
if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'render':
      if (network::get('id') != '') {
        $rom = rom::read(network::get('id'));
        if (isset($rom[network::get('type')])) {
          if (file_exists($rom[network::get('type')]) && is_readable($rom[network::get('type')])) {
            switch (pathinfo($rom[network::get('type')], PATHINFO_EXTENSION)) {
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
              header('Content-length: '.filesize($rom[network::get('type')]));
              $file = @ fopen($rom[network::get('type')], 'rb');
              if ($file) {
                fpassthru($file);
                exit;
              }
            }
          }
        }
      }
      break;
    case 'dialog':
      $field = current(db::instance()->read('fields', 'id='.db::instance()->quote(network::get('type'))));
      $field['name'] = 'Default '.$field['name'];
      modal::start('Upload Dialog for '.$field['name']);
      echo form::getString($field, $field['path']);
      echo '<div class="preview"></div>';
      echo '<div class="dropzone">Drop files here or click to upload</div>';
      echo '</fieldset></form>';
      echo '</div>';
      echo '<div class="modal-footer">';
      echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
      echo '<span class="btn btn-success btn-file">';
      echo '<input type="file" data-toggle="previewproxy" data-query="/core/proxy.php?action=upload&type='.$field['id'].'" data-key="#id" data-target="#'.$field['id'].'" accept="'.(isset($field['whitelist'])?str_replace(' ', ',', $field['whitelist']):'').'">';
      echo 'Upload</span>';
      echo '</div>';
      break;
    case 'upload':
      if (isset($_POST['data'])) {
        $params = array();
        parse_str($_POST['data'], $params);
        unset($_POST['data']);
        foreach ($params as $key => $value) {
          $_POST[$key] = $value;
        }
      }
      if (network::get('type') != '' && isset($_FILES['object']) && $_FILES['object']['error'] == 0) {
        if (network::post('image') != '') {
          db::instance()->write('fields', array('path' => network::post('image')), 'id='.db::instance()->quote(network::get('type')));
        }
        if (network::post('id') != '') {
          $rom = rom::config(network::post('id'));
          $path = current(db::instance()->read('fields', "id='".network::get('type')."'"))['path'];
          $output = pathinfo($rom['name'], PATHINFO_FILENAME).'-'.network::get('type').'.'.pathinfo($_FILES['object']['name'], PATHINFO_EXTENSION);
          
          if (move_uploaded_file($_FILES['object']['tmp_name'], $path.DIRECTORY_SEPARATOR.$rom['emulator'].DIRECTORY_SEPARATOR.$output)) {
              network::success($path.DIRECTORY_SEPARATOR.$rom['emulator'].DIRECTORY_SEPARATOR.$output);
          } else {
            network::error('Object could not be uploaded', 'true');
          }
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