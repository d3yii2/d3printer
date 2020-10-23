<?php

namespace d3yii2\d3printer\logic;

use d3yii2\d3printer\logic\read\D3PrinterReadConfiguration;
use d3yii2\d3printer\logic\set\D3PrinterPaperSet;
use d3yii2\d3printer\logic\settings\D3PrinterPaperSettings;
use d3yii2\d3printer\logic\settings\D3PrinterPrintSettings;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterConfigurationHealth extends D3PrinterHealth
{
    protected $configuration;
    
    /**
     * D3PrinterConfigurationHealth constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->configuration = new D3PrinterReadConfiguration();
    }
    
    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function isValid(): bool
    {
        //$print = $this->configuration->getPrintSettings();
        //$printLocalSettings = new D3PrinterPrintSettings();
        
        $paper = $this->configuration->getPaperSettings();
        $paperLocalSettings = new D3PrinterPaperSettings();
        $paperSize = $paperLocalSettings->getPaperSize();
        
        if ($paperSize !== $paper['paper_size']) {
            $set = new D3PrinterPaperSet();
            
        }
        
        $energy = $this->configuration->getEnergySettings();
        
        $isValid = true;
        
        if (!$isValid) {
            $this->logError('Configuration has been updated');
        }
        
        return $isValid;
    }
}
