<?php


class Application_Form_Login extends Zend_Form
{
public function init()
    {
        // Setzt die Methode for das Anzeigen des Formulars mit POST
        $this->setMethod('post');
 
        // Ein Email Element hinzufügen
        $this->addElement('text', 'name', array(
            'label'      => 'Name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
        ));
 
        // Das Kommentar Element hinzufügen
        $this->addElement('password', 'password', array(
            'label'      => 'Passwort:',
            'required'   => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 15))
                )
        ));

        // Den Submit Button hinzufügen
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Login',
        ));
 
        /*
        // Und letztendlich etwas CSRF Protektion hinzufügen
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
        */
    }
	
}