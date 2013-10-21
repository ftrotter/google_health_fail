<?php

/**
 * BaseNumberType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $number_type
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
class NumberType extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('number_type');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('number_type', 'string', 30, array(
             'type' => 'string',
             'length' => 30,
             'fixed' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

}
