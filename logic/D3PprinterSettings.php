<?php

namespace d3yii2\d3printer\logic;

use Yii;
use yii\web\HttpException;

/**
 * Class D3Pprinter
 * @package d3yii2\d3printer\logic
 */
class D3PprinterSettings
{
    protected $settings;
    
    /**
     * D3Pprinter constructor.
     */
    public function __construct()
    {
        $this->settings = $this->getSettings();
    }
    
    /**
     * @param string $url
     * @return array
     */
    public function getSettings(): array
    {
        $settings = [
            'cartridge_min' => 9,
            'drum_min' => 9,
            'device_url' => '',
            'configuration_url' => '',
        ];
        
        return $settings;
    }
    
    public function getPrinterDeviceUrl()
    {
        return $this->settings['device_url'];
    }

    public function getPrinterConfigurationUrl()
    {
        return $this->settings['configuration_url'];
    }
    
    /**
     * @return string
     */
    public function getCartridgeMinValue(): string
    {
        return $this->settings['cartridge_min'];
    }
    
    /**
     * @return string
     */
    public function getDrumMinValue(): string
    {
        return $this->settings['drum_min'];
    }
}