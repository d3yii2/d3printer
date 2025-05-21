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
        ini_set('default_socket_timeout', 5);

        $task = $this->createTask($taskClassName, $printerName);
        $spoolingDirectory = $task->printer->getSpoolDirectory();
        unset($task);
        $this->out('Spooling directory: ' . $spoolingDirectory);
        $this->sleepAfterMicroseconds = 1000000; //1 sekunde
        $error = false;
        while ($this->loop()) {
            try {
                if (!$files = D3FileHelper::getDirectoryFiles($spoolingDirectory)) {
                   continue;
                }
                $this->out(date('Y-m-d H:i:s') . ' files count: ' . count($files));
                $task = $this->createTask($taskClassName, $printerName);
                foreach ($files as $filePath) {
                    $this->out($filePath);
                    $task->putFile($filePath);
                    if (!unlink($filePath)) {
                        throw new D3TaskException('Cannot delete file: ' . $filePath);
                    }
                }
                $task->disconnect();
                unset($files, $filePath, $task);
                if ($error) {
                    $this->out('');
                    $this->out(date('Y-m-d H:i:s') . ' No Errors');
                    $error = false;
                }
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

    /**
     * @param string|null $taskClassName
     * @param string $printerName  - printer component name
     * @return FtpTask|mixed
     * @throws D3TaskException
     */
    public function createTask(?string $taskClassName, string $printerName)
    {
        if (!$taskClassName) {
            $task = new FtpTask($this);
        } else {
            $task = new $taskClassName($this);
        }
        $task->printerName = $printerName;
        $task->execute();
        return $task;
    }
}

