<?php


namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\accessRights\D3PrinterViewPanelUserRole;
use d3yii2\d3printer\components\ZebraPrinter;
use d3yii2\d3printer\logic\panel\DisplayDataLogic;
use d3yii2\d3printeripp\components\PrinterIPPComponent;
use d3yii2\d3printeripp\logic\BasePrinter;
use d3yii2\d3printeripp\logic\PrinterSupplies;
use d3yii2\d3printeripp\logic\PrinterSystem;
use d3yii2\d3printeripp\logic\ValueFormatter;
use eaBlankonThema\components\FlashHelper;
use Exception;
use unyii2\yii2panel\Controller;
use yii\filters\AccessControl;
use Yii;

class InfoPanelController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'status',
                            'ipp-status',
                            'zebra-info',
                            'godex-info',
                        ],
                        'roles' => [
                            D3PrinterViewPanelUserRole::NAME,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $printerComponent
     * @param string $healthComponent
     * @return string
     */
    public function actionStatus(string $printerComponent, string $healthComponent): string
    {
        try {
            $logic = new DisplayDataLogic($printerComponent, $healthComponent);

            $displayData = $logic->getTableDisplayData();

            return $this->render('status', ['displayData' => $displayData]);

        } catch (Exception $e) {
            FlashHelper::processException($e);
        }
        return '';
    }

    /**
     * @return string
     */
    public function actionIppStatus(string $printerSlug): string
    {
        try {
            /** @var PrinterIPPComponent $manager */
            $manager = Yii::$app->printerIPP;
            
            /** @var BasePrinter $printer */
            $printer = $manager->getPrinter($printerSlug);

            if (!$printer) {
                $response['status'] = 'error';
                $response['message'] = 'Printer not found';
                return $response;
            }

            $status = $printer->getFullStatus();

            $displayData = [
                'printerName' => $status['system']['name'] ?? '?',
                'printerAccessUrl' => $status['system']['deviceUri'] ?? '?',
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
                            'label' => Yii::t('d3printeripp', 'Status'),
                            'value' => isset($status['system'][PrinterSystem::STATUS_UP_DOWN])
                                ? ValueFormatter::coloredUpDownValue($status['system'][PrinterSystem::STATUS_UP_DOWN])
                                : '?',
                        ],
                        [
                            'label' => Yii::t('d3printeripp', 'Cartridge'),
                            'value' => isset($status['supplies'][PrinterSupplies::STATUS_MARKER_LEVEL])
                                ? ValueFormatter::coloredDangerLessValue(
                                    $status['supplies'][PrinterSupplies::STATUS_MARKER_LEVEL],
                                    50, //$status['supplies']['lowLevel']
                                ) . '%'
                                : '?',
                        ],
                        [
                            'label' => Yii::t('d3printeripp', 'IP'),
                            'value' => $status['system']['host'] ?? '?',
                        ],
                    ],
                ],
                //'deviceErrors' => $displayData['deviceErrors'],
                //'lastLoggedErrors' => []
            ];

            return $this->render('status', ['displayData' => $displayData]);

        } catch (Exception $e) {
            FlashHelper::processException($e);
        }
        return '';
    }

    public function actionZebraInfo(string $component): string
    {
        try {

            /** @var ZebraPrinter $arduino */
            $printer = clone Yii::$app->get($component);
            return $this->render('zebra-info',['printer' => $printer]);

        } catch (Exception $e) {
            FlashHelper::processException($e);
        }
        return '';
    }

    public function actionGodexInfo(string $component): string
    {
        try {

            /** @var ZebraPrinter $printer */
            $printer = Yii::$app->get($component);
            return $this->render(
                'godex-info',
                [
                    'printer' => $printer,
                    'printerStatus' => $printer->loadStatus(),
                ]
            );

        } catch (Exception $e) {
            FlashHelper::processException($e);
        }
        return '';
    }
}
