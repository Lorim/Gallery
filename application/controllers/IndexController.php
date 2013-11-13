<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $db = Zend_Registry::get('db');
        var_dump( $db->fetchAll("SELECT VERSION();") );
    }

	public function newsAction()
	{

		$oNews = new Application_Model_News($oConfig);
		
		$paginator = Zend_Paginator::factory($oNews->getNews($this->_request->getParam('date')));
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage(1);
		$this->view->news = $paginator;
	}
}

