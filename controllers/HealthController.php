<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\D3PrinterHealth;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\web\Controller;

class HealthController extends Controller
{
    /**
     * Check the printer status, cartridge and drum
     * If less than normal, send the alert to Email
     * @return void
     * @throws GuzzleException
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
