<?php

namespace d3yii2\d3printer\logic;

use d3yii2\d3printer\logic\settings\D3PrinterAlertSettings;
use Yii;

/**
 * Class D3Printer
 * @package d3yii2\d3printer\logic
 */
class D3PrinterHealth
{
    protected $errors = [];
    protected $info = [];
    protected $mailer;
    protected $alertSettings;
    
    public const LOG_SEPARATOR = '-------------';
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        $this->mailer = Yii::$app->mailer;
        $this->alertSettings = new D3PrinterAlertSettings();
    }
    
    /**
     * @param string $content
     */
    public function logInfo(string $content): void
    {
        Yii::info($content, 'd3printer-info');
    }
    
    /**
     * @param string $content
     */
    public function logErrors(string $content): void
    {
        Yii::error($content, 'd3printer-error');
    }
    
    /**
     * @param string $content
     */
    public function sendToEmail(string $content)
    {
        $conf = $this->getMailerConfig();
        
        $this->mailer
            ->compose()
            ->setFrom($conf['from'])
            ->setTo($conf['to'])
            ->setSubject($conf['subject'])
            ->setTextBody($content)
            ->send();
    }
    
    /**
     * @return array
     */
    public function getMailerConfig(): array
    {
        return [
            'from' => $this->alertSettings->getEmailFrom(),
            'to' => $this->alertSettings->getEmailTo(),
            'subject' => $this->alertSettings->getEmailSubject(),
        ];
    }
    
    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
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
    
    /**
     * @param int $count
     * @return array
     */
    public static function getLastLoggedErrors(int $limit = 20): array
    {
        $logPath = Yii::getAlias('@runtime') . '/logs/d3printer/';
        $month = date('m');
        
        $errors = [];        
        while($month >=1 ) {
            $dateObj   = \DateTime::createFromFormat('!m', $month);
            $monthName = $dateObj->format('F');
            $errLog = $logPath . $monthName . '-' . date('Y') . '-error.log';
            if (file_exists($errLog)) {
                $logContent = file_get_contents($errLog);
                $allErrors = explode(self::LOG_SEPARATOR, $logContent);
                $allErrors = array_reverse($allErrors);
                $count = count($allErrors);
                if ($limit >= $count) {
                    $limit = $count;
                }
                for ($i = 0; $i < $limit; $i ++) {
                    $errors[] = $allErrors[$i];
                }
                break;
            }
            $month --;
        }
        return $errors;
    }
}
