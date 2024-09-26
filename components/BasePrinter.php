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
class BasePrinter extends Component
{

    /**
     * @var string
     */
    public $printerCode;

    /**
    * @var string base directory in runtime directory for spool directories
    */
    public $baseDirectory = 'd3printer';

    /**
     * @var string server printer name on windows
     */
    public $printerName;


    public $printFilesCount;

    public $sleepSeconds = 0;
    /**
     * @throws Exception
     */
    public function saveFileInSpoolDirectory(string $content, int $copies = 1, string $fileName = ''): bool
    {
        if(!$content){
            throw new Exception('Empty file content');
        }
        if (!$fileName) {
            $fileName = uniqid($this->printerCode, true) . '.txt';
        }
        $spoolDirectory = D3FileHelper::getRuntimeDirectoryPath($this->getSpoolDirectory());

        for ($i = 1; $i <= $copies; $i++) {
            $pi = pathinfo($fileName);
            $toFile = $spoolDirectory . '/' . $pi['filename'] . $i . '.' . $pi['extension'];
            file_put_contents($toFile, $content);
        }

        return true;
    }

    public function getSpoolDirectory(): string
    {
        return $this->baseDirectory  . '/spool_' . $this->printerCode;
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

    public function saveErrors(array $errors): string
    {
        $hash = $this->getLogHash($errors);

        return D3FileHelper::filePutContentInRuntime('logs/d3printer', $this->getErrorsFilename(), $hash);
    }

    public function isChangedErrors(array $errors): bool
    {
        return $this->getLogHash($errors) !== $this->getLastLogHash();
    }

    private function getErrorsFilename(): string
    {
        return $this->printerCode . '-healthError.txt';
    }

    private function getLogHash(array $errors): string
    {
        return implode(PHP_EOL, $errors);
    }

    private function getLastLogHash(): string
    {
        return D3FileHelper::fileGetContentFromRuntime('logs/d3printer', $this->getErrorsFilename()) ?? '';
    }
}
