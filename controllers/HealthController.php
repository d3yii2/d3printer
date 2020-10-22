<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\D3PprinterDeviceHealth;
use d3yii2\d3printer\logic\D3PprinterHealth;
use yii\web\Controller;

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
        $deviceHealth = new D3PprinterDeviceHealth();
        
        $deviceHealth->checkStatus();
        $deviceHealth->checkCartridge();
        $deviceHealth->checkDrum();
        
        if ($deviceHealth->hasAlerts()) {
            $deviceHealth->sendAlerts();
        } else {
            echo 'Health OK';
        }
    }
}
