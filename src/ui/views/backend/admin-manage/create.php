<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $categoryForm mgrechanik\yii2category\ui\forms\backend\BaseCategoryForm */
/* @var $module \mgrechanik\yii2category\Module */
$module = $this->context->module;

$this->title = Yii::t('yii2category', 'Creating a category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2category', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render($module->categoryFormView, [
        'categoryForm' => $categoryForm,
    ]) ?>

</div>