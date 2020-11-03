<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\accessRights\D3PrinterFullUserRole;
use d3yii2\d3printer\logic\D3PrinterHealth;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class HealthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                        ],
                        'roles' => [D3PrinterFullUserRole::NAME],
                    ],
                ],
            ],
        ];
    }
    
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
           echo str_replace(PHP_EOL, '<br>', $message);
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
        }
    }
}
