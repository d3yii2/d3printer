<?php

namespace d3yii2\d3printer\logic\panel;

use d3yii2\d3printer\components\D3Printer;
use d3yii2\d3printer\logic\tasks\FtpPingTask;
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
    protected $printer;
    protected $deviceHealth;
    protected $configHealth;
    protected $displayData = [];
    
    public const DISPLAY_VERTICAL = 'vertical';
    public const DISPLAY_INLINE = 'inline';
    
    /**
     * @param string $printerName
     * @throws \yii\base\Exception
`     */
    public function __construct(string $printerName)
    {
        $this->printer = $this->getPrinter($printerName);
        $this->deviceHealth = $this->printer->deviceHealth();
        $this->configHealth = $this->printer->configHealth();
        $this->setDisplayData();
    }
    
    /**
     *
     */
    public function setDisplayData(): void
    {
        $this->displayData['printerName'] = $this->printer->printerName;
        $this->displayData['printerAccessUrl'] = $this->printer->deviceHealth()->getAccessUrl();
        $this->displayData['lastLoggedErrors'] = $this->deviceHealth->logger->getLastLoggedErrors();
        $this->setDisplayValue('printerCode', $this->printer->printerCode);
        $this->setDisplayValue('status', $this->getStatusDisplayValue());
        $this->setDisplayValue('cartridge', $this->getCartridgeDisplayValue());
        $this->setDisplayValue('drum', $this->getDrumDisplayValue());
        $this->setDisplayValue('deviceErrors', $this->deviceHealth->logger->getErrors());
        $this->setDisplayValue('ftpState', $this->getFTPStatusDisplayValue());
    }
    
    /**
     * @param string $componentKey
     * @return \d3yii2\d3printer\components\D3Printer
     * @throws \yii\base\Exception
     */
    public function getPrinter(string $componentKey): D3Printer
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
     */
    protected function getFTPStatusDisplayValue(): string
    {
        $isOk = !FtpPingTask::hasDeadlockFile($this->printer->printerCode);
        
        return $isOk
            ? Html::tag('span', 'OK',  ['style' => 'color:darkgreen'])
            : Html::tag('span', Yii::t('d3printer', 'Down'),  ['style' => 'color:red']);
    }
    
    /**
     * Return data for ThTableSimple widget
     * @return array
     */
    public function getTableDisplayData($direction = self::DISPLAY_VERTICAL): array
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
