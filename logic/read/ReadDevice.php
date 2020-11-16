<?php

namespace d3yii2\d3printer\logic\read;


/**
 * Class ReadDevice
 * @package d3yii2\d3printer\logic\read
 */
class ReadDevice extends Read
{
    private const PARSE_STATUS_EXPR = "//*[@id='deviceStatus_tableCell']";
    private const PARSE_CARTRIDGE_EXPR = "//table[@class='mainContentArea width100 pad10']//table[@class='width100']/tr[1]/td[2]";
    private const PARSE_DRUM_EXPR = "//table[@class='mainContentArea width100 pad10']/tr/td[2]/table[@class='width100']/tr/td[2]";
    
    /**
     * @param string $expr
     * @return string|null
     */
    public function status(string $expr = self::PARSE_STATUS_EXPR): ?string
    {
        $statusNode = $this->parse($expr);
        
        if (0 === count($statusNode)) {
            return null;
        }
        
        return trim($statusNode->item(0)->nodeValue);
    }
    
    /**
     * @param string $expr
     * @return string|null
     */
    public function cartridgeRemaining(string $expr = self::PARSE_CARTRIDGE_EXPR): ?string
    {
        $statusNode = $this->parse($expr);
        
        if (0 === count($statusNode)) {
            return null;
        }
        
        $percent = trim($statusNode->item(0)->nodeValue);
        
        $percent = intval(str_replace('%', '', $percent));
        
        return $percent;
    }
    
    /**
     * @param string $expr
     * @return string|null
     */
    public function drumRemaining(string $expr = self::PARSE_DRUM_EXPR): ?string
    {
        $statusNode = $this->parse($expr);
        
        if (0 === count($statusNode)) {
            return null;
        }
        
        $percent = trim($statusNode->item(0)->nodeValue);
        
        $percent = intval(str_replace('%', '', $percent));
        
        return $percent;
    }
}
