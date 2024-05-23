<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\D3CommandController;
use d3system\helpers\D3FileHelper;
use d3yii2\d3printer\components\D3Printer;
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
     *  Cron example: /usr/bin/php <sitepath>/yii d3printer/health-cron
     *  Pass the component name as the $healthComponent
     * @param string $healthComponent
     * @param string|null $resetConfig
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionIndex(string $healthComponent)
    {
        try {
            /** @var D3Printer $component */
            $component = D3Printer::getPrinterComponent($healthComponent);
            $deviceHealth = $component->deviceHealth();

            $configHealth = $component->configHealth();
            
            $spoolerHealth = $component->spoolerHealth();
            
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
                'status' => trim($deviceHealth->getStatus()),
                'cartridgeRemaining' => trim($deviceHealth->getCartridgeRemaining()),
                'drumRemaining' => trim($deviceHealth->getDrumRemaining()),
                'configState' => trim($configStateMessages),
                'spoolerHasMultipleFiles' => $spoolerHealth->hasMultipleFiles()
            ];
            
            $this->out('Status: ' . $stateData['status']);
            $this->out('Cartridge: ' . $stateData['cartridgeRemaining']);
            $this->out('Drum: ' . $stateData['drumRemaining']);
            $this->out('Spooler has files: ' . ( $stateData['spoolerHasMultipleFiles'] ? 'Yes' : 'No' ) );
            $this->out($configStateMessages);
    
            $dataJson = Json::encode($stateData);
            
            D3FileHelper::filePutContentInRuntime('d3printer/' . $component->printerCode, 'status.json', $dataJson);
            
            return ExitCode::OK;
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::error($e->getMessage(), 'd3printer-error');
        }
    }
}

