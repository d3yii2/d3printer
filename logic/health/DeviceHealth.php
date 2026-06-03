<?php

namespace d3yii2\d3printer\logic\health;

use d3yii2\d3printer\logic\read\ReadDevice;
use d3yii2\d3printer\logic\read\ReadDeviceCached;

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
    
    /**
     * @throws \yii\base\Exception
     */
    public function init()
    {
        parent::init();
        $this->device = $this->cached
            // Get the last cached state from runtime file
            ? new ReadDeviceCached($this->printerCode)
            // Get live data from printer
            : new ReadDevice($this->getAccessUrl());
        $this->logger->addInfo(
            'Printer: ' . trim($this->printerName) . ' (' . trim($this->printerCode) . ')' . PHP_EOL .
            'Health:' . PHP_EOL);
    }
    
    /**
     * @return string
     */
    public function getAccessUrl(): string
    {
        return $this->accessSettings['home_url'];
    }
    
    public function getStatus()
    {
        return $this->device->status();
    }
    
    /**
     * @return bool
     */
    public function statusOk(): bool
    {
        $status = $this->getStatus();
        
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
    
    
    public function getCartridgeRemaining()
    {
        $remain = preg_replace('/[^\d%]+/', '', $this->device->cartridgeRemaining());
        if ($remain === '%%') {
            return '0%';
        }
        return $remain;
    }
    
    /**
     * @return bool
     */
    public function cartridgeOk(): bool
    {
        $value = $this->getCartridgeRemaining();
        
        if (!$value) {
            $this->logger->addError('Cartridge level not readable! (Displayed: ' . $value . ')');
            return false;
        }
    
        if (strstr($value, '<')) {
            $this->logger->addError('Cartridge level too low! ' . $value);
            return false;
        }
        
        $min = $this->alertSettings->getCartridgeMinValue();

        if ((float) $value > (float) $min) {
            $this->logger->addInfo('Cartridge OK (' . trim($value) . ')');
            return true;
        }
        
        $this->logger->addError('Remaining Cartridge level too low: ' . $value . ' (Minimum is:  ' . $min . ')');
        return false;
    }
    
    public function getDrumRemaining()
    {
        return preg_replace('/[^\d%]+/', '', $this->device->drumRemaining());
    }
    
    
    /**
     * @return bool
     */
    public function drumOk(): bool
    {
        $value = $this->getDrumRemaining();
    
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
