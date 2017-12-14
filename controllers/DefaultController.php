<?php

namespace app\modules\crawler\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;


use app\modules\crawler\models\Task;

class DefaultController extends Controller
{
    
    public function actionIndex()
    {

        $providerNext = new ActiveDataProvider([
            'query' => Task::upcomingQuery(),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $providerPending = new ActiveDataProvider([
            'query' => Task::pendingQuery(),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $providerFailed = new ActiveDataProvider([
            'query' => Task::failedQuery(),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);


        return $this->render('index', [
            'providerNext' => $providerNext,
            'providerPending' => $providerPending,
            'providerFailed' => $providerFailed,

        ]);
    }
}
