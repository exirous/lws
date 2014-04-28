<?php

Yii::import('application.models._base.BaseCourse');
/**
 * @method Course find
 * @method Course[] findAll
 * @method Course findByPk
 * @method Course[] findAllByPk
 * @method Course findByAttributes
 * @method Course[] findAllByAttributes
 * @method Course findBySql
 * @method Course[] findAllBySql
 * @method Course cache
 * @method Course resetScope
 * @method Course with
 * @method Course together
 * @method Course populateRecord
 * @method Course scopeLimit
 * @method Course scopeOffset
 * @method Course scopeOrder
 * @method Course scopeAllColumns
 * @method Course scopeSelect
 * @method Course byName
 */
class Course extends BaseCourse
{
    /**
     * @static
     * @param string $className
     * @return Course
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getPublicAttributes()
    {
        $subjects = [];
        foreach ($this->subjects as $subject)
            $subjects[] = $subject->publicAttributes;

        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'subjects'=>$subjects,
            'rank_order'=>$this->rank_id ? $this->rank->order : ''
        ];
    }
}