<?php

Yii::import('application.models._base.BaseMaterialSubject');

/**
 * @method MaterialSubject find
 * @method MaterialSubject[] findAll
 * @method MaterialSubject findByPk
 * @method MaterialSubject[] findAllByPk
 * @method MaterialSubject findByAttributes
 * @method MaterialSubject[] findAllByAttributes
 * @method MaterialSubject findBySql
 * @method MaterialSubject[] findAllBySql
 * @method MaterialSubject cache
 * @method MaterialSubject resetScope
 * @method MaterialSubject with
 * @method MaterialSubject together
 * @method MaterialSubject populateRecord
 * @method MaterialSubject scopeLimit
 * @method MaterialSubject scopeOffset
 * @method MaterialSubject scopeOrder
 * @method MaterialSubject scopeAllColumns
 * @method MaterialSubject scopeSelect
 * @method MaterialSubject byName
 */
class MaterialSubject extends BaseMaterialSubject
{
    /**
     * @static
     * @param string $className
     * @return MaterialSubject
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getRenderAttributes()
    {
        $materials = [];
        require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
        $parser = new JBBCode\Parser();
        $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
        foreach ($this->materials as $material)
        {
            $materialAttributes = $material->renderAttributes;
            $parser->parse($materialAttributes['text']);
            $materialAttributes['text'] = nl2br($parser->getAsHTML());
            $materials[] = $materialAttributes;
        }
        return [
            'id'=>$this->id,
            'slug'=>$this->slug,
            'name'=>$this->name,
            'materials'=>$materials
        ];
    }

}