<?php
/**
 * Base Active Record class
 *
 * @method BActiveRecord find
 * @method BActiveRecord[] findAll
 * @method BActiveRecord findByPk
 * @method BActiveRecord[] findAllByPk
 * @method BActiveRecord findByAttributes
 * @method BActiveRecord[] findAllByAttributes
 * @method BActiveRecord findBySql
 * @method BActiveRecord[] findAllBySql
 * @method BActiveRecord cache
 * @method BActiveRecord resetScope
 * @method BActiveRecord with
 * @method BActiveRecord together
 * @method BActiveRecord populateRecord
 */


abstract class BActiveRecord extends GxActiveRecord
{
    protected abstract function getTextColumns();

    private $newTextFieldValues = array();

    /**
     * Returns the errors for all attributes in string
     *
     * @return string
     */
    public function getErrorsString()
    {
        $errors = $this->getErrors();
        $errorMsg = '';

        if (!$errors)
            return NULL;

        foreach ($errors as $error)
            $errorMsg .= ' ' . implode(' ', $error);

        return $errorMsg;
    }

    /**
     * Limit scope
     * Sets limit to query
     *
     * @param integer $limit
     * @return BActiveRecord
     */
    public function scopeLimit($limit)
    {
        if (!$limit)
            return $this;

        $this->getDbCriteria()->mergeWith(array('limit' => $limit));

        return $this;
    }

    /**
     * Offset scope
     * Sets offset to query
     *
     * @param integer $offset
     * @return BActiveRecord
     */
    public function scopeOffset($offset)
    {
        if (!$offset)
            return $this;

        $this->getDbCriteria()->mergeWith(array('offset' => $offset));

        return $this;
    }

    /**
     * Adds order to query request
     *
     * supported parameters:
     * <string>: custom order string
     * <array(<string:field>[, <string:direction>, <string:tableAlias>])>
     * <array(<array(<string:field>[, <string:direction>, <string:tableAlias>])[, ...]>)>
     *
     * @param string|array $order
     * @return BActiveRecord
     */
    public function scopeOrder($order = null)
    {
        if (!$order)
            return $this;

        if (is_string($order))
        {
            $this->getDbCriteria()->mergeWith(array('order' => $order));
            return $this;
        }

        //@todo maybe add an exception here
        if (!is_array($order))
            return $this;

        //array of string mode
        if (is_string($order[0]))
            $order = array($order);

        //'array of array of string' mode
        foreach ($order as $orderField)
        {
            $prefix = (array_key_exists(2, $orderField) && $orderField[2]) ? $orderField[2]: $this->tableAlias;
            if (!empty($prefix))
                $prefix = '`' . $prefix . '`.';

            $o = $prefix . $orderField[0];
            if (array_key_exists(1, $orderField) && $orderField[1])
                $o .= ' ' . $orderField[1];
            $this->getDbCriteria()->mergeWith(array('order' => $o));
        }
        return $this;
    }

    /**
     * This scope adds criteria for query to select all columns in the table.
     * @return BActiveRecord
     */
    public function scopeAllColumns()
    {
        $getColumnNames = $this->tableSchema->getColumnNames();

        $this->getDbCriteria()->mergeWith(array('select' => implode(',', $getColumnNames)));

        return $this;
    }

    /**
     * This scope adds criteria for query to select
     * @param string $rows
     * @return BActiveRecord
     */
    public function scopeSelect($rows)
    {
        $this->getDbCriteria()->mergeWith(array('select' => $rows));

        return $this;
    }

    /**
     * This scope searches through name using LIKE %val$
     * If applied to a model without 'name' field, throws an Exception
     *
     * @param $val string Value to search for
     * @param bool $exact Whether or not to search for exact value, defaults to false
     * @throws Exception
     * @return BActiveRecord
     */
    public function byName($val, $exact = false)
    {
        if (!array_key_exists('name', $this->attributes))
            throw new Exception;

        $this->dbCriteria->addSearchCondition('name', $val, !$exact);
        return $this;
    }

    /**
     * Return Enum values for give attribute name
     *
     * @param string $attribute
     * @return array
     */
    public function getEnumValues($attribute)
    {
        $tableSchema = $this->getTableSchema();

        if (!isset($tableSchema) || !isset($tableSchema->columns) || !isset($tableSchema->columns[$attribute]))
            return array();

        $column = $tableSchema->columns[$attribute];

        if (strpos($column->dbType, 'enum') === false)
            return array();

        preg_match('/\((.*)\)/', $column->dbType, $matches);

        $enumValues = array();
        foreach (explode(',', $matches[1]) as $value)
        {
            $value = str_replace("'", null, $value);
            $enumValues[] = $value;
        }

        return $enumValues;
    }


    /**
     * @param string $name property name
     * @param mixed $value property value
     * @return mixed|void
     */
    public function __set($name, $value)
    {
        if ($this->setAttribute($name, $value) === false)
        {
            if ($this->getIsTextParam($name))
                $this->_setNewTextFieldValue($name, $value);
            /**
             * This method is used to set value like "textTitle[1]" somewhere in validation process
             * Might cause performance drop!!!
             */
            //elseif ($this->getIsNewRecord() && (preg_match('/^([^\[]+)\[(\d+)\]$/', $name, $match) != 0)
            //    && in_array($match[1], $this->getTextColumns()))
            //    $this->newTextFieldValues[$match[1]][$match[2]] = $value;
            else
                parent::__set($name, $value);
        }
    }

