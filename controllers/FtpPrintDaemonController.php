<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\DaemonController;
use d3system\exceptions\D3TaskException;
use d3system\helpers\D3FileHelper;
use d3yii2\d3printer\logic\D3PrinterException;
use d3yii2\d3printer\logic\tasks\FtpTask;
use Exception;
use Yii;
use yii\console\ExitCode;
use yii\helpers\VarDumper;


class FtpPrintDaemonController extends DaemonController
{

    /**
     * @throws D3TaskException|\yii\db\Exception
     * @throws \yii\base\Exception
     */
    public function actionIndex(
        string $printerName,
        string $taskClassName = null
    ): int
    {
        /** process settings */
        $this->loopTimeLimit = 30;
        $this->loopExitAfterSeconds = 0;
        $this->memoryIncreasedPercents = 30;

        if (!$taskClassName) {
            $task = new FtpTask($this);
        } else {
            $task = new $taskClassName($this);
        }
        $task->printerName = $printerName;
        $task->execute();
        $this->sleepAfterMicroseconds = 1000000; //1 sekunde
        $error = false;
        while ($this->loop()) {
            try {
                if (!$files = $task->printer->getSpoolDirectoryFiles()) {
                   continue;
                }
                $this->out('files count: ' . count($files));
                $task->connect();
                $task->authorize();
                foreach ($files as $filePath) {

                    $this->out($filePath);
                    $task->putFile($filePath);

                    if (!unlink($filePath)) {
                        throw new D3TaskException('Cannot delete file: ' . $filePath);
                    }
                }
                unset($files, $filePath);
                if ($error) {
                    $this->out('');
                    $this->out(date('Y-m-d H:i:s') . ' No Errors');
                    $error = false;
                }
                $task->disconnect();
            } catch (D3PrinterException $e) {
                $this->stdout('!');
            } catch (Exception $e) {
                if($e->getMessage() === (string)$error) {
                    $this->stdout('!');
                } else {
                    $error = get_class($e) . ': ' . $e->getMessage();
                    $this->out('');
                    $this->out(date('Y-m-d H:i:s') . ' ' . $error);
                    Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                }
            }
        }
        return ExitCode::OK;
    }
}

