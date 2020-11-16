<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\D3CommandController;
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
     *  Cron example: /usr/bin/php <sitepath>/yii d3printer/health-cron <component name>
     *  Pass the component name as the $component
     * @param string $component
     * @return int
     */
    public function actionIndex(string $component)
    {
        try {
            $message = Yii::$app->{$component}->commonHealth()->check();
            echo $message;
            return ExitCode::OK;
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
        }
    }
}

