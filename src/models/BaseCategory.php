<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category\models;

use Yii;
use mgrechanik\yiimaterializedpath\MaterializedPathBehavior;

/**
 * This is the parent class for base Active Record category model
 *
 * Your category model should have next fields/columns:
 * @property int $id
 * @property string $path Path to parent node
 * @property int $level Level of the node in the tree
 * @property int $weight Weight among siblings
 * 
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.0
 */
abstract class BaseCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'materializedpath' => [
                'class' => MaterializedPathBehavior::class,
                'modelScenarioForChildrenNodesWhenTheyDeletedAfterParent' => 'SCENARIO_NOT_DEFAULT',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level', 'weight'], 'integer'],
            [['path'], 'string', 'max' => 255],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yii2category', 'ID'),
            'path' => Yii::t('yii2category', 'Path to parent node'),
            'level' => Yii::t('yii2category', 'Level of the node in the tree'),
            'weight' => Yii::t('yii2category', 'Weight among siblings'),
        ];
    }
}
