<?php
class Application_Model_Guestbook
{
    protected $_comment;
    protected $_created;
    protected $_name;
    protected $_id;
    protected $_newsdate;
    protected $_active;
 
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Ungültige Guestbook Eigenschaft');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Ungültige Guestbook Eigenschaft');
        }
        return $this->$method();
    }
 
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
 
    public function setComment($text)
    {
        $this->_comment = (string) $text;
        return $this;
    }
 
    public function getComment()
    {
        return $this->_comment;
    }
 
    public function setName($name)
    {
        $this->_name = (string) $name;
        return $this;
    }
 
    public function getName()
    {
        return $this->_name;
    }
 
    public function setCreated($ts)
    {
        $this->_created = $ts;
        return $this;
    }
 
    public function getCreated()
    {
        return $this->_created;
    }
 
    public function getCreateddate()
    {
        return substr($this->_created, 0, 10);
    }
    public function getCreatedtime()
    {
        return substr($this->_created, 11);
    }
    
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
    }
    
    public function setNewsdate($newsdate)
    {
    	$this->_newsdate = (string) $newsdate;
    	return $this;
    }
    
    public function getNewsdate()
    {
    	return $this->_newsdate;
    }
    
    public function setActive($active)
    {
    	$this->_active = (string) $active;
    	return $this;
    }
    
    public function getActive()
    {
    	return $this->_active;
    }
}
