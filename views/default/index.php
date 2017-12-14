<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'My Crawler';

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Crawler!</h1>

        <p class="lead">Reusable and independent crawler for yii</p>

        <?php /*
        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
        */ ?>
    </div>

    <div class="body-content">
        <div class="alert alert-warning">
            <h3>Todo</h3>
            <ul>
                <li>cleanup script for failed taks (download or import)</li>
            </ul>
        </div>


        <p>
            <?= Html::a(Yii::t('app', 'Cronjob'), ['/crawler/task/cleanup'], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to perfom the crawler cleanup cronjob?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <div class="row">
            <div class="col-lg-4">
                <h2>Next tasks</h2>

                <?= GridView::widget([
                    'dataProvider' => $providerNext,
                    'pager' => false,
                    'summary' => 'Total {totalCount}',
                    'columns' => [
                        'created:date',
                        [
                            'format' => 'html',
                            'attribute' => 'host_id',
                            'value' => function($model) {
                                return Html::a($model->host->host, ['/crawler/host/view', 'id' => $model->host->id]);
                            }
                        ],
                        'priority',
                        

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'controller' => 'crawler',
                        ],
                    ],
                ]); ?>

                <p><?= Html::a('Next tasks', ['task/index', 'tab' => 'upcoming'], ['class' => 'btn btn-default']); ?></p>
            </div>

            <div class="col-lg-4">
                <h2>Pending tasks</h2>

                <?= GridView::widget([
                    'dataProvider' => $providerPending,
                    'pager' => false,
                    'summary' => 'Total {totalCount}',
                    'columns' => [
                        'downloaded:date',
                        [
                            'format' => 'html',
                            'attribute' => 'host_id',
                            'value' => function($model) {
                                return Html::a($model->host->host, ['/crawler/host/view', 'id' => $model->host->id]);
                            }
                        ],

                        'priority',
                        
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'controller' => 'crawler',
                        ],
                    ],
                ]); ?>

                <p><?= Html::a('Pending tasks', ['task/index', 'tab' => 'pending'], ['class' => 'btn btn-default']); ?></p>
            </div>


            <div class="col-lg-4">
                <h2>Failed tasks</h2>

                <?= GridView::widget([
                    'dataProvider' => $providerFailed,
                    'pager' => false,
                    'summary' => 'Total {totalCount}',
                    'columns' => [
                        'combinedFailed:date',
                        [
                            'format' => 'html',
                            'attribute' => 'host_id',
                            'value' => function($model) {
                                return Html::a($model->host->host, ['/crawler/host/view', 'id' => $model->host->id]);
                            }
                        ],
                        
                        'priority',

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'controller' => 'crawler',
                        ],
                    ],
                ]); ?>
                <p><?= Html::a('Failed tasks', ['task/index', 'tab' => 'failed'], ['class' => 'btn btn-default']); ?></p>
            </div>
        </div>

    </div>
</div>
