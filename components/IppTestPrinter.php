<?php

namespace d3yii2\d3printer\components;

use d3system\exceptions\D3TaskException;
use d3yii2\d3printeripp\components\PrinterIPP;
use obray\ipp\Printer as IppPrinterClient;

class IppTestPrinter extends PrinterIPP
{

    /**
     * @var string base directory in runtime directory for spool directories
     */
    public string $baseDirectory = 'd3printer';

    /**
     * ipp example 'ipp://192.168.88.216:631/ipp/print'
     */
    public int $uriPort = 631;
    public string $uriPath = 'ipp/print';
    public string $user = '';
    public string $password = '';

    public string $printerName;
    public string $printerIp;
    public string $printerCode;
    public string $port;
    public string $pincode;

    /**
     * @return void
     * Wraper to set up PrinterIPP component from the d3prineripp package having different architecture and config
     */
    public function init()
    {
        // PrinterIPP component requires printer configuration, add it dynamically
        $this->printers[] = [
            'name' => $this->printerName,
            'slug' => $this->printerCode,
            'host' => $this->printerIp,
            'port' => $this->port,
            'pincode' => $this->pincode,
        ];


        parent::init();
    }

    /**
     * @return void
     * d3ipp module have no Spooler configuration, let's add fallback
     */
    public function getSpoolDirectory(): string
    {
        return $this->baseDirectory . '/spool_' . $this->printerCode;
    }


    /**
     * @throws D3TaskException
     */
    public function print(string $url, int $copies = 1, ?string $printerSlug = null): bool
    {
        $currentLimit = ini_get('max_execution_time');
        set_time_limit(10);
        usleep(1000000);
        $tryCounter = 1;
        while ($tryCounter <= 5) {

            // Use first printer instance if no printer specified
            if (!$printerSlug) {
                $printerSlug = $this->printers[0]['slug'];
            }


            //Test
            $options = [
                'job-name' => 'Test printing Job #12345',
                'copies' => $copies,
                'media' => 'iso_a4_210x297mm',
                'sides' => 'one-sided',
                'print-quality' => 'high'
            ];

            $result = $this->printBySlug($printerSlug, file_get_contents($url), $options);

            if ($result['success']) {
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