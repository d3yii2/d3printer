<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\DaemonController;
use d3yii2\d3printer\logic\tasks\FtpPingTask;

class FtpPingDaemonController extends DaemonController
{
    protected $printer;
    
    public function options($actionID)
    {
        return ['printer'];
    }
    
    /**
     * @return \d3yii2\d3printer\logic\tasks\FtpPrintTask
     */
    public function getTask(): FtpPingTask
    {
        $task = new FtpPingTask($this);
        $task->printerName = $this->printer;
        return $task;
    }
}

