<?php

class Application_Model_CommentMapper {

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
            $this->setDbTable('Application_Model_DbTable_Comment');
        }
        return $this->_dbTable;
    }

    public function save(Application_Model_Comment $comment) {

        $data = array(
            'name' => $comment->getName(),
            'comment' => $comment->getComment(),
            'created' => date('Y-m-d H:i:s'),
            'newsid' => $comment->getNewsid(),
            'active' => $comment->getActive()
        );
        if (null === ($id = $comment->getId())) {
            unset($data['id']);
            try {
                $this->getDbTable()->insert($data);
            } catch (Exception $ex) {
                
            }
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function delete(Application_Model_Comment $comment) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $comment->getId());
        $this->getDbTable()->delete($where);
    }

    public function find($id, Application_Model_Comment $comment) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();

        $comment->setId($row->id)
                ->setName($row->name)
                ->setComment($row->comment)
                ->setCreated($row->created)
                ->setNewsid($row->newsid)
                ->setActive($row->active);

        return $comment;
    }

    public function fetchAll() {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Comment();
            $entry->setId($row->id)
                    ->setName($row->name)
                    ->setComment($row->comment)
                    ->setCreated($row->created)
                    ->setNewsid($row->newsid)
                    ->setActive($row->active);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function findComments($newsid) {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $resultSet = $this->getDbTable()->fetchAll(
                    "newsid = '$newsid'"
            );
        } else {
            $resultSet = $this->getDbTable()->fetchAll(
                    "newsid = '$newsid' AND (active = 1)"
            );
        }
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Comment();
            $entry->setId($row->id)
                    ->setName($row->name)
                    ->setComment($row->comment)
                    ->setCreated($row->created)
                    ->setNewsid($row->newsid)
                    ->setActive($row->active);
            $entries[] = $entry;
        }
        return $entries;
    }

}
