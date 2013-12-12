<?php
class Application_Model_Gallery
{
    protected $_id;
    protected $_created;
    protected $_title;
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
            throw new Exception('Ungültige Gallery Eigenschaft');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Ungültige Gallery Eigenschaft');
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
               "thumb" =>  $this->getThumb("/images/" . $path . "/" . basename($sPicture))
            );
            
        }
        $this->_pictures = $aPictures;
    	return $this;
    }
    
    public function getPictures()
    {
        return $this->_pictures;
    }
    
    private function getThumb($sPath)
    {
        $path = dirname(APPLICATION_PATH."/../public" .$sPath);
        $file = basename($sPath);
        if(!file_exists($path."/".$file)) return;
        try {
            if(!file_exists($path."/thumb")) {
                mkdir($path."/thumb");
            }
            if(!file_exists($path. "/thumb/".$file)) {
                $thumb = Application_Thumb_Factory::create($path."/".$file); 
                $thumb->resize(188,280)->save($path. "/thumb/".$file);
            }
        }  catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
            return $sPath;
        }
        return dirname($sPath). "/thumb/".$file;
    }
    
    public function getPath()
    {
    	return $this->_path;
    }
    
    static public function getPaths()
    {
        $sBasepath = APPLICATION_PATH . '/../public/images/*';
        return glob($sBasepath, GLOB_ONLYDIR);
    }
}
