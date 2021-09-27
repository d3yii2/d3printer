<?php


namespace d3yii2\d3printer\logic\tasks;

use d3yii2\d3printer\logic\D3PrinterException;
use yii\helpers\VarDumper;
use FTP\Connection;


class FtpTask extends PrinterTask
{
    /**
     * @var null|bool|Connection
     */
    protected $connection;
    
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
    protected $ftpTimeout = 20;
    
    protected $username = 'anonymous';
    
    protected $password = '';
    
    /**
     * @throws D3PrinterException
     */
    protected function connect(): void
    {
        $port = $this->printer->port ?? $this->port;
        $connectTimeout = $this->printer->connectTimeout ?? $this->connectTimeout;
        
        if (!$this->connection = ftp_connect($this->printer->printerIp, $port, $connectTimeout)) {
            throw new D3PrinterException('Can not connect to ftp at: ' . $this->printer->printerIp . ' Port: ' . $port . ' Timeout:' . $connectTimeout);
        }
        
        $this->controller->out('Connection OK');
        
        $timeout = $this->printer->ftpTimeout ?? $this->ftpTimeout;
        ftp_set_option($this->connection, FTP_TIMEOUT_SEC, $timeout);
    }
    
    public function authorize()
    {
        $user = $this->printer->ftpUsername ?? $this->username;
        $password = $this->printer->ftpPassword ?? $this->password;
    
        if (!$login_result = ftp_login($this->connection, $user, $password)) {
            echo VarDumper::dumpAsString($login_result);
            throw new D3PrinterException('FTP login failed! ' . VarDumper::dumpAsString($login_result));
        }
    
        $this->controller->out('Login OK');
    }
}