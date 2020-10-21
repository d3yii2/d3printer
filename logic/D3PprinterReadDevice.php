<?php

namespace d3yii2\d3printer\logic;

use yii\base\Exception;

/**
 * Class D3PprinterReadDevice
 * @package d3yii2\d3printer\logic
 */
class D3PprinterReadDevice extends D3PprinterRead
{
    public const STATUS_READY = 'Ready';
    public const STATUS_DOWN = 'Off';
    
    /**
     * @return string
     */
    public function getPrinterPageUrl(): string
    {
        return 'http://cewood.weberp.loc/printer/Home.html';
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
        if (self::STATUS_READY !== $status && self::STATUS_DOWN !== $status) {
            throw new Exception('Status value is not correct');
        }
        
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
    
    
}