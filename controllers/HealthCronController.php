<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\D3CommandController;
use d3system\helpers\D3FileHelper;
use d3yii2\d3printer\components\D3Printer;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\Exception;
use yii\console\ExitCode;
use yii\helpers\Json;

/**
 * Class HealthCronController
 * @package d3yii2\d3printer\controllers
 */
class HealthCronController extends D3CommandController
{
    /**
     *  Cron example: /usr/bin/php <sitepath>/yii d3printer/health-cron <component name>
     *  Pass the component name as the $printerCode
     * @param string $printer
     * @return int
     */
    public function actionIndex(string $printer)
    {
        try {
            $printerComponent = D3Printer::getPrinterComponent($printer);
            $deviceHealth = $printerComponent->deviceHealth();
            
            $stateData = [
                'status' => $deviceHealth->getStatus(),
                'cartridgeRemaining' => $deviceHealth->getCartridgeRemaining(),
                'drumRemaining' => $deviceHealth->getDrumRemaining()
            ];
            
            $this->out('Status: ' . $stateData['status']);
            $this->out('Cartridge: ' . $stateData['cartridgeRemaining']);
            $this->out('Drum: ' . $stateData['drumRemaining']);
            
            $dataJson = Json::encode($stateData);
            
            D3FileHelper::filePutContentInRuntime('d3printer/' . $printerComponent->printerCode, 'status.json', $dataJson);
            
            return ExitCode::OK;
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
        }
    }
}

