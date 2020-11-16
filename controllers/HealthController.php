<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\accessRights\D3PrinterFullUserRole;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class HealthController
 * @package d3yii2\d3printer\controllers
 */
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
    public function actionIndex(string $component)
    {
        try {
            $message = Yii::$app->{$component}->commonHealth()->check();
            echo str_replace(PHP_EOL, '<br>', $message);
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
        }
    }
}
