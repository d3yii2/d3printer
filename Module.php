<?php

namespace d3yii2\d3printer;

use d3system\yii2\base\D3Module;
use Yii;

class Module extends D3Module
{
    public $controllerNamespace = 'd3yii2\d3printer\controllers';
    
    public function getLabel(): string
    {
        return Yii::t('d3printer', 'd3yii2/d3printer');
    }
}
