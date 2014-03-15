<?php

class AdminController extends Controller
{
    var $activeMenu = '';

    public function beforeAction($action)
    {
        $this->layout = 'single';
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $this->render('index');
    }


    public function actionTest()
    {
    }

    public function actionAddTables()
    {
        $addTables = AddTableConfig::model()->findAll();
        $this->render('addtables', compact('addTables'));
    }


    public function actionAddTable($id)
    {
        $addTable = AddTableConfig::model()->findByPk($id);
        if (!$addTable)
            $addTable = new AddTableConfig();
        $message = false;
        if (Yii::app()->getRequest()->getPost('submit'))
        {
            $addTable->title = Yii::app()->getRequest()->getPost('title');

            if ($addTable->save())
            {
                $message = array('Saved Successfully!', 'green');
                if (!$id)
                    $this->redirect('/admin/addTable/' . $addTable->id);
            }
            else
                $message = array('Error occured!', 'red');
        }
        $this->render('addtable', compact('addTable', 'message'));
    }

    public function actionGetAddTableFieldInfo()
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        $field = AddTableField::model()->findByPk(Yii::app()->getRequest()->getQuery('id'));
        if ($field)
        {
            $values = array(); //(array('order'=>'`order` desc'))
            foreach ($field->addTableFieldValues(array('order' => '`order` desc')) as $value)
                $values[] = $value->value;

            $content['data'] = array(
                'title' => $field->title,
                'name' => $field->name,
                'values' => $values,
                'searchable' => $field->searchable,
                'visible' => $field->visible,
                'type' => $field->type,
                'id' => $field->id);
            $content['result'] = 'ok';
        }
        $this->render('//common/json', compact('content'));
    }

    public function actionDelAddTableField()
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        $addTableField = AddTableField::model()->findByPk(Yii::app()->getRequest()->getPost('id'));
        $command = Yii::app()->db->createCommand('ALTER TABLE `add_table_' . $addTableField->add_table_id . '` DROP COLUMN `' . $addTableField->name . '`');
        if ($addTableField->delete())
        {
            $command->execute();
            $content['result'] = 'ok';
        }
        $this->render('//common/json', compact('content'));
    }

    public function actionSaveAddTableField($id)
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        $addTable = AddTableConfig::model()->findByPk($id);
        if (!$addTable)
        {
            $this->render('//common/json', compact('content'));
            return;
        }

        if (Yii::app()->getRequest()->getPost('id'))
            $addTableField = AddTableField::model()->findByPk(Yii::app()->getRequest()->getPost('id'));
        if (!isset($addTableField) || !$addTableField)
        {
            $addTableField = new AddTableField();
            $addTableField->order = 500;
            $oldName = false;
        }
        else
            $oldName = $addTableField->name;
        $addTableField->title = Yii::app()->getRequest()->getPost('title');
        $addTableField->name = Yii::app()->getRequest()->getPost('name');
        $addTableField->visible = Yii::app()->getRequest()->getPost('visible');
        $addTableField->searchable = Yii::app()->getRequest()->getPost('searchable');
        $addTableField->type = Yii::app()->getRequest()->getPost('type');
        $addTableField->add_table_id = $addTable->id;
        $isNew = false;
        if (!$addTableField->id)
            $isNew = true;

        if ($addTableField->save())
        {
            $fieldValues = array();
            AddTableFieldValue::model()->deleteAllByAttributes(array('add_table_field_id' => $addTableField->id));
            if (in_array($addTableField->type, array('set', 'enum')))
            {
                $fieldValues = Yii::app()->getRequest()->getPost('values', array());
                $order = 1;
                foreach ($fieldValues as $value)
                {
                    if (!$value) continue;
                    $val = new AddTableFieldValue();
                    $val->add_table_field_id = $addTableField->id;
                    $val->value = $value;
                    $val->order = $order;
                    $val->save();
                    $order++;
                }
            }

            $index = false;
            $column = false;
            switch ($addTableField->type)
            {
                case 'varchar':
                    $column = 'VARCHAR(100)';
                    break;
                case 'model':
                case 'brand':
                    $column = 'INT(10) UNSIGNED NOT NULL';
                    break;
                case 'int':
                case 'range':
                    $column = 'INT(10) DEFAULT 0';
                    break;
                case 'date':
                    $column = 'DATE';
                    break;
                case 'checkbox':
                    $column = 'ENUM("yes","no") DEFAULT "no"';
                    break;
                case 'set':
                    if (count($fieldValues))
                    {
                        $column = "SET('" . implode("','", $fieldValues) . "')";
                        break;
                    }
                    break;
                case 'enum':
                    if (count($fieldValues))
                    {
                        $column = "ENUM('" . implode("','", $fieldValues) . "')";
                        break;
                    }
                    break;
                default:
                    $column = false;
            }

            if (!$column)
                throw new CHttpException(403, 'Column not specified');

            if ($oldName && !$isNew)
                $command = 'CHANGE';
            else
            {
                $command = 'ADD COLUMN';
                $index = 'ADD INDEX(`' . $addTableField->name . '`)';
            }

            $sql = 'ALTER TABLE `add_table_' . $addTable->id . '` ';
            $sql .= $command . ' ' . ($oldName ? '`' . $oldName . '` ' : '') . '`' . $addTableField->name . '` ' . $column . '' . ($index ? ', ' . $index : '');
            Yii::app()->db->createCommand($sql)->execute();

            $content['result'] = 'ok';
            $content['data'] = array('id' => $addTableField->id, 'title' => $addTableField->title . ' [' . $addTableField->type . '] ' . ($addTableField->visible == 'yes' ? 'visible' : 'hidden'));
        }
        else
            $content['errors'] = $addTableField->getErrors();
        $this->render('//common/json', compact('content'));
    }

    public function actionSaveAddTableFieldOrder()
    {
        $this->layout = 'json';
        $content = array('result' => 'ok');
        $addTableFieldOrder = Yii::app()->getRequest()->getPost('order');
        if ($addTableFieldOrder)
        {
            $addTableFieldOrder = explode(',', $addTableFieldOrder);
            for ($order = 0; $order < count($addTableFieldOrder); $order++)
            {
                $addTableField = AddTableField::model()->findByPk($addTableFieldOrder[$order]);
                if ((intval($addTableField->order) != $order))
                {
                    $addTableField->order = $order;
                    $addTableField->save();
                }
            }
        }
        $this->render('//common/json', compact('content'));
    }


    public function actionDelAddTable($id)
    {
        AddTableConfig::model()->findByPk($id)->delete();
        Yii::app()->db->createCommand('DROP TABLE `add_table_' . $id . '`')->execute();
        $this->redirect('/admin/addTables/');
    }

    public function actionCategories()
    {
        $categories = Category::model()->findAllByAttributes(array('category_id' => NULL));
        $addTables = AddTableConfig::model()->findAll();
        $this->render('categories', compact('categories', 'addTables'));
    }

    public function actionGetCategoryInfo()
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        $category = Category::model()->findByPk(Yii::app()->getRequest()->getQuery('id'));
        if ($category)
        {
            $content['data'] = array(
                'title' => $category->title,
                'name' => $category->name,
                'add_table' => $category->add_table_id,
                'id' => $category->id);
            $content['result'] = 'ok';
        }
        $this->render('//common/json', compact('content'));
    }

    public function actionDelCategory()
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        $category = Category::model()->findByPk(Yii::app()->getRequest()->getPost('id'));
        if ($category->delete())
        {
            $content['result'] = 'ok';
        }
        Yii::app()->cache->delete('category_widget');
        $this->render('//common/json', compact('content'));
    }

    public function actionSaveCategory()
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        if (Yii::app()->getRequest()->getPost('id'))
            $category = Category::model()->findByPk(Yii::app()->getRequest()->getPost('id'));
        if (!isset($category) || !$category)
            $category = new Category();
        $category->title = Yii::app()->getRequest()->getPost('title');
        $category->name = Yii::app()->getRequest()->getPost('name');
        $category->add_table_id = Yii::app()->getRequest()->getPost('add_table');
        if (Yii::app()->getRequest()->getPost('category_id'))
            $category->category_id = Yii::app()->getRequest()->getPost('category_id');
        else
            $category->category_id = NULL;

        $category->order = 99;
        if ($category->save())
        {
            $content['result'] = 'ok';
            $content['data'] = array('id' => $category->id, 'add_table' => $category->add_table_id, 'title' => $category->title . ($category->add_table_id ? ' [' . $category->addTable->title . ']' : ''), 'name' => $category->name);
        }
        Yii::app()->cache->delete('category_widget');
        $this->render('//common/json', compact('content'));
    }

    public function actionSaveCategoryOrder()
    {
        $this->layout = 'json';
        $content = array('result' => 'ok');
        $catOrder = Yii::app()->getRequest()->getPost('order');
        $catId = intval(Yii::app()->getRequest()->getPost('cat_id'));
        if ($catOrder)
        {
            $catOrder = explode(',', $catOrder);
            for ($order = 0; $order < count($catOrder); $order++)
            {
                $category = Category::model()->findByPk($catOrder[$order]);
                $updateParent = ($catId && ($catId == $catOrder[$order]));
                if ((intval($category->order) != $order) || $updateParent)
                {
                    if ($updateParent)
                        $category->category_id = Yii::app()->getRequest()->getPost('cat_parent') ? Yii::app()->getRequest()->getPost('cat_parent') : NULL;
                    $category->order = $order;
                    $category->save();
                }
            }
        }
        Yii::app()->cache->delete('category_widget');
        $this->render('//common/json', compact('content'));
    }


    public function actionCountries()
    {
        $countries = Country::model()->findAll();
        $this->render('countries', compact('countries'));
    }


    public function actionCountry($id)
    {
        $country = Country::model()->findByPk($id);
        if (!$country)
            $country = new Country();
        $message = false;
        if (Yii::app()->getRequest()->getPost('submit'))
        {
            $country->name = Yii::app()->getRequest()->getPost('name');
            if ($country->save())
            {
                $message = array('Saved Successfully!', 'green');
                if (!$id)
                    $this->redirect('/admin/country/' . $country->id);
            }
            else
                $message = array('Error occured!', 'red');
        }
        $this->render('country', compact('country', 'message'));
    }

    public function actionGetCityInfo()
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        $field = City::model()->findByPk(Yii::app()->getRequest()->getQuery('id'));
        if ($field)
        {
            $content['data'] = array(
                'name' => $field->name,
                'id' => $field->id);
            $content['result'] = 'ok';
        }
        $this->render('//common/json', compact('content'));
    }

    public function actionDelCity()
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        $city = City::model()->findByPk(Yii::app()->getRequest()->getPost('id'));
        if ($city->delete())
        {
            $content['result'] = 'ok';
        }
        $this->render('//common/json', compact('content'));
    }

    public function actionSaveCity($id)
    {
        $this->layout = 'json';
        $content = array('result' => 'error');
        $country = Country::model()->findByPk($id);
        if (!$country)
        {
            $this->render('//common/json', compact('content'));
            return;
        }

        if (Yii::app()->getRequest()->getPost('id'))
            $city = City::model()->findByPk(Yii::app()->getRequest()->getPost('id'));
        if (!isset($city) || !$city)
        {
            $city = new City();
            $city->order = 500;
        }
        $city->name = Yii::app()->getRequest()->getPost('name');
        $city->country_id = $country->id;

        if ($city->save())
        {
            $content['result'] = 'ok';
            $content['data'] = array('id' => $city->id, 'title' => $city->name);
        }
        else
            $content['errors'] = $city->getErrors();
        $this->render('//common/json', compact('content'));
    }

    public function actionSaveCityOrder()
    {
        $this->layout = 'json';
        $content = array('result' => 'ok');
        $cityOrder = Yii::app()->getRequest()->getPost('order');
        if ($cityOrder)
        {
            $cityOrder = explode(',', $cityOrder);
            for ($order = 0; $order < count($cityOrder); $order++)
            {
                $city = City::model()->findByPk($cityOrder[$order]);
                if ((intval($city->order) != $order))
                {
                    $city->order = $order;
                    $city->save();
                }
            }
        }
        $this->render('//common/json', compact('content'));
    }


    public function actionDelCountry($id)
    {
        Country::model()->findByPk($id)->delete();
        Yii::app()->db->createCommand('DROP TABLE `add_table_' . $id . '`')->execute();
        $this->redirect('/admin/countries/');
    }


}