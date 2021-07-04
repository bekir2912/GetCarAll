<?php

use yii\db\Migration;

class m171128_113715_create_table_socials extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%socials}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(255)->notNull(),
            'url' => $this->string(255)->notNull(),
            'icon' => $this->string(255)->notNull(),
            'order' => $this->integer(11)->notNull()->defaultValue('0'),
            'status' => $this->integer(11)->notNull()->defaultValue('0'),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%socials}}');
    }
}
