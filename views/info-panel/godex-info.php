<?php

use d3yii2\d3printer\components\PrinterStatus;
use d3yii2\d3printer\components\ZebraPrinter;
use eaBlankonThema\widget\ThDetailView;
use eaBlankonThema\widget\ThExternalLink;
use eaBlankonThema\widget\ThPanel;
use yii\base\Model;

/**
 * @var ZebraPrinter $printer ;
 * @var PrinterStatus $printerStatus ;
 */

/** === TITLE ==== */
$title = [
    $printer->printerCode,
    $printer->printerIp
];

$panelTitle = ThExternalLink::widget([
    'text' => implode(' ', $title),
    'url' => 'http://' . $printer->printerIp,
]);

/** === BODY ==== */
$countSpoolerFiles = count($printer->spoolDirectoryFiles);
$body = ThDetailView::widget([
    'model' => new Model(),
    'attributes' => [
        [
            'label' => Yii::t('d3printer', 'Spooled files'),
            'value' => $countSpoolerFiles
        ],
        [
            'label' => Yii::t('d3printer', 'Status'),
            'value' => $printerStatus->getActualStatusReport() . ' (' . $printerStatus->getStatusTime() . ')',
        ]
    ]
]);

/** === SHOW PANEL ==== */
echo ThPanel::widget([
    'type' => $countSpoolerFiles || !$printerStatus->isReady()
        ? ThPanel::TYPE_DANGER
        : ThPanel::TYPE_DEFAULT,
    'header' => $panelTitle,
    'leftIcon' => 'print',
    'body' => $body,
]);
