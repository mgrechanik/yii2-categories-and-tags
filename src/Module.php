<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category;

use mgrechanik\yiiuniversalmodule\UniversalModule;
use mgrechanik\yii2category\models\BaseCategory;
use mgrechanik\yii2category\models\Category;
use mgrechanik\yii2category\ui\forms\backend\BaseCategoryForm;
use mgrechanik\yii2category\ui\forms\backend\CategoryForm;

/**
 * Module to handle simple category of active record models.
 * 
 * You can use your own Active Record models with fields you need.
 * 
 * Since it is [[UniversalModule]] and holds only backend functionality 
 * you are expected to set up this module to your application with 'backend' mode:
 * 
 *   'modules' => [
 *       'category' => [                           // any name you want
 *           'class' => 'mgrechanik\yii2category\Module',
 *           'mode' => 'backend',                 // mode
 *       ]
 *   ]
 * 
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.0
 */
class Module extends UniversalModule
{
    public $backendControllers = [
        'admin-manage'
    ];
    
    /**
     * {@inheritdoc}
     */       
    public $defaultRoute = 'admin-manage';
    
    /**
     * @var \mgrechanik\yii2category\models\BaseCategory  The name of the class of your 
     * Active Record category model.
     * This one is expected to be of [[BaseCategory]] type.
     * By default we use Category model this module provides but you can use your own.
     */
    public $categoryModelClass = Category::class;
    
    /**
     * @var \mgrechanik\yii2category\ui\forms\backend\BaseCategoryForm  The name of the class of your 
     * category form model.
     * This one is expected to be of [[BaseCategoryForm]] type.
     * By default we use Category model form this module provides but you can use your own
     */
    public $categoryFormModelClass = CategoryForm::class;    
    
    /**
     * @var callable Callback to create a label for category at category index page
     * with indent of the each node in the category hierarchy
     */
    public $indentedNameCreatorCallback = 'mgrechanik\yii2category\Module::createIndentedName';

    /**
     * @var string The name of the view to be used as category index view
     * @see \yii\base\View::render() 
     */
    public $categoryIndexView = 'index';
    
    /**
     * @var string The name of the view to be used as category create view
     * @see \yii\base\View::render() 
     */
    public $categoryCreateView = 'create';

        /**
     * @var string The name of the view to be used as category update view
     * @see \yii\base\View::render() 
     */
    public $categoryUpdateView = 'update';

    /**
     * @var string The name of the view to be used as category form view
     * @see \yii\base\View::render() 
     */
    public $categoryFormView = '_form';  
    
    /**
     * @var string The name of the view to be used as category view view
     * @see \yii\base\View::render() 
     */
    public $categoryViewView = 'view';    
    
    /**
     * @var boolean Whether to redirect to categories index page after category has been created 
     */
    public $redirectToIndexAfterCreate = true;       
    
    /**
     * @var boolean Whether to redirect to categories index page after category has been updated 
     */
    public $redirectToIndexAfterUpdate = true;        
    
    /**
     * @var boolean Whether to validate category models after form has been validated.
     */
    public $validateCategoryModel = false;      
    
    /**
     * @var string Flash message to be shown after category has been created. 
     */
    public $creatingSuccessMessage = 'New category has been created successfully';  

    /**
     * @var string Flash message to be shown after category has been updated. 
     */
    public $updatingSuccessMessage = 'The category has been updated';    
    
    /**
     * @var string Flash message to be shown after category has been deleted. 
     */
    public $deletingSuccessMessage = 'The category has been deleted';        
    
    /**
     * @inheritdoc
     */    
    public function init() {
        parent::init();
        if (!is_subclass_of($this->categoryModelClass, BaseCategory::class)) {
            throw new \yii\base\InvalidConfigException('Your Active Record category model does not fit this module');
        }
        
        if (!is_subclass_of($this->categoryFormModelClass, BaseCategoryForm::class)) {
            throw new \yii\base\InvalidConfigException('Your category form model does not fit this module');
        }        
    }
    

    /**
     * Default implementation for [[indentedNameCreatorCallback]].
     * 
     * It fits [[Category]] model since it relies on name field.
     * 
     * @param array $model
     * @param integer $key
     * @param integer $index
     * @param yii\grid\DataColumn $column
     * @return string
     */
    public static function createIndentedName($model, $key, $index, $column)
    {
        return str_repeat('&nbsp;&nbsp;', $model['level'])
            . str_repeat('-', $model['level']) 
            . ' ' . \yii\helpers\Html::encode($model['name']);
    }
}