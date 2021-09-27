<?php

namespace d3yii2\d3printer\logic\tasks;

use d3system\exceptions\D3TaskException;
use \d3yii2\d3printer\logic\D3PrinterException;
use yii\helpers\VarDumper;

class FtpPrintTask extends FtpTask
{
    public $printerName;
    public $ftpPath;
    public $poolingRuntimeDirectory;
    public $copies = 1;
    public $poolFilePattern = '*.pdf';
    
    /**
     * @throws D3TaskException|D3PrinterException
     */
    public function execute(): void
    {
        parent::execute();
        
        $this->controller->out('a');
        
        $poolingDir = $this->getPoolingDir();
        
        $this->controller->out('Pooling dir: ' . $poolingDir);

        $files = glob($poolingDir . '/' . $this->poolFilePattern);

        if (empty($files)) {
            $this->controller->out('No files found');
        } else {
    
            // Set FTP connection
            $this->connect();

            // FTP user login
            $this->authorize();
    
            foreach ($files as $filePath) {
                $copyFileName = basename($filePath);
        
                $i = 1;
                while ($i <= $this->copies) {
                    $this->controller->out('f');
                    $this->controller->out($copyFileName);
            
                    if (!ftp_put($this->connection, $this->getFtpPath() . $copyFileName, $filePath, FTP_BINARY)) {
                        throw new D3TaskException("can not ftp_put! " . VarDumper::dumpAsString(error_get_last()));
                    }
            
                    if (!unlink($filePath)) {
                        throw new D3TaskException('Cannot delete file: ' . $filePath);
                    }
            
                    $this->controller->out('g');
                    $i++;
                }
            }
        }
    }
    
    /**
     * @return string
     */
    public function getPoolingDir(): string
    {
         return $this->poolingRuntimeDirectory ?? $this->getRuntimePath();
    }
    
    /**
     * @return string
     */
    public function getFtpPath(): string
    {
         return $this->ftpPath ?? '';
    }
}