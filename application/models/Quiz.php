<?php

class Application_Model_Quiz extends Zend_Db_Table_Abstract 
{
    protected $_name = 'quiz';
    
    function diplayquiz($story) { 
        $select = $this->select()->from('quiz')->where("story_path='$story'");
        return $this->fetchAll($select)->toArray();
        
    }
    
    
    function addQuiz($data) {
        $row = $this->createRow();
        $row->story_path = $data['story_path'];
        $row->question = $data['q1'];      
        $row->answers = $data['ans1_1'].",".$data['ans2_1'].",".$data['correctAns1'];
        return $row->save();
        
        
    }

}

