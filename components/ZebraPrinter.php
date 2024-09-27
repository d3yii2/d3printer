<?php

namespace d3yii2\d3printer\components;

use d3system\helpers\D3FileHelper;
use yii\base\Component;
use yii\base\Exception;
use yii\base\View;
use Zebra\Client;
use yii;
use Zebra\CommunicationException;
use Zebra\Zpl\Builder;

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
            $printer = new ZebraClient($this->printerIp, $this->printerPort);
            $command = (new Builder())->command('! U1 getvar "device.host_status"');

            $response = $printer->sendAndRead($command->toZpl());
            $errors = $this->fetchErrors($response);

            if(\count($errors) > 0) {
                return $errors;
            }
        } catch (CommunicationException $exception) {
            return ['Can not connect'];
        }

        return [];
    }

    private function fetchErrors(string $response): array
    {
        $parsedResponse = explode(',', current(explode("\n", $response)));
        $errorList = ZebraClient::ERROR_HEALTH_LIST;

        if (
            \count(array_diff_key($parsedResponse, $errorList)) > 0 ||
            \count(array_diff_key($errorList, $parsedResponse)) > 0
        ) {
            throw new Exception(sprintf(
                'Error list format does not match, received: %s parsed: %s',
                $response,
                implode(',', $parsedResponse)
            ));
        }

        $errors = [];
        foreach ($parsedResponse as $key => $item) {
            $error = $errorList[$key];
            if($error['show'] && $error['code'] === $item) {
                $errors[] = $error['label'];
            }
        }

        return $errors;
    }
}
