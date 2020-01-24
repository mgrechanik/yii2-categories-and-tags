<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category\ui\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use mgrechanik\yii2category\ui\forms\backend\BaseCategoryForm;
use mgrechanik\yii2category\services\CategoryManageServiceInterface;
use mgrechanik\yiimaterializedpath\ServiceInterface as TreeServiceInterface;
use yii\data\ArrayDataProvider;


/**
 * Controller to handle CRUD operations for category
 * 
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.0
 */
class AdminManageController extends Controller
{
    /**
     * @var CategoryManageServiceInterface Service to manage category
     */
    private $service;

    /**
     * {@inheritdoc}
     */    
    public function __construct($id, $module, CategoryManageServiceInterface $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * List of all Category models.
     * 
     * This page is intended to show all category in hierarchical form. 
     * That is why it does not use pagination.
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        // Getting our Category tree as a simple array
        $treeService = Yii::createObject(TreeServiceInterface::class);
        $root = $treeService->getRoot($this->module->categoryModelClass);
        // for dataProvider we want result indexed by id, that is the work of the last 'true' parameter
        $tree = $treeService->buildFlatTree($root, true, false, true);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $tree,
        ]);
        
        return $this->render($this->module->categoryIndexView, [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single category model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render($this->module->categoryViewView, [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new category model.
     * 
     * @return mixed
     */
    public function actionCreate()
    {
        /* @var $module \mgrechanik\yii2category\Module */
        $module = $this->module;
        
        $modelClass = $module->categoryModelClass;
        $formClass = $module->categoryFormModelClass;
        $model = new $modelClass;


        $categoryForm = new $formClass(['model' => $model, 'module' => $module]);
        $categoryForm->scenario = BaseCategoryForm::SCENARIO_CREATE;
        $categoryForm->chooseRoot();
        $categoryForm->operation = BaseCategoryForm::OP_APPEND_TO;

        if ($categoryForm->load(Yii::$app->request->post()) && $categoryForm->validate()) {
            try {
                if ($id = $this->service->create($categoryForm, $module->validateCategoryModel)) {
                    Yii::$app->session->setFlash('success', Yii::t('yii2category', $module->creatingSuccessMessage));
                    return $this->redirect(
                        $module->redirectToIndexAfterCreate ? ['index'] : ['view', 'id' => $id]
                    );
                }
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render($module->categoryCreateView, [
            'categoryForm' => $categoryForm,
        ]);
    }

    /**
     * Updates an existing category model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        /* @var $module \mgrechanik\yii2category\Module */
        $module = $this->module;
        
        $formClass = $module->categoryFormModelClass;
        
        $categoryForm = new $formClass(['model' => $model, 'module' => $module]);
        $categoryForm->loadAdditionalAttributesFromModel($model);
        $categoryForm->scenario = BaseCategoryForm::SCENARIO_UPDATE;
        $parent = $model->parent();
        $categoryForm->newParent = $parent->getId();
        $categoryForm->operation = BaseCategoryForm::OP_VIEW;

        if ($categoryForm->load(Yii::$app->request->post()) && $categoryForm->validate()) {
            try {
                if ($this->service->update($model->id, $categoryForm, $module->validateCategoryModel)) {
                    Yii::$app->session->setFlash('success', Yii::t('yii2category', $module->updatingSuccessMessage));
                    return $this->redirect(
                        $module->redirectToIndexAfterUpdate ? ['index'] : ['view', 'id' => $id]
                    );
                }
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render($module->categoryUpdateView, [
            'categoryForm' => $categoryForm,
        ]);
    }

    /**
     * Deletes an existing category model.
     * 
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        /* @var $module \mgrechanik\yii2category\Module */
        $module = $this->module;        
        Yii::$app->session->setFlash('success', Yii::t('yii2category', $module->deletingSuccessMessage));
        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * 
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $categoryClass = $this->module->categoryModelClass;
        if (($model = $categoryClass::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('yii2category', 'The requested category does not exist'));
    }
}
