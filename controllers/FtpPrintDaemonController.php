<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\DaemonController;
use d3system\exceptions\D3TaskException;
use d3system\helpers\D3FileHelper;
use Exception;
use Yii;
use yii\console\ExitCode;


/**
 * use for printing files from a spool directory to printer.
  * use for
 * - Cewood - FTP task
 * - farmeko - ZPL
 */
class FtpPrintDaemonController extends DaemonController
{

    /**
     * @throws D3TaskException|\yii\db\Exception
     * @throws \yii\base\Exception
     */
    public function actionIndex(
        string $printerName
    ): int
    {
        /** process settings */
        $this->loopTimeLimit = 30;
        $this->loopExitAfterSeconds = 0;
        $this->memoryIncreasedPercents = 30;
        ini_set('default_socket_timeout', 5);

        /** get printer directory */
        if (!isset(Yii::$app->{$printerName})) {
            throw new \yii\base\Exception('Illegal component name: ' . $printerName);
        }
        $printer = Yii::$app->{$printerName};
        $spoolingDirectory = $printer->getSpoolDirectory();

        $this->out('Spooling directory: ' . $spoolingDirectory);
        $this->sleepAfterMicroseconds = 1000000; //1 sekunde
        $error = false;
        $canNotDeleteFile = false;
        $printerErrorMessage = false;
        while ($this->loop()) {
            if ($canNotDeleteFile) {
                sleep(60);
                continue;
            }
            try {
                if (!$files = D3FileHelper::getDirectoryFiles($spoolingDirectory)) {
                   continue;
                }
                $this->out(date('Y-m-d H:i:s') . ' files count: ' . count($files));
                foreach ($files as $filePath) {
                    $this->out($filePath);
                    if (!$printer->print($filePath)) {
                        sleep(60);
                        continue;
                    }
                    $this->out('Printed');
                    if (!unlink($filePath)) {
                        $canNotDeleteFile = true;
                        throw new D3TaskException('Cannot delete file: ' . $filePath);
                    }
                }
                unset($files);
                if ($error) {
                    $this->out('');
                    $this->out(date('Y-m-d H:i:s') . ' No Errors');
                    $error = false;
                }
            } catch (Exception $e) {
                if ($printerErrorMessage !== $e->getMessage()) {
                    $printerErrorMessage = $e->getMessage();
                    $this->out((string)$e);
                    Yii::error($e);
                } else {
                    $this->out('E');
                }
            }
        }
        return ExitCode::OK;
    }
}
