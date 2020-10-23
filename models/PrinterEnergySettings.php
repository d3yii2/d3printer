<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;


class PrinterEnergySettings extends Model
{
    public $sleep;
    
    public function rules()
    {
        return [
            [['sleep'], 'string']
        ];
    }
    
    public function fields()
    {
        return ['sleep'];
    }
    
    public function attributeLabels()
    {
        return [
        ];
    }
    
    public function prepare(): void
    {
        foreach ($this->attributes() as $attribute) {
            $value = Yii::$app->settings->get(self::getSectionName(), $attribute);
            
            if (!is_null($value)) {
                $this->{$attribute} = $value;
            }
        }
    }
    
    public function attributes()
    {
        return ['sleep'];
    }
    
    public static function getSectionName(): string
    {
        return 'Settings-PrinterEnergySettings';
    }
}

