<?php

namespace d3yii2\d3printer\controllers;

use aluksne\manufacture\components\ProductQrCode;
use d3system\commands\DaemonController;
use d3system\exceptions\D3TaskException;
use d3yii2\d3printer\components\D3PrinterGodex5000;
use Exception;
use Yii;
use yii\console\ExitCode;


class Godex5000SpoolDaemonController extends DaemonController
{

    /**
     * @throws \d3system\exceptions\D3TaskException|\yii\db\Exception|\yii\base\Exception
     */
    public function actionIndex(string $printComponent = ''): int
    {
        if (!Yii::$app->has($printComponent)){
            throw new \yii\base\Exception('Illegal component name: ' . $printComponent);
        }
        if (!$codeComponent = Yii::$app->get($printComponent)) {
            throw new \yii\base\Exception('Illegal component name: ' . $printComponent);
        }
        if(!($codeComponent instanceof  D3PrinterGodex5000)) {
            throw new \yii\base\Exception('Component must be class ' . D3PrinterGodex5000::class . ' get ' . get_class($codeComponent));
        }
        $spoolingDirectory = $codeComponent->getSpoolDirectory();
        $this->out('Spooling directory: ' . $spoolingDirectory);
        $this->sleepAfterMicroseconds = 1 * 1000000; //1 sekunde

        while ($this->loop()) {
            if (!$files = $codeComponent->getSpoolDirectoryFiles()) {
                continue;
            }
            foreach ($files as $filePath) {
                $this->out($filePath);
                try {
                    $codeComponent->getStatusOk();
                    $commands = file_get_contents($filePath);
                    $codeComponent->printer->sendCommands($commands);
                    $codeComponent->getStatusOk();
                    if (!unlink($filePath)) {
                        throw new D3TaskException('Cannot delete file: ' . $filePath);
                    }
                    unset($commands);
                } catch (Exception $e) {
                    Yii::error(
                        'File: ' . $filePath . PHP_EOL .
                        $e->getMessage() . PHP_EOL .
                        $e->getTraceAsString());
                    $this->out(date('Y-m-d H:i:s') . ' ' . $e->getMessage());
                }
            }
            unset($files);
            $codeComponent->disconect();
        }
        return ExitCode::OK;
    }
}

