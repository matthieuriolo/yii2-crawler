<?php

namespace app\modules\crawler\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "CrawlerHost".
 *
 * @property integer $id
 * @property string $created
 * @property string $crawled
 * @property string $host
 * @property integer $crawled_count
 *
 * @property Task[] $tasks
 */
class Host extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CrawlerHost';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created', 'crawled'], 'safe'],
            
            [['host'], 'required'],
            [['crawled_count'], 'integer', 'min' => 0],
            [['host'], 'string', 'max' => 255],
            [['host'], 'unique'],
        ];
    }



    public function getDb() {
        return Yii::$app->getModule('crawler')->db;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created' => Yii::t('app', 'Created'),
            'crawled' => Yii::t('app', 'Last crawled'),
            'host' => Yii::t('app', 'Host'),
            'crawled_count' => Yii::t('app', 'Count crawled'),
            'tasks.count' => Yii::t('app', 'Tasks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['host_id' => 'id']);
    }

    public function used() {
        $this->crawled = new Expression('NOW()');
        $this->crawled_count += 1;
        return $this->save();
    }

    public function getCountCrawler() {
        throw new Exception('Deprecated');
        return $this->getCrawlers()->count() ? : 0;
    }


    public static function register($host) {
        if($obj = self::find()->andWhere(['=', 'host', $host])->one()) {
            return $obj;
        }

        $obj = new self();
        $obj->host = $host;

        if(!$obj->save()) {
            throw new Exception('Cannot save CrawlerHost');
        }

        return $obj;
    }
}
