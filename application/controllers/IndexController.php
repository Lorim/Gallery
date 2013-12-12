<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        
        $oNews = new Application_Model_NewsMapper();
        $paginator = Zend_Paginator::factory($oNews->findNews());
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(2);
        
        $this->view->news = $paginator;
        
        $form = new Application_Form_Comment();
        $form->getElement('active')->setValue(0);
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            if ($form->isValid($request->getParams())) {
                $comment = new Application_Model_Comment($form->getValues());
                $mapper = new Application_Model_CommentMapper();
                $mapper->save($comment);
                $form->reset();
            }
            $this->view->commentsubmit = true;
        }
        
        $guestbook = new Application_Model_CommentMapper();
        $this->view->comments = $guestbook;
        $this->view->form = $form;
    }
    
    public function newsAction() {
        $oNews = new Application_Model_NewsMapper();
        $oEntry = new Application_Model_News();
        $oNewsEntry = $oNews->find($this->_getParam('id'), $oEntry);
        
        $this->_helper->layout()->getView()->headTitle($oNewsEntry->getTitle(), 
                Zend_View_Helper_Placeholder_Container_Abstract::SET);
        
        $this->view->news = $oNewsEntry;

        $form = new Application_Form_Comment();
        $form->getElement('active')->setValue(0);
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getParams())) {
                $comment = new Application_Model_Comment($form->getValues());
                $mapper = new Application_Model_CommentMapper();
                $mapper->save($comment);
                $form->reset();
            }
            $this->view->commentsubmit = true;
        }
        
        $guestbook = new Application_Model_CommentMapper();
        $this->view->comments = $guestbook;
        $this->view->form = $form;
        
    }

    public function galleryAction() {
        $oGallery = new Application_Model_GalleryMapper();
        $iGalleryid = $this->_request->getParam('id');
        if($iGalleryid) {
            $this->_helper->viewRenderer->setRender('galleryid'); 
            $this->view->entry = $oGallery->find($iGalleryid, new Application_Model_Gallery);
        } else {
            $this->view->entry = $oGallery->findGalleries();
        }
    }


    public function loginAction() {
        $form = new Application_Form_Login();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $auth = Zend_Auth::getInstance();

                $result = $auth->authenticate(new Application_Auth_Adapter($form->getValue('name'), $form->getValue('password')));
                switch ($result->getCode()) {
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        echo 'user name is unvalid';
                        break;
                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        echo 'unvalid password provided';
                        break;
                    default:
                        $this->_helper->redirector('index', 'index', 'default');
                        break;
                }
            }
        }

        $this->view->form = $form;
    }

    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();

        $this->_helper->redirector->gotoUrl($this->getRequest()->getServer('HTTP_REFERER'));
    }

    public function editnewsAction() {
        $gb = new Application_Model_CommentMapper();
        $comment = $gb->find($this->_getParam('comment'), new Application_Model_Comment());

        switch ($this->_getParam('c')) {
            case 'enable':
                $comment->setActive(1);
                $gb->save($comment);
                break;

            case 'disable':
                $comment->setActive(0);
                $gb->save($comment);
                break;
            case 'delete':
                $gb->delete($comment);
                break;
        }

        $this->_helper->redirector->gotoUrl($this->getRequest()->getServer('HTTP_REFERER'));

        $this->_helper->redirector->gotoRoute(
                array(
                    'controller' => 'index',
                    'action' => 'index'
                )
        );
    }

    public function kontaktAction() {
        $form = new Application_Form_Kontakt();

        $request = $this->getRequest();

        if ($request->isPost()) {
            if ($form->isValid($request->getParams())) {
                //$form->reset();
                $view = new Zend_View();
                $view->setScriptPath(APPLICATION_PATH . "/views/mail");


                $view->form = $form->getValues();

                $html = $view->render('kontakt.phtml');
                //Zend_Debug::dump($view);
                //die();
                $mail = new Zend_Mail('UTF-8');
                $mail->setFrom("gallery@se519.de")
                        ->addTo("admin@lonie.de")
                        ->setSubject('Neue Kontaktanfrage von ' . $form->getValue('name'));
                $mail->setBodyHtml($html);
                try {
                    $mail->send();
                } catch (exception $ex) {
                    Zend_Debug::dump($ex);
                }
            }
            $this->view->commentsubmit = true;
        }

        $this->view->form = $form;
    }

}
