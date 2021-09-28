<?php

namespace d3yii2\d3printer\logic\panel;

use d3yii2\d3printer\components\D3Printer;
use d3yii2\d3printer\components\Printer;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;

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
    protected $deviceHealth;
    protected $configHealth;
    protected $displayData = [];
    
    public const DISPLAY_VERTICAL = 'vertical';
    public const DISPLAY_INLINE = 'inline';

    /**
     * @param string $printerName
     * @param string $healthPrinterName
     * @throws \yii\base\Exception `
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(string $printerName, string $healthPrinterName)
    {
        $this->printer = $this->getPrinter($printerName);
        /** @var D3Printer $d3printer */
        $d3printer = Yii::$app->get($healthPrinterName);
        $this->deviceHealth = $d3printer->deviceHealth();
        $this->configHealth = $d3printer->configHealth();
        $this->setDisplayData();
    }
    
    /**
     *
     */
    public function setDisplayData(): void
    {
        $this->displayData['printerName'] = $this->printer->printerName;
        $this->displayData['printerAccessUrl'] = $this->deviceHealth->getAccessUrl();
        $this->displayData['lastLoggedErrors'] = $this->deviceHealth->logger->getLastLoggedErrors();
        $this->setDisplayValue('printerCode', $this->printer->printerCode);
        $this->setDisplayValue('status', $this->getStatusDisplayValue());
        $this->setDisplayValue('cartridge', $this->getCartridgeDisplayValue());
        $this->setDisplayValue('drum', $this->getDrumDisplayValue());
        $this->setDisplayValue('deviceErrors', $this->deviceHealth->logger->getErrors());
        $this->setDisplayValue('ftpState', $this->getFTPStatusDisplayValue());
        $this->setDisplayValue('spool', $this->getSpoolerFilesCount());
    }
    
    /**
     * @param string $componentKey
     * @return \d3yii2\d3printer\components\Printer
     * @throws \yii\base\Exception
     */
    public function getPrinter(string $componentKey): Printer
    {
        if (!isset(Yii::$app->{$componentKey})) {
            throw new Exception('Missing Printer config for: ' . $componentKey);
        }
        
        return Yii::$app->{$componentKey};
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
            ? Html::tag('span', $this->deviceHealth->device->getCartridgeDisplayedValue(),  ['style' => 'color:darkgreen'])
            : Html::tag('span', $this->deviceHealth->device->getCartridgeDisplayedValue(),  ['style' => 'color:red']);
    }
    
    /**
     * @return string
     */
    protected function getDrumDisplayValue(): string
    {
        $isOk = $this->deviceHealth->drumOk();
        
        return $isOk
            ? Html::tag('span', $this->deviceHealth->device->getDrumDisplayedValue(),  ['style' => 'color:darkgreen'])
            : Html::tag('span', $this->deviceHealth->device->getDrumDisplayedValue(),  ['style' => 'color:red']);
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    protected function getFTPStatusDisplayValue(): string
    {
        $isOk = $this->printer->existDeadFile();
        
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
