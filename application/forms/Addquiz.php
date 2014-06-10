<?php

class Application_Form_Addquiz extends Zend_Form
{

    public function init()
    {
        
        $img =  new Zend_Form_Element_File('img');
        $img->setRequired();
        $img->setDestination("/var/www/zendapp/images");
        $img->addValidator('Count', false, 1);
        $img->addValidator('Size', false, 102400);
        $img->addValidator('Extension', false, 'jpg,png,gif');
        $img->setValueDisabled(true);
        $this->setAttrib('enctype', 'multipart/form-data');
        $img->setLabel('Image: ');
        

        $submit = new Zend_Form_Element_Submit('اضف');//btn btn-warning 
        $submit->setOptions(array('class'=>'btn btn-success active btn-default'));
        //$element = new Zend_Form_Element_Text('ans1_1');
        
        //$this->setAttrib('enctype', 'multipart/form-data');
        $this->addElements(array($img,$submit));
    }


}

