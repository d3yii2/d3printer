<?php

namespace d3yii2\d3printer\logic;

use d3yii2\d3printer\logic\read\D3PrinterReadConfiguration;
use d3yii2\d3printer\logic\set\D3PrinterEnergySet;
use d3yii2\d3printer\logic\set\D3PrinterPaperSet;
use d3yii2\d3printer\logic\set\D3PrinterPrintSet;
use d3yii2\d3printer\logic\settings\D3PrinterEnergySettings;
use d3yii2\d3printer\logic\settings\D3PrinterPaperSettings;
use d3yii2\d3printer\logic\settings\D3PrinterPrintSettings;
use GuzzleHttp\Exception\GuzzleException;
use yii\base\Exception;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterConfigurationHealth extends D3PrinterHealth
{
    protected $printerData;
    
    /**
     * D3PrinterConfigurationHealth constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->printerData = new D3PrinterReadConfiguration();
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function paperSizeOk(): bool
    {
        $printer = $this->printerData->getPaperSettings();
        
        if (D3PrinterPaperSettings::DEFAULT_PAPER_SIZE !== $printer['tray1_size']) {
            $this->addInfo("Paper settings don't match: " . D3PrinterPaperSettings::DEFAULT_PAPER_SIZE . ' | ' . $printer['paper_size']);
            return false;
        }
        
        return true;
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function energySleepOk(): bool
    {
        $printer = $this->printerData->getEnergySettings();
        
        //@FIXME
        // Atbilde ir formātā: x Minute[s]. Ja uzdots stundās, salīdzināšana nestrādās 
        $printerSleepData = explode(' ', $printer['sleep_after']);
        
        if (D3PrinterEnergySettings::DEFAULT_SLEEP_AFTER !== $printerSleepData[0]) {
            $this->addInfo("Energy sleep setting don't match: " . D3PrinterEnergySettings::DEFAULT_SLEEP_AFTER . ' | ' . $printerSleepData[0]);
            return false;
        }
        
        return true;
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function printOrientationOk(): bool
    {
        $printer = $this->printerData->getPrintSettings();
        
        if (D3PrinterPrintSettings::DEFAULT_ORIENTATION !== $printer['orientation']) {
            $this->addInfo("Orientation setting don't match: " . D3PrinterPrintSettings::DEFAULT_ORIENTATION . ' | ' . $printer['orientation']);
            return false;
        }
        
        return true;
    }
    
    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function updatePaperConfig(): void
    {
        $paper = new D3PrinterPaperSet();
        if ($paper->update()) {
            $this->addInfo('Paper configuration updated to: ' . PHP_EOL . print_r($paper->getSentData(), true));
        }
    }
    
    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function updateEnergyConfig(): void
    {
        $energy = new D3PrinterEnergySet();
        if ($energy->update()) {
            $this->addInfo('Energy configuration updated to: ' . PHP_EOL . print_r($energy->getSentData(), true));
        }
    }
    
    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function updatePrintConfig(): void
    {
        $print = new D3PrinterPrintSet();
        if ($print->update()) {
            $this->addInfo('Print configuration updated to: ' . PHP_EOL . print_r($print->getSentData(), true));
        }
    }
}
