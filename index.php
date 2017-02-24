<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
page::start();

echo '<br><div class="row">';
echo '<div class="col-sm-1">';

echo '</div>';
echo '<div class="col-sm-6">';

panel::start('Beta Warning', 'danger');
echo '<p>This is currenly in BETA phase. Please be aware that it could damage your roms, media or gamelists.</p>';
panel::end();

panel::start('Welcome to RetroPie', 'primary');
echo '<p>RetroPie allows you to turn your Raspberry Pi or PC into a retro-gaming machine. It builds upon Raspbian, EmulationStation, RetroArch and many other projects to enable you to play your favourite Arcade, home-console, and classic PC games with the minimum set-up. For power users it also provides a large variety of configuration tools to customise the system as you want.</p>';
panel::end();
 
echo '<div class="row">';
echo '<div class="col-sm-7">';

panel::start('CPU');
$load = system::getLoadAverage();
echo '<p><b>Overview:</b> '.system::getUptime().'</p>';
echo '<div class="row">';
echo '<div class="col-sm-4" id="'.uniqid().'" data-provider="gauge" title="Load"  label="1min" data-query-min="0" data-query-max="5" data-query="'.$load['1min'].'" width="100"></div>';
echo '<div class="col-sm-4" id="'.uniqid().'" data-provider="gauge" title="Load"  label="5min" data-query-min="0" data-query-max="5" data-query="'.$load['5min'].'" width="100"></div>';
echo '<div class="col-sm-4" id="'.uniqid().'" data-provider="gauge" title="Load"  label="15min" data-query-min="0" data-query-max="5" data-query="'.$load['15min'].'" width="100"></div>';
echo '</div>';
panel::end();

echo '</div>';
echo '<div class="col-sm-5">';

panel::start('Temperature');
echo '<p>CPU Frequency: <b>'.system::getCPUFreq().'MHz</b></p>';
echo '<div class="row">';
echo '<div class="col-sm-6" id="'.uniqid().'" data-provider="gauge" title="CPU Temperature"  label="celsius" data-query-min="30" data-query-max="85" data-query="'.system::getCPUTemp().'" width="100"></div>';
echo '<div class="col-sm-6" id="'.uniqid().'" data-provider="gauge" title="GPU Temperature" label="celsius" data-query-min="30" data-query-max="85" data-query="'.system::getGPUTemp().'" width="100"></div>';
echo '</div>';
panel::end();

echo '</div>';
echo '</div>';

echo '</div>';
echo '<div class="col-sm-4">';

panel::start('<b>Latest RetroPie News</b>', 'info');
if (network::pingRemoteUrl(db::read('config', 'news_feed'))) {
  $xml = xml::dump(cache::setRemoteCache('newsfeed', db::read('config', 'news_feed')));
  if (isset($xml['channel']) && isset($xml['channel']['item'])) {
    foreach (array_slice($xml['channel']['item'], 0, 3) as $news) {
      echo '<a href="'.$news['link'].'" target="_blank" style="text-decoration: none !important;"><div>';
      echo '<span style="color: black"><b>'.$news['title'].'</b></span> <div class="pull-right">read more</div>';
      echo '</div></a>';
    }
    echo '<div><a href="'.db::read('config', 'news_feed').'" target="_blank" style="text-decoration: none !important;">...read more...</a></div>';
  }
} else {
  echo "Failed to retrive newsfeed. Your device is currently offline.";
}
panel::end();

panel::start('RetroPie Monitor', 'info');
$emulators = emulator::readAll();
$total_roms = 0;
foreach ($emulators as $emulator) {
  $total_roms += $emulator['count'];
}
echo '<p><div>Total Emulators: <div class="pull-right"><b>'.sizeof($emulators).'</b></div></div>';
echo '<div>Total Roms: <div class="pull-right"><b>'.$total_roms.'</b></div></div></p>';
panel::end();
 
panel::start('Storage');
$storage = system::getStorage();
$sto_percent = round($storage['used']/$storage['total']*100, 2);
$storage_color = '';
if ($sto_percent > 70 && $sto_percent < 85) {
  $storage_color = 'progress-bar-warning';
} elseif ($sto_percent >= 85) {
  $storage_color = 'progress-bar-danger';
}
echo '<p>Used: <b>'.round($storage['used']/1024/1024, 2).'GB</b> ('.$sto_percent.'%) Free: <b>'.round($storage['free']/1024/1024, 2).'GB</b> Total: <b>'.round($storage['total']/1024/1024, 2).'GB</b></p>';
echo '<div class="progress">';
echo '<div class="progress-bar '.$storage_color.'" role="progressbar" aria-valuenow="'.$storage['used'].'" aria-valuemin="0" aria-valuemax="'.$storage['total'].'" style="width: '.$sto_percent.'%;">'.$sto_percent.'%</div>';
echo '</div>';
panel::end();

panel::start('Memory');
$memory = system::getMemory();
$mem_percent = round($memory['used']/$memory['total']*100, 2);
$memory_color = '';
if ($mem_percent > 70 && $mem_percent < 85) {
  $memory_color = 'progress-bar-warning';
} elseif ($mem_percent >= 85) {
  $memory_color = 'progress-bar-danger';
}
echo '<p>Used: <b>'.round($memory['used']/1024, 2).'MB</b> ('.$mem_percent.'%) Free: <b>'.round($memory['free']/1024, 2).'MB</b> Total: <b>'.round($memory['total']/1024, 2).'MB</b></p>';
echo '<div class="progress">';
echo '<div class="progress-bar '.$memory_color.'" role="progressbar" aria-valuenow="'.$memory['used'].'" aria-valuemin="0" aria-valuemax="'.$memory['total'].'" style="width: '.$mem_percent.'%;">'.$mem_percent.'%</div>';
echo '</div>';
panel::end();

panel::start('Swap');
$swap = system::getSwap();
$swap_percent = round($swap['used']/$swap['total']*100, 2);
$swap_color = '';
if ($swap_percent > 70 && $swap_percent < 85) {
  $swap_color = 'progress-bar-warning';
} elseif ($swap_percent >= 85) {
  $swap_color = 'progress-bar-danger';
}
echo '<p>Used: <b>'.round($swap['used']/1024, 2).'MB</b> ('.$swap_percent.'%) Free: <b>'.round($swap['free']/1024, 2).'MB</b> Total: <b>'.round($swap['total']/1024, 2).'MB</b></p>';
echo '<div class="progress">';
echo '<div class="progress-bar '.$swap_color.'" role="progressbar" aria-valuenow="'.$swap['used'].'" aria-valuemin="0" aria-valuemax="'.$swap['total'].'" style="width: '.$swap_percent.'%;">'.$swap_percent.'%</div>';
echo '</div>';
panel::end();

echo '</div>';
echo '<div class="col-sm-1">';

echo '</div>';
echo '</div>';

page::end();

?>