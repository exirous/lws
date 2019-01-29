<?php

class AwardsController extends Controller
{
    const MESSAGE_LIMIT = 12;

    var $layout = "json";

    public function actions()
    {
        return array(
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionGet()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('awardId', 0);
                $this->returnSuccess($this->_getAward($id));
                break;
        }
    }


    public function actionSave()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $awardId = $request->getRawBodyParam('id', 0);
                $name = $request->getRequiredRawBodyParam('name', '');
                $sub_name = $request->getRequiredRawBodyParam('sub_name', '');
                $award_replace_id = $request->getRawBodyParam('award_replace_id', null);
                $only_one_allowed = intval($request->getRawBodyParam('only_one_allowed', 1));
                $temp_image = $request->getRawBodyParam('temp_image', '');
                $top = intval($request->getRequiredRawBodyParam('top', 100));
                $left = intval($request->getRequiredRawBodyParam('left', 100));
                $order = intval($request->getRequiredRawBodyParam('order', 1));
                $this->returnSuccess($this->_saveAward($awardId, $name, $sub_name, $award_replace_id, $only_one_allowed, $top, $left, $order, $temp_image));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionList()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess($this->_getAwards());
                break;
            default:
                $this->returnError();
        }
    }


    public function _getTopic($id)
    {
        $topic = ForumTopic::model()->findByPk($id);
        if (!$topic)
            $this->returnError();
        $topic = $topic->getRenderAttributes();
        $topic['limit'] = self::MESSAGE_LIMIT;
        return $topic;
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error)
        {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    private function _getAward($id)
    {
        return Award::model()->findByPk($id)->getAttributes();
    }

    private function _saveAward($awardId, $name, $sub_name, $award_replace_id, $only_one_allowed, $top, $left, $order, $temp_image)
    {
        if ($awardId == 0)
            $award = new Award();
        else
            $award = Award::model()->findByPk($awardId);
        if (!$award)
            throw new Exception('Award not found');

        $award->name = $name;
        $award->sub_name = $sub_name;
        $award->award_replace_id = $award_replace_id;
        $award->only_one_allowed = $only_one_allowed;
        $award->top = $top;
        $award->left = $left;
        $award->order = $order;
        $award->save();
        $srcFile = dirname(Yii::app()->basePath) . '/img/temp/' . $temp_image;
        $destinationFile = dirname(Yii::app()->basePath) . '/img/awards/' . $award->id . '.png';
        if ($temp_image && file_exists($srcFile)) {
            @rename($srcFile, $destinationFile);
            chmod($destinationFile, 0777);
        }

        return $award->getAttributes();
    }

    private function _getAwards()
    {
        $awards = [];
        foreach (Award::model()->findAll() as $award)
        {
            $awardAttrs = $award->getShortAttributes();
            $awards[] = $awardAttrs;
        }
        return $awards;
    }

    public function actionUpload()
    {
        try {
            if (Yii::app()->user->isGuest)
                throw new Exception("ЭЭ??");

            if ((Yii::app()->user->model->id != '14') && (Yii::app()->user->model->id != '1'))
                throw new Exception('Permission denied');

            $src = substr(md5(time()), 0, 10);
            $file = CUploadedFile::getInstanceByName('file');
            rename($file->tempName, dirname(Yii::app()->basePath) . '/img/temp/' . $src);
            chmod(dirname(Yii::app()->basePath) . '/img/temp/' . $src, 0777);
            $this->returnSuccess($src);
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
    }

}