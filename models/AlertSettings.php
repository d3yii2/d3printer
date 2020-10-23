<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\Model;


class AlertSettings extends Model
{
    public $cartridge_remain_min;
    public $drum_remain_min;
    public $email_from;
    public $email_to = [];
    public $email_subject;
    
    public function rules()
    {
        return [
            [['cartridge_remain_min', 'drum_remain_min', 'email_from', 'email_subject'], 'string'],
            [['email_to'], 'safe'],
        ];
    }
    
    public function fields()
    {
        return ['cartridge_remain_min', 'drum_remain_min', 'email_from', 'email_to', 'email_subject'];
    }
    
    public function attributeLabels()
    {
        return [
            'cartridge_remain_min' => Yii::t('d3printer', 'Cartridge minimum %'),
            'drum_remain_min' => Yii::t('d3printer', 'Drum minimum %'),
            'email_from' => Yii::t('d3printer', 'System email From:'),
            'email_to' => Yii::t('d3printer', 'Receivers emails'),
            'email_subject' => Yii::t('d3printer', 'Email subject'),
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
    
    public function beforeValidate()
    {
        $this->email_to = implode('|', $this->email_to);
        
        return parent::beforeValidate();
    }
    
    public function attributes()
    {
        return ['cartridge_remain_min', 'drum_remain_min', 'email_from', 'email_to', 'email_subject'];
    }
    
    public static function getSectionName(): string
    {
        return 'Settings-AlertSettings';
    }
}

