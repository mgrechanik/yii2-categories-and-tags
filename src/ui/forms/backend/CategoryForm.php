<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category\ui\forms\backend;

use Yii;

/**
 * This is the Form which fits [[mgrechanik\yii2category\models\Category]] Active Record model.
 * 
 * It is simple implementation of [[BaseCategoryForm]] with additional field 'name'
 * added to it.
 *
 * 
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.0
 */
class CategoryForm extends BaseCategoryForm
{
    // custom form fields:
    
    public $name;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        foreach ($scenarios as $key => $val) {
            $scenarios[$key][] = 'name';
        }
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], 'string', 'max' => 255],
            [['name'], 'required'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => Yii::t('yii2category', 'Name'),
        ]);
    }

}