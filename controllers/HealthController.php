<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\D3PrinterConfigurationHealth;
use d3yii2\d3printer\logic\D3PrinterDeviceHealth;
use yii\web\Controller;
use Yii;
use Exception;

class HealthController extends Controller
{
    /**
     * Check the printer status, cartridge and drum
     * If less than normal, send the alert to Email
     * @return bool
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        try {
            $deviceHealth = new D3PrinterDeviceHealth();
    
            $deviceHealth->checkStatus();
            $deviceHealth->checkCartridge();
            $deviceHealth->checkDrum();
    
            $configHealth = new D3PrinterConfigurationHealth();
            if (!$configHealth->isValid()) {
        
            }
    
            if ($deviceHealth->hasAlerts()) {
                $deviceHealth->sendAlerts();
            } else {
                echo 'Health OK';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage());
            
        }
    }
}
