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
     * @return string
     */
    public function getLabel(): string
    {
        return Yii::t('d3printer', 'd3yii2/d3printer');
    }
}
