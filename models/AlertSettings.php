<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;

/**
 * Class AlertSettings
 * @package d3yii2\d3printer\models
 */
class AlertSettings extends Model
{
    public $cartridge_remain_min;
    public $drum_remain_min;
    public $email_from;
    public $email_to;
    public $email_subject;
    
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['cartridge_remain_min', 'drum_remain_min', 'email_from', 'email_subject'], 'string'],
            [['email_to'], 'safe'],
        ];
    }
    
    /**
     * @return string[]
     */
    public function fields(): array
    {
        return ['cartridge_remain_min', 'drum_remain_min', 'email_from', 'email_to', 'email_subject'];
    }
    
    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cartridge_remain_min' => Yii::t('d3printer', 'Cartridge minimum %'),
            'drum_remain_min' => Yii::t('d3printer', 'Drum minimum %'),
            'email_from' => Yii::t('d3printer', 'System email From:'),
            'email_to' => Yii::t('d3printer', 'Receivers emails'),
            'email_subject' => Yii::t('d3printer', 'Email subject'),
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
        return ['cartridge_remain_min', 'drum_remain_min', 'email_from', 'email_to', 'email_subject'];
    }
    
    /**
     * @return string
     */
    public static function getSectionName(): string
    {
        return 'Settings-AlertSettings';
    }
    
    /**
     * @return bool
     */
    public function beforeValidate(): bool
    {
        $this->email_to = implode('|', $this->email_to);
        
        return parent::beforeValidate();
    }
}

