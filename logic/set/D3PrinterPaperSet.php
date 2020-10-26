<?php

namespace d3yii2\d3printer\logic\set;

use d3yii2\d3printer\logic\settings\D3PrinterAccessSettings;

/**
 * Class D3PrinterPaperSet
 * @package d3yii2\d3printer\logic\set
 */
class D3PrinterPaperSet extends D3PrinterSet
{
    /**
     * D3PrinterPaperSet constructor.
     */
    public function __construct()
    {
        $this->accessSettings = new D3PrinterAccessSettings();
        parent::__construct();
    }
    
    /**
     * @return string
     */
    protected function getConnectionUrl(): string
    {
        return $this->accessSettings->getPaperSetupUrl();
    }
}
