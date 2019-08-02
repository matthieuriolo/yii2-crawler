<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Crawler */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crawler-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'priority')->dropDownList($model->priorities) ?>

    <?= $form->field($model, 'type')->dropDownList($model->types) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'timezone')->dropDownList(ArrayHelper::index(timezone_identifiers_list(), function($val) {
    	return $val;
    })); ?>

    <?= $form->field($model, 'data')->textarea(['class' => 'form-control max-width-100']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
