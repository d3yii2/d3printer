<?php

namespace d3yii2\d3printer\logic\settings;

use d3yii2\d3printer\models\AlertSettings;
use d3yii2\d3printer\models\PrinterPaperSettings;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterPaperSettings
{
    protected $model;
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        $this->model = new PrinterPaperSettings();
        $this->model->prepare();
    }
    
    /**
     * @return string
     */
    public function getPaperSize(): string
    {
        return $this->model->paper_size;
    }

    /**
     * @return string
     */
    public function getPaperType(): string
    {
        return $this->model->paper_type;
    }
}
