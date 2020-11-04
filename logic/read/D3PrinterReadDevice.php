<?php

namespace d3yii2\d3printer\logic\read;

use d3yii2\d3printer\logic\settings\D3PrinterAccessSettings;
use yii\base\Exception;

/**
 * Class D3PrinterReadDevice
 * @package d3yii2\d3printer\logic
 */
class D3PrinterReadDevice extends D3PrinterRead
{
    public const STATUS_READY = 'Ready';
    public const STATUS_DOWN = 'Off';
    public const STATUS_PRINTING = 'Printing document.';
    
    protected $accessSettings;
    
    /**
     * D3PrinterReadDevice constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->accessSettings = new D3PrinterAccessSettings();
        parent::__construct();
    }
    
    /**
     * @return string|null
     * @throws Exception
     */
    public function getStatus(): ?string
    {
        $statusNode = $this->parse("//*[@id='deviceStatus_tableCell']");
        
        if (0 === count($statusNode)) {
            echo 'Cannot parse status value';
            return null;
        }
        
        $status = trim($statusNode->item(0)->nodeValue);
        
        // Something was wrong... Readed status value is not the same as expected.
        // if (self::STATUS_READY !== $status && self::STATUS_DOWN !== $status) {
        //     throw new Exception('Status value is not correct');
        // }
        
        return $status;
    }
    
    /**
     * @return string
     * @throws Exception
     */
    public function getCartridgeRemaining(): ?string
    {
        $statusNode = $this->parse("//table[@class='mainContentArea width100 pad10']//table[@class='width100']/tr[1]/td[2]");
        
        if (0 === count($statusNode)) {
            echo 'Cannot parse Cartridge remaining percentage';
            return null;
        }
        
        $percent = trim($statusNode->item(0)->nodeValue);
        
        $percent = intval(str_replace('%', '', $percent));
        
        return $percent;
    }
    
    /**
     * @return string
     * @throws Exception
     */
    public function getDrumRemaining(): ?string
    {
        $statusNode = $this->parse("//table[@class='mainContentArea width100 pad10']/tr/td[2]/table[@class='width100']/tr/td[2]");
        
        if (0 === count($statusNode)) {
            echo 'Cannot parse Drum remaining percentage';
            return null;
        }
        
        $percent = trim($statusNode->item(0)->nodeValue);
        
        $percent = intval(str_replace('%', '', $percent));
        
        return $percent;
    }
    
    /**
     * @return string
     */
    protected function getConnectionUrl(): string
    {
        return $this->accessSettings->getPrinterDeviceUrl();
    }
}
