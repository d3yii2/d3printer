<?php

namespace d3yii2\d3printer\logic;

use Yii;
use yii\web\HttpException;

/**
 * Class D3Pprinter
 * @package d3yii2\d3printer\logic
 */
class D3Pprinter
{
    protected $connectionUrl;
    protected $settings;
    
    /**
     * D3Pprinter constructor.
     */
    public function __construct()
    {
        $this->connectionUrl = $this->getConnectionUrl();
    }
    
    protected function getConnectionUrl()
    {}
    
    /**
     * @param string $url
     * @return string
     */
    public function connect(string $url): string
    {
        try{
            if (!$content = file_get_contents($url)) {
                throw new HttpException('Cannot connect to Printer: ' . $url);
            }
            return $content;
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
        }
    }
}