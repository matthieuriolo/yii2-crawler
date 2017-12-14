<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;


$this->title = 'Task: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crawler'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crawler-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        
        <?php if(is_null($model->file)) { ?>
        |
        <?= Html::a(Yii::t('app', 'Execute'), ['execute', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data' => [
                #'confirm' => Yii::t('app', 'Are you sure you want to down this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php } ?>
    </p>

    <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'Overview',
                    'content' => DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            
                            [
                                'format' => 'html',
                                'attribute' => 'domain_id',
                                'value' => function($model) {
                                    return Html::a($model->host->host, ['host/view', 'id' => $model->host->id]);
                                }
                            ],

                            'priority',
                            'type',
                            'url:url',
                            
                            'created:datetime',
                            'locked:datetime',
                            'downloaded:datetime',
                            'imported:datetime',
                            'failed:datetime',
                            'failed_count:integer',
                            'file',
                            'failed_import:datetime',
                            'failed_import_count:integer',

                            'data',
                        ],
                    ])
                ],

                [
                    'label' => 'Meta informations',
                    'content' => $this->render('_meta', [
                            'model' => $meta,
                            'dataProvider' => $providerMetas,
                        ])
                ]
            ]
        ]
    ) ?>
</div>
