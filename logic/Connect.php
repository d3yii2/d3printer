<?php

namespace d3yii2\d3printer\logic;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use yii\base\Exception;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class Connect
{
    protected $url;
    protected $client;
    
    /**
     * @param string $url
     */
    public function __construct(?string $url = null)
    {
        $this->url = $url;
        $this->client = new Client();
    }
    
    /**
     * @return string
     * @throws Exception
     * @throws GuzzleException
     */
    public function connect(): string
    {
        if (!$this->url) {
            throw new Exception('Cannot connect to Printer: connection URL not specified');
        }
        
        $params['headers'] = $this->getHeaders();

        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', $this->url, $params);

        $statusCode = $response->getStatusCode();
        if (200 !== $statusCode) {
            throw new Exception('Cannot connect! Status code:' . $statusCode . ' Reason: ' . $response->getReasonPhrase());
        }
        
        return (string)$response->getBody();
    }
    
    /**
     * @param string $url
     * @param array $data
     * @param bool $json
     * @return ResponseInterface
     * @throws Exception
     * @throws GuzzleException
     */
    public function sendPost(string $url, array $data, bool $json = false): ResponseInterface
    {
        if (empty($url)) {
            throw new Exception('Cannot POST to Printer: URL not specified');
        }
        
        $params = ['form_params' => $data];
        
        if ($json) {
            $params['headers'] = ['Content-Type' => 'application/json'];
            $params['body'] = json_encode($data);
        }
        
        $response = $this->client->request('POST', $url, $params);
        
        $statusCode = $response->getStatusCode();
        
        if (!200 === $statusCode) {
            throw new Exception('Cannot post the data: Status code:' . $statusCode . ' Reason: ' . $response->getReasonPhrase());
        }
        
        return $response;
    }
    
    /**
     * @param false $json
     * @return string[]
     */
    public function getHeaders($json = true): array
    {
        $headers = [
            'Accept-language' => 'en',
            'User-Agent' => 'Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10',
        ];
    
        if ($json) {
            $params['headers'][] = ['Content-Type' => 'application/json'];
        }
        
        return $headers;
    }
}
