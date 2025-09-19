<?php

namespace d3yii2\d3printer\logic\settings;

use d3yii2\d3printer\models\AlertSettings as AlertSettingsModel;

/**
 * Class AlertSettings
 * @package d3yii2\d3printer\logic\settings
 */
class AlertSettings
{
    protected $model;

    /**
     * AlertSettings constructor.
     * @param string $addKey
     */
    public function __construct(string $addKey)
    {
        $this->model = new AlertSettingsModel();
        $this->model->prepare($addKey);
    }
    
    /**
     * @return string
     */
    public function getCartridgeMinValue(): string
    {
        return $this->model->cartridge_remain_min;
    }
    
    /**
     * @return string
     */
    public function getDrumMinValue(): string
    {
        return $this->model->drum_remain_min;
    }
    
    /**
     * @return string
     */
    public function getEmailFrom(): string
    {
        return $this->model->email_from;
    }

    /**
     * @return array
     */
    public function getEmailTo(): array
    {
        $emailTo = trim($this->model->email_to, '| \t\n\r\0\x0B');
        if (!$emailTo) {
            return [];
        }
        return explode('|', $emailTo);
    }
    
    /**
     * @return string
     */
    public function getEmailSubject(): string
    {
        return $this->model->email_subject;
    }
}
