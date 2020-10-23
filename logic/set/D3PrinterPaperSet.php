<?php

namespace d3yii2\d3printer\logic\set;

use d3yii2\d3printer\logic\D3Printer;
use d3yii2\d3printer\logic\settings\D3PrinterAccessSettings;

class D3PrinterPaperSet extends D3Printer
{
    public function __construct()
    {
        $this->accessSettings = new D3PrinterAccessSettings();
        parent::__construct();
    }
    
    protected function getConnectionUrl()
    {
        return $this->accessSettings->getPaperSetupUrl();
    }
}