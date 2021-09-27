<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\DaemonController;
use d3yii2\d3printer\logic\tasks\FtpPrintTask;

class FtpPrintDaemonController extends DaemonController
{
    protected $printer;
    
    public function options($actionID)
    {
        return ['printer'];
    }
    
    /**
     * @return \d3yii2\d3printer\logic\tasks\FtpPrintTask
     */
    public function getTask(): FtpPrintTask
    {
        $task = new FtpPrintTask($this);
        $task->printerName = $this->printer;
        return $task;
    }
}

