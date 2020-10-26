<?php

namespace d3yii2\d3printer\logic;

use d3system\helpers\D3FileHelper;
use yii\base\Exception;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3Printer
{
    protected $connectionUrl;
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        $this->connectionUrl = $this->getConnectionUrl();
    }
    
    protected function getConnectionUrl()
    {
    }

    public function connect(): string
    {
        if (empty($this->connectionUrl)) {
            throw new Exception('Cannot connect to Printer: connection URL not specified');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_URL, urlencode($this->connectionUrl));
        $response = curl_exec($ch);
        curl_close($ch);
        D3FileHelper::filePutContentInRuntime('printer','a.txt',$response);
        return $response;

    }
}
