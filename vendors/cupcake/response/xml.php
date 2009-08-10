<?php

class XMLResponse {
  public $data;
  public $xml;
  
  public function __construct($data) {
    $this->data = $data;
    $this->xml = new XmlWriter();
  }
  
  public function write($data) {
    foreach($data as $key => $value){
      if(is_array($value)){
        $this->xml->startElement($key);
        $this->write($this->xml, $value);
        $this->xml->endElement();
        continue;
      }
      $this->xml->writeElement($key, $value);
    }    
  }
  
  public function to_xml() {
    $this->xml->openMemory();
    $this->xml->startDocument('1.0', 'UTF-8');
    $this->xml->startElement('root');
    
    $this->write($this->data);
    
    $this->xml->endElement();
    return $this->xml->outputMemory(true);
  }
}

?>