<?php

namespace d3yii2\d3printer\logic;

use d3yii2\d3printer\logic\read\D3PrinterReadConfiguration;
use d3yii2\d3printer\logic\set\D3PrinterEnergySet;
use d3yii2\d3printer\logic\set\D3PrinterPaperSet;
use d3yii2\d3printer\logic\set\D3PrinterPrintSet;
use d3yii2\d3printer\logic\settings\D3PrinterEnergySettings;
use d3yii2\d3printer\logic\settings\D3PrinterPaperSettings;
use d3yii2\d3printer\logic\settings\D3PrinterPrintSettings;
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
        $configured = new D3PrinterPaperSettings();
        $configuredSize = $configured->getPaperSize();
        
        if ($configuredSize !== $printer['paper_size']) {
            $this->addInfo("Paper settings don't match: " . $configuredSize . ' | ' . $printer['paper_size']);
            return false;
        }
        
        return true;
    }
    
    /**
     * @throws Exception
     */
    public function updatePaperConfig(): void
    {
        $set = new D3PrinterPaperSet();
        
        $data = [
            'okSet' => 'Apply',
            'sizePromptSupported' => 'no',
            'DefaultPaperSize' => '14',
            'DefaultPaperType' => '27',
            'ManualFeed' => 'EWS_OFF',
            'SizePrompt' => 'EWS_OFF',
            'Duplex' => 'EWS_OFF',
            'Tray1Size' => '16',
            'Tray1Type' => '1',
            'PaperOutHandling' => 'EWS_OFF',
        ];
        
        $response = $set->update($data);
        $this->addInfo('Paper configuration updated to: ' . PHP_EOL . print_r($data, true));
    }
    
    /**
     * @throws Exception
     */
    public function updateEnergyConfig(): void
    {
        $set = new D3PrinterEnergySet();
        
        $data = [
            'ShutDown_timer_changed' => 'no',
            'AutoOff_timer_changed' => 'no',
            'aoao_active_off_supported' => '1',
            'AutoOff' => 'EWS_AO_15Min',
            'ShutDown' => 'EWS_SD_4Hours',
            'delayShutDown' => 'on',
            'Apply' => 'Apply',
        ];
        
        $response = $set->update($data);
        $this->addInfo('Energy configuration updated to: ' . PHP_EOL . print_r($data, true));
    }
    
    /**
     * @throws Exception
     */
    public function updatePrintConfig(): void
    {
        $set = new D3PrinterPrintSet();
        
        $data = [
            'Copies' => '1',
            'WideA4' => 'EWS_NO',
            'A5FeedOrientation' => 'Portrait',
            'Courier' => 'Courier_Regular',
            'Orientation' => 'orient_Portrait',
            'Apply' => 'Apply',
        ];
        
        $response = $set->update($data);
        $this->addInfo('Print configuration updated to: ' . PHP_EOL . print_r($data, true));
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
        
        $configured = new D3PrinterEnergySettings();
        $configuredSleep = $configured->getInactivitySleep();
        //$configuredShutdown = $configured->getShutdown();
        
        if ($configuredSleep !== $printerSleepData[0]) {
            $this->addInfo("Energy sleep setting don't match: " . $configuredSleep . ' | ' . $printerSleepData[0]);
            return false;
        }
        
        // Not in use
        /*if ($configuredShutdown === $printer['shut_down_after']) {
            $this->addInfo("Energy shutdown setting don't match: " . $configuredShutdown . ' | ' . $printer['shut_down_after']);
            return false;
        }*/
        
        return true;
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function printOrientationOk(): bool
    {
        $printer = $this->printerData->getPrintSettings();
        
        $configured = new D3PrinterPrintSettings();
        $configuredOrientation = $configured->getOrientation();
        
        if ($configuredOrientation !== $printer['orientation']) {
            $this->addInfo("Orientation setting don't match: " . $configuredOrientation . ' | ' . $printer['orientation']);
            return false;
        }
        
        // Not in use
        /*if ($configuredShutdown === $printer['shut_down_after']) {
            $this->addInfo("Energy shutdown setting don't match: " . $configuredShutdown . ' | ' . $printer['shut_down_after']);
            return false;
        }*/
        
        return true;
    }
}