    private function _setNewTextFieldValue($name, $value)
    {
        if (!is_array($value))
        {
            $string = $value;
            $value = array();

            $languages = Yii::app()->langHandler->getLanguages();
            foreach ($languages as $id => $system_name)
                $value[$id] = $string;
        }

        $this->newTextFieldValues[$name] = $value;
    }

    public function __get($name)
    {
        if ($this->getIsTextParam($name))
        {
            if (isset($this->newTextFieldValues[$name]))
                return $this->newTextFieldValues[$name];
            else
                return $this->getTextField($name);
        }
        /*
         * This method is used by TextValidator to get value by name like "textTitle[1]"
         * Might cause performance drop!!!
         */
        //if ($this->getIsNewRecord() && (preg_match('/^([^\[]+)\[(\d+)\]$/', $name, $match) != 0))
        //    return $this->{$match[1]}[$match[2]];

        return parent::__get($name);
    }

    public function beforeSave()
    {
        $textColumns = $this->getTextColumns();
        if (!empty($textColumns))
        {
            if ($this->getIsNewRecord())
                $this->mergeNewTextFieldsWithBlankValues();

            foreach ($this->newTextFieldValues as $name => $value)
            {
                foreach ($value as $language => $val)
                    $this->setTextField($name, $language, $val, $textColumns);
            }
        }
        return parent::beforeSave();
    }

    public function mergeNewTextFieldsWithBlankValues()
    {
        $newTextFieldValuesBlank = array();
        foreach ($this->getTextColumns() as $columnName)
        {
            $languages = Yii::app()->langHandler->getLanguages();
            foreach ($languages as $id => $system_name)
                $newTextFieldValuesBlank[$columnName][$id] = '';
        }

        $this->newTextFieldValues = BMap::mergeArray($newTextFieldValuesBlank, $this->newTextFieldValues, true);
    }

    public function saveAttributes($attributes)
    {
        $textColumns = $this->getTextColumns();
        foreach ($attributes as $name)
        {
            if (array_key_exists($name, $this->newTextFieldValues))
            {
                $value = $this->newTextFieldValues[$name];
                foreach ($value as $language => $val)
                {
                    $this->setTextField($name, $language, $val, $textColumns);
                }
                unset($attributes[$name]);
                unset($this->newTextFieldValues[$name]);
            }
        }

        return parent::saveAttributes($attributes);
    }

    public function refresh()
    {
        $this->newTextFieldValues = array();
        return parent::refresh();
    }

    protected function setTextField($tf, $languageId, $data, $textColumns)
    {
        if (!in_array($tf, $textColumns))
            throw new Exception(Yii::t('base', '{f} is not a text column', array('{f}' => $tf)));

        $columnName = $this->getTextRelationColumnName($tf);

        //if text is null create it
        if (!$this->{$columnName})
        {
            $textId = new TextId();
            $textId->save();

            $this->{$columnName} = $textId->id;
        }

        $t = Text::model()->findByPk(array('id' => $this->{$columnName}, 'language_id' => $languageId));
        if (!$t)
        {
            $t = new Text();
            $t->id = $this->{$columnName};
            $t->language_id = $languageId;
            $t->text = $data;
        }
        else
            $t->text = $data;
        $t->save();
    }

    public function getTextField($tf, $languageId = false)
    {
        if (!$this->getIsTextParam($tf))
            throw new Exception(Yii::t('base', '{f} is not a text column', array('{f}' => $tf)));

        if ($languageId)
        {
            $t = Text::model()->findByPk(array('id' => $this->{$tf}, 'language_id' => $languageId));
            if (!$t)
                return '';
            else
                return $t->text;
        }
        else
        {
            $texts = array();

            $columnName = $this->getTextRelationColumnName($tf);
            $t = Text::model()->findAllByAttributes(array('id' => $this->{$columnName}));

            foreach ($t as $text)
                /** @var $text Text */
                $texts[$text->language_id] = $text->text;

            return $texts;
        }

    }

    public function afterDelete()
    {
        foreach ($this->getTextColumns() as $tf)
            Text::model()->deleteAllByAttributes(array('id' => $this->{$this->getTextRelationColumnName($tf)}));
        parent::afterDelete();
    }

    public function getForeignKeys()
    {
        $cols = $this->getMetaData()->columns;
        $fKeys = array();
        foreach ($cols as $k => $v)
        {
            if ($v->isForeignKey)
                $fKeys[] = $k;
        }
        return $fKeys;
    }

    public function getTextRelationColumnName($textField)
    {
        // @todo. May be there is a better way to get column by relation name
        $relation = $this->relations();
        $columnName = $relation[$textField][2];

        return $columnName;
    }

    /**
     * Adds a NOT IN condition to the query
     *
     * @param $col    string column to check
     * @param $values array values to check against
     *
     * @return BActiveRecord
     */
    public function notIn($col, $values)
    {
        if (!is_array($values))
            return $this;

        $this->getDbCriteria()->addNotInCondition($col, $values);
        return $this;
    }

    public function getIsTextParam($param)
    {
        return in_array($param, $this->getTextColumns());
    }
}

?>
