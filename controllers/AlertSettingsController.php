<?php

namespace d3yii2\d3printer\controllers;

use d3system\actions\D3SettingAction;
use d3system\yii2\web\D3SystemView;
use d3yii2\d3printer\accessRights\D3PrinterFullUserRole;
use d3yii2\d3printer\models\AlertSettings;
use ea\app\config\LeftMenuDef;
use ea\app\controllers\LayoutController;
use yii\filters\AccessControl;


class AlertSettingsController extends LayoutController
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
                            'settings',
                        ],
                        'roles' => [D3PrinterFullUserRole::NAME],
                    ],
                ],
            ],
        ];
    }
    
    public function actions()
    {
        return [
            'settings' => [
                'class' => D3SettingAction::class,
                'modelClass' => AlertSettings::class,
                'view' => 'update',
            ],
        ];
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
