<?php

namespace app\modules\crawler;


use Yii;
use yii\db\Connection;
use yii\di\Instance;

class Module extends \yii\base\Module {
	public $db = 'db';
	public $userAgent;
	public $sleep;
	public $filesDir;
	public $defaultPriority;
	public $priorities;

    public function init() {
        parent::init();

        $this->db = Instance::ensure($this->db, Connection::className());

		if(Yii::$app instanceof \yii\console\Application) {
			$this->controllerNamespace = 'app\modules\crawler\commands';
		}

        Yii::configure($this, require __DIR__ . '/config.php');
    }
}