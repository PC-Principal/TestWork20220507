<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%data}}`.
 */
class m220516_150422_create_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('data', [
            'id' => $this->primaryKey(),
            'data' => $this->text()->defaultValue(NULL),
            'user_id' => $this->integer(),
            'repo_date_update' => $this->timestamp()->defaultValue(null)
        ]);

        $this->createIndex(
            'idx-data-user-id',
            'data',
            'user_id'
        );

        $this->addForeignKey(
            'fk-data-id',
            'data',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('data');
    }
}
