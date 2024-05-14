<?php

namespace controllers;

use d3system\commands\DaemonController;
use d3system\exceptions\D3TaskException;
use d3yii2\d3printer\logic\D3PrinterException;
use d3yii2\d3printer\logic\tasks\FtpTask;
use Exception;
use Yii;
use yii\console\ExitCode;


class LinuxPrintDaemonController extends DaemonController
{

    /**
     * @throws D3TaskException|\yii\db\Exception
     */
    public function actionIndex(string $printerName): int
    {
        $task = new FtpTask($this);
        $task->printerName = $printerName;
        $task->execute();
        $spoolingDirectory = $task->printer->getSpoolDirectory();
        $this->out('Spooling directory: ' . $spoolingDirectory);
        $this->sleepAfterMicroseconds = 1000000; //1 sekunde
        $error = false;
        while ($this->loop()) {
            try {
                if (!$files = $task->printer->getSpoolDirectoryFiles()) {
                   continue;
                }
                $task->connect();
                $task->authorize();
                foreach ($files as $filePath) {
                    //https://manpages.ubuntu.com/manpages/jammy/man1/lp.1.html
                    shell_exec('lp -d ' . $printerName . ' "' . $filePath . '"');
                    $this->out($filePath);
                    $task->putFile($filePath);

                    if (!unlink($filePath)) {
                        throw new D3TaskException('Cannot delete file: ' . $filePath);
                    }
                }
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

