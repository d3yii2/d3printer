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
     */
    public function connect()
    {
        if (empty($this->connectionUrl)) {
            throw new Exception('Cannot connect to Printer: connection URL not specified');
        }
        
        if (!$content = file_get_contents($this->connectionUrl)) {
            throw new Exception('Cannot connect to Printer: ' . $this->connectionUrl);
        }
        return $content;
    }
}
