<?php

use d3system\yii2\web\D3SystemView;
use d3yii2\d3printer\logic\D3PrinterDeviceHealth;
use d3yii2\d3printer\logic\read\D3PrinterReadConfiguration;
use d3yii2\d3printer\logic\read\D3PrinterReadDevice;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThAlertList;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThReturnButton;
use yii\helpers\Html;
use yii\helpers\Url;

LayoutAsset::register($this);

/**
 * @var D3SystemView $this
 * @var d3yii2\d3printer\models\AlertSettings $model
 * @var D3PrinterReadDevice $device
 * @var D3PrinterReadConfiguration $deviceConfig
 */


$this->title = 'Device info';

$this->setPageHeader($this->title);
$this->setPageIcon('');
$this->addPageButtons(ThReturnButton::widget([
    'backUrl' => ['index'],
]));

if ($device) {
    $status = $device->getStatus();
    $statusOk = in_array($status, [D3PrinterReadDevice::STATUS_READY, D3PrinterReadDevice::STATUS_PRINTING, D3PrinterReadDevice::STATUS_SLEEP]);
    $cartridge = $device->getCartridgeRemaining();
    $drum = $device->getDrumRemaining();
}

if ($deviceConfig) {
    $configAttributeLabels = $deviceConfig->attributeLabels();
}
?>
<div class="row">
    <?= ThAlertList::widget() ?>
    <div class="col-md-9">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Device</h4>
                <?php if ($device): ?>
                    Status: <?= $statusOk
                        ? '<span style="color:darkgreen">' . $status . '</span>'
                        : '<span style="color:red">' . $status . '</span>'
                    ?><br>
                    Cartridge: <?= $cartridge ?>%<br>
                    Drum: <?= $drum ?>%
                <?php endif; ?>
                <hr>
                <?= ThButton::widget([
                    'type' => ThButton::TYPE_PRIMARY,
                    'label' => 'Set Printer defaults',
                    'link' => Url::to(['/d3printer/set-printer-defaults'])
                ]) ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Print Settings</h4>
                <?php
                if ($deviceConfig) {
                    foreach ($deviceConfig->getPrintSettings() as $key => $value) {
                        echo $configAttributeLabels[$key] . ':  ' . $value . '<br>';
                    }
                } ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Paper Settings</h4>
                <?php
                if ($deviceConfig) {
                    foreach ($deviceConfig->getPaperSettings() as $key => $value) {
                        echo $configAttributeLabels[$key] . ':  ' . $value . '<br>';
                    }
                } ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Energy Settings</h4>
                <?php
                if ($deviceConfig) {
                    foreach ($deviceConfig->getEnergySettings() as $key => $value) {
                        echo $configAttributeLabels[$key] . ':  ' . $value . '<br>';
                    }
                } ?>
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <h4>Last Errors</h4>
                <?php
                foreach (D3PrinterDeviceHealth::getLastLoggedErrors() as $error) {
                    echo str_replace(PHP_EOL, '<br>', $error) . '<hr>';
                } ?>
            </div>
        </div>
    </div>
</div>
