<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;

/**
 * Class PrinterAccessSettings
 * @package d3yii2\d3printer\models
 */
class PrinterAccessSettings extends Model
{
    public $print_setup_url;
    public $home_url;
    public $device_info_url;
    public $paper_setup_url;
    public $energy_setup_url;
    
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['home_url', 'device_info_url', 'print_setup_url', 'paper_setup_url', 'energy_setup_url'], 'string']
        ];
    }
    
    /**
     * @return string[]
     */
    public function fields(): array
    {
        return ['home_url', 'device_info_url', 'print_setup_url', 'paper_setup_url', 'energy_setup_url'];
    }
    
    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'print_setup_url' => Yii::t('d3printer', 'Print setup URL'),
        ];
    }
    
    /**
     * 
     */
    public function prepare(): void
    {
        foreach ($this->attributes() as $attribute) {
            $value = Yii::$app->settings->get(self::getSectionName(), $attribute);
            
            if (!is_null($value)) {
                $this->{$attribute} = $value;
            }
        }
    }
    
    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return ['home_url', 'device_info_url', 'print_setup_url', 'paper_setup_url', 'energy_setup_url'];
    }
    
    /**
     * @return string
     */
    public static function getSectionName(): string
    {
        return 'Settings-PrinterAccessSettings';
    }
}
