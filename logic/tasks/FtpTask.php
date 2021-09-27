<?php


namespace d3yii2\d3printer\logic\tasks;


use d3system\exceptions\D3TaskException;
use yii\helpers\VarDumper;

class FtpTask extends PrinterTask
{
    protected $connection;
    
    protected $port = 21;

    protected $connectTimeout = 2;

    protected $ftpTimeout = 10;
    
    protected function connect()
    {
        $port = $this->printer->port ?? $this->port;
        $connectTimeout = $this->printer->connectTimeout ?? $this->connectTimeout;
        
        if (!$this->connection = ftp_connect($this->printer->printerIp, $port, $connectTimeout)) {
            throw new D3TaskException('Can not connect to ftp at: ' . $this->printer->printerIp . ' Port: ' . $port . ' Timeout:' . $connectTimeout);
        }
        
        $this->controller->out('Connection OK');
    
        $user = $this->printer->ftpUser ?? 'ftpuser'; //'anonymous'
        $password = $this->printer->ftpPassword ?? 'laurisftptest'; //
    
        if (!$login_result = ftp_login($this->connection, $user, $password)) {
            echo VarDumper::dumpAsString($login_result);
            throw new D3TaskException('FTP login failed! ' . VarDumper::dumpAsString($login_result));
        }
        
        $this->controller->out('Login OK');
    
        $ftpTimeout = $this->printer->ftpTimeout ?? $this->ftpTimeout;
        ftp_set_option($this->connection, FTP_TIMEOUT_SEC, $ftpTimeout);
        $this->controller->out('Timeout set to: ' . $ftpTimeout);
    }
}