<?php

class Application_Model_News implements Iterator{
	
	private $position = 0;
	private $_data;
	
	public function __construct() {
		$this->position = 0;
		$oConfig = new Zend_Config_Xml(APPLICATION_PATH.'/configs/config.xml', 'production');
		$this->_data = $this->parseConfig($oConfig);
	}
	
	private function parseConfig($oConfig) {
		$aConfig = array();
		foreach($oConfig->data as $oElement) {
			$aPictures = array();
			$aList = glob(APPLICATION_PATH.'/../public/images/'.(string)$oElement->path."/*.jpg");
			
			foreach($aList as $sPicture) {
				$aPictures[] = "/images/" . (string)$oElement->path . "/" . basename($sPicture);
			}
			$aDates = explode("-", (string)$oElement->date);
			
			$aConfig[] = array(
					'date' 		=> (string)$oElement->date,
					'title'		=> (string)$oElement->title,
					'teaser'	=> (string)$oElement->teaser,
					'pictures'	=> $aPictures
					);
		}
		return $aConfig;
	}
	
	public function debug() {
		echo "<pre>";
		var_dump($this->_data);
		echo "</pre>";
	}
	public function getNews($sDate = NULL) {
		if($sDate === NULL) return $this->_data;
		foreach($this->_data as $aData) {
			if($aData['date'] == $sDate) {
				return array($aData);
			}
		}
		return array();
	}
	public function current () {
		return $this->_data[$this->position];
	}
	public function key () {
		return $this->position;
	}
	public function next () {
		++$this->position;
	}
	public function rewind () {
		$this->position = 0;
	}
	public function valid () {
		return isset($this->_data[$this->position]);
	}
	
	
}