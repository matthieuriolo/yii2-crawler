<?php

namespace app\modules\crawler\helpers;

use Yii;
use yii\db\Expression;
use app\modules\crawler\models\Task;

class TaskHelper {
	public static function cleanUpUnlock() {
        # unlock all old lockings
        $conditions = ['or'];
        foreach(array_keys(Task::getAllPriorities()) as $prio) {
            $config = Task::getConfig($prio);

            $conditions[] = [
                'and',
                ['=', 'priority', $prio],
                ['<=', 'locked', new Expression('NOW() - INTERVAL ' . $config['unlock_after'] . ' SECOND')],
            ];
        }

        $query = Task::find()
            ->andWhere(['IS NOT', 'locked', null])
            ->andWhere($conditions)
        ;

        foreach($query->each() as $task) {
            $task->locked = null;
            $task->save();
        }
    }
    
    public static function cleanUpFailed() {
        # remove all failed tasks
        foreach(Task::failedQuery()->each() as $task) {
            $task->delete();
        }
    }
    
    public static function cleanup() {        
        self::cleanUpUnlock();
        self::cleanUpFailed();
    }

}