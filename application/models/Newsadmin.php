<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_News {

    private $_newsfile;
    private $_newsConfig;

    public function __construct() {
        $this->_newsfile = APPLICATION_PATH . '/configs/config.xml';
        $this->_newsConfig = new Zend_Config_Xml($this->_newsfile, 'production');

    }
    
    public function getNews()
    {
        return $this->_newsConfig->toArray();
    }
    
    public function save() {
        $writer = new Zend_Config_Writer_Xml(array(
            'config' => $this->_newsConfig,
            'filename' => $this->_newsfile.".xml"
        ));
        try{
        $writer->write();
        } catch (Exception $e) {
            Zend_Debug::dump($e);
        }
    }
}
