<?php


namespace app\modules\crawler\commands;

use Yii;

use yii\helpers\Console;
use yii\console\Controller;
use yii\console\widgets\Table;
use app\modules\crawler\models\Task;

class TaskController extends Controller {
	const STATUS_TABLE_LIMIT = 10;

    public function actionStatus() {
    	$f = Yii::$app->formatter;

    	echo "\n#\n", "## Next tasks\n",
    		Table::widget([
			    'headers' => [
			    	Yii::t('app', 'Id'), 
				    Yii::t('app', 'Created'), 
				    Yii::t('app', 'Priority'),
				    Yii::t('app', 'URL'), 
			   ],
			    'rows' => array_map(function($model) use($f) {
			    	return [
			    		$f->asInteger($model->id),
			    		$f->asDatetime($model->created),
			    		$f->asText($model->priority),
			    		$f->asText($model->url),
			    	];
			    }, Task::upcomingQuery()->limit(self::STATUS_TABLE_LIMIT)->all()),
			])
		;

		
    	echo "\n#\n", "## Next tasks\n",
    		Table::widget([
			    'headers' => ['Id', 'Download', 'URL', 'Priority'],
			    'rows' => array_map(function($model) use($f) {
			    	return [
			    		$f->asInteger($model->id),
			    		$f->asDatetime($model->downloaded),
			    		$f->asText($model->priority),
			    		$f->asText($model->url),
			    	];
			    }, Task::pendingQuery()->limit(self::STATUS_TABLE_LIMIT)->all()),
			])
		;

        echo "\n#\n", "## Next tasks\n",
        	Table::widget([
			    'headers' => ['Id', 'Failed', 'Priority', 'URL'],
			    'rows' => array_map(function($model) use($f) {
			    	return [
			    		$f->asInteger($model->id),
			    		$f->asDatetime($model->combinedFailed),
			    		$f->asText($model->priority),
			    		$f->asText($model->url),
			    	];
			    }, Task::pendingQuery()->limit(self::STATUS_TABLE_LIMIT)->all()),
			])
		;
    }

    public function actionView($id) {
    	$model = $this->findTask($id);
    	$labels = $model->attributeLabels();
    	$f = Yii::$app->formatter;



        echo "\n#\n", "## Task Detail View\n";
    	echo Table::widget([
			    'headers' => [
			    	Yii::t('app', 'Property'), 
				    Yii::t('app', 'Value')
			   	],
			    
			    'rows' => [
			    	[
			    		$labels['id'],
			    		$f->asInteger($model->id),
			    	],
			    	
			    	[
			    		$labels['host_id'],
			    		$f->asText($model->host->host),
			    	],
					
					[
			    		$labels['priority'],
			    		$f->asText($model->priority),
			    	],

			    	[
			    		$labels['type'],
			    		$f->asText($model->type),
			    	],

			    	[
			    		$labels['url'],
			    		$f->asText($model->url),
			    	],
			    	
			    	[
			    		$labels['created'],
			    		$f->asDatetime($model->created),
			    	],

			    	

			    	[
			    		$labels['locked'],
			    		$f->asDatetime($model->locked),
			    	],

			    	[
			    		$labels['downloaded'],
			    		$f->asDatetime($model->downloaded),
			    	],

			    	[
			    		$labels['failed'],
			    		$f->asDatetime($model->failed),
			    	],

			    	[
			    		$labels['failed_count'],
			    		$f->asInteger($model->failed_count),
			    	],

			    	[
			    		$labels['file'],
			    		$f->asText($model->file),
			    	],

			    	[
			    		$labels['failed_import'],
			    		$f->asDatetime($model->failed_import),
			    	],

			    	[
			    		$labels['failed_import_count'],
			    		$f->asInteger($model->failed_import_count),
			    	],
			    ],
			])
		;
    }

    protected function findTask($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new Exception('A task with the given identifier does not exist.');
        }
    }

    public function actionProcess() {
    	echo "Sleep scheduler is set to " . Yii::$app->controller->module->sleep . "\n";
    	
        while(true) {
        	try {
	        	if($crawler = Task::nextTask()) {

	        		$msg = 'Processing task ' 
	        			. $crawler->id 
	        			. '/' . $crawler->priority 
	        			. '/' . date('Y-m-d H:i:s')
	        			. "\n" . $crawler->url
	        		;

	        		$this->stdout($msg . "\n");
	        		Yii::info($msg, 'crawler');

	        		# process crawler
	        		if($crawler->process()) {
	        			$msg = 'Successfully retrieved data from ' . $crawler->url;
		        		$this->stdout($msg . "\n", Console::FG_GREEN);
		        		Yii::info($msg, 'crawler');
	        		}else {
	        			# there was an error try to detect what it was
	        			if($crawler->failedFetching()) {
	        				$msg = 'Failed to retrieve data from ' . $crawler->url;
			        		$this->stderr($msg . "\n");
			        		Yii::error($msg, 'crawler');
	        			}else {
		        			$msg = 'Failed, retry later to retrieve data from ' . $crawler->url;
			        		$this->stdout($msg . "\n");
			        		Yii::warning($msg, 'crawler');
	        			}
	        		}
	        	}else if(Yii::$app->controller->module->sleep > 0) {
	        		# sleep
	        		#$this->stdout("No work\n");
	        		#Yii::warning('no work', 'crawler');
	        		sleep(Yii::$app->controller->module->sleep);
	        	}
	        }catch(Exception $e) {
	        	$this->stderr("Error\n", Console::FG_RED);
	        	Yii::error($e, 'crawler');
	        }
        }
    }
}
