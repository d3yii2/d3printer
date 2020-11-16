<?php return [
    [
        'label' => Yii::t('d3printer', 'Printer status'),
        'type' => 'submenu',
        'url' => ['/d3printer/device-info/index', 'component' => 'bouncerPrinterHealth'],
    ],
    [
        'label' => Yii::t('d3printer', 'Alert Settings'),
        'type' => 'submenu',
        'url' => ['/d3printer/alert-settings/settings'],
    ],
];
