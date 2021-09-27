<?php


namespace d3yii2\d3printer\components;


use PhpExec\Exception;
use yii\base\Component;
use yii\helpers\VarDumper;
use PhpExec\Command;

class SendToPrinter extends Component
{
    /**
     * @var string server printer name on windows
     */
    public $printerName;

    /**
     * @var string
     */
    public $printerIp;
    
    /**
     * @var string $poolingRuntimeDirectory
     */
    public $poolingRuntimeDirectory;

    /**
     * @param string $filepath
     * @param int $copies
     * @return bool
     * @throws \yii\base\Exception
     * @todo papildus parami: host, mode (pasive/active), user, password, timeout sec, debug
     */
    public function printToFtpFilesystem(string $filepath, int $copies = 1): bool
    {
        echo 'a';
        if(!file_exists($filepath)){
            throw new \yii\base\Exception('Neeksite fails: ' . $filepath);
        }
        $copyToFile = basename($filepath,'.pdf');
        if(!$conn_id = ftp_connect($this->printerIp)){
            throw new \yii\base\Exception("Can not connect to ftp! ");
        }
        if(!$login_result = ftp_login($conn_id, 'anonymous', 'anonymous@domain.com')){
            echo VarDumper::dumpAsString($login_result);
            throw new \yii\base\Exception("can not login ftp! " . VarDumper::dumpAsString($login_result));
        }
        ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 10);
        echo 'd';
        if ((!$conn_id) || (!$login_result)) {
            throw new \yii\base\Exception("FTP connection has failed!");
        }
        $i = 1;
        while($i<=$copies) {
            echo 'f';
            echo '"' . $copyToFile . $i . '.pdf"';

            if(!@ftp_put($conn_id, $copyToFile . $i . '.pdf', $filepath, FTP_BINARY)){
                throw new \yii\base\Exception("can not ftp_put! " . VarDumper::dumpAsString(error_get_last()));
            }
            echo 'g';
            $i++;
        }

        return true;

    }
}