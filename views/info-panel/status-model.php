<?php

use d3yii2\d3printer\models\Panel;
use eaBlankonThema\widget\ThDetailView;
use yii\helpers\Html;

/**
 * @var Panel $panelModel ;
 */


?>
<div class="panel rounded">
    <div class="panel-heading text-center">
        <i class="fa fa-print"></i>
        <?= $panelModel->printerAccessUrl
                ? Html::a(
                    $panelModel->printerName . ' <i class="fa fa-external-link align-middle"></i>',
                    $panelModel->printerAccessUrl
                )
                : $panelModel->printerName
        ?>
    </div>
    <div class="panel-body rounded-bottom">
        <?= ThDetailView::widget([
                'model' => $panelModel,
                'attributes' => $panelModel->displayAttributes(),
        ]) ?>
    </div>
</div>
