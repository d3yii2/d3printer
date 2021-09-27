<?php

use d3system\yii2\web\D3SystemView;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThAlertList;
use eaBlankonThema\widget\ThTableSimple2;
use yii\widgets\DetailView;

LayoutAsset::register($this);

/**
 * @var D3SystemView $this
 * @var d3yii2\d3printer\models\AlertSettings $model
 * @var array $displayData;
 */

?>
<div class="row">
    <?= ThAlertList::widget() ?>
    <div class="col-md-9">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <?= DetailView::widget($displayData['info']) ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Last Errors</h4>
<!--                --><?//= ThTableSimple2::widget($displayData['lastLoggedErrors']) ?>
            </div>
        </div>
    </div>
</div>
