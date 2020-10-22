<?php

namespace d3yii2\d3printer\logic;

use Yii;
use yii\web\HttpException;

/**
 * Class D3Pprinter
 * @package d3yii2\d3printer\logic
 */
class D3PprinterDeviceHealth extends D3PprinterHealth
{
    protected $device;
    
    /**
     * D3Pprinter constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->device = new D3PprinterReadDevice();
    }
    
    public function checkStatus(): bool
    {
        $status = $this->device->getStatus();
        
        $isOk = D3PprinterReadDevice::STATUS_READY === $status;
        
        if (!$isOk) {
            $this->logAlert("Device looks down! Status: " . $status);
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
        
        $min = $this->device->getSettings()->getCartridgeMinValue();
    
        $isOk = $remaining > $min ;
    
        if (!$isOk) {
            $this->logAlert('Cartrige level is too low: ' . $remaining . '% (should be > ' . $min . '%)');
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
    
        $min = $this->device->getSettings()->getDrumMinValue();
    
        $isOk = $remaining > $min;
    
        if (!$isOk) {
            $this->logAlert('Drum is too low: ' . $remaining . '% (should be > ' . $min . '%)');
        }

        return $isOk;
    }
}
