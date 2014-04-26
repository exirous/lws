<?php

Yii::import('application.models._base.BaseSubject');
/**
 * @method Subject find
 * @method Subject[] findAll
 * @method Subject findByPk
 * @method Subject[] findAllByPk
 * @method Subject findByAttributes
 * @method Subject[] findAllByAttributes
 * @method Subject findBySql
 * @method Subject[] findAllBySql
 * @method Subject cache
 * @method Subject resetScope
 * @method Subject with
 * @method Subject together
 * @method Subject populateRecord
 * @method Subject scopeLimit
 * @method Subject scopeOffset
 * @method Subject scopeOrder
 * @method Subject scopeAllColumns
 * @method Subject scopeSelect
 * @method Subject byName
 */
class Subject extends BaseSubject
{
    /**
     * @static
     * @param string $className
     * @return Subject
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getPublicAttributes()
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
        ];
    }

}