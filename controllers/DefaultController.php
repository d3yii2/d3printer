<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\read\ReadConfiguration;
use d3yii2\d3printer\logic\read\ReadDevice;
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
        $printerDevice = new ReadDevice();
        $status = $printerDevice->status();
        echo 'Status:' . PHP_EOL . $status . PHP_EOL;
        
        $cartridgeRemaining = $printerDevice->cartridgeRemaining();
        echo 'Cartridge:' . PHP_EOL . $cartridgeRemaining . PHP_EOL;
        
        $drumRemaining = $printerDevice->drumRemaining();
        echo 'Drum:' . PHP_EOL . $drumRemaining . PHP_EOL;
        
        $printerConfig = new ReadConfiguration();
        $paperSettings = $printerConfig->paperSettings();
        echo 'Paper Settings:' . PHP_EOL . print_r($paperSettings, true);
        
        $printSettings = $printerConfig->printSettings();
        echo 'Print Settings:' . PHP_EOL . print_r($printSettings, true);
        
        $energySettings = $printerConfig->energySettings();
        echo 'Energy Settings:' . PHP_EOL . print_r($energySettings, true);
        
        return true;
    }
}
