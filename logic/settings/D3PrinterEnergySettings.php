<?php

namespace d3yii2\d3printer\logic\settings;

use d3yii2\d3printer\models\PrinterEnergySettings;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterEnergySettings
{
    protected $model;
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        $this->model = new PrinterEnergySettings();
        $this->model->prepare();
    }
    
    /**
     * @return string
     */
    public function getInactivitySleep(): string
    {
        return $this->model->sleep;
    }
}
