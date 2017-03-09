<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'config':
      switch (network::get('id')) {
        case 'config':
          cache::setClientVariable('admin_id', network::get('id'));
          $output = '';
          $output .= '<form class="form" data-validate="form" data-toggle="post" data-query="/modules/admin/controller.php?action=save&id=config" method="POST"><fieldset>';
          foreach (db::instance()->read('config') as $item) {
            $output .= form::getString($item, $item['value']).'<br>';
          }
          $output .= '<button class="btn btn-success" disabled data-validate="form">Save Changes</button>';
          $output .= '</fieldset></form>';
          network::success($output);
          break;
        case 'emulators':
          cache::setClientVariable('admin_id', network::get('id'));
          $output = '';
          $emulators = db::instance()->read('emulators');
          $output .= '<form class="form" data-validate="form" data-toggle="form" data-query="/modules/admin/controller.php?action=save&id=emulators" method="POST"><fieldset>';
          $options = array();
          $options['count'] = array('hidden' => true);
          $options['id'] = array('width' => '200px', 'readonly' => 'true', 'validator' => 'data-fv-notempty');
          $options['name'] = array('validator' => 'data-fv-notempty');
          $output .= form::getTable($emulators, $options);
          $output .= '<button class="btn btn-success" disabled data-validate="form">Save Changes</button>';
          $output .= '</fieldset></form><br><br>';
          network::success($output);
          break;
        default:
          network::error('invalid id - '.network::get('id'));
          break;
      }
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>