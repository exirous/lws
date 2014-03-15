<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    public function __construct($id, $module = null)
    {
        if (Yii::app()->getRequest()->getQuery('lang'))
            Yii::app()->params['language'] = Yii::app()->getRequest()->getQuery('lang');

        //Yii::app()->getClientScript()->registerScript('',CClientScript::POS_READY);
        return parent::__construct($id, $module);
    }

    public function beforeAction($action)
    {
        if (isset($_COOKIE['letmepeek']) || isset($_GET['iwanttotakeapeek']))
        {
            if (isset($_GET['iwanttotakeapeek']))
                setcookie('letmepeek', '1', time() + 60 * 60 * 24 * 365, '/');
            return true;
        }
        else
        {
            die(file_get_contents('workinprogress.html'));
        }
    }


    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/main';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
}