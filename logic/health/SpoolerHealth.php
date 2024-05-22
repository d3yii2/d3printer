<?php

namespace d3yii2\d3printer\logic\health;

use d3system\exceptions\D3TaskException;
use Yii;

/**
 * Class SpoolerHealth
 * @package d3yii2\d3printer\logic\health
 */
class SpoolerHealth extends Health
{    
    /**
     * @var ReadDevice $device
     */
    public $printer;

    /**
     * @throws \yii\base\Exception
     */
    public function init()
    {
        parent::init();

        if (!isset(Yii::$app->{$this->printerCode})) {
            throw new D3TaskException('Printer config not found. Check the component in app config');
        }

        $this->printer = Yii::$app->{$this->printerCode};
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->printer->getSpoolDirectoryFiles();
    }

    /**
     * @return bool
     */
    public function hasMultipleFiles(): bool
    {
        $files = $this->printer->getSpoolDirectoryFiles();
        
        return count($files) > 1;
    }    
}
