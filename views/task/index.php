<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Tabs;

$tab = Yii::$app->request->get('tab');

$this->title = Yii::t('app', 'Tasks');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crawler'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crawler-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Task'), ['create'], ['class' => 'btn btn-success']) ?>
        |
        <?= Html::a(Yii::t('app', 'Cleanup'), ['cleanup'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to perfom the crawler cleanup cronjob?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
    <?= Tabs::widget([
        'items' => [
            [
                'label' => 'All',
                'active' => $tab == 'all' || is_null($tab),
                'content' => $this->render('_index_all', [
                    'dataProvider' => $providerAll,
                ]),
            ],

            [
                'label' => 'Upcoming',
                'active' => $tab == 'upcoming',
                'content' => $this->render('_index_upcoming', [
                    'dataProvider' => $providerUpcoming,
                ]),
            ],

            [
                'label' => 'Pending',
                'active' => $tab == 'pending',
                'content' => $this->render('_index_pending', [
                    'dataProvider' => $providerPending,
                ]),
            ],

            [
                'label' => 'Failed',
                'active' => $tab == 'failed',
                'content' => $this->render('_index_failed', [
                    'dataProvider' => $providerFailed,
                ]),
            ],
        ]
    ]); ?>
</div>
