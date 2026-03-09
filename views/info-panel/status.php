<?php

use eaBlankonThema\widget\ThExternalLink;use eaBlankonThema\widget\ThPanel;

/**
 * @var string $header ;
 * @var string $errorMessage ;
 * @var array $displayData ;
 * @var string $printerUrl
 * @var bool $allPassed
 */
$body = '';
if ($errorMessage) {
    $body .= '<div class="alert alert-danger">' . $errorMessage . '</div>';
}
$body .= '
<div class="table-responsive">
    <table class="table">
        <tbody>';
foreach ($displayData as $item) {
    $class = '';
    $body .= '<tr>
        <td>' . $item['label'] . '</td>
        <td' . $class . '>' . $item['value'] . '</td>
        </tr>';
}

$body .= '</tbody></table></div>';

if (!$allPassed) {
    $type = ThPanel::TYPE_DANGER;
} else {
    $type = ThPanel::TYPE_SUCCESS;
}

echo ThPanel::widget([
    'type' => $type,
    'leftIcon' => 'fa fa-print',
    'isCollapsed' => $type === ThPanel::TYPE_SUCCESS,
    'showCollapseButton' => true,
    'header' => ThExternalLink::widget([
        'url' => $printerUrl,
        'text' => $header
    ]),
    'body' => $body,
]);
