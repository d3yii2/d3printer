<?php

namespace d3yii2\d3printer\logic\read;


use yii\base\Exception;

/**
 * Class ReadDevice
 * @package d3yii2\d3printer\logic\read
 */
class ReadDevice extends Read
{
    protected $cartridgeDisplayedValue;
    protected $drumDisplayedValue;
    
    private const PARSE_STATUS_EXPR = "//*[@id='deviceStatus_tableCell']";
    private const PARSE_CARTRIDGE_EXPR = "//table[@class='mainContentArea width100 pad10']//table[@class='width100']/tr[1]/td[2]";
    private const PARSE_DRUM_EXPR = "//table[@class='mainContentArea width100 pad10']/tr/td[2]/table[@class='width100']/tr/td[2]";
    
    /**
     * @return mixed
     */
    public function getCartridgeDisplayedValue()
    {
        return $this->cartridgeDisplayedValue;
    }

    /**
     * @return mixed
     */
    public function getDrumDisplayedValue()
    {
        return $this->drumDisplayedValue;
    }


    /**
     * @param string $expr
     * @return string|null
     * @throws Exception
     */
    public function status(string $expr = self::PARSE_STATUS_EXPR): ?string
    {
        $statusNode = $this->parse($expr);
        
        if (0 === count($statusNode)) {
            return null;
        }
        
        return preg_replace(
            '#\W+#',
            ' ',
            trim($statusNode->item(0)->nodeValue)
        );
    }

    /**
     * @param string $expr
     * @return string|null
     * @throws Exception
     */
    public function cartridgeRemaining(string $expr = self::PARSE_CARTRIDGE_EXPR): ?string
    {
        $statusNode = $this->parse($expr);
        
        if (0 === count($statusNode)) {
            return null;
        }
        
        $this->cartridgeDisplayedValue = $this->getSanitizedValue($statusNode->item(0));
        
        return $this->cartridgeDisplayedValue;
    }

    /**
     * @param string $expr
     * @return string|null
     * @throws Exception
     */
    public function drumRemaining(string $expr = self::PARSE_DRUM_EXPR): ?string
    {
        $statusNode = $this->parse($expr);
        
        if (0 === count($statusNode)) {
            return null;
        }
    
        $this->drumDisplayedValue = $this->getSanitizedValue($statusNode->item(0));
        
        return $this->drumDisplayedValue;
    }
}
