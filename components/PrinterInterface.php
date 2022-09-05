<?php

namespace d3yii2\d3printer\components;

interface PrinterInterface
{
    public function printToSpoolDirectory($model): void;
    public function printSpooledFiles(): int;
}