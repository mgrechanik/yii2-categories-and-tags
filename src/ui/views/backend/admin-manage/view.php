<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model mgrechanik\yii2category\models\BaseCategory  */
// But actually for this view $model is:
/* @var $model mgrechanik\yii2category\models\Category  */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2category', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [    
                'attribute' => 'name',
                'label' => Yii::t('yii2category', 'Name')
            ],
            [
                'label' => Yii::t('yii2category', 'Parent'),
                'value' => function ($model, $widget){
                    $parent = $model->parent();
                    return $parent->isRoot() ? Yii::t('yii2category', 'root') 
                            : Html::a(Html::encode($parent->name), ['view', 'id' => $parent->id]);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'level',
                'label' => Yii::t('yii2category', 'Level')
            ],
        ],
    ]) ?>

</div>
