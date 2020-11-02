<?php

namespace d3yii2\d3printer\logic;

use d3yii2\d3printer\logic\settings\D3PrinterAlertSettings;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\Exception;

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

        if (YII_DEBUG) {
            // Save emails to runtime instead sending
            $this->mailer->useFileTransport = true;
        }
        
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
    public static function getLastLoggedErrors(int $limit = 10): array
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
    
    /**
     * @param $content
     * @return string
     */
    public function getLogHash($content): string
    {
        return md5($content);
    }
    
    /**
     * @param $content
     * @return string
     */
    public function getLastLogHash(): string
    {
        $path = Yii::getAlias('@runtime') . '/logs/d3printer/lastLogHash.txt';
        $hash = file_exists($path) ? file_get_contents($path) : '';
        return $hash;
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
    
    public function updateLogHash($content): bool
    {
        $hash = $this->getLogHash($content);
        $path = Yii::getAlias('@runtime') . '/logs/d3printer/lastLogHash.txt';
        return file_put_contents($path, $hash);                   
    }
    
    /**
     * @return string
     * @throws GuzzleException
     * @throws Exception
     */
    public function check(): string
    {
        /**
         *  Get the device live data from printer Homepage
         */
    
        $deviceHealth = new D3PrinterDeviceHealth();
    
        // Check the state of the device: Alive / Off
        $deviceHealth->statusOk();
    
        // Check the Cartridge remaining %
        $deviceHealth->cartridgeOk();
    
        // Check the Drum remaining %
        $deviceHealth->drumOk();
    
        // Get the devive live data from printer Configuration page
        $configHealth = new D3PrinterConfigurationHealth();
    
    
        /**
         * Compare System configuration with Printer data
         */
    
        if (!$configHealth->paperSizeOk()) {
            $configHealth->updatePaperConfig();
        }
    
        if (!$configHealth->printOrientationOk()) {
            $configHealth->updatePrintConfig();
        }
    
        if (!$configHealth->energySleepOk()) {
            $configHealth->updateEnergyConfig();
        }
    
        $alertInfoContent = $deviceHealth->getMessages($deviceHealth->getInfo()) . PHP_EOL;
        $alertErrorContent = '';
    
        if ($deviceHealth->hasErrors()) {
            $alertErrorContent .= PHP_EOL . 'Device Health Problems:' . PHP_EOL . $deviceHealth->getMessages($deviceHealth->getErrors());
        }
    
        $alertInfoContent .= PHP_EOL . PHP_EOL . $configHealth->getMessages($configHealth->getInfo());
    
        if ($configHealth->hasErrors()) {
            $alertErrorContent .= PHP_EOL . 'Config Health Problems:' . PHP_EOL . $configHealth->getMessages($configHealth->getErrors());
        }
    
        $alertMsg = $alertInfoContent . PHP_EOL . $alertErrorContent;
        //echo str_replace(PHP_EOL, '<br>', $alertInfoContent . PHP_EOL . $alertErrorContent);
    
        $deviceHealth->logInfo($alertInfoContent . PHP_EOL . D3PrinterHealth::LOG_SEPARATOR . PHP_EOL);
        
        if ($deviceHealth->hasErrors() || $configHealth->hasErrors()) {
            $deviceHealth->logErrors($alertErrorContent . PHP_EOL . D3PrinterHealth::LOG_SEPARATOR . PHP_EOL);

            if ($this->isNewLogHash($alertMsg)) {
                $deviceHealth->sendToEmail($alertMsg);
            }
        }
    
        $this->updateLogHash($alertMsg);
    
        return $alertMsg;
    }
}
