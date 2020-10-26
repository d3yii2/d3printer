<?php

namespace d3yii2\d3printer\logic;

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

    /**
     * @return string
     * @throws Exception
     */
    public function connect(): string
    {
        if (empty($this->connectionUrl)) {
            throw new Exception('Cannot connect to Printer: connection URL not specified');
        }
        $options = [
            'http' => [
                'protocol_version' => '1.1',
                'method' => 'GET',
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
                    "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
            ]
        ];
        $context = stream_context_create($options);
        if (!$stream = file_get_contents($this->connectionUrl,false,$context)) {
            throw new Exception('Cannot connect to Printer: ' . $this->connectionUrl);
        }
        return $stream;

    }
}
