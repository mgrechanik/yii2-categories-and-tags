<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category\services;

use mgrechanik\yii2category\ui\forms\backend\BaseCategoryForm;

/**
 * Category manage service interface
 * 
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.0
 */
interface CategoryManageServiceInterface
{
    /**
     * Creating new category from form data 
     * 
     * @param BaseCategoryForm $form
     * @param boolean $runValidation Whether to run validation before saving
     * @return integer|null Id of the new created category or null when error
     */
    public function create(BaseCategoryForm $form, $runValidation = true);
    
    /**
     * Updating category with form data 
     * 
     * @param integer $id The id of the category model we are updating
     * @param BaseCategoryForm $form
     * @param boolean $runValidation  Whether to run validation before saving
     * @return boolean Whether the operation succeded
     */
    public function update($id, BaseCategoryForm $form, $runValidation = true);
}

