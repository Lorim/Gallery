<?php

class Application_Model_NewsMapper {

    protected $_dbTable;

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
            $this->setDbTable('Application_Model_DbTable_News');
        }
        return $this->_dbTable;
    }

    public function save(Application_Model_News $news) {

        $data = array(
            'created' => $news->getCreated(),
            'title' => $news->getTitle(),
            'teaser' => $news->getTeaser(),
            'path' => $news->getPath()
        );
        if (null === ($id = $news->getId())) {
            unset($data['id']);
            try {
                $this->getDbTable()->insert($data);
            } catch (Exception $ex) {
                
            }
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function delete(Application_Model_News $news) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $news->getId());
        $this->getDbTable()->delete($where);
    }

    public function find($id, Application_Model_News $news) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();

        $news->setId($row->id)
                ->setCreated($row->created)
                ->setTitle($row->title)
                ->setTeaser($row->teaser)
                ->setPath($row->path);

        return $news;
    }

    public function fetchAll() {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_News();
            $entry->setId($row->id)
                    ->setCreated($row->created)
                    ->setTitle($row->title)
                    ->setTeaser($row->teaser)
                    ->setPath($row->path);
                $entries[] = $entry;
        }
        return $entries;
    }

    public function findNews($newsdate) {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $resultSet = $this->getDbTable()->fetchAll();
        } else {
            $resultSet = $this->getDbTable()->fetchAll(
                    "created = '$newsdate'"
            );
        }
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_News();
            $entry->setId($row->id)
                    ->setCreated($row->created)
                    ->setTitle($row->title)
                    ->setTeaser($row->teaser)
                    ->setPath($row->path);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function findPictures($path)
    {
        $sPath = $news->getPath();
        $aList = glob(APPLICATION_PATH.'/../public/images/'.$sPath."/*.jpg");
        foreach($aList as $sPicture) {
            $aPictures[] = "/images/" . $sPath . "/" . basename($sPicture);
        }
        return $aPictures;
    }
}
