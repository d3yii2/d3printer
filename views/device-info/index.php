<?php

use d3system\yii2\web\D3SystemView;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThReturnButton;
use d3yii2\d3printer\logic\read\D3PrinterReadDevice;

LayoutAsset::register($this);

/**
 * @var D3SystemView $this
 * @var d3yii2\d3printer\models\AlertSettings $model
 * @var \d3yii2\d3printer\logic\read\D3PrinterReadDevice $device
 */


$this->title = 'Device info';

$this->setPageHeader($this->title);
$this->setPageIcon('');
$this->addPageButtons(ThReturnButton::widget([
    'backUrl' => ['index'],
]));

$status = $device->getStatus();
$cartridge = $device->getCartridgeRemaining();
$drum = $device->getDrumRemaining();

?>
<div class="row">
    <div class="col-md-9">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                Status: <?= D3PrinterReadDevice::STATUS_READY === $status
                    ? '<span style="color:darkgreen">' . $status . '</span>'
                    : '<span style="color:red">' . $status . '</span>'
                ?><br>
                Cartridge: <?= $cartridge ?>%<br>
                Drum: <?= $drum ?>%
            </div>
        </div>
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                Last Alerts:
            </div>
        </div>
    </div>
</div>
