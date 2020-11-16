<?php

use d3system\yii2\web\D3SystemView;
use d3yii2\d3printer\logic\health\DeviceHealth;
use d3yii2\d3printer\logic\read\ReadConfiguration;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\components\FlashHelper;
use eaBlankonThema\widget\ThAlertList;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThReturnButton;
use yii\helpers\Url;

LayoutAsset::register($this);

/**
 * @var D3SystemView $this
 * @var d3yii2\d3printer\models\AlertSettings $model
 * @var DeviceHealth $deviceHealth
 * @var ReadConfiguration $configHealth
 * @var bool $statusOk
 * @var string $status
 * @var bool $cartridgeOk
 * @var string $cartridge
 * @var bool $drumOk
 * @var string $drum
 * @var array $lastLoggedErrors;
 */


$this->title = 'DeviceHealth info';

$this->setPageHeader($this->title);
$this->setPageIcon('');
$this->addPageButtons(ThReturnButton::widget([
    'backUrl' => ['index'],
]));

if ($configHealth) {
    $configAttributeLabels = $configHealth->device->attributeLabels();
}
?>
<div class="row">
    <?= ThAlertList::widget() ?>
    <div class="col-md-9">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4><?= $deviceHealth->printerName ?? '' ?></h4>
                <?php if ($deviceHealth): ?>
                    Status: <?= $statusOk
                        ? '<span style="color:darkgreen">' . $status . '</span>'
                        : '<span style="color:red">' . $status . '</span>'
                    ?><br>
                    Cartridge: <?= $cartridgeOk
                    ? '<span style="color:darkgreen">' . $cartridge . '%</span>'
                    : '<span style="color:red">' . $cartridge . '%</span>'
                    ?><br>
                    Drum: <?= $drumOk
                        ? '<span style="color:darkgreen">' . $drum . '%</span>'
                        : '<span style="color:red">' . $drum . '%</span>'
                    ?>
                <?php endif; ?>
                <hr>
                <?= ThButton::widget([
                    'type' => ThButton::TYPE_PRIMARY,
                    'label' => 'Set Printer defaults',
                    'link' => Url::to(['/d3printer/set-printer-defaults', 'component' => Yii::$app->request->get('component')])
                ]) ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Print Settings</h4>
                <?php
                if ($configHealth) {
                    foreach ($configHealth->device->printSettings() as $key => $value) {
                        echo $configAttributeLabels[$key] . ':  ' . $value . '<br>';
                    }
                } ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Paper Settings</h4>
                <?php
                if ($configHealth) {
                    foreach ($configHealth->device->paperSettings() as $key => $value) {
                        echo $configAttributeLabels[$key] . ':  ' . $value . '<br>';
                    }
                } ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Energy Settings</h4>
                <?php
                if ($configHealth) {
                    foreach ($configHealth->device->energySettings() as $key => $value) {
                        echo $configAttributeLabels[$key] . ':  ' . $value . '<br>';
                    }
                } ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Last Errors</h4>
                <?php
                foreach ($lastLoggedErrors as $error) {
                    echo str_replace(PHP_EOL, '<br>', $error) . '<hr>';
                } ?>
            </div>
        </div>
    </div>
</div>
