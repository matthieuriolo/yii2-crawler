<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;


$this->title = 'Host: ' . $model->host;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crawler'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Hosts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crawler-host-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php /*
    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    */ ?>

    <?= Tabs::widget([
        'items' => [
            [
                'label' => 'Overview',
                'content' => $this->render('_view', [
                    'model' => $model,
                ]),
            ],

            [
                'label' => 'Tasks',
                'content' => $this->render('/task/_index_all', [
                    'dataProvider' => $providerTasks,
                ]),
            ],
        ]           
    ]) ?>
</div>
