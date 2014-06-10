<?php

class Application_Model_Advice extends Zend_Db_Table_Abstract {

    protected $_name = 'advices';

    function story($story) {
        $user_data = Zend_Auth::getInstance()->getStorage()->read();
        if ($user_data->type == 'parent') {
            $select = $this->select()->setIntegrityCheck(false)->from('advices')->joinInner('stories', 'level=story_id')->where("path='$story'");
            return $this->fetchAll($select)->toArray();
        }
    }

}
