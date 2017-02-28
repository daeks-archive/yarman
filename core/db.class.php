<?php
  
class db
{
  private static $instance;

  private $jdb = '.jdb';
  private $sdb = '.sdb';
  
  private $handle;
  
  public static function instance($name = 'core') {
      if (!db::$instance instanceof self) {
           db::$instance = new self($name);
      }
      return db::$instance;
  }
  
  public function __construct($name = 'core')
  {
    $this->handle = new PDO('sqlite:'.DATA.DIRECTORY_SEPARATOR.$name.$this->sdb);
  }
  
  public function construct()
  {    
    foreach (array_slice(scandir(DB), 2) as $item) {
      $this->install(pathinfo($item, PATHINFO_FILENAME));
    }
  }

  public function read($module, $id = null, $column = 'value', $key = 'id', $replace = array())
  {
    $needle = array('%USER%');
    $default_user = get_current_user();
    if (file_exists(DATA.DIRECTORY_SEPARATOR.'user')) {
      $default_user = trim(file_get_contents(DATA.DIRECTORY_SEPARATOR.'user'));
    }
    $haystack = array($default_user);
  
    if ($this->handle->query('SELECT 1 FROM '.$module)) {
      if ($id != null) {
        $result = $this->handle->query('SELECT '.$column.' FROM '.$module.' WHERE '.$key.' = \''.$id.'\'');
        $output = $result->fetch();
        return str_replace($needle, $haystack, $output[$column]);
      } else {
        $result = $this->handle->query('SELECT * FROM '.$module);
        $output = array();
        foreach ($result->fetchAll() as $item) {
          foreach ($item as $key => $value) {
            $item[$key] = str_replace($needle, $haystack, $value);
          }
          array_push($output, $item);
        }
        return $output;
      } 
    } else {
      return $this->readFile($module, $id, $column, $key, $replace);
    }
  }
  
  private function readFile($module, $id = null, $column = 'value', $key = 'id', $replace = array())
  {
    $needle = array('%USER%');
    $default_user = get_current_user();
    if (file_exists(DATA.DIRECTORY_SEPARATOR.'user')) {
      $default_user = trim(file_get_contents(DATA.DIRECTORY_SEPARATOR.'user'));
    }
    $haystack = array($default_user);
  
    $array = array();
    if (file_exists(DATA.DIRECTORY_SEPARATOR.$module.$this->jdb)) {
      $array = json_decode(file_get_contents(DATA.DIRECTORY_SEPARATOR.$module.$this->jdb), true);
    } elseif (file_exists(MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$module.$this->jdb)) {
      $array = json_decode(file_get_contents(MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$module.$this->jdb), true);
    } elseif (file_exists(DB.DIRECTORY_SEPARATOR.$module.$this->jdb)) {
      $array = json_decode(file_get_contents(DB.DIRECTORY_SEPARATOR.$module.$this->jdb), true);
    }
    foreach ($replace as $key => $value) {
      array_push($needle, $key);
      array_push($haystack, $value);
    }

    if ($id != null) {
      foreach ($array['data'] as $item) {
        if (isset($item[$key]) && isset($item[$column]) && $item[$key] == $id) {
          return str_replace($needle, $haystack, $item[$column]);
        }
      }
    } else {
      $output = array();
      foreach ($array['data'] as $item) {
        foreach ($item as $key => $value) {
          $item[$key] = str_replace($needle, $haystack, $value);
        }
        array_push($output, $item);
      }
      return $output;
    }
  }
  
  public function write($module, $id, $data, $where = null)
  {
    if ($this->handle->query('SELECT 1 FROM '.$module)) {
      if ($this->handle->query('SELECT 1 FROM '.$module.' WHERE id = '.$this->handle->quote($id))) {
        $tmp = '';
        foreach($data as $key=>$value) {
          $tmp .= $key.'=?,'; 
        }
        $sql = 'UPDATE '.$module.' SET '.rtrim($tmp,',');
        if($where != null) {
          $sql .= ' WHERE '.$where;
        } else {
          $sql .= ' WHERE id = '.$this->handle->quote($id);
        }
        $stmt = $this->handle->prepare($sql);
        $stmt->execute(array_values($data));
      } else {
        $tmp = '';
        foreach($data as $key=>$value) {
          $tmp .= '?,'; 
        }
        $stmt = $this->handle->prepare('INSERT INTO '.$module.' ('.implode(',', array_keys($data)).') VALUES ('.rtrim($tmp,',').')');
        $stmt->execute(array_values($data));
      }
    }
  }
  
  public function remove($module, $id, $where = null)
  {
    if ($this->handle->query('SELECT 1 FROM '.$module)) {
      if ($this->handle->query('SELECT 1 FROM '.$module.' WHERE id = '.$this->handle->quote($id))) {
        $sql = 'DELETE FROM '.$module;
        if($where != null) {
          $sql .= ' WHERE '.$where;
        } else {
          $sql .= ' WHERE id = '.$this->handle->quote($id);
        }
        $stmt = $this->handle->prepare($sql);
        $stmt->execute();
      }
    }
  }
  
  public function install($module)
  {
    if (!$this->handle->query('SELECT 1 FROM schema')) {
      $stmt = $this->handle->prepare('CREATE TABLE IF NOT EXISTS schema (id text primary key not null, version integer);');
      $stmt->execute();
    }
    if (!$this->handle->query('SELECT 1 FROM '.$module)) {
      $array = array();
      if (file_exists(DATA.DIRECTORY_SEPARATOR.$module.$this->jdb)) {
        $array = json_decode(file_get_contents(DATA.DIRECTORY_SEPARATOR.$module.$this->jdb), true);
      } elseif (file_exists(MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$module.$this->jdb)) {
        $array = json_decode(file_get_contents(MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$module.$this->jdb), true);
      } elseif (file_exists(DB.DIRECTORY_SEPARATOR.$module.$this->jdb)) {
        $array = json_decode(file_get_contents(DB.DIRECTORY_SEPARATOR.$module.$this->jdb), true);
      }

      $this->handle->beginTransaction();
      if (isset($array['version'])) {
        $stmt = $this->handle->prepare('INSERT INTO schema (id, version) VALUES (\''.$module.'\','.$array['version'].');');
        $stmt->execute();
                  
        if (isset($array['schema'])) {
          $columns = array();
          foreach ($array['schema'] as $item) {
            array_push($columns, strtolower($item['name']).' '.strtoupper($item['type']));
          }
          $stmt = $this->handle->prepare('CREATE TABLE IF NOT EXISTS '.$module.' ('.implode(',' , $columns).');');
          $stmt->execute();
        }
        if (isset($array['data'])) {
          foreach ($array['data'] as $item) {
            $columns = array();
            $values = array();
            foreach ($item as $key => $value) {
              array_push($columns, $key);
              if (!is_numeric($value) && !is_bool($value)) {
                $value = $this->handle->quote($value);
              }
              array_push($values, $value);
            }
            $stmt = $this->handle->prepare('INSERT INTO '.$module.' ('.implode(',' , $columns).') VALUES ('.implode(',' , $values).');');
            $stmt->execute();
          }
        }
      }
      $this->handle->commit();
    }
  }
}
  
?>