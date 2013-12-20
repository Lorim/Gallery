<?php

class Application_Model_GalleryMapper {

    protected $_dbTable;
    protected $_dbPreviewTable;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Ungültiges Table Data Gateway angegeben');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Gallery');
        }
        return $this->_dbTable;
    }

    public function setDbPreviewTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Ungültiges Table Data Gateway angegeben');
        }
        $this->_dbPreviewTable = $dbTable;
        return $this;
    }
    
    public function getDbPreviewTable() {
        if (null === $this->_dbPreviewTable) {
            $this->setDbpreviewTable('Application_Model_DbTable_Gallerypreview');
        }
        return $this->_dbPreviewTable;
    }
    
    public function save(Application_Model_Gallery $gallery) {

        $data = array(
            'created' => $gallery->getCreated(),
            'title' => $gallery->getTitle(),
            'path' => $gallery->getPath(),
            'active' => $gallery->getActive(),
            'tag' => $gallery->getTag()
        );
        if (null === ($id = $gallery->getId())) {
            unset($data['id']);
            try {
                $this->getDbTable()->insert($data);
            } catch (Exception $ex) {
                
            }
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
            $this->getDbPreviewTable()->delete(
                    $this->getDbPreviewTable()->getAdapter()->quoteInto('gid = ?', $gallery->getId())
                    );
            foreach($gallery->getPreviews() as $pid => $pic) {
                if($pic == "") continue;
                $data = array(
                    'gid' => $gallery->getId(),
                    'pid' => $pid,
                    'picture' => $pic
                );
                try {
                $this->getDbPreviewTable()->insert($data);
                } catch (Exception $e) {
                    Zend_Debug::dump($e);
                }
            }
        }
    }
    
    public function delete(Application_Model_Gallery $gallery) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $gallery->getId());
        $this->getDbTable()->delete($where);
    }

    public function find($id, Application_Model_Gallery $gallery) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        
        $gallery->setId($row->id)
                ->setCreated($row->created)
                ->setTitle($row->title)
                ->setPath($row->path)
                ->setTag($row->tag)
                ->setActive($row->active)
                ->setPreviews($this->findPreviews($row->id));
        
        return $gallery;
    }

    public function fetchAll() {
        $resultSet = $this->getDbTable()->fetchAll();

        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Gallery();
            $entry->setId($row->id)
                    ->setCreated($row->created)
                    ->setTitle($row->title)
                    ->setPath($row->path)
                    ->setTag($row->tag)
                    ->setActive($row->active)
                    ->setPreviews($this->findPreviews($row->id));
                $entries[] = $entry;
        }
        return $entries;
    }

    public function findGalleries($tag = NULL) {
        $where = "";
        if($tag !== NULL) {
            $where = $this->getDbTable()->getAdapter()->quoteInto('tag = ?', $tag);
        }
        try{
        if (Zend_Auth::getInstance()->hasIdentity()) {
            if($tag !== NULL) {
            $resultSet = $this->getDbTable()->fetchAll(
                    $this->getDbTable()->select()->order("created DESC")->where($where)
                    );
            } else {
                $resultSet = $this->getDbTable()->fetchAll(
                    $this->getDbTable()->select()->order("created DESC")
                    );
            }
        } else {
            if($tag !== NULL) {
                $resultSet = $this->getDbTable()->fetchAll(
                    $this->getDbTable()
                        ->select()
                        ->order("created DESC")
                        ->where("active = 1")
                        ->where($where)
                    );
            } else {
                $resultSet = $this->getDbTable()->fetchAll(
                    $this->getDbTable()
                        ->select()
                        ->order("created DESC")
                        ->where("active = 1")
                    );    
            }
        }
        } catch(Exception $e) {
            Zend_Debug::dump($e);
        }
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Gallery();
            $entry->setId($row->id)
                    ->setCreated($row->created)
                    ->setTitle($row->title)
                    ->setPath($row->path)
                    ->setActive($row->active)
                    ->setTag($row->tag)
                    ->setPreviews($this->findPreviews($row->id));
            $entries[] = $entry;
        }
        return $entries;
    }

    public function findPreviews($iGalleryid)
    {
        $resultSet = $this->getDbPreviewTable()->fetchAll(
                    $this->getDbPreviewTable()
                        ->select()
                        ->where("gid = " . $iGalleryid)
                    );
        $entries = array();
        
        foreach ($resultSet as $row) {
            $entries[$row->pid] = $row->picture;
        }
        return $entries;
    }
}
