<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Crawler Hosts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crawler-host-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php /*
    <p>
        <?= Html::a(Yii::t('app', 'Create Crawler Domain'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    */ ?>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'host',
            'created:datetime',
            'crawled:datetime',
            'crawled_count:integer',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
