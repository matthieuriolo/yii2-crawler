<?php

namespace app\modules\crawler\models;

use Yii;
use Exception;


/**
 * This is the model class for table "CrawlerCategory".
 *
 * @property integer $id
 * @property string $name
 *
 * @property CrawlerCategory[] $crawlerCategories
 * @property Crawler[] $crawlers
 */
class Meta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CrawlerMeta';
    }



    static public function getDb() {
        return Yii::$app->getModule('crawler')->db;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['value'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 32],
            #[['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'value' => Yii::t('app', 'Value'),
            'task_Metas' => Yii::t('app', 'Usage'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask_Metas()
    {
        return $this->hasMany(Task_Meta::className(), ['meta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['id' => 'task_id'])
            ->viaTable('CrawlerTask_Meta', ['meta_id' => 'id']);
    }

    public function getCountCrawler() {
        throw new Exception('deprecated');
        return $this->getCrawlers()->count() ? : 0;
    }


    public static function register($name) {
        if($obj = self::find()->andWhere(['LIKE', 'name', $name])->one()) {
            return $obj;
        }

        $obj = new self();
        $obj->name = $name;

        if(!$obj->save()) {
            throw new Exception('Cannot save Crawler.Meta');
        }

        return $obj;
    }
}
