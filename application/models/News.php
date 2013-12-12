<?php
class Application_Model_News
{
    protected $_id;
    protected $_created;
    protected $_title;
    protected $_teaser;
    protected $_path;
    protected $_active;
    protected $_pictures;
 
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
            throw new Exception('Ungültige News Eigenschaft');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Ungültige News Eigenschaft');
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
 
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
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
    
    public function setTitle($text)
    {
        $this->_title = (string) $text;
        return $this;
    }
 
    public function getTitle()
    {
        return $this->_title;
    }
 
    public function setTeaser($name)
    {
        $this->_teaser = (string) $name;
        return $this;
    }
 
    public function getTeaser()
    {
        return $this->_teaser;
    }
    
    public function setActive($active)
    {
        $this->_active = $active;
        return $this;
    }
    
    public function getActive()
    {
        return $this->_active;
    }
    public function setPath($path)
    {
    	$this->_path = (string) $path;

        $aList = glob(APPLICATION_PATH.'/../public/images/'.$path."/*.jpg");
        $aPictures = array();
        foreach($aList as $sPicture) {
            $aPictures[] = array(
               "original" => "/images/" . $path . "/" . basename($sPicture),
               "thumb" => Application_Model_Thumb::getThumb("/images/" . $path . "/" . basename($sPicture))
            );
            
        }
        $this->_pictures = $aPictures;
    	return $this;
    }
    
    public function getPath()
    {
    	return $this->_path;
    }
    
    public function getPictures()
    {
        return (array)$this->_pictures;
    }
    
    static public function getPaths()
    {
        $sBasepath = APPLICATION_PATH . '/../public/images/*';
        return glob($sBasepath, GLOB_ONLYDIR);
    }
}
