<?php

namespace app\modules\crawler\models;

use Yii;

/**
 * This is the model class for table "CrawlerTask_Meta".
 *
 * @property integer $id
 * @property integer $meta_id
 * @property integer $task_id
 *
 * @property Task $task
 * @property Meta $meta
 */
class Task_Meta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CrawlerTask_Meta';
    }


    public function getDb() {
        return Yii::$app->getModule('crawler')->db;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meta_id', 'task_id'], 'required'],
            [['meta_id', 'task_id'], 'integer'],
            [['meta_id', 'task_id'], 'unique', 'targetAttribute' => ['meta_id', 'task_id'], 'message' => 'The combination of Category ID and Crawler ID has already been taken.'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['meta_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meta::className(), 'targetAttribute' => ['meta_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'meta_id' => Yii::t('app', 'Meta ID'),
            'task_id' => Yii::t('app', 'Task ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeta()
    {
        return $this->hasOne(Meta::className(), ['id' => 'meta_id']);
    }
}
