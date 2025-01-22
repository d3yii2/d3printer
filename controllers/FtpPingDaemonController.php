<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\DaemonController;
use d3system\exceptions\D3TaskException;
use d3yii2\d3printer\logic\tasks\FtpTask;
use Exception;
use yii\console\ExitCode;

class FtpPingDaemonController extends DaemonController
{

    /**
     * @throws \yii\db\Exception
     * @throws D3TaskException|\yii\base\Exception
     */
    public function actionIndex(string $printerName): int
    {
        $this->loopTimeLimit = 30;
        $this->loopExitAfterSeconds = 0;
        $this->memoryIncreasedPercents = 30;

        $task = new FtpTask($this);
        $task->printerName = $printerName;
        $task->execute();
        $error = false;
        while ($this->loop()) {
            try {
                $task->connect();
                $task->disconnect();
                $task->printer->unlinkDeadFile();

                if ($error) {
                    $this->out('');
                    $this->out(date('Y-m-d H:i:s') . ' No Errors');
                    $error = false;
                } else {
                    $this->stdout('.');
                }
            } catch (Exception $e) {
                if($e->getMessage() === (string)$error) {
                    $this->stdout('!');
                } else {
                    $error = $e->getMessage();
                    $this->out('');
                    $this->out(date('Y-m-d H:i:s') . ' ' . $error);
                    $task->printer->createDeadFile();
                }
            }
        }
        return ExitCode::OK;
    }
}
