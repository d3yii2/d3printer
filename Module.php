<?php

namespace d3yii2\d3printer;

use d3system\yii2\base\D3Module;
use Yii;
use yii\log\FileTarget;

/**
 * Class Module
 * @package d3yii2\d3printer
 */
class Module extends D3Module
{
    public $controllerNamespace = 'd3yii2\d3printer\controllers';
    
    /**
     * 
     */
    public function init()
    {
        $month = date('F-Y');
    
        Yii::$app->log->setTraceLevel(0); // Disable log data with superglobals like $_SESSION, $_GET, etc 
        
        // Add printer error log
        $errorTarget = new FileTarget();
        $errorTarget->logFile = Yii::getAlias('@runtime') . '/logs/d3printer/' . $month . '-error.log';
        $errorTarget->categories = ['d3printer-error'];
        $errorTarget->logVars = [];
        
        // Add printer info log
        $infoTarget = new FileTarget();
        $infoTarget->logFile = Yii::getAlias('@runtime') . '/logs/d3printer/' . $month . '-info.log';
        $infoTarget->categories = ['d3printer-info'];
        $infoTarget->logVars = [];
       
        Yii::$app->getLog()->targets = [$errorTarget, $infoTarget];
        
        parent::init();
    }
    
    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Yii::t('d3printer', 'd3yii2/d3printer');
    }
}
