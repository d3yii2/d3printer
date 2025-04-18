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
     * check printer statuses. On problems send emails
     * emails set in table SELECT * FROM `setting` WHERE `section` = 'Settings-AlertSettings'
     *  Cron example: /usr/bin/php <sitepath>/yii d3printer/health-cron
     *  Pass the component name as the $healthComponent
     * @param string $healthComponent
     * @return int
     * @throws GuzzleException
     */
    public function actionIndex(string $healthComponent): int
    {
        try {
            /** @var D3Printer $component */
            $component = D3Printer::getPrinterComponent($healthComponent);
            $deviceHealth = $component->deviceHealth();
            $configHealth = $component->configHealth();
            $commonHealth = $component->commonHealth();

            /** check and send emails */
            $commonHealth->check();


            // Update printer configuration for paper, sleep and print if not mach with expected settings (e.g. electricity fault)
            if (!$configHealth->paperSizeOk()) {
                $configHealth->updatePaperConfig();
            }

            if (!$configHealth->energySleepOk()) {
                $configHealth->updateEnergyConfig();
            }

            if (!$configHealth->printOrientationOk()) {
                $configHealth->updatePrintConfig();
            }

            if ($configHealth->logger->hasErrors()) {
                throw new Exception('Update errors:' . PHP_EOL . $configHealth->logger->getErrorMessages());
            }


            $configStateMessages = $configHealth->logger->getInfoMessages();

            $stateData = [
                'status' => $deviceHealth->getStatus(),
                'cartridgeRemaining' => $deviceHealth->getCartridgeRemaining(),
                'drumRemaining' => $deviceHealth->getDrumRemaining(),
                'configState' => $configStateMessages,
            ];

            $this->out('Status: ' . $stateData['status']);
            $this->out('Cartridge: ' . $stateData['cartridgeRemaining']);
            $this->out('Drum: ' . $stateData['drumRemaining']);
            $this->out($configStateMessages);

            $dataJson = Json::encode($stateData);

            D3FileHelper::filePutContentInRuntime('d3printer/' . $component->printerCode, 'status.json', $dataJson);

            return ExitCode::OK;
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}

