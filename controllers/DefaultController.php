<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\read\D3PrinterReadConfiguration;
use d3yii2\d3printer\logic\read\D3PrinterReadDevice;
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
        $printerDevice = new D3PrinterReadDevice();
        $status = $printerDevice->getStatus();
        $cartridgeRemaining = $printerDevice->getCartridgeRemaining();
        $drumRemaining = $printerDevice->getDrumRemaining();
        
        $printerConfig = new D3PrinterReadConfiguration();
        $paperSettings = $printerConfig->getPaperSettings();
        $printSettings = $printerConfig->getPrintSettings();
        $energySettings = $printerConfig->getEnergySettings();
        
        return true;
    }
}
