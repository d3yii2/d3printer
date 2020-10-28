<?php

namespace d3yii2\d3printer\logic\settings;

use d3yii2\d3printer\models\AlertSettings;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterAlertSettings
{
    protected $model;
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        $this->model = new AlertSettings();
        $this->model->prepare();
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
     * @return strings
     */
    public function getEmailFrom(): string
    {
        return $this->model->email_from;
    }
    
    /**
     * @return string
     */
    public function getEmailTo(): array
    {
        if(!$this->model->email_to){
            return [];
        }

        return explode('|', trim($this->model->email_to,'| \t\n\r\0\x0B'));
    }
    
    /**
     * @return string
     */
    public function getEmailSubject(): string
    {
        return $this->model->email_subject;
    }
}
