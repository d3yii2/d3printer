<?php

namespace d3yii2\d3printer\components;

use d4yii2\d4store\models\D4StoreStoreProduct;
use Dbr\Ezpl\Builder;
use Dbr\Ezpl\Command\CommandPipe;
use Dbr\Ezpl\Command\Service\Status;
use Dbr\Ezpl\Driver\NetworkConnector;
use Dbr\Ezpl\Printer;
use yii\base\Exception;

/**
 *
 * @property-read string $checkResponseLabel
 * @property-read string $checkResponseCode
 */
class D3PrinterGodex5000 extends D3PrinterBase implements D3PrinterInterface
{

    /**
     * @var int
     */
    public $printerReadyCounter = 3;

    /**
     * @var string
     */
    public $printerIp;

    /**
     * @var \Dbr\Ezpl\Printer
     */
    public $printer;

    /** @var string */
    private $_response;
    /**
     * @var \Dbr\Ezpl\Driver\NetworkConnector
     */
    private $connector;

    /**
     * @throws \Exception
     */
    public function connect(): void
    {
        if ($this->printer) {
            return;
        }
        $this->connector = new NetworkConnector($this->printerIp);
        $this->printer = new Printer($this->connector);
    }

    public function disconect(): void
    {
        $this->connector->close();
        $this->printer = null;
    }

    /**
     * @throws \Exception
     */
    public function print(Object $data): void
    {
        $this->connect();
        $this->printer->send($data);
    }

    /**
     * @throws \Exception
     */
    public function check(): bool
    {
        $this->connect();
        $command = (new Builder(new CommandPipe()))->requestStatus();
        $this->_response = $this->printer->sendAndRead($command);

        switch ($this->_response) {
            case Status::STATUS_READY;
            case Status::STATUS_PRINTER_IS_PRINTING;
                return true;
        }

        return false;
    }

    public function getCheckResponseCode(): string
    {
        return $this->_response;
    }

    public function getCheckResponseLabel(): string
    {
        return Status::getResponseLabel($this->_response);
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function spoolLabelCommands(D4StoreStoreProduct $product): void
    {
        $command = $this->getCommand($product);
        $commands = $command->compose();
        $this->saveToSpoolDirectory($commands);
    }

    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function getStatusOk(): void
    {
        $i = 0;
        while (($status = $this->getStatus()) && !$status->isOk()) {
            sleep(1);
            $i++;
            if ($i > $this->printerReadyCounter) {
                throw new Exception('Printer: ' . $status->getLabel());
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function getStatus(): Status
    {
        $this->connect();
        $statusCommand = (new Builder(new CommandPipe()))->requestStatus();
        $response = $this->printer->sendAndRead($statusCommand);
        return new Status($response);
    }
}
