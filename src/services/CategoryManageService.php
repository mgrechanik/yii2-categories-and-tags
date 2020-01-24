<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category\services;

use Yii;
use mgrechanik\yii2category\ui\forms\backend\BaseCategoryForm;
use mgrechanik\yiimaterializedpath\ServiceInterface as TreeServiceInterface;

/**
 * Category manage service
 * 
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.0
 */
class CategoryManageService implements CategoryManageServiceInterface
{
    /**
     * @var TreeServiceInterface Service to manage trees 
     */
    private $service;
    
    /**
     * {@inheritdoc}
     */    
    public function __construct()
    {
        $this->service = Yii::createObject(TreeServiceInterface::class);
    }

    /**
     * {@inheritdoc}
     */    
    public function create(BaseCategoryForm $form, $runValidation = true)
    {
        $categoryModelClass = $this->getModelClassFromForm($form);
        $model = new $categoryModelClass();
        $form->loadAdditionalAttributesToModel($model);
        $parent = $this->findModel($form->newParent, $categoryModelClass);
        if ($this->saveModel($model, $parent, $form->operation, $runValidation)) {
            return $model->id;
        }
    }

    /**
     * {@inheritdoc}
     */    
    public function update($id, BaseCategoryForm $form, $runValidation = true)
    {
        $categoryModelClass = $this->getModelClassFromForm($form);
        $model = $this->findModel($id, $categoryModelClass);
        $parent = $this->findModel($form->newParent, $categoryModelClass);
        $form->loadAdditionalAttributesToModel($model);
        return $this->saveModel($model, $parent, $form->operation, $runValidation);
    }

    /**
     * Get the model by it's "id"
     * 
     * @param integer $id The id of the category
     * @param string $categoryModelClass
     * @return \mgrechanik\yii2category\\models\BaseCategoryModel|
     *         \mgrechanik\yiimaterializedpath\tools\RootNode  Category model
     * @throws \Exception
     */
    protected function findModel($id, $categoryModelClass)
    {
        // It finds AR model from table or RootNode when 'id' is negative
        if ($model = $this->service->getModelById($categoryModelClass, $id)) {
            return $model;
        }
        throw new \Exception('The category does not exist.');
    }

    /**
     * Saving the model
     * 
     * @param \mgrechanik\yii2category\\models\BaseCategoryModel $model The model being processed
     * @param \mgrechanik\yii2category\\models\BaseCategoryModel|
     *        \mgrechanik\yiimaterializedpath\tools\RootNode $parent Parent node to the $model
     * @param integer $operation Operation with setting up position of the $model
     * @param boolean $runValidation Whether to run validation before saving
     * @return boolean Whether the operation succeded
     * @throws \LogicException
     */
    protected function saveModel($model, $parent, $operation, $runValidation)
    {
        switch ($operation)
        {
            case BaseCategoryForm::OP_APPEND_TO :
                return $model->appendTo($parent, $runValidation);
            case BaseCategoryForm::OP_INSERT_BEFORE :
                return $model->insertBefore($parent, $runValidation);
            case BaseCategoryForm::OP_INSERT_AFTER :
                return $model->insertAfter($parent, $runValidation);
            case BaseCategoryForm::OP_VIEW :
                if ($model->isNewRecord) {
                    throw new \LogicException('New categorys could not be saved without setting up position');
                }
                return $model->save($runValidation);
        }
    }
    
    /**
     * Get the class of the category model of this module
     * 
     * @param BaseCategoryForm $form
     * @return string
     */
    protected function getModelClassFromForm(BaseCategoryForm $form)
    {
        return get_class($form->getModel());
    }
}