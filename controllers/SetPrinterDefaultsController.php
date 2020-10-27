<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\D3PrinterConfigurationHealth;
use d3yii2\d3printer\logic\D3PrinterDeviceHealth;
use eaBlankonThema\components\FlashHelper;
use eaBlankonThema\widget\ThAlert;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\bootstrap\Alert;
use yii\web\Controller;
use yii\web\Response;

class SetPrinterDefaultsController extends Controller
{
    /**
     * Update the printer Configuration
     * @return Response
     * @throws GuzzleException
     */
    public function actionIndex(): Response
    {
        try {
            /**
             *  Get the devive live data from printer Homepage
             */
            
            // Get the devive live data from printer Configuration page
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
            
            $configHealth->logInfo($alertInfoContent . PHP_EOL . '-------------' . PHP_EOL);
    
            if ($configHealth->hasErrors() || $configHealth->hasErrors()) {
                FlashHelper::addDanger('Errors occured: ' . $alertErrorContent);
                $configHealth->logErrors($alertErrorContent . PHP_EOL . '-------------' . PHP_EOL);
                $configHealth->sendToEmail($alertMsg);
            } else {
                FlashHelper::addSuccess('Printer Configuration updated');
            }
            
            return $this->redirect(['/d3printer/device-info']);
            
        } catch (Exception $e) {
            FlashHelper::addDanger('Errors occured: ' . $e->getMessage());
            Yii::error($e->getMessage(), 'd3printer-error');
            return $this->redirect(['/d3printer/device-info']);
        }
    }
}
