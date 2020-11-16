<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\accessRights\D3PrinterFullUserRole;
use d3yii2\d3printer\components\D3Printer;
use d3yii2\d3printer\logic\D3PrinterConfigurationHealth;
use d3yii2\d3printer\logic\health\ConfigurationHealth;
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
     * Update the printer ConfigurationHealth
     * @return Response
     * @throws GuzzleException
     */
    public function actionIndex(string $component): Response
    {
        try {
            // Get the live data from printer ConfigurationHealth page
            /** @var ConfigurationHealth $configHealth */
            $configHealth = Yii::$app->{$component}->configHealth();
            
            $configHealth->updatePaperConfig();
            $configHealth->updatePrintConfig();
            $configHealth->updateEnergyConfig();
            
            $alertErrorContent = '';
            $alertInfoContent = $configHealth->logger->getInfoMessages();
            
            if ($configHealth->logger->hasErrors()) {
                $alertErrorContent .= PHP_EOL . 'Update errors:' . PHP_EOL . $configHealth->logger->getErrorMessages();
            }
            
            $alertMsg = $alertInfoContent . PHP_EOL . $alertErrorContent;
            
            $configHealth->logger->logInfo($alertInfoContent);
            
            if ($configHealth->logger->hasErrors() || $configHealth->logger->hasErrors()) {
                FlashHelper::addDanger('Errors occured: ' . $alertErrorContent);
                $configHealth->logger->logErrors($alertErrorContent);
                $configHealth->logger->sendToEmail($alertMsg);
            } else {
                FlashHelper::addSuccess('Printer ConfigurationHealth updated');
            }
            
            return $this->redirect(['/d3printer/device-info', 'component' => $component]);
            
        } catch (Exception $e) {
            FlashHelper::addDanger('Errors occured: ' . $e->getMessage());
            Yii::error($e->getMessage(), 'd3printer-error');
            Yii::error($e->getTraceAsString(), 'd3printer-error');
            return $this->redirect(['/d3printer/device-info']);
        }
    }
}
