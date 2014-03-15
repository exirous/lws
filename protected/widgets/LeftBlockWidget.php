<?php

/** @var $controller Controller */
class LeftBlockWidget extends CWidget
{
    public function run()
    {
        /** @var $controller Controller */
        $controller = $this->controller;
        $categories = Category::model()->roots()->findAll();
        $this->render('leftblockwidgetview',compact('categories'));
    }
}