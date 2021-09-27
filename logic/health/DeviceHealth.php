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
    
    /**
     * @var ReadDevice $device
     */
    public $device;
    
    public function init()
    {
        parent::init();
        $this->device = new ReadDevice($this->getAccessUrl());
        $this->logger->addInfo('Device Health' . PHP_EOL . 'Printer: ' . $this->printerName . ' (' . $this->printerCode . ')');
    }
    
    /**
     * @return string
     */
    public function getAccessUrl(): string
    {
        return $this->accessSettings['home_url'];
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
        $value = $this->device->cartridgeRemaining();
        
        if (!$value) {
            $this->logger->addError('Cartrige level not readable! (Displayed: ' . $value . ')');
            return false;
        }
    
        if (strstr($value, '<')) {
            $this->logger->addError('Cartrige level too low! ' . $value);
            return false;
        }
        
        $min = $this->alertSettings->getCartridgeMinValue();

        if ((float) $value > (float) $min) {
            $this->logger->addInfo('Cartridge OK (' . $value . ')');
            return true;
        }
        
        $this->logger->addError('Remaining Cartrige level too low: ' . $value . ' (Minimum is:  ' . $min . ')');
        return false;
    }
    
    /**
     * @return bool
     */
    public function drumOk(): bool
    {
        $value = $this->device->drumRemaining();
    
        if (!$value) {
            $this->logger->addError('Drum value not readable! (Displayed: ' . $value . ')');
            return false;
        }
    
        if (strstr($value, '<')) {
            $this->logger->addError('Drum value too low! ' . $value);
            return false;
        }

        $min = $this->alertSettings->getDrumMinValue();
        
        if ((float) $value > (float) $min) {
            $this->logger->addInfo('Drum OK (' . $value . ')');
            return true;
        }
        
        $this->logger->addError('Remaining Drum too low: ' . $value . ' (Minimum is:  ' . $min . ')');
        return false;
    }
}
