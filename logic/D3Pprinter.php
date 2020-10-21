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
    protected $printerPageUrl;
    
    /**
     * D3Pprinter constructor.
     */
    public function __construct()
    {
        $this->printerPageUrl = $this->getPrinterPageUrl();
    }
    
    /**
     * @return string
     */
    public function getPrinterPageUrl(): string
    {
        return '';
    }
    
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