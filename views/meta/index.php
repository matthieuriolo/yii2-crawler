<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;


$this->title = Yii::t('app', 'Meta Informations');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crawler'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="crawler-meta-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_list', [
        'dataProvider' => $dataProvider,
    ]); ?>
</div>
