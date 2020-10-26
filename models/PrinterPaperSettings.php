<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;

/**
 * Class PrinterPaperSettings
 * @package d3yii2\d3printer\models
 */
class PrinterPaperSettings extends Model
{
    public $paper_size;
    public $paper_type;
    
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['paper_size', 'paper_type'], 'string']
        ];
    }
    
    /**
     * @return string[]
     */
    public function fields(): array
    {
        return ['paper_size', 'paper_type'];
    }
    
    /**
     * @return array
     */
    public function attributeLabels()
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
        return ['paper_size', 'paper_type'];
    }
    
    /**
     * @return string
     */
    public static function getSectionName(): string
    {
        return 'Settings-PrinterPaperSettings';
    }
}
