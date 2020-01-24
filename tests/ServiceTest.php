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
 * Testing Service for managing categorys
 */
class ServiceTest extends DbTestCase
{
    public $service;
    
    protected function setUp()
    {
        parent::setUp();
        $this->service = \Yii::createObject(\mgrechanik\yii2category\services\CategoryManageServiceInterface::class);
        $this->haveFixture('category', 'category_basic');
    }
    
    
    public function testCreateAddingToWrongParent()
    {
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => new Category(),
            'scenario' => CategoryForm::SCENARIO_CREATE,
            'operation' => CategoryForm::OP_APPEND_TO,
            'newParent' => 100
        ]);
        $this->expectExceptionMessage('The category does not exist.');
        $this->service->create($form, false);
    }      
    
    public function testCreateInViewMode()
    {
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => new Category(),
            'scenario' => CategoryForm::SCENARIO_CREATE,
            'operation' => CategoryForm::OP_VIEW,
            'newParent' => 1
        ]);
        $this->expectExceptionMessage('New categorys could not be saved without setting up position');
        $this->service->create($form, false);
    }      
    
    public function testCreateAddingToCorrectParentRoot()
    {
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => new Category(),
            'scenario' => CategoryForm::SCENARIO_CREATE,
            'operation' => CategoryForm::OP_APPEND_TO,
            'newParent' => -100
        ]);
        $treeService = $form->service;
        $this->service->create($form, false);
        
        $root = $treeService->getRoot(Category::class);
        $lastChild = $root->lastChild();
        
        $this->assertCount(3, $root->children());
        $this->assertEquals('some name', $lastChild->name);
        $this->assertEquals(1, $lastChild->level);
        $this->assertEquals('', $lastChild->path);
        $this->assertEquals(3, $lastChild->weight);
    } 
    
    public function testCreateAddingToCorrectParentNotRoot()
    {
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => new Category(),
            'scenario' => CategoryForm::SCENARIO_CREATE,
            'operation' => CategoryForm::OP_APPEND_TO,
            'newParent' => 1
        ]);
        $this->service->create($form, false);
        
        $parent = Category::findOne(1);
        $lastChild = $parent->lastChild();
        
        $this->assertCount(3, $parent->children());
        $this->assertEquals('some name', $lastChild->name);
        $this->assertEquals(2, $lastChild->level);
        $this->assertEquals('1/', $lastChild->path);
        $this->assertEquals(3, $lastChild->weight);
    }   
    
    public function testCreateInsertingBeforeCorrectNode()
    {
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => new Category(),
            'scenario' => CategoryForm::SCENARIO_CREATE,
            'operation' => CategoryForm::OP_INSERT_BEFORE,
            'newParent' => 1
        ]);
        $treeService = $form->service;
        $this->service->create($form, false);
        
        $root = $treeService->getRoot(Category::class);
        $lastChild = $root->firstChild();
        
        $this->assertCount(3, $root->children());
        $this->assertEquals('some name', $lastChild->name);
        $this->assertEquals(1, $lastChild->level);
        $this->assertEquals('', $lastChild->path);
        $this->assertEquals(1, $lastChild->weight);
    }    
    
    public function testCreateInsertingAfterCorrectNode()
    {
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => new Category(),
            'scenario' => CategoryForm::SCENARIO_CREATE,
            'operation' => CategoryForm::OP_INSERT_AFTER,
            'newParent' => 1
        ]);
        $treeService = $form->service;
        $this->service->create($form, false);
        
        $root = $treeService->getRoot(Category::class);
        $children = $root->children();
        $newChild = $children[1];
        
        $this->assertCount(3, $root->children());
        $this->assertEquals('some name', $newChild->name);
        $this->assertEquals(1, $newChild->level);
        $this->assertEquals('', $newChild->path);
        $this->assertEquals(2, $newChild->weight);
    }  
    
    public function testUpdateAddingWrongModel()
    {
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => new Category(),
            'scenario' => CategoryForm::SCENARIO_UPDATE,
            'operation' => CategoryForm::OP_APPEND_TO,
            'newParent' => -100
        ]);
        $this->expectExceptionMessage('The category does not exist.');
        $this->service->update(100, $form, false);
    }     
    
    public function testUpdateAddingToWrongModel()
    {
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => new Category(),
            'scenario' => CategoryForm::SCENARIO_UPDATE,
            'operation' => CategoryForm::OP_APPEND_TO,
            'newParent' => 100
        ]);
        $this->expectExceptionMessage('The category does not exist.');
        $this->service->update(0, $form, false);
    }  
    
    public function testMovingToCorrectParentNotRoot()
    {
        $model = Category::findOne(2);
        $parent = Category::findOne(1);
        $root = $model->getRoot();
        $model7 = Category::findOne(7);
        
        $form = new CategoryForm([
            'name' => 'some name',
            'model' => $model,
            'scenario' => CategoryForm::SCENARIO_CREATE,
            'operation' => CategoryForm::OP_APPEND_TO,
            'newParent' => 1
        ]);
        $this->service->update(2, $form, false);
        
        $model->refresh();
        $model7->refresh();
        
        $this->assertCount(1, $root->children());
        $this->assertCount(3, $parent->children());
        
        $this->assertEquals('some name', $model->name);
        $this->assertEquals(2, $model->level);
        $this->assertEquals('1/', $model->path);
        $this->assertEquals(3, $model->weight);
        
        $this->assertEquals('1/2/5/', $model7->path);
        $this->assertEquals(4, $model7->level);
    }       
    
}