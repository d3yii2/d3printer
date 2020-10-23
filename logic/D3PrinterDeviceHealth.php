<?php

namespace d3yii2\d3printer\logic;

use d3yii2\d3printer\logic\read\D3PrinterReadDevice;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterDeviceHealth extends D3PrinterHealth
{
    protected $device;
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->device = new D3PrinterReadDevice();
    }
    
    public function checkStatus(): bool
    {
        $status = $this->device->getStatus();
        
        $isOk = D3PrinterReadDevice::STATUS_READY === $status;
        
        if (!$isOk) {
            $this->logError("Device looks down! Status: " . $status);
        }
        
        return $isOk;
    }
    
    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function checkCartridge(): bool
    {
        $remaining = $this->device->getCartridgeRemaining();
        
        $min = $this->alertSettings->getCartridgeMinValue();
        
        $isOk = $remaining > $min;
        
        if (!$isOk) {
            $this->logError('Cartrige level is too low: ' . $remaining . '% (should be > ' . $min . '%)');
        }
        
        return $isOk;
    }
    
    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function checkDrum(): bool
    {
        $remaining = $this->device->getDrumRemaining();
        
        $min = $this->alertSettings->getDrumMinValue();
        
        $isOk = $remaining > $min;
        
        if (!$isOk) {
            $this->logError('Drum is too low: ' . $remaining . '% (should be > ' . $min . '%)');
        }
        
        return $isOk;
    }
}
