<?php

namespace d3yii2\d3printer\logic\read;


/**
 * Class ReadDeviceCached
 * @package d3yii2\d3printer\logic\read
 */
class ReadDeviceCached extends ReadCached
{

    /**
     * @return string|null
     */
    public function status(): ?string
    {
        return $this->getValue('status');
    }
    
    /**
     * @return string|null
     */
    public function cartridgeRemaining(): ?string
    {
        return $this->getValue('cartridgeRemaining');
    }
    
    /**
     * @return string|null
     */
    public function drumRemaining(): ?string
    {
        return $this->getValue('drumRemaining');
    }
}
