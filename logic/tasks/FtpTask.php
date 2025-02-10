<?php


namespace d3yii2\d3printer\logic\tasks;

use d3system\exceptions\D3TaskException;
use d3yii2\d3printer\logic\D3PrinterException;
use Suin\FTPClient\FTPClient;
use yii\helpers\VarDumper;


class FtpTask extends PrinterTask
{
    /**
     * @var resource
     */
    public $connection;
    
    /**
     * @var int $port
     */
    protected $port = 21;
    
    /**
     * @var int $connectTimeout
     */
    protected $connectTimeout = 20;
    
    /**
     * @var int $ftpTimeout
     */
    protected $ftpTimeout = 2;
    
    protected $username = 'anonymous';
    
    protected $password = '';
    
    /**
     * @throws D3PrinterException
     */
    public function connect(): void
    {
        $port = $this->printer->port ?? $this->port;
        $connectTimeout = $this->printer->connectTimeout ?? $this->connectTimeout;
        
        if (!$this->connection = ftp_connect($this->printer->printerIp, $port, $connectTimeout)) {
            throw new D3PrinterException('Can not connect to ftp at: ' . $this->printer->printerIp . ' Port: ' . $port . ' Timeout:' . $connectTimeout);
        }

        $timeout = $this->printer->ftpTimeout ?? $this->ftpTimeout;
        ftp_set_option($this->connection, FTP_TIMEOUT_SEC, $timeout);
    }

    /**
     * @throws D3PrinterException
     */
    public function authorize(): void
    {
        $user = $this->printer->ftpUsername ?? $this->username;
        $password = $this->printer->ftpPassword ?? $this->password;
    
        if (!$login_result = ftp_login($this->connection, $user, $password)) {
            echo VarDumper::dumpAsString($login_result);
            throw new D3PrinterException('FTP login failed! ' . VarDumper::dumpAsString($login_result));
        }
    
        $this->controller->out('Login OK');
        ftp_pasv($this->connection, true);
    }

    public function disconnect(): void
    {
        if ($this->connection) {
            ftp_close($this->connection);
        }
    }

    /**
     * @param string $filePath
     * @param int $tryTimes try put file times
     * @param int $usleep sleep in microseconds between try. default 0.5 second
     * @throws D3TaskException
     */
    public function putFile(string $filePath, int $tryTimes = 5, int $usleep = 1000000): void
    {
        $port = $this->printer->port ?? $this->port;
        $client = new FTPClient($this->printer->printerIp, $port);
        usleep($usleep);
        $tryCounter = 1;
        while ($tryCounter <= $tryTimes) {
            if ($client->upload($filePath, basename($filePath)) !== false) {
                return;
            }
            $tryCounter ++;
            usleep($usleep);
        }
        throw new D3TaskException('Can not ftp_put! ' . PHP_EOL
            . 'file: ' . $filePath
        );
    }
}
