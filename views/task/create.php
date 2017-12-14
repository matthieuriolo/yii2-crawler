<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Create Task');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crawler'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crawler-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
