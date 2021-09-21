<?php

namespace d3yii2\d3printer\components;

interface D3PrinterInterface
{

    /**
     * @param Object $data
     * @throws \Exception
     */
    public function print(Object $data): void;

    /**
     * @throws \Exception
     */
    public function check(): bool;

    public function getCheckResponseCode(): string;

    public function getCheckResponseLabel(): string;
}
