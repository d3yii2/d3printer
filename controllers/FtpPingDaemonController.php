<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\DaemonController;
use d3system\helpers\D3FileHelper;
use d3yii2\d3printer\logic\tasks\FtpTask;
use Exception;
use yii\console\ExitCode;

class FtpPingDaemonController extends DaemonController
{

    /**
     * @throws \yii\db\Exception
     * @throws \d3system\exceptions\D3TaskException|\yii\base\Exception
     */
    public function actionIndex(string $printerName): int
    {
        $task = new FtpTask($this);
        $task->printerName = $printerName;
        $task->execute();
        $error = false;
        $deadFileName = 'dead_' . $printerName . '.txt';
        while ($this->loop()) {
            try {
                $task->connect();
                $task->disconnect();
                D3FileHelper::fileUnlinkInRuntime($task->printer->baseDirectory, $deadFileName);

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
                    D3FileHelper::filePutContentInRuntime($task->printer->baseDirectory, $deadFileName, '1');
                }
            }
        }
        return ExitCode::OK;
    }

}

