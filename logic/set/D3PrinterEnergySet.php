<?php

namespace d3yii2\d3printer\logic\set;

use d3yii2\d3printer\logic\settings\D3PrinterAccessSettings;

/**
 * Class D3PrinterEnergySet
 * @package d3yii2\d3printer\logic\set
 */
class D3PrinterEnergySet extends D3PrinterSet
{
    /**
     * D3PrinterEnergySet constructor.
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
        return $this->accessSettings->getEnergySetupUrl();
    }
}

