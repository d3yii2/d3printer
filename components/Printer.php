<?php


namespace d3yii2\d3printer\components;


use d3system\helpers\D3FileHelper;
use PhpExec\Exception;
use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use PhpExec\Command;

/**
 * work only on windows
 * load html page, convert ot PHP and send to windows printer
 *
 * Class Printer
 */
class Printer extends Component
{

    /**
     * @var string
     */
    public $printerCode;

    /**
     *
     *//**
     * @var string
     */
    public $baseDirectory = 'd3printer';
    /**
     * @var string server printer name on windows
     */
    public $printerName;

    /**
     * @var string  path to chrome exe. Used for generate PDF
     */
    public $chromeExe;

    /**
     * @var string path to PDFtoPrinter exe fiel
     * @see http://www.columbia.edu/~em36/pdftoprinter.html
     */
    public $PDFtoPrinter;

    /**
     * @var string
     */
    public $printerIp;

    /**
     * @param string $url URL return label with barcode
     * @param int $copies
     * @return bool
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function print(string $url, int $copies = 1): bool
    {
        $temPath =escapeshellarg($this->getTempFile('4printer','pdf'));

        if (!$this->exec($this->chromeExe,
            [
                '--headless',
                '--print-to-pdf=' . $temPath,
                '"'.$url.'"'
            ]
        )) {
            return false;
        }
        sleep(1);
        $result = $this->exec($this->PDFtoPrinter,[
            $temPath,
            '"'.$this->printerName.'" copies=' . $copies
        ]);

        if(file_exists($temPath)){
            unlink($temPath);
        }
        return $result;

    }

    /**
     * Create a temp file and get full path
     * @param string $prefix (optional) Name prefix
     * @param string $extension
     * @return string Full temp file path
     * @throws NotFoundHttpException When tmp directory doesn't exist or failed to create
     */
    private function getTempFile(string $prefix = 'temp',string $extension = 'tmp'): string
    {
        $tmpDir = Yii::getAlias('@runtime/tmp');
        if (!is_dir($tmpDir) && (!@mkdir($tmpDir) && !is_dir($tmpDir))) {
            throw new NotFoundHttpException ('temp directory does not exist: ' . $tmpDir);
        }

        return preg_replace('#\.tmp$#','.'.$extension,tempnam($tmpDir, $prefix));

    }

    /**
     * @param string $execCommand
     * @param array $arguments
     * @return bool
     * @throws Exception
     */
    public function exec(string $execCommand,array $arguments = []): bool
    {

        $command = new Command($execCommand, $arguments);
        $result = $command->run();
        if (!$result->isSuccess()) {
            Yii::error('Exec error: ' . $execCommand);
            Yii::error('Arguments: ' . VarDumper::dumpAsString($arguments));
            Yii::error('Output: ' . $result->getOutput());
            Yii::error('ExitCode: ' . $result->getExitCode());
            Yii::error('ErrorOutput: ' . $result->getErrorOutput());
            return false;
        }
        return true;
    }

    /**
     * @param string $filepath
     * @param int $copies
     * @return bool
     * @throws \yii\base\Exception
     * @todo papildus parami: host, mode (pasive/active), user, password, timeout sec, debug
     */
    public function printToFtpFilesystem(string $filepath, int $copies = 1): bool
    {
        if (!file_exists($filepath)) {
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
            $erroris = '';
            $copied = false;
            $iloop = 0;
            while ($iloop < 4) {
                if(@ftp_put($conn_id, $copyToFile . $i . '.pdf', $filepath, FTP_BINARY)){
                    $copied = true;
                    echo '$i=' . $iloop . ';';
                    break;
                    //throw new \yii\base\Exception("can not ftp_put! " . VarDumper::dumpAsString(error_get_last()) . ' file: ' .$copyToFile . $i . '.pdf' );
                }

                $erroris .= VarDumper::dumpAsString(error_get_last()) . ' file: ' .$copyToFile . $i . '.pdf' . PHP_EOL ;
                $iloop ++;
                sleep(1);
            }
            if (!$copied) {
                echo ';NoFtping;';
                throw new \yii\base\Exception("can not ftp_put! " . $erroris);
            }
            echo 'g';
            $i++;
        }
        ftp_close($conn_id);
        return true;

    }

    /**
     * @throws \yii\base\Exception
     */
    public function printToSpoolDirectory(string $filepath, int $copies = 1): bool
    {
        if(!file_exists($filepath)){
            throw new \yii\base\Exception('Neeksite fails: ' . $filepath);
        }
        $spoolDirectory = D3FileHelper::getRuntimeDirectoryPath($this->getSpoolDirectory());
        $pi = pathinfo($filepath);
        for ($i = 1; $i <= $copies; $i++) {
            $toFile = $spoolDirectory . '/' . $pi['filename'] . $i . '.' . $pi['extension'];
            if (!copy($filepath, $toFile)) {
                throw new \yii\base\Exception('Can not copy file to ' . $toFile);
            }
        }

        return true;
    }

    /**
     * @throws \yii\base\Exception
     */
    public function saveFileInSpoolDirectory(string $content, int $copies = 1, string $fileName = ''): bool
    {
        if($content){
            throw new \yii\base\Exception('Empty file content');
        }
        if (!$fileName) {
            $fileName = uniqid($this->printerCode, true) . '.txt';
        }
        $spoolDirectory = D3FileHelper::getRuntimeDirectoryPath($this->getSpoolDirectory());

        for ($i = 1; $i <= $copies; $i++) {
            $pi = pathinfo($fileName);
            $toFile = $spoolDirectory . '/' . $pi['filename'] . $i . '.' . $pi['extension'];
            file_put_contents($toFile, $content);
        }

        return true;
    }

    public function getSpoolDirectory(): string
    {
        return $this->baseDirectory  . '/spool_' . $this->printerCode;
    }

    /**
     * @throws \yii\base\Exception
     */
    public function getSpoolDirectoryFiles(): array
    {
        if ($list = D3FileHelper::getDirectoryFiles($this->getSpoolDirectory())) {
            return $list;
        }

        return [];
    }

    private function createDeadFileName(): string
    {
        return 'dead_' . $this->printerCode . '.txt';
    }

    /**
     * @throws \yii\base\Exception
     */
    public function createDeadFile(): void
    {
        D3FileHelper::filePutContentInRuntime($this->baseDirectory, $this->createDeadFileName(), '1');
    }

    /**
     * @throws \yii\base\Exception
     */
    public function unlinkDeadFile(): void
    {
        D3FileHelper::fileUnlinkInRuntime($this->baseDirectory, $this->createDeadFileName());
    }

    /**
     * @throws \yii\base\Exception
     */
    public function existDeadFile(): bool
    {
        return D3FileHelper::fileExist($this->baseDirectory, $this->createDeadFileName());
    }
}