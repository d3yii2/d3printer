<?php


namespace d3yii2\d3printer\controllers;

use d3yii2\d3printer\accessRights\D3PrinterViewPanelUserRole;
use d3yii2\d3printer\logic\panel\DisplayDataLogic;
use eaBlankonThema\components\FlashHelper;
use Exception;
use unyii2\yii2panel\Controller;
use yii\filters\AccessControl;

class InfoPanelController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'status',
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
}
