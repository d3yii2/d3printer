<?php

namespace d3yii2\d3printer\logic\health;

use d3yii2\d3printer\logic\read\ReadDevice;

/**
 * Class DeviceHealth
 * @package d3yii2\d3printer\logic\health
 */
class DeviceHealth extends Health
{
    public const STATUS_READY = 'ready';
    public const STATUS_DOWN = 'off';
    public const STATUS_PRINTING = 'printing';
    public const STATUS_SLEEP = 'sleep';
    
    public $device;
    
    public function init()
    {
        parent::init();
        $this->device = new ReadDevice($this->accessSettings['home_url']);
        $this->logger->addInfo('Device Health' . PHP_EOL . 'Printer: ' . $this->printerName . ' (' . $this->printerCode . ')');
    }
    
    /**
     * @return bool
     */
    public function statusOk(): bool
    {
        $status = $this->device->status();
        
        if (!$status) {
            $this->logger->addError('Cannot parse Status value');
            return false;
        }
        
        $status = strtolower($status);
        if (
            strpos($status, self::STATUS_READY) !== false
            || strpos($status, self::STATUS_PRINTING) !== false
            || strpos($status, self::STATUS_SLEEP) !== false
        ) {
            $this->logger->addInfo('Status OK (' . $status . ')');
            return true;
        }
        
        $this->logger->addError('Device looks down! Status: "' . $status . '"');
        return false;
    }
    
    /**
     * @return bool
     */
    public function cartridgeOk(): bool
    {
        $remaining = $this->device->cartridgeRemaining();
        
        if (!$remaining) {
            $this->logger->addError('Cannot parse Cartridge value');
            return false;
        }
        
        $min = $this->alertSettings->getCartridgeMinValue();
        
        if ($remaining > $min) {
            $this->logger->addInfo('Cartridge OK (' . $remaining . '%)');
            return true;
        }
        
        $this->logger->addError('Remaining Cartrige level too low: ' . $remaining . '% (Configured minimum:  ' . $min . '%)');
        return false;
    }
    
    /**
     * @return bool
     */
    public function drumOk(): bool
    {
        $remaining = $this->device->drumRemaining();
    
        if (!$remaining) {
            $this->logger->addError('Cannot parse Drum value');
            return false;
        }
        
        $min = $this->alertSettings->getDrumMinValue();
        
        if ($remaining > $min) {
            $this->logger->addInfo('Drum OK (' . $remaining . '%)');
            return true;
        }
        
        $this->logger->addError('Remaining Drum too low: ' . $remaining . '% (Configured minimum:  ' . $min . '%)');
        return false;
    }
}
