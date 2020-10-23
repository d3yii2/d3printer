<?php return [
    [
        'label' => Yii::t('d3printer', 'Printer status'),
        'type' => 'submenu',
        'url' => ['/d3printer/device-info/index'],
    ],
    [
        'label' => Yii::t('d3printer', 'Access Settings'),
        'type' => 'submenu',
        'url' => ['/d3printer/printer-access-settings/settings'],
    ],
    [
        'label' => Yii::t('d3printer', 'Alert Settings'),
        'type' => 'submenu',
        'url' => ['/d3printer/alert-settings/settings'],
    ],
    [
        'label' => Yii::t('d3printer', 'Paper Settings'),
        'type' => 'submenu',
        'url' => ['/d3printer/printer-paper-settings/settings'],
    ],
    [
        'label' => Yii::t('d3printer', 'Print Settings'),
        'type' => 'submenu',
        'url' => ['/d3printer/printer-print-settings/settings'],
    ],
    [
        'label' => Yii::t('d3printer', 'Energy Settings'),
        'type' => 'submenu',
        'url' => ['/d3printer/printer-energy-settings/settings'],
    ],
];
