<?php

/** @var $controller Controller */
class UserPanelWidget extends CWidget
{
    public function run()
    {
        /** @var $controller Controller */
        $controller = $this->controller;
        $this->render('userpanelwidgetview');
    }
}