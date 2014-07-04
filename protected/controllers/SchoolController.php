<?php

class SchoolController extends Controller
{

    public function actionItem()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_POST:
                $text = $request->getRequiredRawBodyParam('text', '', AHttpRequest::PARAM_TYPE_STRING);
                $title = $request->getRequiredRawBodyParam('title', '', AHttpRequest::PARAM_TYPE_STRING);
                $this->returnSuccess($this->_addNews($title, $text));
                break;
            default:
                $this->returnError();
        }
    }


    public function actionMaterials()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_GET:
                $subjectSlug = $request->getParam('slug', 'flight_basics');
                $this->returnSuccess($this->_getMaterials($subjectSlug));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionMaterial()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('id');
                $this->returnSuccess($this->_getMaterial($id));
                break;
            case AHttpRequest::METHOD_POST:
                $id = $request->getRequiredRawBodyParam('id');
                $text = $request->getRequiredRawBodyParam('text');
                $title = $request->getRequiredRawBodyParam('title');
                $slug = $request->getRequiredRawBodyParam('slug');
                $this->returnSuccess($this->_saveMaterial($id, $title, $text, $slug));
                break;

            default:
                $this->returnError();
        }
    }


    public function actionTest()
    {


        //print $parser->getAsHtml();
    }

    public function _getMaterial($id)
    {
        $material = Material::model()->findByPk($id);
        if (!$material)
            $this->returnError('Material not found');
        return $material->renderAttributes;
    }

    public function _saveMaterial($id, $title, $text, $slug)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->canMakeOrders())
            return null;

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $subject = MaterialSubject::model()->findByAttributes(['slug' => $slug]);
            if (!$subject)
                throw new Exception('Такого предмета нету');

            if ($id > 0)
                $material = Material::model()->findByPk($id);
            else {
                $material = new Material();
                $material->subject_id = $subject->id;
            }

            $material->text = $text;
            $material->title = $title;
            if (!$material->save())
                throw new Exception($material->getErrorsString());
            $transaction->commit();
            return $material->renderAttributes;
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return null;
    }

    public function _getMaterials($subjectSlug)
    {

        $subject = MaterialSubject::model()->findByAttributes(['slug' => $subjectSlug]);
        if (!$subject)
            return [];


        return $subject->getRenderAttributes();
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }
}