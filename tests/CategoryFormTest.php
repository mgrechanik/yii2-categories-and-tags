<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category\tests;

use mgrechanik\yii2category\ui\forms\backend\CategoryForm;
use mgrechanik\yii2category\models\Category;

/**
 * Testing Category Form
 */
class CategoryFormTest extends DbTestCase
{
    /**
     * {@inheritdoc}
     */    
    protected function setUp()
    {
        parent::setUp();
    }
    
    public function testUseFormWithoutModelSet()
    {
        $form = new CategoryForm();
        $this->expectExceptionMessage('Form\'s model property must be set');
        $form->getRootId();
    }    
    
    public function testGetRootId()
    {
        $form = new CategoryForm(['model' => new Category()]);
        $this->assertEquals(-100, $form->getRootId());
    }    
    
    public function testChooseRoot()
    {
        $form = new CategoryForm(['model' => new Category()]);
        $form->chooseRoot();
        $this->assertEquals(-100, $form->newParent);
    }    
    
    public function testGetAdditionalAttributes()
    {
        $form = new CategoryForm();
        $this->assertEquals(['name'], $form->getAdditionalAttributes());
    }     
    
    public function testLoadAdditionalAttributesFromModel()
    {
        $form = new CategoryForm();
        $form->loadAdditionalAttributesFromModel(new Category(['name' => 'new']));
        $this->assertEquals('new', $form->name);
    }      
    
    public function testLoadAdditionalAttributesToModel()
    {
        $form = new CategoryForm(['name' => 'new']);
        $model = new Category();
        $form->loadAdditionalAttributesToModel($model);
        $this->assertEquals('new', $model->name);
    }    
    
    public function testGetExceptIdsForNewModel()
    {
        $form = new CategoryForm(['model' => new Category()]);
        $this->assertEquals([], $form->getExceptIds());
    }      
    
    public function testGetExceptIdsForExistedModel()
    {
        $this->haveFixture('category', 'category_basic');
        $form = new CategoryForm(['model' => Category::findOne(1)]);
        $this->assertEquals([1], $form->getExceptIds());
    }   
    
    // test rules
    
    public function testValidationNewParentCorrect()
    {
        $this->haveFixture('category', 'category_basic');
        $form = new CategoryForm([
            'name' => 'name', 
            'model' => Category::findOne(1), 
            'scenario' => CategoryForm::SCENARIO_UPDATE
        ]);
        $form->newParent = 2;
        $form->validate();
        $this->assertEmpty($form->errors);
    }       
    
    public function testValidationNewParentNotCorrect()
    {
        $this->haveFixture('category', 'category_basic');
        $form = new CategoryForm([
            'name' => 'name', 
            'model' => Category::findOne(1), 
            'scenario' => CategoryForm::SCENARIO_UPDATE
        ]);
        $form->newParent = 1;
        $form->validate();
        $this->assertNotEmpty($form->errors);
    }     
    
    public function testValidationOperationNotCorrectInsertBeforeRoot()
    {
        $this->haveFixture('category', 'category_basic');
        $form = new CategoryForm([
            'name' => 'name', 
            'model' => Category::findOne(1), 
            'scenario' => CategoryForm::SCENARIO_UPDATE,
            'operation' => CategoryForm::OP_INSERT_BEFORE,
            'newParent' => -100
        ]);
        $form->validate();
        $this->assertNotEmpty($form->errors);
    }      
    
    public function testValidationOperationNotCorrectInsertAfterRoot()
    {
        $this->haveFixture('category', 'category_basic');
        $form = new CategoryForm([
            'name' => 'name', 
            'model' => Category::findOne(1), 
            'scenario' => CategoryForm::SCENARIO_UPDATE,
            'operation' => CategoryForm::OP_INSERT_AFTER,
            'newParent' => -100
        ]);
        $form->validate();
        $this->assertNotEmpty($form->errors);
    }   
    
    // end test rules
    
}