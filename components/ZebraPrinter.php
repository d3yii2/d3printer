<?php

namespace d3yii2\d3printer\components;

use yii\base\Exception;
use yii;

/**

 */
class ZebraPrinter extends BasePrinter  implements PrinterInterface
{

    public ?string $printerIp = null;
    public int $printerPort = 6101;
    public ?string $templateFile = null;
    public string $printerClientClass = ZebraClient::class;

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
     */
    public function processStatus(): PrinterStatus
    {
        $printerClass = $this->printerClientClass;
        return (new $printerClass($this->printerIp, $this->printerPort))
            ->processStatus();
    }

}
