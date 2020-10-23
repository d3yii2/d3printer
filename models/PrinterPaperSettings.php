<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;


class PrinterPaperSettings extends Model
{
    public $paper_size;
    public $paper_type;
    
    public function rules()
    {
        return [
            [['paper_size', 'paper_type'], 'string']
        ];
    }
    
    public function fields()
    {
        return ['paper_size', 'paper_type'];
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
        return ['paper_size', 'paper_type'];
    }
    
    public static function getSectionName(): string
    {
        return 'Settings-PrinterPaperSettings';
    }
}

