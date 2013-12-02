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
	
}

