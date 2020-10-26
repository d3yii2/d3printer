<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;

/**
 * Class PrinterEnergySettings
 * @package d3yii2\d3printer\models
 */
class PrinterEnergySettings extends Model
{
    public $sleep;
    
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['sleep'], 'string']
        ];
    }
    
    /**
     * @return string[]
     */
    public function fields(): array
    {
        return ['sleep'];
    }
    
    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
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
        return ['sleep'];
    }
    
    /**
     * @return string
     */
    public static function getSectionName(): string
    {
        return 'Settings-PrinterEnergySettings';
    }
}
