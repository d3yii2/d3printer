<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\D3PprinterReadConfiguration;
use d3yii2\d3printer\logic\D3PprinterReadDevice;
use yii\web\Controller;

class DefaultController extends Controller
{
    /**
     * Testing returned data
     * @return bool
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $printerDevice = new D3PprinterReadDevice();
        $status = $printerDevice->getStatus();
        $cartridgeRemaining = $printerDevice->getCartridgeRemaining();
        $drumRemaining = $printerDevice->getDrumRemaining();
        
        $printerConfig = new D3PprinterReadConfiguration();
        $paperSettings = $printerConfig->getPaperSettings();
        $printSettings = $printerConfig->getPrintSettings();
        $energySettings = $printerConfig->getEnergySettings();
        
        return true;
    }
}
