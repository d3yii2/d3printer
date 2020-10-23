<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;


class PrinterPrintSettings extends Model
{
    
    public function rules()
    {
        return [
            [[], 'string']
        ];
    }
    
    public function fields()
    {
        return [];
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
        return [];
    }
    
    public static function getSectionName(): string
    {
        return 'Settings-PrinterPrintSettings';
    }
}

