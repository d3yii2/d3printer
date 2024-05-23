<?php

namespace d3yii2\d3printer\logic\health;

use d3yii2\d3printer\logic\Logger;
use d3yii2\d3printer\logic\Mailer;
use d3yii2\d3printer\logic\settings\AlertSettings;
use yii\base\Component;

/**
 * Class Health
 * @package d3yii2\d3printer\logic\health
 */
class Health extends Component
{
    /** @var Logger $logger */
    public $logger;
    public $device;
    public $printerCode;
    public $printerName;
    
    protected $accessSettings = [];
    protected $alertSettings;
    protected $mailer;
    protected $cached;
    
    /**
     * Health constructor.
     * @param array $accessSettings
     * @param string $printerCode
     * @param string $printerName
     */
    public function __construct(array $accessSettings, string $printerCode, string $printerName, $cached = false)
    {
        $this->accessSettings = $accessSettings;
        $this->printerCode = $printerCode;
        $this->printerName = $printerName;
        $this->cached = $cached;
        parent::__construct();
    }
    
    /**
     *
     */
    public function init()
    {
        $this->alertSettings = new AlertSettings($this->printerCode);
        $this->logger = new Logger($this->printerCode, $this->printerName);
        $this->mailer = new Mailer();
    }
    
    /**
     * @return Logger
     */
    public function logger(): Logger
    {
        return $this->logger;
    }
    
    /**
     * @return Mailer
     */
    public function mailer(): Mailer
    {
        return $this->mailer;
    }
}
