<?php

namespace d3yii2\d3printer\components;

use d3system\exceptions\D3TaskException;
use obray\ipp\Printer as IppPrinterClient;

class IppPrinter extends Printer
{

    /**
     * ipp example 'ipp://192.168.88.216:631/ipp/print'
     */
    public int $uriPort = 631;
    public string $uriPath = 'ipp/print';
    public string $user = '';
    public string $password = '';

    public function createClient(): IppPrinterClient
    {
        return new IppPrinterClient(
            'ipp://' . $this->printerIp . ':' . $this->uriPort . '/' . $this->uriPath,
            $this->user,
            $this->password
        );
    }

    /**
     * @throws D3TaskException
     */
    public function print(string $url, int $copies = 1): bool
    {
        $currentLimit = ini_get('max_execution_time');
        set_time_limit(10);
        $ipp = $this->createClient();
        usleep(1000000);
        $tryCounter = 1;
        while ($tryCounter <= 5) {
            $response = $ipp->printJob(file_get_contents($url));
            if ($response->statusCode->getClass() === 'successful') {
                set_time_limit($currentLimit);
                return true;
            }
            $tryCounter++;
            usleep(1000000);
        }
        set_time_limit($currentLimit);
        if (isset($response)) {
            throw new D3TaskException('Can not print! ' . PHP_EOL
                . 'file: ' . $url . PHP_EOL
                . 'response: ' . $response->statusCode
            );
        }
        return false;
    }
}