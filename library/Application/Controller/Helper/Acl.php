<?php

class Application_Controller_Helper_Acl {

    public $acl;

    public function __construct() {
        $this->acl = new Zend_Acl();
        $this->setRoles();
        $this->setResources();
        $this->setPrivileges();
        $this->setAcl();
    }

    private function setRoles() {
        $this->acl->addRole(new Zend_Acl_Role("guest"));
        $this->acl->addRole(new Zend_Acl_Role("admin"));
    }

    private function setResources() {
        $this->acl
                ->add(new Zend_Acl_Resource("index"))
                ->add(new Zend_Acl_Resource("user"))
                ->add(new Zend_Acl_Resource("error"))
                ->add(new Zend_Acl_Resource("admin"));
    }

    private function setPrivileges() {
        //$this->acl->allow(null,null);
        $this->acl->allow(
                "guest", array("index"), null);

        $this->acl->allow(
                null, array(
            "user",
            "error"
                )
        );

        $this->acl->allow("admin");
    }

    private function setAcl() {
        Zend_Registry::set("acl", $this->acl);
    }

}
