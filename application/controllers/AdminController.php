<?php

class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function commentsAction()
    {
		
    	$guestbook = new Application_Model_GuestbookMapper();
    	$this->view->entries = $guestbook->fetchAll();

    }
    
    public function newsAction()
    {
        $oNews = new Application_Model_NewsMapper();
        $this->view->entries = $oNews->fetchAll();        
    }
    
    public function updatenewsAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $sField = $this->_request->getParam('name');
        $sValue = $this->_request->getParam('value');
        
        $oNews = new Application_Model_NewsMapper();
        $oNewsentry = $oNews->find($this->_request->getParam('pk'), new Application_Model_News());
        $oNewsentry->{$sField} = $sValue;
        $oNews->save($oNewsentry);
        
    }
}

