<?php

class UserController extends Zend_Controller_Action {

    private $sess = null;
    private $user_data = null;

    public function init() {
        $this->user_data = Zend_Auth::getInstance()->getStorage()->read();
        $this->sess = new Zend_Session_Namespace("Zend_Auth");
        $authorization = Zend_Auth::getInstance();
        
        $action = $this->getRequest()->getActionName();
        $_SESSION['action'] = $action;
        if ($action == 'displaystory') {
            if (!$authorization->hasIdentity()) {
                $this->redirect("/user/login");
            }
        }
    }

    public function indexAction() {
        
    }

    public function signupAction() {
        $signUpForm = new Application_Form_Signup();
        $this->view->signUpForm = $signUpForm;

        if ($this->getRequest()->isPost()) {
            if ($signUpForm->isValid($this->getRequest()->getParams())) {
                if ($this->_request->getParam('cpassword') === $this->_request->getParam('password')) {
                    $user = new Application_Model_User();


                    try {
                        $user->signUp($this->_request->getParams());
                        $result = new Application_Model_Result();
                        $result->signup($this->_request->getParam('email'));
                        $this->redirect("user/login");
                    } catch (Exception $e) {
                        echo "هذا الايميل موجود بالفعل";
                    }
                } else {
                    echo 'Check the password Please!';
                }
            }
        }
    }

    public function loginAction() {
        $logIn = new Application_Form_Login();
        $this->view->signInForm = $logIn;

        if ($this->_request->isPost()) {
            if ($logIn->isValid($this->getRequest()->getParams())) {
                $this->view->logInForm = $logIn;
                $data = $this->_request->getParams();

                $db = Zend_Db_Table::getDefaultAdapter();
                $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'email', 'password');
                $authAdapter->setIdentity($data['email']);
                $authAdapter->setCredential(md5($data['password']));
                $result = $authAdapter->authenticate();

                if ($result->isValid()) {

                    $auth = Zend_Auth::getInstance();
                    $storage = $auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject(array('admin','email', 'id', 'fname', 'lname', 'type')));

                    $_SESSION['email'] = $data['email'];
                    //redirect to home page////////////////////////////////
                    $this->redirect('user/index');
                }
            }
        }
    }

    public function liststoriesAction() {
        $story = new Application_Model_Story();
        $_SESSION['cat_id'] = $this->_request->getParam('cat_id');
        $this->view->stories = $story->liststories($this->_request->getParam('cat_id'));
        
        if (($this->_request->getParam('lock'))){
            $this->view->lock = $this->_request->getParam('lock');
        }
        
    }

    public function displaystoryAction() {
        
        $_SESSION['story'] = $this->_request->getParam('story');
        $this->view->story = $_SESSION['story'];
        
        if ($this->user_data->type == 'parent' || $this->user_data->admin == 'true') {
            $user = new Application_Model_Advice();
            $this->view->advices = $user->story($this->_request->getParam('story'));
        } else {
            
            $levels = new Application_Model_Result();
            $userLevel = $levels->getUserLevel();
            
            $story = new Application_Model_Story();
            $storyLevel = $story->getStoryLevel();
            
            if ($userLevel < $storyLevel){
                $this->redirect("user/liststories/cat_id/".$_SESSION['cat_id']."/lock/true");
            }
        }     
    }

    public function quizAction() {
        if (isset($_SESSION['story'])){
            $story = new Application_Model_Story();
            $this->view->quizType = $story->getQuizType($_SESSION['story']);

            $quiz = new Application_Model_Quiz();
            $this->view->quiz = $quiz->diplayquiz($_SESSION['story']);
        }
        else{
            $this->redirect("user/");
        }
    }

    public function resultAction() {
        if ($this->_request->isPost()) {
            $result = new Application_Model_Result();
            $this->view->result = $result->result($this->getRequest()->getParams());
        }
    }

    public function evaluatesiteAction() {
        if ($this->_request->isPost()) {
            $site = new Application_Model_Evaluatesite();
            $site->evaluatesite($this->getRequest()->getParams());
            $this->redirect("user/");
        }
    }

    public function evaluatechildAction() {
        if ($this->_request->isPost()) {
            $child = new Application_Model_Evaluatechild();
            $child->evaluatechild($this->getRequest()->getParams());
            $this->redirect("user/");
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->redirect("user/login");
    }
    
    public function displaystepsAction() {
        $_SESSION['story'] = $this->_request->getParam('story');
        $story = new Application_Model_Story();
        $this->view->stories = $story->getStaticStory();
    }

}
