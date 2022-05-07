<?php

use yii\db\Migration;

/**
 * Class m220506_193404_insert_users_table
 */
class m220506_193404_insert_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('users', ['name'], [
            ['fprochazka'],
            ['hranicka'],
            ['jaroslavlibal'],
            ['pilec'],
            ['mishak87'],
            ['newPOPE'],
            ['stekycz'],
            ['janedbal'],
            ['brabijan'],
            ['northys'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('users');
    }


}
