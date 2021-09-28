<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\DaemonController;
use d3system\exceptions\D3TaskException;
use d3system\helpers\D3FileHelper;
use d3yii2\d3printer\logic\tasks\FtpTask;
use Exception;
use Yii;
use yii\console\ExitCode;


class FtpPrintDaemonController extends DaemonController
{

    /**
     * @throws \d3system\exceptions\D3TaskException|\yii\db\Exception
     */
    public function actionIndex(string $printerName): int
    {
        $task = new FtpTask($this);
        $task->printerName = $printerName;
        $task->execute();
        $spoolingDirectory = $task->printer->baseDirectory . '/spool_' . $printerName;
        $this->out('Spooling directory: ' . $spoolingDirectory);
        $this->sleepAfterMicroseconds = 1 * 1000000; //1 sekunde
        while ($this->loop()) {
            try {
                if (!$files = D3FileHelper::getDirectoryFiles($spoolingDirectory)) {
                   continue;
                }
                $task->connect();
                $task->authorize();
                foreach ($files as $filePath) {

                    $this->out($filePath);
                    $task->putFile($filePath);

                    if (!unlink($filePath)) {
                        throw new D3TaskException('Cannot delete file: ' . $filePath);
                    }
                }
                $task->disconnect();
            } catch (Exception $e) {
                $error = true;
                if($e->getMessage() === (string)$error) {
                    $this->out('!');
                } else {
                    $error = $e->getMessage();
                    $this->out(date('Y-m-d H:i:s') . $error);
                    Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                }
            }
        }
        return ExitCode::OK;
    }
}

