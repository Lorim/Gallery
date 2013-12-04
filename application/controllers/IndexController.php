<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        /*
          $guestbook = new Application_Model_GuestbookMapper();
          $this->view->entries = $guestbook->fetchAll();
         */
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
                        $this->_helper->redirector('news', 'index', 'default');
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

    public function newsAction() {


        $oNews = new Application_Model_News();

        $paginator = Zend_Paginator::factory($oNews->getNews($this->_request->getParam('date')));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(1);
        $this->view->news = $paginator;
        $form = new Application_Form_Guestbook();
        $form->getElement('active')->setValue(0);
        $request = $this->getRequest();

        if ($request->isPost()) {
            if ($form->isValid($request->getParams())) {
                $comment = new Application_Model_Guestbook($form->getValues());
                $mapper = new Application_Model_GuestbookMapper();
                $mapper->save($comment);
                $form->reset();
            }
            $this->view->commentsubmit = true;
        }
        $guestbook = new Application_Model_GuestbookMapper();
        $this->view->comments = $guestbook;
        $this->view->form = $form;
    }

    public function editnewsAction() {
        $gb = new Application_Model_GuestbookMapper();
        $comment = $gb->find($this->_getParam('comment'), new Application_Model_Guestbook());

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
                    'action' => 'news'
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
