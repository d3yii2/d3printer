<?php

namespace d3yii2\d3printer\components;

use d3system\helpers\D3FileHelper;
use yii\base\Component;
use yii\base\Exception;
use yii\base\View;
use Zebra\Client;
use yii;
use Zebra\CommunicationException;

/**

 */
class ZebraPrinter extends BasePrinter  implements PrinterInterface
{

    /**
     * @var string
     */
    public $printerIp;

    /**
     * @var integer
     */
    public $printerPort = 6101;

    /**
     * @var string
     */
    public $templateFile;

    /**
     * @throws Exception
     * @throws yii\db\Exception
     */
    public function printSpooledFiles(): int
    {

        if (!$files = $this->getSpoolDirectoryFiles()) {
            return 0;
        }

        $i = 0;
        foreach ($files as $filePath) {
            if ($i) {
                sleep($this->sleepSeconds);
            }
            try {
                $this->print($filePath);
                if (!unlink($filePath)) {
                    throw new Exception('Can not delete file ' . $filePath);
                }
                $i++;
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }

            if (!$this->printFilesCount) {
                continue;
            }

            if ($i >= $this->printFilesCount) {
                break;
            }
        }
        return $i;
    }

    /**
     * @throws Exception
     */
    public function printToSpoolDirectory($model, int $copies = 1): void
    {
        throw new Exception('Not implemented method printToSpoolDirectory in child object');
    }

    /**
     * @param $filePath
     * @return void
     */
    public function print($filePath): void
    {
        $printer = new Client($this->printerIp, $this->printerPort);
        $fileContent = file_get_contents($filePath);
        $printer->send($fileContent);
    }

    public function collectErrors(): array
    {
        try {
            $printer = new Client($this->printerIp, $this->printerPort);
        } catch (CommunicationException $exception) {
            return ['Can not connect'];
        }

        return [];
    }
}
