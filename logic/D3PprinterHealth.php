<?php

namespace d3yii2\d3printer\logic;

use Yii;
use yii\swiftmailer\Mailer;
use yii\web\HttpException;

/**
 * Class D3Pprinter
 * @package d3yii2\d3printer\logic
 */
class D3PprinterHealth
{
    protected $alerts = [];
    protected $mailer;
    
    /**
     * D3Pprinter constructor.
     */
    public function __construct()
    {
        $this->mailer = Yii::$app->mailer;
    }
    
    public function getMailerConfig()
    {
        $conf = [
            'from' => 'system@cewwod.loc',
            'to' => 'info@cewwod.loc',
            'subject' => 'Kļūda printerī',
            'body' => '',
        ];
    
        return $conf;
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
    public function logAlert(string $msg)
    {
        echo $msg . PHP_EOL;
        
        $this->alerts[] = $msg;
        
        //Yii::warning($msg);
    }
}
