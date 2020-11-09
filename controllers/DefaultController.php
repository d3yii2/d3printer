<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\read\D3PrinterReadConfiguration;
use d3yii2\d3printer\logic\read\D3PrinterReadDevice;
use yii\base\Exception;
use yii\console\Controller;

class DefaultController extends Controller
{
    
    /**
     * Testing returned data
     * @return bool
     * @throws Exception
     */
    public function actionIndex()
    {
        $printerDevice = new D3PrinterReadDevice();
        $status = $printerDevice->getStatus();
        echo 'Status:' . PHP_EOL . $status . PHP_EOL;
        
        $cartridgeRemaining = $printerDevice->getCartridgeRemaining();
        echo 'Cartridge:' . PHP_EOL . $cartridgeRemaining . PHP_EOL;
        
        $drumRemaining = $printerDevice->getDrumRemaining();
        echo 'Drum:' . PHP_EOL . $drumRemaining . PHP_EOL;
        
        $printerConfig = new D3PrinterReadConfiguration();
        $paperSettings = $printerConfig->getPaperSettings();
        echo 'Paper Settings:' . PHP_EOL . print_r($paperSettings, true);
    
        $printSettings = $printerConfig->getPrintSettings();
        echo 'Print Settings:' . PHP_EOL . print_r($printSettings, true);

        $energySettings = $printerConfig->getEnergySettings();
        echo 'Energy Settings:' . PHP_EOL . print_r($energySettings, true);
    
        return true;
    }
}
