<?php

namespace d3yii2\d3printer\logic\health;

use d3yii2\d3printer\logic\Logger;
use GuzzleHttp\Exception\GuzzleException;
use yii\base\Exception;

/**
 * Class CommonHealth
 * @package d3yii2\d3printer\logic\health
 */
class CommonHealth extends Health
{
    /** @var DeviceHealth $deviceHealth */
    protected $deviceHealth;
    
    /** @var ConfigurationHealth $configHealth */
    protected $configHealth;
    
    public function init()
    {
        parent::init();
        $this->deviceHealth = new DeviceHealth($this->accessSettings, $this->printerCode, $this->printerName);
        $this->configHealth = new ConfigurationHealth($this->accessSettings, $this->printerCode, $this->printerName);
    }
    
    /**
     * @return string
     * @throws GuzzleException
     * @throws Exception
     */
    public function check(): string
    {
        $alertInfoContent = '';
        $alertErrorContent = '';
        
        /**
         *  Get the device live data from printer Homepage
         */
        
        // Check the state of the device: Alive / Off
        $this->deviceHealth->statusOk();
        
        // Check the Cartridge remaining %
        $this->deviceHealth->cartridgeOk();
        
        // Check the Drum remaining %
        $this->deviceHealth->drumOk();
        
        // Get info messages from device logger
        $alertInfoContent = $this->deviceHealth->logger->getInfoMessages();
        
        // Get error messages from device logger
        if ($this->deviceHealth->logger->hasErrors()) {
            $alertErrorContent .= PHP_EOL . 'Device Health Problems:' . PHP_EOL . $this->deviceHealth->logger->getErrorMessages();
        }
        
        /**
         * Compare System configuration with Printer data
         */
        
        if (!$this->configHealth->paperSizeOk()) {
            $this->configHealth->updatePaperConfig();
        }
        
        if (!$this->configHealth->printOrientationOk()) {
            $this->configHealth->updatePrintConfig();
        }
        
        // Sleep is ok at this time and config change detected by print settings (Orientation)
        /*if (!$this->configHealth->energySleepOk()) {
            $this->configHealth->updateEnergyConfig();
        }*/
        
        $alertInfoContent .= PHP_EOL . PHP_EOL . $this->configHealth->logger->getInfoMessages();
        $this->deviceHealth->logger->logInfo($alertInfoContent);
        
        if ($this->configHealth->logger->hasErrors()) {
            $alertErrorContent .= PHP_EOL . 'Config Health Problems:' . PHP_EOL . $this->configHealth->logger->getErrorMessages();
        }
        
        $alertMsg = $alertInfoContent . $alertErrorContent;
        
        if ($this->deviceHealth->logger->hasErrors() || $this->configHealth->logger->hasErrors()) {
            $this->deviceHealth->logger->logErrors($alertErrorContent);
            
            if ($this->deviceHealth->logger->isNewLogHash($alertErrorContent)) {
                
                $conf = [
                    'from' => $this->alertSettings->getEmailFrom(),
                    'to' => $this->alertSettings->getEmailTo(),
                    'subject' => $this->alertSettings->getEmailSubject(),
                ];
                
                $this->deviceHealth->mailer->send($alertMsg, $conf);
            }
            
            $this->deviceHealth->logger->updateLogHash($alertErrorContent);
        } else {
            $this->deviceHealth->logger->deleteLogHash();
        }
        
        return $alertMsg;
    }
}