<?php

namespace d3yii2\d3printer\logic\panel;

use d3yii2\d3printer\components\D3Printer;
use d3yii2\d3printer\components\Printer;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;
use d3yii2\d3printer\logic\health\DaemonHealth;

class DisplayDataLogic
{
    /**
     * @var \d3yii2\d3printer\components\D3Printer $printer
     * @var \d3yii2\d3printer\logic\health\DeviceHealth $deviceHealth
     * @var \d3yii2\d3printer\logic\health\ConfigurationHealth $configHealth
     */
    public $emptyDefaultValue = '-';

    /** @var \d3yii2\d3printer\components\Printer  */
    protected $printer;

    /**
     * @var \d3yii2\d3printer\logic\health\DeviceHealth $deviceHealth
     */
    protected $deviceHealth;

    /**
     * @var DaemonHealth $daemonHealth
     */
    protected $daemonHealth;

    /**
     * @var \d3yii2\d3printer\logic\health\ConfigurationHealth $configHealth
     */
    protected $configHealth;

    protected $displayData = [];

    public const DISPLAY_VERTICAL = 'vertical';
    public const DISPLAY_INLINE = 'inline';

    /**
     * @param string $printerComponent
     * @param string $healthComponent
     * @throws \yii\base\Exception `
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(string $printerComponent, string $healthComponent)
    {
        $this->printer = D3Printer::getPrinterComponent($printerComponent);

        /**
         * @var \d3yii2\d3printer\logic\health\Health $health
         */
        $health = D3Printer::getPrinterComponent($healthComponent);
        $this->deviceHealth = $health->deviceHealth(true);
        $this->daemonHealth = $health->daemonHealth(true);
        // @TODO
        //$this->configHealth = $health->configHealth(true);


