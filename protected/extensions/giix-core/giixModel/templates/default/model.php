<?php
/**
 * This is the template for generating the model class of a specified table.
 * In addition to the default model Code, this adds the CSaveRelationsBehavior
 * to the model class definition.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 * - $representingColumn: the name of the representing column for the table (string) or
 *   the names of the representing columns (array)
 */
/** @var $modelClass string */
/** @var $this GiixModelCode */
?>
<?php echo "<?php\n"; ?>

Yii::import('<?php echo "{$this->baseModelPath}.{$this->baseModelClass}"; ?>');
/**
 * @method <?php echo $modelClass; ?> find
 * @method <?php echo $modelClass; ?>[] findAll
 * @method <?php echo $modelClass; ?> findByPk
 * @method <?php echo $modelClass; ?>[] findAllByPk
 * @method <?php echo $modelClass; ?> findByAttributes
 * @method <?php echo $modelClass; ?>[] findAllByAttributes
 * @method <?php echo $modelClass; ?> findBySql
 * @method <?php echo $modelClass; ?>[] findAllBySql
 * @method <?php echo $modelClass; ?> cache
 * @method <?php echo $modelClass; ?> resetScope
 * @method <?php echo $modelClass; ?> with
 * @method <?php echo $modelClass; ?> together
 * @method <?php echo $modelClass; ?> populateRecord
 * @method <?php echo $modelClass; ?> scopeLimit
 * @method <?php echo $modelClass; ?> scopeOffset
 * @method <?php echo $modelClass; ?> scopeOrder
 * @method <?php echo $modelClass; ?> scopeAllColumns
 * @method <?php echo $modelClass; ?> scopeSelect
 * @method <?php echo $modelClass; ?> byName
 */
class <?php echo $modelClass; ?> extends <?php echo $this->baseModelClass . "\n";?>
{
    /**
     * @static
     * @param string $className
     * @return <?php echo $modelClass . "\n"; ?>
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}