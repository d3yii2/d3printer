<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\D3PrinterHealth;
use GuzzleHttp\Exception\GuzzleException;
use yii\base\Exception;
use Yii;
use yii\console\Controller;

/**
* Class PrinterHealthController
*/
class HealthCronController extends Controller
{
    
    /**
     * @throws GuzzleException
     *  Cron example: /usr/bin/php <sitepath>/yii d3printer/health-cron
     */
    public function actionIndex()
    {
        try {
            $health = new D3PrinterHealth();
            $message = $health->check();
            echo $message;
        
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
        }
    }
}

