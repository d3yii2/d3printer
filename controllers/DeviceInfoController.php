<?php

namespace d3yii2\d3printer\controllers;

use d3system\actions\D3SettingAction;
use d3system\yii2\web\D3SystemView;
use d3yii2\d3printer\accessRights\D3PrinterFullUserRole;
use d3yii2\d3printer\components\D3Printer;
use d3yii2\d3printer\logic\health\ConfigurationHealth;
use d3yii2\d3printer\models\AlertSettings;
use ea\app\controllers\LayoutController;
use eaBlankonThema\components\FlashHelper;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Class DeviceInfoController
 * @package d3yii2\d3printer\controllers
 */
class DeviceInfoController extends LayoutController
{
    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;

    /**
     * specify route for identifying active menu item
     */
    public $menuRoute = 'd3printer/device-info/index';
    /**
     * @var D3Printer
     */
    private $printerComponent;


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
                            'index',
                            'alert-settings',
                            'set-default-settings'
                        ],
                        'roles' => [D3PrinterFullUserRole::NAME],
                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();
        if(!$component = Yii::$app->request->get('component')){
            return;
        }
        /** @var D3Printer $deviceHealth */
        if($this->printerComponent = Yii::$app->{$component}) {
            /** @var D3SystemView $view */
            $view = $this->getView();
            $view->setLeftMenu($this->printerComponent->leftMenu);
            $this->menuRoute = $this->printerComponent->leftMenuUrl;
        }
    }

    public function actions()
    {
        return [
            'alert-settings' => [
                'class' => D3SettingAction::class,
                'modelClass' => AlertSettings::class,
                'view' => 'alert_settings',
            ],
        ];
    }

    public function actionIndex(string $component): string
    {
        $deviceHealth = null;
        $printerCode = null;
        $configHealth = null;
        $statusOk = null;
        $status = null;
        $cartridgeOk = null;
        $cartridge = null;
        $drumOk = null;
        $drum = null;
        $lastLoggedErrors = [];

        try {
            /** @var D3Printer $deviceHealth */
            $deviceHealth = $this->printerComponent->deviceHealth();
            $printerCode = $this->printerComponent->printerCode;
            $statusOk = $deviceHealth->statusOk();
            $status = $deviceHealth->device->status();

            $cartridgeOk = $deviceHealth->cartridgeOk();
            $cartridge = $deviceHealth->device->cartridgeRemaining();

            $drumOk = $deviceHealth->drumOk();
            $drum = $deviceHealth->device->drumRemaining();

            /** @var ConfigurationHealth $configHealth */
            $configHealth = $this->printerComponent->configHealth();

            $deviceErrors = $deviceHealth->logger->getErrors();
            if (!empty($deviceErrors)) {
                foreach ($deviceErrors as $err) {
                    FlashHelper::addDanger($err);
                }
            }
            $lastLoggedErrors = $deviceHealth->logger->getLastLoggedErrors();
        } catch (Exception $e) {
            $health = false;
            FlashHelper::processException($e);
        }
        return $this->render(
            'index',
            compact(
                'deviceHealth',
                'configHealth',
                'statusOk',
                'status',
                'cartridgeOk',
                'cartridge',
                'drumOk',
                'drum',
                'lastLoggedErrors',
                'printerCode',
                'component'
            ));
    }

    /**
     * Update the printer ConfigurationHealth
     * @param string $component
     * @return Response
     * @throws GuzzleException
     */
    public function actionSetDefaultSettings(string $component): Response
    {
        try {
            // Get the live data from printer ConfigurationHealth page
            /** @var ConfigurationHealth $configHealth */
            $configHealth = $this->printerComponent->configHealth();

            $configHealth->updatePaperConfig();
            $configHealth->updatePrintConfig();
            $configHealth->updateEnergyConfig();

            $alertErrorContent = '';
            $alertInfoContent = $configHealth->logger->getInfoMessages();

            if ($configHealth->logger->hasErrors()) {
                $alertErrorContent .= PHP_EOL . 'Update errors:' . PHP_EOL . $configHealth->logger->getErrorMessages();
            }

            $alertMsg = $alertInfoContent . PHP_EOL . $alertErrorContent;

            $configHealth->logger->logInfo($alertInfoContent);

            if ($configHealth->logger->hasErrors() || $configHealth->logger->hasErrors()) {
                FlashHelper::addDanger('Errors occured: ' . $alertErrorContent);
                $configHealth->logger->logErrors($alertErrorContent);
                $configHealth->logger->sendToEmail($alertMsg);
            } else {
                FlashHelper::addSuccess('Printer ConfigurationHealth updated');
            }

            return $this->redirect(['/d3printer/device-info', 'component' => $component]);

        } catch (Exception $e) {
            FlashHelper::addDanger('Errors occured: ' . $e->getMessage());
            Yii::error($e->getMessage(), 'd3printer-error');
            Yii::error($e->getTraceAsString(), 'd3printer-error');
            return $this->redirect(['index']);
        }
    }

}
