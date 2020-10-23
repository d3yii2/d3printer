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
    protected $alerts = [];
    protected $mailer;
    protected $alertSettings;
    
    /**
     * D3Printer constructor.
     */
    public function __construct()
    {
        $this->mailer = Yii::$app->mailer;
        $this->alertSettings = new D3PrinterAlertSettings();
    }
    
    public function sendAlerts()
    {
        $content = '';
        
        foreach ($this->alerts as $msg) {
            $content .= $msg . PHP_EOL;
        }
        $this->sendToEmail($content);
    }
    
    /**
     * @param string $msg
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
    
    public function getMailerConfig()
    {
        $conf = [
            'from' => $this->alertSettings->getEmailFrom(),
            'to' => $this->alertSettings->getEmailTo(),
            'subject' => $this->alertSettings->getEmailSubject(),
        ];
        
        return $conf;
    }
    
    /**
     * @return bool
     */
    public function hasAlerts(): bool
    {
        return !empty($this->alerts);
    }
    
    /**
     * @param string $msg
     */
    public function logError(string $msg)
    {
        echo $msg . PHP_EOL;
        
        $this->alerts[] = $msg;
        
        //Yii::warning($msg);
    }
    
    /**
     * @param string $msg
     */
    public function logInfo(string $msg)
    {
        echo $msg . PHP_EOL;
        
        //Yii::info($msg);
    }
}
