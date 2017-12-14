<?php

namespace app\modules\crawler\models;

use Yii;
use Exception;
use DateTime;


use yii\db\Expression;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;

use linslin\yii2\curl\Curl;


/**
 * This is the model class for table "Task".
 *
 * @property integer $id
 * @property string $url
 * @property string $type
 * @property resource $data
 * @property string $priority
 * @property integer $host_id
 * @property integer $prioritized_task_id
 * @property string $created
 * @property string $imported
 * @property string $failed
 * @property integer $failed_count
 * @property string $file
 * @property string $failed_import
 * @property integer $failed_import_count
 *
 * @property Host $host
 * @property TaskMeta[] $taskMetas
 * @property Meta[] $metas
 */
class Task extends \yii\db\ActiveRecord
{

    # max attempts to find a unused file name for the downloaded file
    const MAX_FILE_LOOP = 100;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'CrawlerTask';
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
            [['url', 'type'], 'required'],
            [['type', 'data', 'priority'], 'string'],
            [['host_id', 'prioritized_task_id', 'failed_count', 'failed_import_count'], 'integer', 'min' => 0],
            #[['created', 'imported', 'failed', 'failed_import', 'locked', 'downloaded'], 'datetime'],
            [['created', 'imported', 'failed', 'failed_import', 'locked', 'downloaded'], 'safe'],
            
            [['url', 'file'], 'string', 'max' => 255],
            
            #[['url'], 'unique'],

            [['url'], 'url'],
            [['type'], 'in', 'range' => array_keys($this->getTypes())],
            [['priority'], 'in', 'range' => array_keys($this->getPriorities())],
            #[['priority'], 'string', 'max' => 255],
            
            [['host_id'], 'exist', 'skipOnError' => true, 'targetClass' => Host::className(), 'targetAttribute' => ['host_id' => 'id']],
            [['prioritized_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['priotized_task_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'type' => Yii::t('app', 'Type'),
            'data' => Yii::t('app', 'Data'),
            'priority' => Yii::t('app', 'Priority'),
            'host_id' => Yii::t('app', 'Host'),
            'created' => Yii::t('app', 'Created'),
            'imported' => Yii::t('app', 'Imported'),
            'locked' => Yii::t('app', 'Locked'),
            'downloaded' => Yii::t('app', 'Downloaded'),
            'failed' => Yii::t('app', 'Failed'),
            'failed_count' => Yii::t('app', 'Failed Count'),
            'file' => Yii::t('app', 'File'),
            'failed_import' => Yii::t('app', 'Failed Import'),
            'failed_import_count' => Yii::t('app', 'Failed Import Count'),
        ];
    }

    public function getPriorities() {
        return self::getAllPriorities();
    }

    public function getCombinedFailed() {
        if($this->failed) {
            if($this->failed_import) {
                return $this->failed > $this->failed_import ? $this->failed : $this->failed_import;
            }

            return $this->failed;
        }else if($this->failed_import) {
            return $this->failed_import;
        }
        
        return null;
    }

    static public function getAllPriorities() {
        $prios = Yii::$app->getModule('crawler')->priorities;

        $prios = array_map(function($value, $key) {
            return $value + ['id' => $key];
        }, $prios, array_keys($prios));

        return ArrayHelper::map($prios, 'id', 'label');
    }

    public function getTypes() {
        return [
            'get' => Yii::t('app', 'Get'),
            'post' => Yii::t('app', 'post'),
        ];
    }

