<?php

namespace d3yii2\d3printer\logic\settings;

use d3yii2\d3printer\models\PrinterAccessSettings;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterAccessSettings
{
    protected $model;
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        $this->model = new PrinterAccessSettings();
        $this->model->prepare();
    }
    
    /**
     * @return string
     */
    public function getPrinterDeviceUrl(): string
    {
        return $this->model->home_url??'';
    }
    
    /**
     * @return string
     */
    public function getPrinterConfigurationUrl(): string
    {
        return $this->model->device_info_url??'';
    }
    
    /**
     * @return string
     */
    public function getPrintSetupUrl(): string
    {
        return $this->model->print_setup_url??'';
    }
    
    /**
     * @return string
     */
    public function getPaperSetupUrl(): string
    {
        return $this->model->paper_setup_url??'';
    }
    
    /**
     * @return string
     */
    public function getEnergySetupUrl(): string
    {
        return $this->model->energy_setup_url??'';
    }
}