<?php

namespace d3yii2\d3printer\logic\set;

use d3yii2\d3printer\logic\D3Printer;
use d3yii2\d3printer\logic\settings\D3PrinterAccessSettings;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use yii\base\Exception;

class D3PrinterSet extends D3Printer
{
    protected $accessSettings;
    protected $sentData;

    /**
     * D3PrinterSet constructor.
     */
    public function __construct()
    {
        $this->accessSettings = new D3PrinterAccessSettings();
        parent::__construct();
    }
    
    /**
     * @param array|null $sendData
     * @return Response
     * @throws Exception
     * @throws GuzzleException
     */
    public function update(?array $sendData = null): Response
    {
        $postAttrs = $sendData ?? $this->getSendAttributes();
        
        /** @var Response $response */
        if ($response = $this->sendPost($this->getConnectionUrl(), $postAttrs)) {
            $this->sentData = $postAttrs;
        }
        
        return $response;
    }
    
    /**
     * @return mixed
     */
    public function getSentData()
    {
        return $this->sentData;
    }
    
    /**
     * Should be inherited in associated child class ( D3PrinterPaperSet | D3PrinterPrintSet | D3PrinterEnergySet )
     * @return array
     */
    public function getSendAttributes(): array
    {
        return [];            
    }
}
