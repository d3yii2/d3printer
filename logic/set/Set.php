<?php

namespace d3yii2\d3printer\logic\set;

use d3yii2\d3printer\logic\Connect;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use yii\base\Exception;

class Set extends Connect
{
    protected $accessSettings;
    protected $sentData;
    
    /**
     * @param string $url
     * @param array|null $sendData
     * @return Response
     * @throws Exception
     * @throws GuzzleException
     */
    public function update(string $url, ?array $sendData = null): Response
    {
        $postAttrs = $sendData ?? $this->getSendAttributes();
        
        /** @var Response $response */
        if ($response = $this->sendPost($url, $postAttrs)) {
            $this->sentData = $postAttrs;
        }
        
        return $response;
    }
    
    /**
     * Should be inherited in associated child class ( SetPaper | SetPrint | SetEnergy )
     * @return array
     */
    public function getSendAttributes(): array
    {
        return [];
    }
    
    /**
     * @return mixed
     */
    public function getSentData()
    {
        return $this->sentData;
    }
}
