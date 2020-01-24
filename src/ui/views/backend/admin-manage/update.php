<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $categoryForm mgrechanik\yii2category\ui\forms\backend\BaseCategoryForm */
/* @var $model mgrechanik\yii2category\models\Category */
/* @var $module \mgrechanik\yii2category\Module */
$module = $this->context->module;

$model = $categoryForm->model;
$this->title = Yii::t('yii2category', 'Update Category: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2category', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('yii', 'Update');
?>
<div class="category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render($module->categoryFormView, [
        'categoryForm' => $categoryForm,
    ]) ?>

</div>
