<?php

namespace d3yii2\d3printer\logic\tasks;

use d3system\exceptions\D3TaskException;
use d3yii2\d3printer\logic\D3PrinterException;
use Exception;
use Yii;

class FtpPingTask extends FtpTask
{
    public $printerName;
    
    /**
     * @throws D3TaskException
     */
    public function execute()
    {
        parent::execute();
    
        try {
            $this->connect();
            if (!$this->removeDeadlockFile()) {
                throw new D3TaskException('Cannot remove deadlock file');
            }
    
            $this->controller->out('---');
        } catch (Exception | D3PrinterException $e) {
            $error = $e->getMessage();
            
            if (!$this->writeDeadlockFile()) {
                $error .= 'Cannot write deadlock file';
            }
            
            throw new D3TaskException($error);
        }
    }
    
    /**
     * @return bool|int
     */
    protected function writeDeadlockFile()
    {
        if (!self::hasDeadlockFile($this->printerName)) {
             return file_put_contents(self::getDeadlockFilePath($this->printerName), 1);
        }
        return true;
    }
    
    /**
     * @return bool
     */
    protected function removeDeadlockFile()
    {
        return !self::hasDeadlockFile($this->printerName) || unlink(self::getDeadlockFilePath($this->printerName));
    }
    
    /**
     * @param string $printerName
     * @return bool
     */
    public static function hasDeadlockFile(string $printerName): bool
    {
        return file_exists(self::getDeadlockFilePath($printerName));
    }
    
    /**
     * @param string $printerName
     * @return string
     */
    public static function getDeadlockFilePath(string $printerName): string
    {
        return Yii::getAlias('@runtime') .  '/d3printer/' . $printerName . '-dead.txt';
    }
}