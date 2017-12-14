<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tab;


echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'host',
        'created:datetime',
        'crawled:datetime',
        'crawled_count:integer',
        [
            'attribute' => 'tasks',
            'format' => 'integer',
            'value' => $model->getTasks()->count()
        ],
    ],
]) ?>