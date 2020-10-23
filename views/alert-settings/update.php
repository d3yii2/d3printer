<?php

use d3system\yii2\web\D3SystemView;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThReturnButton;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;

LayoutAsset::register($this);

/**
 * @var D3SystemView $this
 * @var d3yii2\d3printer\models\AlertSettings $model
 */


$this->title = 'Alert settings';

$this->setPageHeader($this->title);
$this->setPageHeaderDescription('');
$this->setPageIcon('');
$this->addPageButtons(ThReturnButton::widget([
    'backUrl' => ['index'],
]));
?>
<div class="row">
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
                    <?= $form->field($model, 'cartridge_remain_min')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'drum_remain_min')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'email_from')->textInput(['maxlength' => true]) ?>
                   
                   <?php $model->email_to = explode('|', $model->email_to) ?>
                    <?= $form->field($model, 'email_to')->widget(Select2::class, [
                        //'data' => $data,
                        'options' => ['multiple' => true],
                        'pluginOptions' => [
                            'tags' => true,
                            'tokenSeparators' => [',', ' '],
                            'minimumInputLength' => 3
                        ],
                    ]); ?>
                    <?= $form->field($model, 'email_subject')->textInput(['maxlength' => true]) ?>
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
