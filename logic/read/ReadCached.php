<?php

namespace d3yii2\d3printer\logic\read;

use d3system\helpers\D3FileHelper;
use d3yii2\d3printer\components\D3Printer;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;
use Yii;

/**
 * Class ReadCached
 * @package d3yii2\d3printer\logic\read
 */
class ReadCached
{
   protected $data = [];
    
    /**
     * @param string $printerCode
     */
    public function __construct(string $printerCode)
    {
        try {
            if ($content = D3FileHelper::fileGetContentFromRuntime('d3printer/' . $printerCode, 'status.json')) {
                $this->data = Json::decode($content);
            }
        } catch (InvalidArgumentException|Exception $e) {
            Yii::error('Cannot decode printer status JSON' . $e->getMessage());
        }
    }
    
    /**
     * @param $key
     * @return string|null
     */
    protected function getValue($key): ?string
    {
        return $this->data[$key] ?? null;
    }
}
