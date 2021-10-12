<?php

use d3system\yii2\web\D3SystemView;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThAlertList;
use eaBlankonThema\widget\ThTableSimple2;
use yii\helpers\Html;
use yii\widgets\DetailView;

LayoutAsset::register($this);

/**
 * @var D3SystemView $this
 * @var d3yii2\d3printer\models\AlertSettings $model
 * @var array $displayData;
 */

?>
<div class="panel rounded shadow col-sm-4 col-md-4  col-lg-3" style="margin-left: 4px">
    <div class="panel-heading text-left">
        <?= ThAlertList::widget() ?>
        <i class="fa fa-print"></i> <?= Html::a($displayData['printerName'] . ' <i class="fa fa-external-link align-middle"></i>', $displayData['printerAccessUrl']) ?>
    </div>
    <div class="panel-body rounded-bottom">
        <?= ThTableSimple2::widget($displayData['info']) ?>
    </div>
</div>
