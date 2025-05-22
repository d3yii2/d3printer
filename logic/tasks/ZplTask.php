<?php

namespace d3yii2\d3printer\logic\tasks;

use d3system\exceptions\D3TaskException;
use Exception;

class ZplTask extends PrinterTask
{
    /**
     * @var resource
     */
    public $connection;
    
    /**
     * @var int $port
     */
    protected int $port = 9100;
    
    /**
     * @var int $connectTimeout
     */
    protected int $connectTimeout = 20;
    


    public function disconnect(): void
    {
        $this->printer->disconnect();
    }

    /**
     * @param string $filePath
     * @param int $tryTimes try to put file times
     * @param int $usleep sleep in microseconds between try. default 0.5 second
     * @throws D3TaskException
     */
    public function putFile(string $filePath, int $tryTimes = 5, int $usleep = 1000000): void
    {
        usleep($usleep);
        $tryCounter = 1;
        while ($tryCounter <= $tryTimes) {
            try {
                $this->printer->print($filePath);
                return;
            } catch (Exception $e) {
                $tryCounter ++;
            }
            usleep($usleep);
        }
        if (isset($e)) {
            throw new D3TaskException(
                'Can not ftp_put! ' . PHP_EOL
                . 'file: ' . $filePath . PHP_EOL
                . $e->getMessage() . PHP_EOL
                . $e->getTraceAsString()
            );
        }
        throw new D3TaskException('Can not ftp_put! ' . PHP_EOL . 'file: ' . $filePath);
    }
}