    public function beforeSave($insert) {
        if(!($host = @parse_url($this->url, PHP_URL_HOST))) {
            throw new Exception('Could not parse URL');
        }

        $this->host_id = Host::register($host)->id;

        return parent::beforeSave($insert);
    }


    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        }

        # delete file if there is any
        if($this->file) {
            $dir = Yii::getAlias(Yii::$app->getModule('crawler')->filesDir);
            @unlink($dir . $this->file);
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHost()
    {
        return $this->hasOne(Host::className(), ['id' => 'host_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrioritizedTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'prioritized_task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskMetas()
    {
        return $this->hasMany(Task_Meta::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetas()
    {
        return $this->hasMany(Meta::className(), ['id' => 'meta_id'])
            ->viaTable('CrawlerTask_Meta', ['task_id' => 'id']);
    }

    public function deleteMetaValue($name) {
        if($obj = $this->getMetaRecord($name)) {
            $q = $this->getTaskMetas()->andWhere(['=', 'meta_id', $obj->id]);
            foreach($q->each() as $model) {
                $model->delete();
            }
        }
    }

    public function getMetaValue($name) {
        return ($obj = $this->getMetaRecord($name)) ? $obj->value : null;
    }

    public function setMetaValue($name, $value = null) {
        $meta = Meta::find()
            ->andWhere(['LIKE', 'name', $name])
            ->andWhere(is_null($value) ? ['IS', 'value', $value] : ['=', 'value', $value])
            ->one()
        ;

        if(!$meta) {
            # doesnt exist yet
            $meta = new Meta([
                'name' => $name,
                'value' => $value,
            ]);
    
            if(!$meta->save()) {
                throw new Exception('Cannot save Meta');
            }
        }

        if(($old = $this->getMetaRecord($name)) && $old->id != $meta->id) {
            $old->delete();
        }

        if(!$this->hasMetaRecord($meta)) {
            $this->link('metas', $meta);
        }



        return false;
    }

    public function hasMetaRecord($name) {
        return $this->getMetaRecord($name) ? true : false;
    }

    public function getMetaRecord($name) {
        if($name instanceof Meta) {
            return $this->getTaskMetas()->andWhere(['=', 'meta_id', $name->id])->one();
        }else if(is_string($name)) {
            return $this->getMetas()->andWhere(['LIKE', 'name', $name])->one();
        }

        return null;
    }

    public function failedFetching() {
        $config = $this->getConfig($this->priority);
        if($config['max_fetches'] <= $this->failed_count) {
            return true;
        }

        return false;
    }


    public function failedImporting() {
        $config = $this->getConfig($this->priority);
        if($config['max_imports'] <= $this->failed_import_count) {
            return true;
        }

        return false;
    }

    public function lock() {
        $this->locked = new Expression('NOW()');
        if(!$this->save()) {
            throw new Exception('Cannot lock task');
        }
    }

    public function unlock() {
        $this->locked = null;
        if(!$this->save()) {
            throw new Exception('Cannot unlock task');
        }
    }

    public function process() {
        $ret = false;

        $module = Yii::$app->getModule('crawler');

        $this->lock();

        $transaction = $this->db->beginTransaction();
        try {
            /* save information in host about crawling */
            if($this->host->used()) {
                $type = $this->type;

                $curl = new Curl();
                $curl->setOption(CURLOPT_USERAGENT, $module->userAgent);
                $curl->setRequestBody($this->data);
                
                $response = $curl->$type($this->url);
                if(!$curl->errorCode) {
                    $dir = Yii::getAlias($module->filesDir);
                    FileHelper::createDirectory($dir);

                    # try to find a unused name
                    for($i = 0; $i < self::MAX_FILE_LOOP; $i++) {
                        $file = uniqid();
                        $filePath = $dir . $file;

                        if(!file_exists($filePath)) {
                            break;
                        }else if($i + 1 == self::MAX_FILE_LOOP) {
                            throw new Exception('Could not find a unused file name');
                        }
                    }

                    
                    if(false === @file_put_contents($filePath, $response, LOCK_EX)) {
                        throw new Exception('Cannot store downloaded file ' . ($dir . $file));
                    }

                    $this->downloaded = new Expression('NOW()');
                    $this->file = $file;
                    
                    if(!$this->save()) {
                        throw new Exception('Cannot update task');
                    }

                    $ret = true;
                }else {
                    #error

                    $this->failed = new Expression('NOW()');
                    $this->failed_count += 1;
                    
                    if(!$this->save()) {
                        throw new Exception('Cannot update task');
                    }
                }
            }
        }catch(Exception $e) {
            $transaction->rollback();
            $this->unlock();
            throw $e;
        }
        
        $transaction->commit();
        $this->unlock();


        return $ret;
    }



    /* get the next task from queue */
    public static function nextTask() {
        return self::prioritizedQuery()->one();
    }

    /* get module informationen for priority */
    public static function getConfig($priority) {
        $arr = [];
        $module = Yii::$app->getModule('crawler');

        if(isset($module->priorities[$priority])) {
            $arr = $module->priorities[$priority];
        }

        return array_merge($module->defaultPriority, $arr);
    }







    /* extra queries */
    
    public static function failedQuery() {
        $query = self::find()
            ->andWhere(['IS', 'locked', null])
            ->orderBy('failed_import_count DESC, failed DESC')
        ;

        $conditions = ['or'];
        foreach(array_keys(self::getAllPriorities()) as $prio) {
            $config = self::getConfig($prio);

            $conditions[] = [
                'and',
                ['=', 'priority', $prio],
                ['IS NOT', 'failed', null],
                ['>=', 'failed_count', $config['max_fetches']],
            ];


            $conditions[] = [
                'and',
                ['=', 'priority', $prio],
                ['IS NOT', 'failed_import', null],
                ['>=', 'failed_import_count', $config['max_imports']],
            ];
        }

        $query->andWhere($conditions);
        
        return $query;
    }

    
    public static function pendingQuery() {
        $query = self::find()
            ->andWhere(['IS', 'imported', null])
            ->andWhere(['IS', 'locked', null])
            ->andWhere(['IS NOT', 'file', null])
            ->andWhere(['NOT IN ', 'id', self::failedQuery()->select('id')])


            #order by oldest failed then by oldest task
            ->orderBy('downloaded DESC, created DESC')
        ;

        return $query;
    }


    public static function upcomingQuery() {
        return self::find()
            #join with host und prioritizedTask
            ->joinWith(['host as host', 'prioritizedTask as prioritizedTask'])


            #only entries without a file
            ->andWhere(['IS', Task::tableName() . '.file', null])
            #only unlocked
            ->andWhere(['IS', Task::tableName() . '.locked', null])

            #only those whose prioritized task is already completed (or havent one)
            #or the prioritized task has failed
            ->andWhere([
                'or',
                ['IS', Task::tableName() . '.prioritized_task_id', null],
                ['IS NOT', 'prioritizedTask.file', null],
                ['IN', self::tableName() . '.prioritized_task_id', self::failedQuery()->select('id')]
            ])

            # which have not comletely failed yet
            ->andWhere(['NOT IN ', self::tableName() . '.id', self::failedQuery()->select('id')])

            #order by oldest failed then by oldest creation
            ->orderBy(Task::tableName() . '.failed ASC, ' . Task::tableName() . '.created ASC')
        ;
    }

    public static function prioritizedQuery() {
        $query = self::upcomingQuery();

        $conditions = ['or'];
        foreach(array_keys(self::getAllPriorities()) as $prio) {
            $config = self::getConfig($prio);

            $conditions[] = [
                'and',
                ['=', 'priority', $prio],
                ['<', 'failed_count', $config['max_fetches']],
                [
                    'or',
                    ['<=', 'host.crawled', new Expression('NOW() - INTERVAL ' . $config['delay']. ' SECOND')],
                    ['IS', 'host.crawled', null]
                ]
            ];
        }

        $query->andWhere($conditions);
        return $query;
    }
}
