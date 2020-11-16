<?php

namespace d3yii2\d3printer\components;

use d3yii2\d3printer\logic\health\CommonHealth;
use d3yii2\d3printer\logic\health\ConfigurationHealth;
use d3yii2\d3printer\logic\health\DeviceHealth;
use yii\base\Component;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\components
 */
class D3Printer extends Component
{
    public $printerCode;
    public $printerName;
    public $accessSettings = [];
    
    public function deviceHealth()
    {
        return new DeviceHealth($this->accessSettings, $this->printerCode, $this->printerName);
    }
    
    public function configHealth()
    {
        return new ConfigurationHealth($this->accessSettings, $this->printerCode, $this->printerName);
    }
    
    public function commonHealth()
    {
        return new CommonHealth($this->accessSettings, $this->printerCode, $this->printerName);
    }
}
