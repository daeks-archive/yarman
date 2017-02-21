<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  page::start();
  
  echo '<br><div class="row">';
  echo '<div class="col-sm-1">';
  
  echo '</div>';
  echo '<div class="col-sm-6">';
  
  echo '<div class="panel panel-primary">';
  echo '<div class="panel-heading"><h3 class="panel-title">Welcome</h3></div>';
  echo '<div class="panel-body">';
  echo '<p>This is currenly in BETA phase. Please be aware that it could damage your roms, media or gamelists.</p>';
  echo '<p>Need some more help? Checkout the <a href="https://retropie.org.uk/docs/" target="_blank">documentation</a>.</p>';
  echo '</div>';
  echo '</div>';
  
  echo '<div class="row">';
  echo '<div class="col-sm-7">';
  echo '<div class="panel panel-default">';
  echo '<div class="panel-heading"><h3 class="panel-title">CPU</h3></div>';
  echo '<div class="panel-body">';
  $load = system::getLoadAverage();
  echo '<p><b>Overview:</b> '.system::getUptime().'</p>';
  echo '<div style="display: table; width: 100%;">';
  echo '<div style="display: table-cell; width: 33%" id="'.uniqid().'" data-provider="gauge" title="Load"  label="1min" data-query-min="0" data-query-max="5" data-query="'.$load['1min'].'" width="100"></div>';
  echo '<div style="display: table-cell; width: 33%" id="'.uniqid().'" data-provider="gauge" title="Load"  label="5min" data-query-min="0" data-query-max="5" data-query="'.$load['5min'].'" width="100"></div>';
  echo '<div style="display: table-cell; width: 33%" id="'.uniqid().'" data-provider="gauge" title="Load"  label="15min" data-query-min="0" data-query-max="5" data-query="'.$load['15min'].'" width="100"></div>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '<div class="col-sm-5">';
  echo '<div class="panel panel-default">';
  echo '<div class="panel-heading"><h3 class="panel-title">Temperature</h3></div>';
  echo '<div class="panel-body">';
  
  echo '<div style="display: table; width: 100%;">';
  echo '<div style="display: table-cell; width: 50%" id="'.uniqid().'" data-provider="gauge" title="CPU Temperature"  label="celsius" data-query-min="30" data-query-max="85" data-query="'.system::getCPUTemp().'" width="100"></div>';
  echo '<div style="display: table-cell; width: 50%" id="'.uniqid().'" data-provider="gauge" title="GPU Temperature" label="celsius" data-query-min="30" data-query-max="85" data-query="'.system::getGPUTemp().'" width="100"></div>';
  echo '</div>';
  
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  
  echo '</div>';
  echo '<div class="col-sm-4">';
  
  echo '<div class="panel panel-info">';
  echo '<div class="panel-heading"><h3 class="panel-title">RetroPie Monitor</h3></div>';
  echo '<div class="panel-body">';
  $emulators = emulator::readAll();
  $total_roms = 0;
  foreach($emulators as $emulator) {
    $total_roms += $emulator['count'];
  }
  echo '<p><div>Total Emulators: <div class="pull-right"><b>'.sizeof($emulators).'</b></div></div>';
  echo '<div>Total Roms: <div class="pull-right"><b>'.$total_roms.'</b></div></div></p>';
  echo '</div>';
  echo '</div>';
  
  echo '<div class="panel panel-default">';
  echo '<div class="panel-heading"><h3 class="panel-title">Storage</h3></div>';
  echo '<div class="panel-body">';
  
  $storage = system::getStorage();
  $sto_percent = round($storage['used']/$storage['total']*100, 2);
  $storage_color = '';
  if($sto_percent > 70 && $sto_percent < 85 ) {
    $storage_color = 'progress-bar-warning';
  } else if($sto_percent >= 85) {
    $storage_color = 'progress-bar-danger';
  }
  echo '<p>Used: <b>'.round($storage['used']/1024/1024, 2).'GB</b> ('.$sto_percent.'%) Free: <b>'.round($storage['free']/1024/1024, 2).'GB</b> Total: <b>'.round($storage['total']/1024/1024, 2).'GB</b></p>';
  echo '<div class="progress">';
  echo '<div class="progress-bar '.$storage_color.'" role="progressbar" aria-valuenow="'.$storage['used'].'" aria-valuemin="0" aria-valuemax="'.$storage['total'].'" style="width: '.$sto_percent.'%;">'.$sto_percent.'%</div>';
  echo '</div>';
  
  echo '</div>';
  echo '</div>';
  
  echo '<div class="panel panel-default">';
  echo '<div class="panel-heading"><h3 class="panel-title">Memory</h3></div>';
  echo '<div class="panel-body">';
  
  $memory = system::getMemory();
  $mem_percent = round($memory['used']/$memory['total']*100, 2);
  $memory_color = '';
  if($mem_percent > 70 && $mem_percent < 85 ) {
    $memory_color = 'progress-bar-warning';
  } else if($mem_percent >= 85) {
    $memory_color = 'progress-bar-danger';
  }
  echo '<p>Used: <b>'.round($memory['used']/1024, 2).'MB</b> ('.$mem_percent.'%) Free: <b>'.round($memory['free']/1024, 2).'MB</b> Total: <b>'.round($memory['total']/1024, 2).'MB</b></p>';
  echo '<div class="progress">';
  echo '<div class="progress-bar '.$memory_color.'" role="progressbar" aria-valuenow="'.$memory['used'].'" aria-valuemin="0" aria-valuemax="'.$memory['total'].'" style="width: '.$mem_percent.'%;">'.$mem_percent.'%</div>';
  echo '</div>';
  
  echo '</div>';
  echo '</div>';
  
  echo '<div class="panel panel-default">';
  echo '<div class="panel-heading"><h3 class="panel-title">Swap</h3></div>';
  echo '<div class="panel-body">';
  
  $swap = system::getSwap();
  $swap_percent = round($swap['used']/$swap['total']*100, 2);
  $swap_color = '';
  if($swap_percent > 70 && $swap_percent < 85 ) {
    $swap_color = 'progress-bar-warning';
  } else if($swap_percent >= 85) {
    $swap_color = 'progress-bar-danger';
  }
  echo '<p>Used: <b>'.round($swap['used']/1024, 2).'MB</b> ('.$swap_percent.'%) Free: <b>'.round($swap['free']/1024, 2).'MB</b> Total: <b>'.round($swap['total']/1024, 2).'MB</b></p>';
  echo '<div class="progress">';
  echo '<div class="progress-bar '.$swap_color.'" role="progressbar" aria-valuenow="'.$swap['used'].'" aria-valuemin="0" aria-valuemax="'.$swap['total'].'" style="width: '.$swap_percent.'%;">'.$swap_percent.'%</div>';
  echo '</div>';
  
  echo '</div>';
  echo '</div>';
  
  echo '</div>';
  echo '<div class="col-sm-1">';
  
  echo '</div>';
  echo '</div>';
  
  page::end();
  
?>