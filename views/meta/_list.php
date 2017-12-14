<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

?>


<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'created:datetime',
            'name',
            'value',
            [
                'attribute' => 'task_Metas',
                'format' => 'integer',
                'value' => function($model) {
                    return $model->getTask_Metas()->count();
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'controller' => 'meta',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
