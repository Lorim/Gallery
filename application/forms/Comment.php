<?php


class Application_Form_Comment extends Zend_Form
{
public function init()
    {
        // Setzt die Methode for das Anzeigen des Formulars mit POST
        $this->setMethod('post');
 
        // Ein Email Element hinzufügen
        $this->addElement('text', 'name', array(
            'label'      => 'Dein Name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
        ));
 
        // Das Kommentar Element hinzufügen
        $this->addElement('textarea', 'comment', array(
            'label'      => 'Bitte ein Kommentar:',
            'required'   => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 2000))
                )
        ));
        $this->addElement('hidden', 'newsid', array(
        		'required'   => false,
        		'filters'    => array('StringTrim'),
        		)
        );
        $this->getElement('newsid')->clearDecorators();
        
        $this->addElement('hidden', 'active', array(
        		
        		));
        
 		/*
        // Ein Captcha hinzufügen
        $this->addElement('captcha', 'captcha', array(
            'label'      => "Bitte die 5 Buchstaben eingeben die unterhalb "
                          . "angezeigt werden:",
            'required'   => true,
            'captcha'    => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));
 		*/
        // Den Submit Button hinzufügen
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Absenden',
        ));
 
        /*
        // Und letztendlich etwas CSRF Protektion hinzufügen
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
        */
    }
	
}