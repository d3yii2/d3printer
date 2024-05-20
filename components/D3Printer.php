<?php

namespace d3yii2\d3printer\components;

use d3yii2\d3printer\logic\health\CommonHealth;
use d3yii2\d3printer\logic\health\ConfigurationHealth;
use d3yii2\d3printer\logic\health\DeviceHealth;
use d3yii2\d3printer\logic\health\SpoolerHealth;
use yii\base\Component;
use yii\base\Exception;
use Yii;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\components
 */
class D3Printer extends Component
{
    public $printerCode;
    public $printerName;
    public $accessSettings = [];
    public $leftMenu;
    public $leftMenuUrl;
    
    /**
     * @param string $key
     * @return mixed|object|null
     * @throws \yii\base\Exception
     */
    public static function getPrinterComponent(string $key)
    {
        if (!isset(Yii::$app->{$key})) {
            throw new Exception('Missing Printer config for: ' . $key);
        }
        return Yii::$app->{$key};
    }
    
    public function deviceHealth($cached = false)
    {
        return new DeviceHealth($this->accessSettings, $this->printerCode, $this->printerName, $cached);
    }
    
    public function configHealth($cached = false)
    {
        return new ConfigurationHealth($this->accessSettings, $this->printerCode, $this->printerName, $cached);
    }
    
    public function commonHealth($cached = false)
    {
        return new CommonHealth($this->accessSettings, $this->printerCode, $this->printerName, $cached);
    }

    public function spoolerHealth($cached = false)
    {
        return new SpoolerHealth($this->accessSettings, $this->printerCode, $this->printerName, $cached);
    }
}
