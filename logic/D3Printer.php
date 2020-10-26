<?php

namespace d3yii2\d3printer\logic;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_URL, urlencode($this->connectionUrl));
        $response = curl_exec($ch);
        curl_close($ch);
        D3FileHelper::filePutContentInRuntime('printer','a.txt',$response);
        return $response;

    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $json
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception
     * @throws GuzzleException
     */
    public function sendPost(string $url, array $data, bool $json = false)
    {
        if (empty($url)) {
            throw new Exception('Cannot POST to Printer: URL not specified');
        }

        $client = new Client();

        $params = ['form_params' => $data];

        if ($json) {
            $params['headers'] = ['Content-Type' => 'application/json'];
            $params['body'] = json_encode($data);
        }

        $response = $client->request('POST', $url, $params);

        $statusCode = $response->getStatusCode();

        if (!200 === $statusCode) {
            throw new Exception('Cannot post the data: Status code:' . $statusCode . ' Reason: ' . $response->getReasonPhrase());
        }

        return $response;
    }
}
