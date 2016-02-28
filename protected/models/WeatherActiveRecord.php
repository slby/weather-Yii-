<?php

/**
 * Class WeatherActiveRecord
 */
class WeatherActiveRecord extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'weather';
    }

    public function primaryKey()
    {
        return 'id';
    }
}
