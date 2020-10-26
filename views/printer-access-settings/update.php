<?php

use d3system\yii2\web\D3SystemView;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThAlertList;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThReturnButton;
use yii\bootstrap\ActiveForm;

LayoutAsset::register($this);

/**
 * @var D3SystemView $this
 * @var d3yii2\d3printer\models\PrinterAccessSettings $model
 */


$this->title = 'Update settings';

$this->setPageHeader($this->title);
$this->setPageHeaderDescription('');
$this->setPageIcon('');
$this->addPageButtons(ThReturnButton::widget([
    'backUrl' => ['index'],
]));
?>
<div class="row">
    <?= ThAlertList::widget()?>
    <div class="col-md-9">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">
                <div class="form-body">
                    <?php
                    $form = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'enableClientValidation' => true,
                        'errorSummaryCssClass' => 'error-summary alert alert-error',
                    ]);
                    ?>
                    <?= $form->field($model, 'home_url')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'device_info_url')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'paper_setup_url')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'print_setup_url')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'energy_setup_url')->textInput(['maxlength' => true]) ?>

                    <div class="form-footer">
                        <div class="pull-right">
                            <?= ThButton::widget([
                                'label' => 'Save',
                                'id' => 'save-' . $model->formName(),
                                'icon' => ThButton::ICON_CHECK,
                                'type' => ThButton::TYPE_SUCCESS,
                                'submit' => true,
                                'htmlOptions' => [
                                    'name' => 'action',
                                    'value' => 'save',
                                ],
                            ]) ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
