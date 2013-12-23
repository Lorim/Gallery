<?php
class Application_Auth_Adapter implements Zend_Auth_Adapter_Interface {
	protected $_username;
	protected $_password;
	public function __construct($username, $password) {
		$this->_username = $username;
		$this->_password = $password;
	}
	
	public function authenticate() {
		$users = array (
				'se519' => array(
						'pwd' => '07318se1907',
						'group' => 'admin'
						)
		);

		if(!isset($users[$this->_username])) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_username);
		}
		if($users[$this->_username]['pwd'] !== $this->_password) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,$this->_username);
		}
		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, (object) $users[$this->_username]);
		
	}
}