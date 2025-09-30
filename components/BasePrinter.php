<?php

namespace d3yii2\d3printer\components;

use d3system\helpers\D3FileHelper;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Json;

/**
 *
 * @property-read array $spoolDirectoryFiles
 * @property-read string $errorsFilename
 * @property-read string $lastLogErrors
 * @property-read string $spoolDirectory
 */
class BasePrinter extends Component
{

    public ?string $printerCode = null;

    /**
    * @var string base directory in runtime directory for spool directories
    */
    public string  $baseDirectory = 'd3printer';

    /**
     * @var null|string server printer name on windows
     */
    public ?string $printerName = null;


    public ?int $printFilesCount = null ;

    public int $sleepSeconds = 0;
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

    public function getErrorsFilename(): string
    {
        return $this->printerCode . '-healthError.txt';
    }

    /**
     * @throws Exception
     */
    public function saveErrors(array $errors): string
    {
        return D3FileHelper::filePutContentInRuntime(
            'logs/d3printer',
            $this->getErrorsFilename(),
            Json::encode($errors)
        );
    }

    /**
     * @throws Exception
     */
    public function loadStatus(): PrinterStatus
    {
        return (new GodexPrinterStatus($this->printerName))->loadSavedStatus();
    }
}
