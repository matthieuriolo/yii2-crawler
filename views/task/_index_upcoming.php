<?php


use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Tabs;




Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'created:datetime',
            'priority',
            'type',
            'url:url',
            

            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'task',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>