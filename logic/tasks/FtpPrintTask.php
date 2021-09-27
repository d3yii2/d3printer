<?php

namespace d3yii2\d3printer\logic\tasks;

use d3system\exceptions\D3TaskException;
use yii\console\ExitCode;
use yii\helpers\VarDumper;

class FtpPrintTask extends FtpTask
{
    public $printerName;
    public $ftpFilePath = 'tests/';
    public $poolingRuntimeDirectory = '/var/www/clients/weberp/cewood/aaa';
    public $copies = 1;
    
    public function execute()
    {
        parent::execute();
        
        $this->controller->out('a');
        
        if (!is_dir($this->poolingRuntimeDirectory)) {
            throw new D3TaskException(
                'Cannot read pooling dir: ' . $this->poolingRuntimeDirectory
            );
        }
    
        $this->controller->out('Pooling dir: ' . $this->poolingRuntimeDirectory);

        $files = glob($this->poolingRuntimeDirectory . '/*.pdf');

        if (empty($files)) {
            $this->controller->out('No files found');
            return ExitCode::OK;
        }
    
        $this->connect();
    
        foreach($files as $file) {
            $copyToFile = basename($file);
    
            $i = 1;
            while ($i <= $this->copies) {
                $this->controller->out('f');
                $this->controller->out($copyToFile);
        
                if (!ftp_put($this->connection, $this->ftpFilePath . $copyToFile, $file, FTP_BINARY)) {
                    throw new D3TaskException("can not ftp_put! " . VarDumper::dumpAsString(error_get_last()));
                }
                
                if (!unlink($file)) {
                    throw new D3TaskException('Cannot delete file: ' . $file);
                }
                
                $this->controller->out('g');
                $i++;
            }
        }
    }
}