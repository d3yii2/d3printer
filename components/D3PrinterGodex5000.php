<?php

namespace d3yii2\d3printer\components;

use Dbr\Ezpl\Builder;
use Dbr\Ezpl\Command\CommandPipe;
use Dbr\Ezpl\Command\Service\Status;
use Dbr\Ezpl\Driver\NetworkConnector;
use Dbr\Ezpl\Printer;
use yii\base\Component;

/**
 *
 * @property-read string $checkResponseLabel
 * @property-read string $checkResponseCode
 */
class D3PrinterGodex5000 extends Component implements D3PrinterInterface
{

    /**
     * @var string
     */
    public $printerIp;

    /**
     * @var \Dbr\Ezpl\Printer
     */
    private $_printer;

    /** @var string */
    private $_response;

    /**
     * @throws \Exception
     */
    private function connect(): void
    {
        if ($this->_printer) {
            return;
        }
        $connector = new NetworkConnector($this->printerIp);
        $this->_printer = new Printer($connector);
    }


    /**
     * @throws \Exception
     */
    public function print(Object $data): void
    {
        $this->connect();
        $this->_printer->send($data->createCommand());
    }

    /**
     * @throws \Exception
     */
    public function check(): bool
    {
        $this->connect();
        $command = (new Builder(new CommandPipe()))->requestStatus();
        $this->_response = $this->_printer->sendAndRead($command);

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
}
