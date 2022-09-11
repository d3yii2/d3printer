<?php

namespace d3yii2\d3printer\components;

use d3system\helpers\D3FileHelper;
use yii\base\Component;
use yii\base\Exception;
use yii\base\View;
use Zebra\Client;
use yii;

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

    public function printSpooledFiles(): int
    {

        if (!$files = $this->getSpoolDirectoryFiles()) {
            return 0;
        }
        $printer = new Client($this->printerIp, $this->printerPort);
        $i = 0;
        foreach ($files as $filePath) {
            if ($i) {
                sleep($this->sleepSeconds);
            }
            if (!$transaction = yii::$app->db->beginTransaction()) {
                throw new \yii\db\Exception('Can not initiate tran');
            }
            try {
                $fileContent = file_get_contents($filePath);
                $printer->send($fileContent);
                $transaction->commit();
                $i++;
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                $transaction->rollBack();
            }
            if (!unlink($filePath)) {
                throw new Exception('Can not delete file ' . $filePath);
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
}