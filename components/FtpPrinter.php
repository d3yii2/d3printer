<?php

namespace d3yii2\d3printer\components;

use d3system\exceptions\D3TaskException;
use Suin\FTPClient\FTPClient;

class FtpPrinter extends Printer
{

    /**
     * @var int $port
     */
    public int $ftpPort = 21;

    /**
     * @throws D3TaskException
     */
    public function print(string $url, int $copies = 1): bool
    {
        $currentLimit = ini_get('max_execution_time');
        set_time_limit(10);
        $client = new FTPClient($this->printerIp, $this->ftpPort);
        usleep(1000000);
        $tryCounter = 1;
        while ($tryCounter <= 5) {
            if ($client->upload($url, basename($url)) !== false) {
                set_time_limit($currentLimit);
                $client->disconnect();
                return true;
            }
            $tryCounter++;
            usleep(1000000);
        }
        set_time_limit($currentLimit);
        throw new D3TaskException('Can not ftp_put! ' . PHP_EOL
            . 'file: ' . $url
        );
    }
}