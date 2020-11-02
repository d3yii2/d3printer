<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\D3CommandController;
use d3yii2\d3printer\logic\D3PrinterHealth;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\Exception;
use yii\console\ExitCode;

/**
 * Class HealthCronController
 * @package d3yii2\d3printer\controllers
 */
class HealthCronController extends D3CommandController
{
    
    /**
     * @throws GuzzleException
     *  Cron example: /usr/bin/php <sitepath>/yii d3printer/health-cron
     *  Console command: php yii d3printer-health-cron
     */
    public function actionIndex(): int
    {
        try {
            $health = new D3PrinterHealth();
            $message = $health->check();
            echo $message;
            return ExitCode::OK;
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
        }
    }
}

