<?php

return [
	#you can define an own name for your crawler but this makes it easy to detect if your crawler accesses a page
	'userAgent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',

	#schedule sleeper
	'sleep' => defined('YII_ENV') && YII_ENV == 'dev' ? 10 : 1, #10 sec (in production 1 sec) sleep time if no work
	
	#which log file
	#'logFile' => '@app/runtime/logs/crawler.log',
	# which log types
	#'logType' => ['error', 'redirect', 'warning'],

	# file destination
	#'filesDir' => '@app/runtime/crawler/',

	
	'defaultPriority' => [
		#default settings accross all priorities

		'delay' => 60, # seconds - task will wait at least given seconds for the same host,
        'max_fetches' => 3, # int+ - attempts for downloading file
        'max_imports' => 3, # int+ - attempts for external thread to import task
        'clean_after' => 60 * 60 * 24 * 360, # seconds - seconds for a delayed deletion after failed download or imports
        'unlock_after' => 60 * 60 * 24, # seconds - seconds for a delayed unlock
	],

	#settings by priority
	'priorities' => [
    	'urgent' => [
    		'label' => 'Urgent',
    		'delay' => 30,

    		'max_fetches' => 1,
    		'max_imports' => 1,

    		'unlock_after' => 1,
    	],

    	'important' => [
    		'label' => 'Important',
    		'delay' => 60 * 1, # 1min
    		'max_fetches' => 2,
    		'max_imports' => 1,
    	],

    	'normal' => [
    		'label' => 'Normal',
    		'delay' => 60 * 10, # 10min
    		'max_fetches' => 3,
    		'max_imports' => 1,
    	],

    	'unimportant' => [
    		'label' => 'Unimportant',
    		'delay' => 60 * 60, # 60min
    		'max_fetches' => 10,
    		'max_imports' => 1,
    	],
	],
];