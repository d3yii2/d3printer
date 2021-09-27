<?php

namespace d3yii2\d3printer\logic\tasks;

class FtpPingTask extends FtpTask
{
    public $printerName;
    
    public function execute()
    {
        parent::execute();
        
        $this->connect();
        
    }
}