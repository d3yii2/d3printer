<?php

namespace d3yii2\d3printer\controllers;

use d3system\yii2\web\D3SystemView;
use d3yii2\d3printer\accessRights\D3PrinterFullUserRole;
use d3yii2\d3printer\logic\health\ConfigurationHealth;
use d3yii2\d3printer\logic\health\DeviceHealth;
use ea\app\config\LeftMenuDef;
use ea\app\controllers\LayoutController;
use eaBlankonThema\components\FlashHelper;
use Exception;
use Yii;
use yii\filters\AccessControl;

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
    public $menuRoute = false;
    
    
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
                        'actions' => ['index'],
                        'roles' => [D3PrinterFullUserRole::NAME],
                    ],
                ],
            ],
        ];
    }
    
    public function actionIndex(string $component)
    {
        $deviceHealth = null;
        $configHealth = null;
        $statusOk = null;
        $status = null;
        $cartridgeOk = null;
        $cartridge = null;
        $drumOk = null;
        $drum = null;
        $lastLoggedErrors = [];
        
        try {
            /** @var DeviceHealth $deviceHealth */
            $deviceHealth = Yii::$app->{$component}->deviceHealth();
            
            $statusOk = $deviceHealth->statusOk();
            $status = $deviceHealth->device->status();
            
            $cartridgeOk = $deviceHealth->cartridgeOk();
            $cartridge = $deviceHealth->device->cartridgeRemaining();

            $drumOk = $deviceHealth->drumOk();
            $drum = $deviceHealth->device->drumRemaining();
    
            /** @var ConfigurationHealth $configHealth */
            $configHealth = Yii::$app->{$component}->configHealth();
            
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
                'lastLoggedErrors'
            ));
    }
    
    public function init(): void
    {
        parent::init();
        if (class_exists('\ea\app\config\LeftMenuDef')) {
            /** @var D3SystemView $view */
            $view = $this->getView();
            $view->setLeftMenu(LeftMenuDef::PRINTER);
        }
    }
}
