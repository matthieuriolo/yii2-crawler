<?php

use yii\db\Migration;

class m171213_203354_init extends Migration
{
    public function up()
    {
      /* table CrawlerHost */
      $this->createTable('{{%CrawlerHost}}', [
        'id' => $this->primaryKey()->unsigned(),
        'created' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        'host' => $this->string(255)->notNull()->unique(),
        'crawled' => $this->dateTime(),
        'crawled_count' => $this->integer()->notNull()->unsigned()->defaultValue(0),
      ], $tableOptions);

      $this->createIndex(
        'idx_created',
        'CrawlerHost',
        'created'
      );

      $this->createIndex(
        'idx_updated',
        'CrawlerHost',
        'crawled'
      );

      /* table CrawlerTask */
      $this->createTable('{{%CrawlerTask}}', [
        'id' => $this->primaryKey()->unsigned(),
        'created' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        'priority' => $this->string(12)->notNull(),

        #this is supposed to be a enum and not a varchar(4)
        'type' => $this->string(4)->notNull(),

        'url' => $this->string(255)->notNull(),
        'host_id' => $this->integer()->notNull()->unsigned(),
        #this is supposed to be a blob and not a text
        'data' => $this->text(),


        'file' => $this->string(16),
        'downloaded' => $this->dateTime(),
        'failed' => $this->dateTime(),
        'failed_count' => $this->integer()->notNull()->unsigned()->defaultValue(0),
        'imported' => $this->dateTime(),
        'failed_import' => $this->dateTime(),
        'failed_import_count' => $this->integer()->notNull()->unsigned()->defaultValue(0),
        'locked' => $this->dateTime(),
        'prioritized_task_id' => $this->integer()->unsigned(),
      ], $tableOptions);

      $this->addForeignKey(
        'fk_Crawler_CrawlerDomain1',
        'CrawlerTask',
        'host_id',
        'CrawlerHost',
        'id',
        'CASCADE',
        'CASCADE'
      );

      $this->addForeignKey(
        'fk_CrawlerTask_CrawlerTask1',
        'CrawlerTask',
        'prioritized_task_id',
        'CrawlerTask',
        'id',
        'SET NULL',
        'CASCADE'
      );

      $this->createIndex(
        'fk_Crawler_CrawlerDomain1_idx',
        'CrawlerTask',
        'host_id'
      );

      $this->createIndex(
        'idx_imported',
        'CrawlerTask',
        'imported'
      );

      $this->createIndex(
        'idx_created',
        'CrawlerTask',
        'created'
      );

      $this->createIndex(
        'idx_priority',
        'CrawlerTask',
        'priority'
      );

      $this->createIndex(
        'idx_failed',
        'CrawlerTask',
        'failed'
      );

      $this->createIndex(
        'idx_failed_import',
        'CrawlerTask',
        'failed_import'
      );

      $this->createIndex(
        'idx_downloaded',
        'CrawlerTask',
        'downloaded'
      );

      $this->createIndex(
        'fk_CrawlerTask_CrawlerTask1_idx',
        'CrawlerTask',
        'prioritized_task_id'
      );

      /* table CrawlerMeta */
      $this->createTable('{{%CrawlerMeta}}', [
        'id' => $this->primaryKey()->unsigned(),
        'created' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        'name' => $this->string(32)->notNull(),
        'value' => $this->string(255),
      ], $tableOptions);

      $this->createIndex(
        'fulltext_name',
        'CrawlerMeta',
        'name'
      );

      $this->createIndex(
        'created_idx',
        'CrawlerMeta',
        'created'
      );

      /* table CrawlerTask_Meta */
      $this->createTable('{{%CrawlerTask_Meta}}', [
        'id' => $this->primaryKey()->unsigned(),
        'meta_id' => $this->integer()->notNull()->unsigned()->unique(),
        'task_id' => $this->integer()->notNull()->unsigned(),
      ], $tableOptions);

      $this->addForeignKey(
        'fk_table1_CrawlerCategory1',
        'CrawlerTask_Meta',
        'meta_id',
        'CrawlerMeta',
        'id',
        'CASCADE',
        'CASCADE'
      );

      $this->addForeignKey(
        'fk_table1_Crawler1',
        'CrawlerTask_Meta',
        'task_id',
        'CrawlerTask',
        'id',
        'CASCADE',
        'CASCADE'
      );

      $this->createIndex(
        'fk_table1_CrawlerCategory1_idx',
        'CrawlerTask_Meta',
        'meta_id'
      );

      $this->createIndex(
        'fk_table1_Crawler1_idx',
        'CrawlerTask_Meta',
        'task_id'
      );

    }

    public function down()
    {
        echo $this->className() . " cannot be reverted.\n";

        return false;
    }

