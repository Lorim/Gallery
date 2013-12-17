<?php

class Application_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
    	if (PHP_SAPI == 'cli') return;
        $acl = Zend_Registry::get("acl");
        $auth = Zend_Auth::getInstance();
        $identity = $auth->hasIdentity() ? $auth->getIdentity()->group : "guest";
        
        $resource = $request->getControllerName();
        $privilege = $request->getActionName();

        /*
         * pass article updates
         */
        if($resource == "cli") return;
        
        if(! $acl->has($resource)) {
        	$view = new Zend_View();
        	$view->setScriptPath(APPLICATION_PATH . "/views/mail");
			$view->message = 'Application error';
			$view->request   = $request;
			$view->user = Zend_Auth::getInstance()->getIdentity();
			$view->exception = $request;
			
			
			$html = $view->render('Acl_missing_resource.phtml');

        }

        if (! $acl->isAllowed($identity, $resource, $privilege)) {
            throw new Zend_Controller_Action_Exception('Not found', 404);
        }
    }
}