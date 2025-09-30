<?php

namespace d3yii2\d3printer\components;

use d3system\helpers\D3FileHelper;
use yii\base\Exception;
use yii\helpers\Json;

class GodexPrinterStatus implements PrinterStatus
{

    private const CODE_CANNOT_CONNECT = '999';
    private const CODE_OTHER_ERROR = '998';
    public ?string $statusTime = null;
    private ?string $actualStatusCode = null;
    private ?string $actualStatusLabel = null;

    public ?string $runtimeDir = 'printer-status';
    public ?string $printerComponentName = null;

    private const STATUS_READY_CODE = '00';
    private const STATUS_LIST = [
        [
            'code' => self::STATUS_READY_CODE,
            'label' => 'Ready',
        ],
        [
            'code' => '01',
            'label' => 'Media Empty or Media Jam',
        ],
        [
            'code' => '02',
            'label' => 'Media Empty or Media Jam',
        ],
        [
            'code' => '03',
            'label' => 'Ribbon Empty',
        ],
        [
            'code' => '04',
            'label' => 'Printhead Up ( Open )',
        ],
        [
            'code' => '05',
            'label' => 'Rewinder Full',
        ],
        [
            'code' => '06',
            'label' => 'File System Full',
        ],
        [
            'code' => '07',
            'label' => 'Filename Not Found',
        ],
        [
            'code' => '8',
            'label' => 'Duplicate Name',
        ],
        [
            'code' => '09',
            'label' => 'Syntax error',
        ],
        [
            'code' => '10',
            'label' => 'Cutter JAM',
        ],
        [
            'code' => '11',
            'label' => 'Extended Memory Not Found',
        ],
        [
            'code' => '20',
            'label' => 'Pause',
        ],
        [
            'code' => '21',
            'label' => 'In Setting Mode',
        ],
        [
            'code' => '22',
            'label' => 'In Keyboard Mode',
        ],
        [
            'code' => '50',
            'label' => 'Printer is Printing',
        ],
        [
            'code' => '60',
            'label' => 'Data in Process',
        ],
        [
            'code' => self::CODE_OTHER_ERROR,
            'label' => 'Other error'
        ],
        [
            'code' => self::CODE_CANNOT_CONNECT,
            'label' => 'Can not connect'
        ]
    ];


    public function __construct(
        string $printerComponentName,
        string $statusCode = null
    )
    {
        $this->printerComponentName = $printerComponentName;
        $this->statusTime = date('Y-m-d H:i:s');
        if ($statusCode) {
            $this->actualStatusCode = $statusCode;
        }
    }

    public function isReady(): bool
    {
        return $this->actualStatusCode === self::STATUS_READY_CODE;
    }

    /**
     * @throws Exception
     */
    public function saveStatus(): string
    {
        $data = [
            'statusTime' => $this->statusTime,
            'actualStatusCode' => $this->actualStatusCode,
            'actualStatusLabel' => $this->actualStatusLabel,
        ];
        return D3FileHelper::filePutContentInRuntime(
            $this->runtimeDir,
            $this->getDataFilename(),
            Json::encode($data)
        );
    }

    public function statusLabel(): string
    {
        foreach (self::STATUS_LIST as $status) {
            if ($status['code'] === $this->actualStatusCode) {
                return $status['label'];
            }
        }
        return $this->actualStatusCode;
    }

    /**
     * @throws Exception
     */
    public function loadSavedStatus(): self
    {
        $rawData =  D3FileHelper::fileGetContentFromRuntime(
            $this->runtimeDir,
            $this->getDataFilename()
        );
        $data = Json::decode($rawData);
        $self = new self($this->printerComponentName);
        $self->actualStatusCode = $data['actualStatusCode'];
        $self->statusTime = $data['statusTime'];
        $self->actualStatusLabel = $data['actualStatusLabel'];
        return $self;
    }

    private function getDataFilename(): string
    {
        return $this->printerComponentName . '-data.json';
    }

    /**
     * @throws Exception
     */
    public function isChangesInErrors(): bool
    {
        $savedStatus = $this->loadSavedStatus();
        return $savedStatus->actualStatusCode !== $this->actualStatusCode;
    }

    public function setCanNotConnect(): void
    {
        $this->actualStatusCode = self::CODE_CANNOT_CONNECT;
        $this->actualStatusLabel = 'Can not connect';
    }

    public function setOtherError(string $error): void
    {
        $this->actualStatusCode = self::CODE_OTHER_ERROR;
        $this->actualStatusLabel = $error;
    }

    public function getActualStatusReport(): string
    {
        return $this->actualStatusCode . ' - ' . $this->actualStatusLabel;
    }

    public function getStatusTime(): string
    {
        return $this->statusTime;
    }
}