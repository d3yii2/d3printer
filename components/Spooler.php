<?php

namespace d3yii2\d3printer\components;

use d3system\helpers\D3FileHelper;
use yii\base\Component;
use yii\base\Exception;

/**
 *
 * @property-read array $spoolDirectoryFiles
 * @property-read string $spoolDirectory
 */
class Spooler extends Component
{
    public string $baseDirectory = 'd3printer';


    /**
     * @throws Exception
     */
    public function sendToSpooler(string $printerCode, string $filepath, int $copies = 1): bool
    {
        if (!file_exists($filepath)) {
            throw new Exception('Do not exist file: ' . $filepath);
        }
        $spoolDirectory = D3FileHelper::getRuntimeDirectoryPath($this->getSpoolDirectory($printerCode));
        $pi = pathinfo($filepath);
        for ($i = 1; $i <= $copies; $i++) {
            $toFile = $spoolDirectory . '/' . $pi['filename'] . $i . '.' . $pi['extension'];
            if (!copy($filepath, $toFile)) {
                throw new Exception('Can not copy file to ' . $toFile);
            }
        }

        return true;
    }

    public function getSpoolDirectory($printerCode): string
    {
        return $this->baseDirectory . '/spool_' . $printerCode;
    }

    /**
     * @throws Exception
     */
    public function getSpoolDirectoryFiles(): array
    {
        if ($list = D3FileHelper::getDirectoryFiles($this->getSpoolDirectory())) {
            return $list;
        }
        return [];
    }
}
