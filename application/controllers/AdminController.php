<?php

class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function commentsAction()
    {
		
    	$guestbook = new Application_Model_CommentMapper();
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
    
    public function addnewsAction()
    {
        $this->_helper->layout()->disableLayout();
        try{
            $oNews = new Application_Model_NewsMapper();
            $oEntry = new Application_Model_News();
            $oEntry->setTitle($this->_request->getParam('title'));
            $oEntry->setTeaser($this->_request->getParam('teaser'));
            $oEntry->setCreated($this->_request->getParam('created'));
            $oEntry->setPath($this->_request->getParam('path'));
            $oEntry->setActive($this->_request->getParam('active', 0));
            
            $oNews->save($oEntry);
        }  catch (Exception $e) {
            echo $this->view->json(array("success" => false));
            //Zend_Debug::dump($e);
        }
        echo $this->view->json(array("success" => true, "id" => 1));
    }
    
    public function galleryAction()
    {
        $oGallery = new Application_Model_GalleryMapper();
        $this->view->entries = $oGallery->fetchAll(); 
    }
    
    public function updategalleryAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $sField = $this->_request->getParam('name');
        $sValue = $this->_request->getParam('value');
        
        $oGallery = new Application_Model_GalleryMapper();
        $oGalleryentry = $oGallery->find($this->_request->getParam('pk'), new Application_Model_Gallery());
        $oGalleryentry->{$sField} = $sValue;
        $oGallery->save($oGalleryentry);
    }
    
    public function addgalleryAction()
    {
        $this->_helper->layout()->disableLayout();
        try{
            $oNews = new Application_Model_GalleryMapper();
            $oEntry = new Application_Model_Gallery();
            $oEntry->setTitle($this->_request->getParam('title'));
            $oEntry->setCreated($this->_request->getParam('created'));
            $oEntry->setPath($this->_request->getParam('path'));
            $oEntry->setActive($this->_request->getParam('active', 0));
            
            $oNews->save($oEntry);
        }  catch (Exception $e) {
            echo $this->view->json(array("success" => false));
            //Zend_Debug::dump($e);
        }
        echo $this->view->json(array("success" => true, "id" => 1));
    }
    
    public function getpathsAction()
    {
        $this->_helper->layout()->disableLayout();
        $aPaths = Application_Model_News::getPaths();
        $aReturn = array();
        foreach($aPaths as $sPath) {
            $sPath = basename($sPath);
            $aReturn[] = array($sPath => $sPath);
        }
        
        echo $this->view->json($aReturn);
    }
}

