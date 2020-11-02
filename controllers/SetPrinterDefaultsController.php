<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\accessRights\D3PrinterFullUserRole;
use d3yii2\d3printer\logic\D3PrinterConfigurationHealth;
use d3yii2\d3printer\logic\D3PrinterDeviceHealth;
use d3yii2\d3printer\logic\D3PrinterHealth;
use eaBlankonThema\components\FlashHelper;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class SetPrinterDefaultsController extends Controller
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
     * Update the printer Configuration
     * @return Response
     * @throws GuzzleException
     */
    public function actionIndex(): Response
    {
        try {
            // Get the live data from printer Configuration page
            $configHealth = new D3PrinterConfigurationHealth();
            
            $configHealth->updatePaperConfig();
            $configHealth->updatePrintConfig();
            $configHealth->updateEnergyConfig();
            
            $alertErrorContent = '';
            $alertInfoContent = $configHealth->getMessages($configHealth->getInfo());
            
            if ($configHealth->hasErrors()) {
                $alertErrorContent .= PHP_EOL . 'Update errors:' . PHP_EOL . $configHealth->getMessages($configHealth->getErrors());
            }
            
            $alertMsg = $alertInfoContent . PHP_EOL . $alertErrorContent;
            
            $configHealth->logInfo($alertInfoContent . PHP_EOL . D3PrinterHealth::LOG_SEPARATOR . PHP_EOL);
    
            if ($configHealth->hasErrors() || $configHealth->hasErrors()) {
                FlashHelper::addDanger('Errors occured: ' . $alertErrorContent);
                $configHealth->logErrors($alertErrorContent . PHP_EOL . D3PrinterHealth::LOG_SEPARATOR . PHP_EOL);
                $configHealth->sendToEmail($alertMsg);
            } else {
                FlashHelper::addSuccess('Printer Configuration updated');
            }
            
            return $this->redirect(['/d3printer/device-info']);
            
        } catch (Exception $e) {
            FlashHelper::addDanger('Errors occured: ' . $e->getMessage());
            Yii::error($e->getMessage(), 'd3printer-error');
            Yii::error($e->getTraceAsString(), 'd3printer-error');
            return $this->redirect(['/d3printer/device-info']);
        }
    }
}
