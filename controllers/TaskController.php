<?php

namespace app\modules\crawler\controllers;

use Yii;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



use app\modules\crawler\models\Task;
use app\modules\crawler\models\Meta;
use app\modules\crawler\helpers\TaskHelper;
/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'execute' => ['POST'],
                    'cleanup' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCleanup() {
        TaskHelper::cleanup();
        return $this->redirect(['index']);
    }

    public function actionExecute($id) {
        $model = $this->findModel($id);

        if($model->locked) {
            Yii::$app->session->setFlash('error', 'Model is in usage');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if($model->file) {
            Yii::$app->session->setFlash('error', 'Task has been already executed');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(!$model->process()) {
            Yii::$app->session->setFlash('error', 'Task could not been executed');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex()
    {
        $providerAll = new ActiveDataProvider([
            'query' => Task::find(),
        ]);

        $providerUpcoming = new ActiveDataProvider([
            'query' => Task::upcomingQuery(),
        ]);

        $providerPending = new ActiveDataProvider([
            'query' => Task::pendingQuery(),
        ]);

        $providerFailed = new ActiveDataProvider([
            'query' => Task::failedQuery(),
        ]);


        return $this->render('index', [
            'providerAll' => $providerAll,
            'providerUpcoming' => $providerUpcoming,
            'providerPending' => $providerPending,
            'providerFailed' => $providerFailed,
        ]);
    }

    /**
     * Displays a single Task model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $model = $this->findModel($id);

        $providerMetas = new ActiveDataProvider([
            'query' => $model->getMetas(),
        ]);


        #$model->setMeta();

        $meta = new Meta();
        if($meta->load(Yii::$app->request->post()) && $meta->validate()) {
            $model->setMetaValue($meta->name, $meta->value);
        }

        return $this->render('view', [
            'model' => $model,
            'meta' => $meta,
            'providerMetas' => $providerMetas,
        ]);
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->locked) {
            Yii::$app->session->setFlash('error', 'Model is in usage');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {   
        $model = $this->findModel($id);

        if($model->locked) {
            Yii::$app->session->setFlash('error', 'Model is in usage');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
