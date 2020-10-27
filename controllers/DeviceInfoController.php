<?php

namespace d3yii2\d3printer\controllers;

use d3system\yii2\web\D3SystemView;
use d3yii2\d3printer\logic\read\D3PrinterReadConfiguration;
use d3yii2\d3printer\logic\read\D3PrinterReadDevice;
use ea\app\config\LeftMenuDef;
use ea\app\controllers\LayoutController;
use eaBlankonThema\components\FlashHelper;
use Exception;
use yii\filters\AccessControl;


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
                        'actions' => [
                            'index',
                        ],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        try {
            $device = new D3PrinterReadDevice();
            $deviceConfig = new D3PrinterReadConfiguration();
        }catch (Exception $e){
            $device = false;
            $deviceConfig = false;
            FlashHelper::processException($e);
        }
        
        return $this->render('index', ['device' => $device, 'deviceConfig' => $deviceConfig]);
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
