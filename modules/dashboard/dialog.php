<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'panel':
      if (network::get('id') != '') {
        switch (network::get('id')) {
          case 'beta':
            panel::start('Beta Warning', 'danger');
            echo '<p>This is currenly in BETA phase. Please be aware that it could damage your roms, media or gamelists.</p>';
            panel::end();
            break;
          case 'welcome':
            panel::start('Welcome to YARMan Web (Yet Another RetroPie Manager)', 'primary');
            echo '<p>RetroPie allows you to turn your Raspberry Pi or PC into a retro-gaming machine. It builds upon Raspbian, EmulationStation, RetroArch and many other projects to enable you to play your favourite Arcade, home-console, and classic PC games with the minimum set-up. For power users it also provides a large variety of configuration tools to customise the system as you want.</p>';
            panel::end();
            break;
          case 'memswap':
            echo '<div class="row">';
            echo '<div class="col-sm-6">';
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
            echo '</div>';
            echo '<div class="col-sm-6">';
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
            echo '</div>';
            break;
          case 'cputemp':
            echo '<div class="row">';
            echo '<div class="col-sm-7">';

            panel::start('CPU');
            $load = system::getLoadAverage();
            echo '<div class="row">';
            echo '<div class="col-sm-4" id="'.uniqid().'" data-provider="gauge" title="Load"  label="1min" data-query-min="0" data-query-max="5" data-query="'.$load['1min'].'" height="100"></div>';
            echo '<div class="col-sm-4" id="'.uniqid().'" data-provider="gauge" title="Load"  label="5min" data-query-min="0" data-query-max="5" data-query="'.$load['5min'].'" height="100"></div>';
            echo '<div class="col-sm-4" id="'.uniqid().'" style="height: 100px" data-provider="gauge" title="Load"  label="15min" data-query-min="0" data-query-max="5" data-query="'.$load['15min'].'" height="100"></div>';
            echo '</div>';
            panel::end();

            echo '</div>';
            echo '<div class="col-sm-5">';

            panel::start('Temperature');
            echo '<div class="row">';
            echo '<div class="col-sm-6" id="'.uniqid().'" data-provider="gauge" title="CPU Temperature"  label="celsius" data-query-min="30" data-query-max="85" data-query="'.system::getCPUTemp().'" height="100"></div>';
            echo '<div class="col-sm-6" id="'.uniqid().'" data-provider="gauge" title="GPU Temperature" label="celsius" data-query-min="30" data-query-max="85" data-query="'.system::getGPUTemp().'"  height="100"></div>';
            echo '</div>';
            panel::end();

            echo '</div>';
            echo '</div>';
            break;
          case 'system':
            panel::start('System Overview');
            $load = system::getLoadAverage();
            echo '<p>'.system::getUptime().'</p>';
            echo '<p>CPU Frequency: <b>'.system::getCPUFreq().'MHz</b></p>';
            panel::end();
            break;
          case 'cpu':
            panel::start('CPU');
            $load = system::getLoadAverage();
            echo '<div class="row">';
            echo '<div class="col-sm-4" id="'.uniqid().'" data-provider="gauge" title="Load"  label="1min" data-query-min="0" data-query-max="5" data-query="'.$load['1min'].'" height="100"></div>';
            echo '<div class="col-sm-4" id="'.uniqid().'" data-provider="gauge" title="Load"  label="5min" data-query-min="0" data-query-max="5" data-query="'.$load['5min'].'" height="100"></div>';
            echo '<div class="col-sm-4" id="'.uniqid().'" data-provider="gauge" title="Load"  label="15min" data-query-min="0" data-query-max="5" data-query="'.$load['15min'].'" height="100"></div>';
            echo '</div>';
            panel::end();
            break;
          case 'temperature':
            panel::start('Temperature');
            echo '<div class="row">';
            echo '<div class="col-sm-6" id="'.uniqid().'" data-provider="gauge" title="CPU Temperature"  label="celsius" data-query-min="30" data-query-max="85" data-query="'.system::getCPUTemp().'" height="100"></div>';
            echo '<div class="col-sm-6" id="'.uniqid().'" data-provider="gauge" title="GPU Temperature" label="celsius" data-query-min="30" data-query-max="85" data-query="'.system::getGPUTemp().'" height="100"></div>';
            echo '</div>';
            panel::end();
            break;
          case 'news':
            panel::start('<b>Latest RetroPie News</b>', 'info');
            $newsfeed = db::instance()->read('config', "id='news_feed'")[0]['value'];
            if (network::pingRemoteUrl($newsfeed)) {
              $xml = xml::dump(cache::setRemoteCache('newsfeed', $newsfeed));
              if (isset($xml['channel']) && isset($xml['channel']['item'])) {
                foreach (array_slice($xml['channel']['item'], 0, 3) as $news) {
                  echo '<a href="'.$news['link'].'" target="_blank" style="text-decoration: none !important;"><div>';
                  echo '<span style="color: black"><b>'.$news['title'].'</b></span> <div class="pull-right">read more</div>';
                  echo '</div></a>';
                }
                echo '<div><a href="'.$xml['channel']['link'].'" target="_blank" style="text-decoration: none !important;">...read more...</a></div>';
              }
            } else {
              echo "Failed to retrive newsfeed. Your device is currently offline.";
            }
            panel::end();
            break;
          case 'storage':
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
            break;
          case 'memory':
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
            break;
          case 'swap':
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
            break;
          default:
            break;
        }
      }
      break;
    default:
      break;
  }
}

?>