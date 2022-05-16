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
            'id' => $this->integer(11),
            'data' => $this->text()->defaultValue(NULL)
        ]);

        $this->createIndex(
            'idx-data-id',
            'data',
            'id'
        );

        $this->addForeignKey(
            'fk-data-id',
            'data',
            'id',
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
