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
    
    /**
     * @var \d3yii2\d3printer\logic\health\DeviceHealth $deviceHealth
     */
    protected $deviceHealth;
    
    /**
     * @var \d3yii2\d3printer\logic\health\ConfigurationHealth $configHealth
     */
    protected $configHealth;

    protected $displayData = [];
    
    public const DISPLAY_VERTICAL = 'vertical';
    public const DISPLAY_INLINE = 'inline';
    
    protected $status;
    
    protected const STATUS_OK = 'status-ok';
    protected const STATUS_FAILED = 'status-failed';
    

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
        $this->displayData['printerAccessUrl'] = $this->deviceHealth->getAccessUrl();
        $this->displayData['lastLoggedErrors'] = $this->deviceHealth->logger->getLastLoggedErrors();
        $this->setDisplayValue('printerCode', $this->printer->printerCode);
        $this->setDisplayValue('status', $this->getStatusDisplayValue());
        $this->setDisplayValue('cartridge', $this->getCartridgeDisplayValue());
        $this->setDisplayValue('drum', $this->getDrumDisplayValue());
        $this->setDisplayValue('deviceErrors', $this->deviceHealth->logger->getErrors());
        $this->setDisplayValue('ftpState', $this->getFTPStatusDisplayValue());
        $this->setDisplayValue('spool', $this->getSpoolerFilesCountValue());
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
            if (empty($value) && '0' !== $value) {
                $value = Yii::t('d3printer', 'No Data');
            }
    
            $value = self::STATUS_OK === $this->status
                ? Html::tag('span', $value, ['class' => 'text-success'])
                : Html::tag('span', $value, ['class' => 'text-danger']);
        
    
            $this->displayData[$key] = trim($value);
        }
    }
    
    /**
     * @return string
     */
    protected function getStatusDisplayValue(): string
    {
        $isOk = $this->deviceHealth->statusOk();
        
        $this->status = $isOk ? self::STATUS_OK : self::STATUS_FAILED;
        
        $status = Yii::t('d3printer', $this->deviceHealth->device->status());
 
        return $status;
    }

    /**
     * @return string
     */
    protected function getCartridgeDisplayValue(): string
    {
        $isOk = $this->deviceHealth->cartridgeOk();
    
        $this->status = $isOk ? self::STATUS_OK : self::STATUS_FAILED;

        return $this->deviceHealth->device->cartridgeRemaining();
    }
    
    /**
     * @return string
     */
    protected function getDrumDisplayValue(): string
    {
        $isOk = $this->deviceHealth->drumOk();
    
        $this->status = $isOk ? self::STATUS_OK : self::STATUS_FAILED;

        return $this->deviceHealth->device->drumRemaining();
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    protected function getFTPStatusDisplayValue(): string
    {
        $isOk = !$this->printer->existDeadFile();
    
        $this->status = $isOk ? self::STATUS_OK : self::STATUS_FAILED;

        return $isOk
            ? 'OK'
            : Yii::t('d3printer', 'Down');
    }

    /**
     * @throws \yii\base\Exception
     */
    public function getSpoolerFilesCount(): int
    {
        return count($this->printer->getSpoolDirectoryFiles());
    }
    
    /**
     * @throws \yii\base\Exception
     */
    public function getSpoolerFilesCountValue(): string
    {
        $count = $this->getSpoolerFilesCount();
    
        $this->status = $count > 0 ? self::STATUS_FAILED : self::STATUS_OK;

        return $count;
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
                            'label' => Yii::t('d3printer','Cartridge / Drum'),
                            'value' => $displayData['cartridge'] . ' / ' . $displayData['drum'],
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
