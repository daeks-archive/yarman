<?php
  
class xml
{
  public static function read($name)
  {
    $xml = new DOMDocument();
    if ($xml->load($name)) {
      return self::parse($xml);
    } else {
      return array();
    }
  }
  
  public static function dump($name)
  {
    $xml = simplexml_load_file($name);
    return json_decode(json_encode($xml), true);
  }
  
  private static function parse($xml, $types = array('game', 'folder'))
  {
    $xpath = new DOMXpath($xml);
    $output = array();
    
    foreach ($types as $type) {
      $items = $xpath->query($type);
      foreach ($items as $item) {
        $output[] = array('type' => $type, 'attributes' => self::parse_attributes($item), 'fields' => self::parse_fields($item));
      }
    }
    return $output;
  }

  private static function parse_attributes($node)
  {
    $output = array();
    if ($node->hasAttributes()) {
      foreach ($node->attributes as $attr) {
        $output[$attr->nodeName] = $attr->nodeValue;
      }
    }
    return $output;
  }
  
  private static function parse_fields($node)
  {
    $output = array();
    foreach ($node->childNodes as $child) {
      if ($child->nodeType == XML_ELEMENT_NODE) {
        $output[$child->nodeName] = $child->nodeValue;
      }
    }
    return $output;
  }

  public static function write($root, $data, $name)
  {
    $output = new DOMDocument('1.0', 'UTF-8');
    $output->formatOutput = true;

    $rootelement = $output->appendChild($output->createElement($root));

    foreach ($data as $row) {
      $childelement = $rootelement->appendChild($output->createElement($row['type']));
      foreach ($row['attributes'] as $key => $value) {
        $childelement->setAttribute($key, $value);
      }
      foreach ($row['fields'] as $key => $value) {
        $f = $childelement->appendChild($output->createElement($key));
        $f->appendChild($output->createTextNode(str_replace(chr(13), '', $value)));
      }
    }

    file_put_contents($name, $output->saveXML());
  }
}
  
?>