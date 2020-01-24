<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

use yii\db\Migration;

/**
 * Handles the creation of table `category`.
 */
class m180908_094405_create_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'path' => $this->string(255)->notNull()->defaultValue('')->comment('Path to parent node'),
            'level' => $this->integer(4)->notNull()->defaultValue(1)->comment('Level of the node in the tree'),
            'weight' => $this->integer(11)->notNull()->defaultValue(1)->comment('Weight among siblings'),
            'name' => $this->string()->notNull()->comment('Name'),
        ]);
        
        $this->createIndex('category_path_index', '{{%category}}', 'path');
        $this->createIndex('category_level_index', '{{%category}}', 'level');
        $this->createIndex('category_weight_index', '{{%category}}', 'weight');        
        
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%category}}');
    }
}
