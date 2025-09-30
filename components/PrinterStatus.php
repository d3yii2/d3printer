<?php

namespace d3yii2\d3printer\components;

use yii\base\Exception;

interface PrinterStatus
{
    public function isReady(): bool;

    /**
     * @throws Exception
     */
    public function saveStatus(): string;

    public function statusLabel(): string;

    /**
     * @throws Exception
     */
    public function loadSavedStatus(): self;

    /**
     * @throws Exception
     */
    public function isChangesInErrors(): bool;

    public function setCanNotConnect(): void;

    public function setOtherError(string $error): void;

    public function getActualStatusReport(): string;

    public function getStatusTime(): string;
}