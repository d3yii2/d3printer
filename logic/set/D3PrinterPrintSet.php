<?php

namespace d3yii2\d3printer\logic\set;

use d3yii2\d3printer\logic\D3Printer;
use d3yii2\d3printer\logic\settings\D3PrinterAccessSettings;
use d3yii2\d3printer\logic\settings\D3PrinterPrintSettings;

class D3PrinterPrintSet extends D3Printer
{
    private $printSettings;
    
    public function __construct()
    {
        $this->accessSettings = new D3PrinterAccessSettings();
        $this->printSettings = new D3PrinterPrintSettings();
        parent::__construct();
    }
    
    protected function getConnectionUrl()
    {
        return $this->accessSettings->getPrintSetupUrl();
    }
}
