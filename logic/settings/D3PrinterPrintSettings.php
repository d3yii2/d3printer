<?php

namespace d3yii2\d3printer\logic\settings;

use d3yii2\d3printer\models\AlertSettings;
use d3yii2\d3printer\models\PrinterPrintSettings;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterPrintSettings
{
    protected $model;
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        $this->model = new PrinterPrintSettings();
        $this->model->prepare();
    }
    
    /**
     * @return string
     */
    public function get(): string
    {
        return $this->model->;
    }

    /**
     * @return string
     */
}
