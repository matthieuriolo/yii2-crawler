yii2-crawler
===============

Reusable multithreaded data crawler for the web based on the [yii2 framework](http://www.yiiframework.com)

# Yii2 Crawler
The crawler is based on the [FIFO](https://en.wikipedia.org/wiki/FIFO_(computing_and_electronics)) principle with a small tweak. A delay resulting into priorities. The tasks will be sorted by their creation time and then the task will be excluded depending when the last request to a host has been. This gives devs an easy way to control the amount of requests per host, however the order is not always garanteed since a task with a shorter delay time always will be performed before a task with a longer delay time (hence prioritized). If you need to ensure a order, have a look at the prioritized task field. Thanks to meta informations the crawler can store additional informations aswell. 

However the module does not provide any tools for analyzing the requested data and focuses on the job of requesting data in controlled manner.

## License
GPL-2

## Requirements

- Yii2 (2.0.13)
- [Curl](https://github.com/linslin/Yii2-Curl) 

## costumizable priorities

You can configure your own priorities by changing the initialization configuration of the crawler module

```php
'modules' => [
    'crawler' => [
        'class' => 'app\modules\crawler\Module',
        'priorities' => [
            'custom' => [
                'label' => 'My custom priority',
                'delay' => 30,

                'max_fetches' => 10,
                'max_imports' => 1,

                'unlock_after' => 60 * 60 * 24 * 7,
            ],
        ],
    ],
],
```

- label: Name of your priority
- delay: Delayed requests for the same host in seconds
- max_fetches: Maximal counter limit for retrieving data from an URL
- max_imports: How often the import counter can be increased until the crawler will recognize it as failed
- unlock_after: The cleanup script will only unlock tasks which have been longer locked than the given seconds

## Using the crawler across multiple applications
It is recommanded to install the crawler in an independent yii2 instance. There is a task command and a task controller for retrieving the status of the crawler. Use the ability of yii2 to set up a second database connection if you want to access the crawler data from an external application. Dont forget to correct to filesDir field too


```php
'components' => [
    'db' => ['default db settings'],
    'db_crawler' => ['additional db settings'],
],

'modules' => [
    'crawler' => [
        'class' => 'app\modules\crawler\Module',
        'db' => 'db_crawler',
        'filesDir' => '@app/../differentLocation/runtime/crawler'
    ]
]
```

## Accessing data

The crawler provides some default queries for accessing the data
```php
use app\modules\crawler\models\Task;

# next task in upcoming
$task = Task::nextTask();


#all upcoming tasks
$query = Task::upcomingQuery();
#all downloaded but unimported tasks
$query = Task::pendingQuery();
#all failed tasks
$query = Task::failedQuery();
```

## Meta informations

```php
$task = Task::findOne($id);

# get meta value
echo $task->getMetaValue('class');
# set meta value
$task->setMetaValue('class', CustomTaskInfo::className());
#delete meta value
$task->deleteMetaValue('typoClASssss');
```


## Logging

not really supported yet

## Prioritized Tasks
A task can point to a prioritized Task which must be executed or must have failed before the crawler starts to execute the main task. Keep in mind that deadlocks (circularity) are possible and must be avoided. If the order of the task are important, you can use this field to ensure an order.


## Multithread
Well, I lied. It's not really multithreaded. But the module can be installed in a own instance of yii2 and with the following command the task manager can be scheduled
```sh
./yii crawler/task/process
```
The process command will run for infinite amount of time. Every time the task manager is about to download a file, the task will be locked. This field can also be used for the external import thread to ensure that other threads do not corrupt the data pool.

This means the application itself is not multithreaded but as long as you have a mysql backend you should be able to run the same application (import/export) multiple times with the same data
