<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->_helper->layout()->getView()->headTitle("Steffen Erdmann", 
                    Zend_View_Helper_Placeholder_Container_Abstract::SET);
        $layout = $this->_helper->layout()->ogurl = 
                $this->getRequest()->getScheme() . 
                '://' . 
                $this->getRequest()->getHttpHost() .
                $this->getRequest()->getRequestUri();
        $this->_helper->layout()->aPreload = array();
    }

    public function indexAction() {
        
        $oGallery = new Application_Model_GalleryMapper();
        $aGallery = $oGallery->findGalleries('Startseite');
        $this->view->entry = false;
        if(count($aGallery)) {
            $this->view->entry = $aGallery[0];
        }
        $layout = $this->_helper->layout();
    }
    
    public function newsAction() {
        $oNews = new Application_Model_NewsMapper();
        $oEntry = new Application_Model_News();
        $layout = $this->_helper->layout();
        $iNewsid = $this->_getParam('id');
        $basePath = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $aPreload = array();
        if($iNewsid) {
            $this->_helper->viewRenderer->setRender('newsid');
            $oNewsEntry = $oNews->find($this->_getParam('id'), $oEntry);
            $this->_helper->layout()->getView()->headTitle($oNewsEntry->getTitle(), 
                    Zend_View_Helper_Placeholder_Container_Abstract::SET);
            $this->view->news = $oNewsEntry;
            $layout->teaser = $oNewsEntry->teaser;
            foreach($oNewsEntry->pictures as $aPic) {
                $aPictures[] = $basePath . $aPic['original'];
                $aPreload[] = $aPic['original'];
            }
            $layout->ogPictures = $aPictures;
        } else {
            $paginator = Zend_Paginator::factory($oNews->findNews());
            $paginator->setCurrentPageNumber($this->_getParam('page'));
            $paginator->setItemCountPerPage(2);
            $this->view->news = $paginator;
            $layout->teaser = $paginator->getItem(1)->teaser;
            foreach($paginator->getItem(1)->pictures as $aPic) {
                $aPictures[] = $basePath . $aPic['original'];
                $aPreload[] = $aPic['original'];
            }
            $layout->ogPictures = $aPictures;
        }
        
        $this->_helper->layout()->aPreload = $aPreload;
        
        $form = new Application_Form_Comment();
        $form->getElement('active')->setValue(0);
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getParams())) {
                $comment = new Application_Model_Comment($form->getValues());
                $mapper = new Application_Model_CommentMapper();
                $mapper->save($comment);
                $form->reset();
                $fm = new Zend_Controller_Action_Helper_FlashMessenger();
                $fm->addMessage('Dein Kommentar war erfolgreich. Er wird<br>angezeigt sobald er freigegeben wurde.');
            }
            $this->view->commentsubmit = true;
        }
        
        $guestbook = new Application_Model_CommentMapper();
        $this->view->comments = $guestbook;
        $this->view->form = $form;
        
    }

    public function galleryAction() {
        $basePath = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $oGallery = new Application_Model_GalleryMapper();
        $iGalleryid = $this->_request->getParam('id');
        $sGalleryTag = $this->_request->getParam('tag');
        if($iGalleryid) {
            $this->_helper->viewRenderer->setRender('galleryid');
            $oGalleryEntry = $oGallery->find($iGalleryid, new Application_Model_Gallery);
            $this->view->entry = $oGalleryEntry;
            $this->_helper->layout()->getView()->headTitle($oGalleryEntry->getTitle(), 
                Zend_View_Helper_Placeholder_Container_Abstract::SET);
            foreach($oGalleryEntry->pictures as $aPic) {
                $aPictures[] = $basePath . $aPic['original'];
            }
            $this->_helper->layout()->ogPictures = $aPictures;
        } else {
            $this->view->entry = $oGallery->findGalleries($sGalleryTag);
            $galleries = $oGallery->findGalleries($sGalleryTag);
            
            if(count($galleries) == 1) {
                $this->_helper->viewRenderer->setRender('galleryid');
                $this->view->entry = $galleries[0];
            } else {
                $this->view->entry = $galleries;
            }
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
                $view->setScriptPath(APPLICATION_PATH . "/views/scripts/mail");
                $view->form = $form->getValues();
                $html = $view->render('kontakt.phtml');
                //Zend_Debug::dump($view);
                //die();
                $mail = new Zend_Mail('UTF-8');
                $mail->setFrom("gallery@se519.de")
                        ->addTo("steffen@se519.de")
                        ->setSubject('Neue Kontaktanfrage von ' . $form->getValue('name'));
                $mail->setBodyHtml($html);
                $fm = new Zend_Controller_Action_Helper_FlashMessenger();
                try {
                    $mail->send();
                    $fm->addMessage('Deine Mail wurde erfolgreich versendet');
                } catch (exception $ex) {
                    $fm->addMessage('Es gab ein Problem beim versenden der Mail.<br>Bitte versuch es später noch einmal.');
                }
            } else {
                $fm = new Zend_Controller_Action_Helper_FlashMessenger();
                $fm->addMessage('In deinen Daten steckt noch ein Fehler. <br>Kontrolliere das rot markierte Feld, korrigiere es und versuchs nochmal.');
            }
            $this->view->commentsubmit = true;
        }

        $this->view->form = $form;
    }
    public function impressumAction() {
        
    }
}
