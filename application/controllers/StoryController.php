<?php

class StoryController extends Zend_Controller_Action {

    private $sess = null;
    private $user_data = null;

    public function init() {
        $this->user_data = Zend_Auth::getInstance()->getStorage()->read();
        $this->sess = new Zend_Session_Namespace("Zend_Auth");
        $authorization = Zend_Auth::getInstance();
        $action = $this->getRequest()->getActionName();
        $_SESSION['action'] = $action;
        if ($this->user_data->admin == 'false') {
            $this->redirect("/user/");
        } else if (!$authorization->hasIdentity()) {
            $this->redirect("/user/login");
        }
    }

    public function indexAction() {
        $categoryModel = new Application_Model_Category();
        $this->view->category = $categoryModel->listAllCategory();
    }

    public function addAction() {
        $sname = $this->_request->getParam('story_name');
        $cat = $this->_request->getParam('cat');
        $type = $this->_request->getParam('type');
        $staticStoryForm = new Application_Form_StaticStory();
        if (!empty($sname) && !empty($cat) && !empty($type)) {
            $story1 = new Application_Model_Story();
            $level = $story1->MaxLevel($cat, $type);
            $imgFolder = "story" . $level."_".$cat;
            $this->view->sname = $sname;
            $this->view->imgFolder = $imgFolder;
            $this->view->cat = $cat;
            $this->view->level = $level;
            $this->view->type = $type;
            $data = array('level' => $level, 'cat_id' => $cat, 'static' => $type, 'name' => $sname, 'path' => $imgFolder);
            $story = new Application_Model_Story();
            $story->addStory($data);
            $staticStoryForm->getElement('fname')->setValue($imgFolder);
            $staticStoryForm->getElement('cat_id')->setValue($cat);
            $staticStoryForm->getElement('level')->setValue($level);
            $this->view->staticStoryForm = $staticStoryForm;
        } elseif ($this->getRequest()->isPost()) {

            if ($staticStoryForm->isValid($this->getRequest()->getParams())) {
                $fname = $this->_request->getParam("fname");
                $category = $this->_request->getParam("cat_id");
                $level = $this->_request->getParam("level");
                $desc = $this->_request->getParam("desc");
                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->addValidator('IsImage', false);
                $files = $upload->getFileInfo();
                foreach ($files as $file => $fileInfo) {
                    if ($upload->isUploaded($file)) {
                        if ($upload->isValid($file)) {
                            $radiationPath = "/var/www/graduation_project/public/images/" . $fname;
                            $extension = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                            $upload->addFilter('Rename', array('target' => $radiationPath . '/static/' . $fileInfo['name'], 'overwrite' => true));
                            $upload->receive($file);
                            $story = new Application_Model_Story();
                            $story->updateStory($category, $level, $fileInfo['name'], $desc);
                        }
                    }
                }
            }
            $check = $this->_request->getParam("checkbox");
            if ($check == 0) {

                $staticStoryForm->getElement('fname')->setValue($fname);
                $staticStoryForm->getElement('cat_id')->setValue($category);
                $staticStoryForm->getElement('level')->setValue($level);
                $this->view->staticStoryForm = $staticStoryForm;
            } else {
                $this->_redirect("story/logic/c/" . $category . "/l/" . $level);
            }
        }
    }

    public function logicAction() {
        $level = $this->_request->getParam("l");
        $category = $this->_request->getParam("c");
        $LogicUpload = new Application_Form_LogicUpload();
        $LogicUpload->getElement('cat_id')->setValue($category);
        $LogicUpload->getElement('level')->setValue($level);
        $this->view->LogicUpload = $LogicUpload;
        
        if ($this->getRequest()->isPost()) {

            if ($LogicUpload->isValid($this->getRequest()->getParams())) {
                $fname = $this->_request->getParam("file");
                $category = $this->_request->getParam("cat_id");
                $level = $this->_request->getParam("level");

                $upload = new Zend_File_Transfer_Adapter_Http();
               // $upload->addValidator('IsFile', false);
                $files = $upload->getFileInfo();
               
                foreach ($files as $file => $fileInfo) {
                    // var_dump($file);
                //exit();
                    if ($upload->isUploaded($file)) {
                        if ($upload->isValid($file)) {
                            $radiationPath = "/var/www/graduation_project/public/" . $fname;
                            $extension = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                            $upload->addFilter('Rename', array('target' => $radiationPath. $fileInfo['name'], 'overwrite' => true));
                            $upload->receive($file);
                            $story = new Application_Model_Story();
                            $story->updatePathStory($category, $level, $fileInfo['name']);
                            $this->_redirect("/user/");
                        }
                    }
                }
            }
        }
    }

    public function addquizAction() {
        $addQuizForm = new Application_Form_Addquiz();
        $this->view->addQuizForm = $addQuizForm;
        if ($this->getRequest()->isPost()) {
            print_r($this->getRequest()->getParams());
            if ($addQuizForm->isValid($this->getRequest()->getParams())) {
                //$addQuizForm->ans1_1->receive();
                /* $addQuizForm->ans1_2->receive();
                  $addQuizForm->ans2_1->receive();
                  $addQuizForm->ans2_2->receive(); */
                //$story = new Application_Model_Quiz();
                //$story->addQuiz($this->getRequest()->getParams());
                echo "valid";
            } else {
                echo "invalid";
            }
        } else {
            echo "get";
        }
    }

}