        $this->setDisplayData();
    }

    /**
     *
     */
    public function setDisplayData(): void
    {
        $this->displayData['printerName'] = $this->printer->printerName;
        $this->displayData['ip'] = $this->printer->printerIp;
        $this->displayData['printerAccessUrl'] = $this->deviceHealth->getAccessUrl();
        $this->displayData['lastLoggedErrors'] = $this->deviceHealth->logger->getLastLoggedErrors();
        $this->setDisplayValue('printerCode', $this->printer->printerCode);
        $this->setDisplayValue('status', $this->getStatusDisplayValue());
        $this->setDisplayValue('cartridge', $this->getCartridgeDisplayValue());
        $this->setDisplayValue('drum', $this->getDrumDisplayValue());
        $this->setDisplayValue('deviceErrors', $this->deviceHealth->logger->getErrors());
        $this->setDisplayValue('ftpState', $this->getFTPStatusDisplayValue());
        $this->setDisplayValue('spool', $this->getSpoolerFilesCount());
        $this->setDisplayValue('daemonStatus', $this->getDaemonStatus());
    }

    /**
     * @return array
     */
    public function getDisplayData(): array
    {
        return $this->displayData;
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function setDisplayValue(string $key, $value): void
    {
        if (is_array($value)) {
            $this->displayData[$key] = $value;

        } else {
            $this->displayData[$key] = empty($value) && is_string($value) ? $this->emptyDefaultValue : trim($value);
        }
    }

    /**
     * @return string
     */
    protected function getStatusDisplayValue(): string
    {
        $isOk = $this->deviceHealth->statusOk();

        $status = Yii::t('d3printer', $this->deviceHealth->device->status());

        return $isOk
            ? Html::tag('span', $status,  ['style' => 'color:darkgreen'])
            : Html::tag('span', $status,  ['style' => 'color:red']);
    }

    /**
     * @return string
     */
    protected function getCartridgeDisplayValue(): string
    {
        $isOk = $this->deviceHealth->cartridgeOk();

        return $isOk
            ? Html::tag('span', $this->deviceHealth->device->cartridgeRemaining(),  ['style' => 'color:darkgreen'])
            : Html::tag('span', $this->deviceHealth->device->cartridgeRemaining(),  ['style' => 'color:red']);
    }

    /**
     * @return string
     */
    protected function getDrumDisplayValue(): string
    {
        $isOk = $this->deviceHealth->drumOk();

        return $isOk
            ? Html::tag('span', $this->deviceHealth->device->drumRemaining(),  ['style' => 'color:darkgreen'])
            : Html::tag('span', $this->deviceHealth->device->drumRemaining(),  ['style' => 'color:red']);
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    protected function getFTPStatusDisplayValue(): string
    {
        $isOk = !$this->printer->existDeadFile();

        return $isOk
            ? Html::tag('span', 'OK',  ['style' => 'color:darkgreen'])
            : Html::tag('span', Yii::t('d3printer', 'Down'),  ['style' => 'color:red']);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function getSpoolerFilesCount(): int
    {
        return count($this->printer->getSpoolDirectoryFiles());
    }

    public function getDaemonStatus(): string
    {
        if($this->daemonHealth->statusOk()) {
            return Html::tag('span', $this->daemonHealth->getStatus(),  ['style' => 'color:darkgreen']);
        }

        $status = $this->daemonHealth->getStatus();
        $statusOutput = $status !== DaemonHealth::STATUS_UNKNOW ? $status : sprintf('%s (%s)', $status, $this->daemonHealth->getRawStatus());

        return Html::tag('span',  $statusOutput,  ['style' => 'color:red']);
    }

    /**
     * Return data for ThTableSimple widget
     * @param string $direction
     * @return array
     */
    public function getTableDisplayData(string $direction = self::DISPLAY_VERTICAL): array
    {
        $displayData = $this->getDisplayData();

        $data = self::DISPLAY_VERTICAL === $direction
            ? [
                'printerName' => $displayData['printerName'],
                'printerAccessUrl' => $displayData['printerAccessUrl'],
                'info' => [
                    'columns' => [
                        [
                            'header' => '',
                            'attribute' => 'label',
                        ],
                        [
                            'header' => '',
                            'attribute' => 'value',
                        ],
                    ],
                    'data' => [
                        [
                            'label' => Yii::t('d3printer', 'Status'),
                            'value' => $displayData['status'],
                        ],
                        [
                            'label' => Yii::t('d3printer','Cartridge'),
                            'value' => $displayData['cartridge'],
                        ],
                        [
                            'label' => Yii::t('d3printer','Drum'),
                            'value' => $displayData['drum']
                        ],
                        [
                            'label' => Yii::t('d3printer', 'FTP status'),
                            'value' => $displayData['ftpState'],
                        ],
                        [
                            'label' => Yii::t('d3printer', 'Spooler'),
                            'value' => $displayData['spool'],
                        ],
                        [
                            'label' => Yii::t('d3printer', 'IP'),
                            'value' => $displayData['ip'],
                        ],
                        [
                            'label' => Yii::t('d3printer', 'Daemon Status'),
                            'value' => $displayData['daemonStatus'],
                        ],
                    ],
                ],
            //'deviceErrors' => $displayData['deviceErrors'],
            //'lastLoggedErrors' => []
            ]
        :[
            'info' => [
                'columns' => [
                    [
                        'header' => 'Name',
                        'attribute' => 'name',
                    ],
                    [
                        'header' => 'Status',
                        'attribute' => 'status',
                    ],
                    [
                        'header' => 'Cartridge',
                        'attribute' => 'cartridge',
                    ],
                    [
                        'header' => 'Drum',
                        'attribute' => 'drum',
                    ]
                ],
                'data' => [
                    [
                        'name' => Html::a($displayData['printerName'], $displayData['printerAccessUrl']),
                        'status' => $displayData['status'],
                        'cartridge' => $displayData['cartridge'],
                        'drum'=> $displayData['drum']
                    ],
                ],
            ],
            //'deviceErrors' => $displayData['deviceErrors'],
            //'lastLoggedErrors' => []
        ];

        /*foreach ($displayData['lastLoggedErrors'] as $error) {
            $data['lastLoggedErrors'][] = str_replace(PHP_EOL, '<br>', $error);
        }*/

        return $data;
    }
}
