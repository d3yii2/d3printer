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
];
