<?php

class AddTable
{
    static $tableStructures = array();

    /** @return Array */
    public static function getStructure($id)
    {
        if (!isset(self::$tableStructures['t' . $id]))
        {
            if (!($cache = Yii::app()->cache->get('addTableStruct_' . $id)))
            {
                $cache = Yii::app()->db->createCommand('SHOW FULL COLUMNS FROM `add_table_' . $id . '`')->queryAll();
                Yii::app()->cache->set('addTableStruct_' . $id, $cache, 60 * 60);
            }
            self::$tableStructures['t' . $id] = $cache;
        }
        return self::$tableStructures['t' . $id];
    }

    /** @return CMysqlColumnSchema[] */
    public static function getCdbColumns($id)
    {
        $schema = array();
        $columns = self::getStructure($id);
        foreach ($columns as $column)
        {
            $col = new CMysqlColumnSchema();
            $col->init($column['Type'], $column['Default']);
            $col->name = $column['Field'];
            $col->rawName = '`' . $column['Field'] . '`';
            $col->isPrimaryKey = false; //$column['Key'] == 'PRI';
            $col->autoIncrement = false;
            $col->isForeignKey = false;
            $schema[$column['Field']] = $col;
        }
        return $schema;
    }

}
