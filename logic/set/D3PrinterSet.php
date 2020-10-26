<?php

namespace d3yii2\d3printer\logic\set;

use d3yii2\d3printer\logic\D3Printer;
use d3yii2\d3printer\logic\settings\D3PrinterAccessSettings;
use GuzzleHttp\Psr7\Response;
use yii\base\Exception;

class D3PrinterSet extends D3Printer
{
    protected $accessSettings;
    
    /**
     * D3PrinterSet constructor.
     */
    public function __construct()
    {
        $this->accessSettings = new D3PrinterAccessSettings();
        parent::__construct();
    }
    
    /**
     * @param array $data
     * @return Response
     * @throws Exception
     */
    public function update(array $data): Response
    {
        /** @var Response $response */
        $response = $this->sendPost($this->getConnectionUrl(), $data);
        return $response;
    }
}
