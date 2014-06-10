
<?php

class Application_Model_Story extends Zend_Db_Table_Abstract{

    protected $_name = 'stories';
    
    function liststories($cat_id) {
        $select=$this->select()->setIntegrityCheck(false)->from('stories')->where("cat_id=$cat_id");
        return $this->fetchAll($select)->toArray(); 
    }
    
    
    function getStoryLevel(){
        $storyLevel = null;
        $select = $this->select()->where("path ='".$_SESSION['story']."'");
        $stories = $this->fetchAll($select)->toArray();
            foreach ($stories as $story){
               $storyLevel = $story['level']; 
            }
            
        return $storyLevel;
    }
    
    function getStaticStory(){
        $select = $this->select()->where("path ='".$_SESSION['story']."'");
        $stories = $this->fetchAll($select)->toArray();
        return $stories;
    }
    
    function getQuizType(){
        $select = $this->select()->where("path ='".$_SESSION['story']."'");
        $quizType = $this->fetchAll($select)->toArray();
        return $quizType;
    }
    
    
    function addStory($data) {
        $row = $this->createRow();
        $row->setFromArray($data);
        return $row->save();
    }

    function updateStory($catId, $level, $image, $desc) {
        
        $select = $this->select()->where('cat_id= ?', $catId)->where('level= ?', $level);
        $result = $this->fetchRow($select)->toArray();
        $where[] = new Zend_Db_Expr(
                    $this->getAdapter()->quoteInto('level = ?', $level) . ' AND ' .
                    $this->getAdapter()->quoteInto('cat_id = ?', $catId)
            );
        if (is_null($result['staticImages'])) {
            $data = array('staticImages' => $image);
            $this->update($data, $where);
        } else {
            $tempImg=$result['staticImages'].",".$image; 
            $data = array('staticImages' => $tempImg);
            $this->update($data, $where);
            
        }
        if (is_null($result['staticStep'])) {
            $data = array('staticStep' => $desc);
            $this->update($data, $where);
        } else {
            $tempStp=$result['staticStep'].",".$desc;
            
            $data = array('staticStep' => $tempStp);
            $this->update($data, $where);
            
        }
    }
    
    function updatePathStory($catId, $level, $filename) {
        $where[] = new Zend_Db_Expr(
                    $this->getAdapter()->quoteInto('level = ?', $level) . ' AND ' .
                    $this->getAdapter()->quoteInto('cat_id = ?', $catId)
            );
        
            $data = array('path' => $filename);
            $this->update($data, $where);  
    }
    function MaxLevel ($catId )
    {
       
    $select = $this->select();
    $select->from($this, 'MAX(level) AS level');
   
    $select->where('cat_id= ?', $catId);
    $result = $this->fetchRow($select);
    $result = $this->fetchRow($select)->toArray();
    $maxlevel = $result['level']+1;
    if(is_null($maxlevel))
    {
        $maxlevel= 1;
    }
    return $maxlevel ;    
        
    }

}