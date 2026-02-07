<?php

namespace d3yii2\d3printer\components;

use d3system\helpers\D3FileHelper;
use yii\base\Exception;
use yii\helpers\Json;

class ZebraPrinterStatus implements PrinterStatus
{

    private const CODE_CANNOT_CONNECT = '999';
    private const CODE_OTHER_ERROR = '998';
    private const STATUS_ERROR = 'Error';
    public ?string $statusTime = null;
    private ?string $actualStatusCode = null;
    private ?string $actualStatusLabel = null;

    public ?string $runtimeDir = 'printer-status';
    public ?string $printerComponentName = null;

    private const STATUS_READY = 'Ready';
    public const ERROR_HEALTH_LIST = [
        [
            'code' => '030',
            'label' => 'communication settings',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'paper out',
            'check' => true,
        ],
        [
            'code' => '1',
            'label' => 'pause active',
            'check' => true,
        ],
        [
            'code' => '1231',
            'label' => 'label length (value in number of dots)',
            'check' => false,
        ],
        [
            'code' => '000',
            'label' => 'number of formats in receive buffer',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'buffer full',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'diagnostic mode active',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'partial format',
            'check' => false,
        ],
        [
            'code' => '000',
            'label' => 'unused',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'corrupt RAM (configuration data lost)',
            'check' => true,
        ],
        [
            'code' => '1',
            'label' => 'under temperature',
            'check' => true,
        ],
        [
            'code' => '1',
            'label' => 'over temperature',
            'check' => true,
        ],
    ];
    private array $response = [];
    private array $errors = [];


    /**
     * @throws Exception
     */
    public function __construct(string $response = null)
    {
        if ($response) {
            $this->processResponse($response);
            $this->statusTime = date('Y-m-d H:i:s');
        }
    }

    /**
     * @throws Exception
     */
    private function processResponse(string $response): void
    {
        $firstRow = trim(current(explode(PHP_EOL, $response)),chr(2).chr(3)."\r\n");
        $parsedResponse = explode(',', $firstRow);

        if (
            count(array_diff_key($parsedResponse, self::ERROR_HEALTH_LIST)) > 0 ||
            count(array_diff_key(self::ERROR_HEALTH_LIST, $parsedResponse)) > 0
        ) {
            throw new Exception(sprintf(
                'Error list format does not match, received: %s parsed: %s',
                $response,
                implode(',', $parsedResponse)
            ));
        }
        foreach ($parsedResponse as $key => $item) {
            $error = self::ERROR_HEALTH_LIST[$key];
            if($error['check'] && $error['code'] === $item) {
                $this->errors[] = $error['label'];
                $this->actualStatusLabel = self::STATUS_ERROR;
                $this->actualStatusCode = self::STATUS_ERROR;
                unset($parsedResponse[$key]);
            } else {
                $this->response[] = $error['label'];
            }
        }
        if (!$this->actualStatusCode) {
            $this->actualStatusCode = self::STATUS_READY;
            $this->actualStatusLabel = self::STATUS_READY;
        }
    }

    public function isReady(): bool
    {
        return $this->actualStatusCode === self::STATUS_READY;
    }

    /**
     * @throws Exception
     */
    public function saveStatus(): string
    {
        $data = [
            'statusTime' => $this->statusTime,
            'response' => $this->response,
            'errors' => $this->errors,
            'actualStatusLabel' => $this->statusLabel(),
            'actualStatusCode' => $this->actualStatusCode,
        ];
        return D3FileHelper::filePutContentInRuntime(
            $this->runtimeDir,
            $this->getDataFilename(),
            Json::encode($data)
        );
    }

    public function statusLabel(): string
    {
       return $this->actualStatusLabel;
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
        $self = new self();
        $self->actualStatusCode = $data['actualStatusCode'];
        $self->actualStatusLabel = $data['actualStatusLabel'];
        $self->statusTime = $data['statusTime'];
        $self->response = $data['response'];
        $self->errors = $data['response'];
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
        return $savedStatus->errorsToString() !== $this->errorsToString();
    }

    private function errorsToString(): string
    {
        $errors = $this->errors;
        sort($errors);
        return implode(', ', $errors);
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
        return $this->actualStatusLabel;
    }

    public function getStatusTime(): string
    {
        return $this->statusTime;
    }
}