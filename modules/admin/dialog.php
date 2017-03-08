<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'view':
      switch (network::get('id')) {
        case 'config':
          cache::setClientVariable($module->id.'_id', network::get('id'));
          $output = '';
          $output .= '<form class="form-horizontal" id="data" name="data" data-toggle="post" data-validate="modal" data-query="'.CONTROLLER.'?action=save" method="POST"><fieldset>';
          foreach (db::instance()->read('config') as $item) {
            $output .= form::getString($item, $item['value']).'<br>';
          }
          $output .= '<button class="btn btn-success" type="submit">Save Changes</button>';
          $output .= '</fieldset></form>';
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