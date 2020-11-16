<?php

namespace d3yii2\d3printer\logic;

use d3system\helpers\D3FileHelper;
use DateTime;
use Yii;
use yii\base\Component;
use Yii\base\Event;
use yii\log\FileTarget;

/**
 * Class Logger
 * @package d3yii2\d3printer\logic
 */
class Logger extends Component
{
    public const LOG_SEPARATOR = '-------------';
    
    protected $errors = [];
    protected $info = [];
    protected $printerCode;
    protected $printerName;
    
    public function __construct(string $printerCode, string $printerName)
    {
        $this->printerCode = $printerCode;
        $this->printerName = $printerName;
        parent::__construct();
    }
    
    public function init()
    {
        Yii::$app->log->setTraceLevel(0); // Disable log data with superglobals like $_SESSION, $_GET, etc
        $this->setLogTarget();
        parent::init();
    }
    
    public function setLogTarget()
    {
        $month = date('F-Y');
    
        // Add printer error log
        $errorTarget = new FileTarget();
        $errorTarget->logFile = Yii::getAlias('@runtime') . '/logs/d3printer/' . $this->printerCode . '-' . $month . '-error.log';
        $errorTarget->categories = ['d3printer-error'];
        $errorTarget->logVars = [];
    
        // Add printer info log
        $infoTarget = new FileTarget();
        $infoTarget->logFile = Yii::getAlias('@runtime') . '/logs/d3printer/' . $this->printerCode . '-' . $month . '-info.log';
        $infoTarget->categories = ['d3printer-info'];
        $infoTarget->logVars = [];
    
        Yii::$app->getLog()->targets = [$errorTarget, $infoTarget];
    }
    
    /**
     * @param string $content
     * @param string $sep
     */
    public function logInfo(string $content, string $sep = PHP_EOL . self::LOG_SEPARATOR . PHP_EOL): void
    {
        Yii::info($content . $sep, 'd3printer-info');
    }
    
    /**
     * @param string $content
     * @param string $sep
     */
    public function logErrors(string $content, string $sep = PHP_EOL . self::LOG_SEPARATOR . PHP_EOL): void
    {
        Yii::error($content . $sep, 'd3printer-error');
    }
    
    /**
     * @param string $msg
     */
    public function addError(string $msg): void
    {
        $this->errors[] = $msg;
    }
    
    /**
     * @param string $msg
     */
    public function addInfo(string $msg): void
    {
        $this->info[] = $msg;
    }
    
    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }
    
    /**
     * @param array $arr
     * @param string $glue
     * @return string
     */
    public function getMessages(array $arr, string $glue = PHP_EOL): string
    {
        return implode($glue, $arr);
    }
    
    public function getInfoMessages(string $glue = PHP_EOL): string
    {
        return $this->getMessages($this->info, $glue);
    }

    public function getErrorMessages(string $glue = PHP_EOL): string
    {
        return $this->getMessages($this->errors, $glue);
    }
    
    /**
     * @param int $limit
     * @return array
     */
    public function getLastLoggedErrors(int $limit = 10): array
    {
        $month = date('m');
        
        $errors = [];
        while ($month >= 1) {
            $logFilename = $this->getLogFilenameByMonth($month);
            $logContent = D3FileHelper::fileGetContentFromRuntime('logs/d3printer', $logFilename);
            if ($logContent) {
                $allErrors = explode(self::LOG_SEPARATOR, $logContent);
                $allErrors = array_reverse($allErrors);
                $count = count($allErrors);
                if ($limit >= $count) {
                    $limit = $count;
                }
                for ($i = 0; $i < $limit; $i++) {
                    $errors[] = $allErrors[$i];
                }
                break;
            }
            $month--;
        }
        return $errors;
    }
    
    /**
     * @param string $month
     * @param string $type
     * @return string
     */
    public function getLogFilenameByMonth(string $month, string $type = 'error'): string
    {
        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');
        return $this->printerCode . '-' . $monthName . '-' . date('Y') . '-' . $type . '.log';
    }
    
    /**
     * @param $content
     * @return bool
     */
    public function isNewLogHash($content): bool
    {
        $lastLogHash = $this->getLastLogHash();
        $hash = $this->getLogHash($content);
        
        return $hash !== $lastLogHash;
    }
    
    public function getLogHashFilename()
    {
        return $this->printerCode . '-lastLogHash.txt';
    }
    
    /**
     * @return string
     */
    public function getLastLogHash(): string
    {
        return D3FileHelper::fileGetContentFromRuntime('logs/d3printer', $this->getLogHashFilename()) ?? '';
    }
    
    /**
     * @param $content
     * @return string
     */
    public function getLogHash($content): string
    {
        return md5($content);
    }
    
    public function updateLogHash($content): bool
    {
        $hash = $this->getLogHash($content);
        return D3FileHelper::filePutContentInRuntime('logs/d3printer', $this->getLogHashFilename(), $hash);
    }
    
    /**
     * @return bool
     */
    public function deleteLogHash(): bool
    {
        $file = D3FileHelper::getRuntimeFilePath('logs/d3printer', $this->getLogHashFilename());
        return file_exists($file) ?? unlink($file);
    }
    
    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
