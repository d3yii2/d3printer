<?php

namespace d3yii2\d3printer\logic;

use d3yii2\d3printer\logic\read\D3PrinterReadDevice;
use yii\base\Exception;

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
    
    public function statusOk(): bool
    {
        $status = $this->device->getStatus();
        if(
            strpos($status,D3PrinterReadDevice::STATUS_READY) !== false
            || strpos($status,D3PrinterReadDevice::STATUS_PRINTING) !== false
            || strpos($status,D3PrinterReadDevice::STATUS_SLEEP) !== false
        ){
            $this->addInfo('Status OK');
            return true;
        }
        
        $this->addError('Device looks down! Status: "' . $status . '"');
        return false;
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function cartridgeOk(): bool
    {
        $remaining = $this->device->getCartridgeRemaining();
        
        $min = $this->alertSettings->getCartridgeMinValue();
        
        if ($remaining > $min) {
            $this->addInfo('Cartridge OK (' . $remaining . '%)');
            return true;
        }
        
        $this->addError('Cartrige level is too low: ' . $remaining . '% (should be > ' . $min . '%)');
        return false;
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function drumOk(): bool
    {
        $remaining = $this->device->getDrumRemaining();
        
        $min = $this->alertSettings->getDrumMinValue();
        
        if ($remaining > $min) {
            $this->addInfo('Drum OK (' . $remaining . '%)');
            return true;
        }
        
        $this->addError('Drum is too low: ' . $remaining . '% (should be > ' . $min . '%)');
        return false;
    }
}
