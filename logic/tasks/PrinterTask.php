<?php

namespace d3yii2\d3printer\logic\tasks;

use d3system\compnents\D3CommandTask;
use d3system\exceptions\D3TaskException;
use d3system\helpers\D3FileHelper;
use Yii;

class PrinterTask extends D3CommandTask
{
    public $printerName;

    /** @var \d3yii2\d3codes\components\PrintWindowsPrinter */
    public $printer;
    
    public function execute()
    {
        $this->controller->out('Printer Name:' . $this->printerName);
        
        if (!isset(Yii::$app->{$this->printerName})) {
            throw new D3TaskException('Printer config not found. Check the component in app config');
        }
        
        $this->printer = Yii::$app->{$this->printerName};
    }
    
    /**
     * @return string
     */
    public function getRuntimePath(): string
    {
        return D3FileHelper::getRuntimeDirectoryPath('d3printer/' . $this->printerName);
    }
}