<?php

class Application_Model_GuestbookMapper {

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
            $this->setDbTable('Application_Model_DbTable_Guestbook');
        }
        return $this->_dbTable;
    }

    public function save(Application_Model_Guestbook $guestbook) {

        $data = array(
            'name' => $guestbook->getName(),
            'comment' => $guestbook->getComment(),
            'created' => date('Y-m-d H:i:s'),
            'newsdate' => $guestbook->getNewsdate(),
            'active' => $guestbook->getActive()
        );
        if (null === ($id = $guestbook->getId())) {
            unset($data['id']);
            try {
                $this->getDbTable()->insert($data);
            } catch (Exception $ex) {
                
            }
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function delete(Application_Model_Guestbook $guestbook) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $guestbook->getId());
        $this->getDbTable()->delete($where);
    }

    public function find($id, Application_Model_Guestbook $guestbook) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();

        $guestbook->setId($row->id)
                ->setName($row->name)
                ->setComment($row->comment)
                ->setCreated($row->created)
                ->setNewsdate($row->newsdate)
                ->setActive($row->active);

        return $guestbook;
    }

    public function fetchAll() {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Guestbook();
            $entry->setId($row->id)
                    ->setName($row->name)
                    ->setComment($row->comment)
                    ->setCreated($row->created)
                    ->setNewsdate($row->newsdate)
                    ->setActive($row->active);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function findComments($newsdate) {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $resultSet = $this->getDbTable()->fetchAll();
        } else {
            $resultSet = $this->getDbTable()->fetchAll(
                    "newsdate = '$newsdate' AND (active = 1)"
            );
        }
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Guestbook();
            $entry->setId($row->id)
                    ->setName($row->name)
                    ->setComment($row->comment)
                    ->setCreated($row->created)
                    ->setNewsdate($row->newsdate)
                    ->setActive($row->active);
            $entries[] = $entry;
        }
        return $entries;
    }

}
