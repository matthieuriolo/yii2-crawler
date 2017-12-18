<?php

namespace app\modules\crawler;


use Yii;
use yii\db\Connection;
use yii\di\Instance;

class Module extends \yii\base\Module {
	public $db = 'db';
	# you can define an own name for your crawler but this makes it easy to detect if your crawler accesses a page
	public $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
	
	# schedule sleeper
	public $sleep = null;
	
	# file destination
	public $filesDir = '@app/runtime/crawler/';

	# default settings accross all priorities
	public $defaultPriority = [
		'delay' => 60, # seconds - task will wait at least given seconds for the same host,
        'max_fetches' => 3, # int+ - attempts for downloading file
        'max_imports' => 3, # int+ - attempts for external thread to import task
        'clean_after' => 31104000, # seconds - seconds for a delayed deletion after failed download or imports
        'unlock_after' => 86400, # seconds - seconds for a delayed unlock
	];


	# common priorities
	public $priorities = [
    	'urgent' => [
    		'label' => 'Urgent',
    		'delay' => 1, # asap

    		'max_fetches' => 1,
    		'max_imports' => 1,

    		'unlock_after' => 3600,
    	],

    	'important' => [
    		'label' => 'Important',
    		'delay' => 60, # 1min
    	],

    	'normal' => [
    		'label' => 'Normal',
    		'delay' => 600, # 10min
    	],

    	'unimportant' => [
    		'label' => 'Unimportant',
    		'delay' => 64800, # 18 hours
    	],
	];

    public function init() {
        parent::init();

        $this->db = Instance::ensure($this->db, Connection::className());

		if(Yii::$app instanceof \yii\console\Application) {
			$this->controllerNamespace = 'app\modules\crawler\commands';
		}


        if(!is_int($this->sleep) || $this->sleep < 1) {
        	#10 sec (in production 1 sec) sleep time if no work
        	$this->sleep = defined('YII_ENV') && YII_ENV == 'dev' ? 10 : 1;
        }
    }
}