<?php

namespace d3yii2\d3printer\logic\tasks;

use d3system\compnents\D3CommandTask;
use d3system\exceptions\D3TaskException;
use d3system\helpers\D3FileHelper;
use d3yii2\d3printer\components\Printer;
use farmeko\app\components\GodexZx1200Printer;
use Yii;
use yii\base\Exception;

class PrinterTask extends D3CommandTask
{
    /** @var string|null printer component name */
    public ?string $printerName = null;

    /** @var Printer|GodexZx1200Printer|object|null */
    public ?object $printer = null;
    
    public function execute(): void
    {
        $this->controller->out('Printer Name:' . $this->printerName);
        
        if (!isset(Yii::$app->{$this->printerName})) {
            throw new D3TaskException('Printer config not found. Check the component in app config');
        }
        $this->printer = Yii::$app->{$this->printerName};
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getRuntimePath(): string
    {
        return D3FileHelper::getRuntimeDirectoryPath('d3printer/' . $this->printerName);
    }
}