<?php

namespace d3yii2\d3printer\components;

interface PrinterInterface
{
    public function printToSpoolDirectory($model, int $copies = 1): void;
    public function printSpooledFiles(): int;
}