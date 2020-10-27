<?php

namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\logic\D3PrinterConfigurationHealth;
use d3yii2\d3printer\logic\D3PrinterDeviceHealth;
use Exception;
use Yii;
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
        try {
            /**
             *  Get the devive live data from printer Homepage
             */
            
            $deviceHealth = new D3PrinterDeviceHealth();
            
            // Check the state of the device: Alive / Off
            $deviceHealth->statusOk();
            
            // Check the Cartridge remaining %
            $deviceHealth->cartridgeOk();
            
            // Check the Drum remaining %
            $deviceHealth->drumOk();
            
            // Get the devive live data from printer Configuration page
            $configHealth = new D3PrinterConfigurationHealth();
            
            
            /**
             * Compare System configuration with Printer data
             */
            
            if (!$configHealth->paperSizeOk()) {
                $configHealth->updatePaperConfig();
            }
            
            if (!$configHealth->printOrientationOk()) {
                $configHealth->updatePrintConfig();
            }
            
            if (!$configHealth->energySleepOk()) {
                $configHealth->updateEnergyConfig();
            }
            
            $alertInfoContent = $deviceHealth->getMessages($deviceHealth->getInfo()) . PHP_EOL;
            $alertErrorContent = '';
            
            if ($deviceHealth->hasErrors()) {
                $alertErrorContent .= PHP_EOL . 'Device Health Problems:' . PHP_EOL . $deviceHealth->getMessages($deviceHealth->getErrors());
            }
            
            $alertInfoContent .= PHP_EOL . PHP_EOL . $configHealth->getMessages($configHealth->getInfo());
            
            if ($configHealth->hasErrors()) {
                $alertErrorContent .= PHP_EOL . 'Config Health Problems:' . PHP_EOL . $configHealth->getMessages($configHealth->getErrors());
            }
            
            $alertMsg = $alertInfoContent . PHP_EOL . $alertErrorContent;
            echo $alertMsg;
            //echo str_replace(PHP_EOL, '<br>', $alertInfoContent . PHP_EOL . $alertErrorContent);
            
            $deviceHealth->logInfo($alertInfoContent . PHP_EOL . '-------------' . PHP_EOL);
    
            if ($deviceHealth->hasErrors() || $configHealth->hasErrors()) {
                $deviceHealth->logErrors($alertErrorContent . PHP_EOL . '-------------' . PHP_EOL);
                $deviceHealth->sendToEmail($alertMsg);
            }
            
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
        }
    }
}
