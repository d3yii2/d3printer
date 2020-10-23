<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;


class PrinterAccessSettings extends Model
{
    public $print_setup_url;
    public $home_url;
    public $device_info_url;
    public $paper_setup_url;
    public $energy_setup_url;
    
    public function rules()
    {
        return [
            [['home_url', 'device_info_url', 'print_setup_url', 'paper_setup_url', 'energy_setup_url'], 'string']
        ];
    }
    
    public function fields()
    {
        return ['home_url', 'device_info_url', 'print_setup_url', 'paper_setup_url', 'energy_setup_url'];
    }
    
    public function attributeLabels()
    {
        return [
            'print_setup_url' => Yii::t('d3printer', 'Print setup URL'),
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
        return ['home_url', 'device_info_url', 'print_setup_url', 'paper_setup_url', 'energy_setup_url'];
    }
    
    public static function getSectionName(): string
    {
        return 'Settings-PrinterAccessSettings';
    }
}

