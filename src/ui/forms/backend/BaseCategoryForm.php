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
use yii\base\Model;
use mgrechanik\yiimaterializedpath\ServiceInterface as TreeServiceInterface;
use mgrechanik\yii2category\models\BaseCategory;

/**
 * This is the parent class for Category Form used in this module
 * 
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.0
 */
abstract class BaseCategoryForm extends Model
{
    const SCENARIO_CREATE = 1;
    const SCENARIO_UPDATE = 2;

    // operations with the position of the category:

    const OP_VIEW = 1;
    const OP_APPEND_TO = 2;
    const OP_INSERT_BEFORE = 3;
    const OP_INSERT_AFTER = 4;

    // base form fields:

    /**
     * @var integer The value of id of the parent node to this one
     */
    public $newParent;

    /**
     * @var integer Operation to perform about position of the current node in the tree
     */    
    public $operation;

    // service:

    /**
     * @var mgrechanik\yii2category\models\BaseCategory Category model
     */
    protected $model;

    /**
     * @var TreeServiceInterface Service for managing trees 
     */
    protected $service;
    
    /**
     * @var \mgrechanik\yii2category\Module The module
     */
    protected $module;    

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->service = Yii::createObject(TreeServiceInterface::class);
    }
    
    /**
     * Getter
     * @return BaseCategory 
     */
    public function getModel()
    {
        return $this->model;
    } 
	
    /**
     * Setter
     * @param BaseCategory  $model
     */
    public function setModel(BaseCategory $model)
    {
        $this->model = $model;
    }
    
    /**
     * Getter
     * @return TreeServiceInterface
     */
    public function getService()
    {
        return $this->service;
    }    
    
    /**
     * Getter
     * @return \mgrechanik\yii2category\Module
     */
    public function getModule()
    {
        return $this->service;
    } 

    /**
     * Setter
     * @param \mgrechanik\yii2category\Module  $module
     */
    public function setModule(\mgrechanik\yii2category\Module $module)
    {
        $this->module = $module;
    }    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $range = [self::OP_APPEND_TO, self::OP_INSERT_BEFORE, self::OP_INSERT_AFTER];
        if ($this->scenario == self::SCENARIO_UPDATE) {
            // View operation is allowed only for existed models
            $range[] = self::OP_VIEW;
        }
        $messageRange = $this->scenario == self::SCENARIO_CREATE ?
            Yii::t('yii2category', 'For new item you need to choose append or insert operation') 
            : Yii::t('yii2category', 'Invalid choice');
        $newParentElementId = \yii\helpers\Html::getInputId($this, 'newparent');
        
        return [
            ['newParent', 'in', 'range' => $this->getValidParentIds()],

            [['newParent', 'operation'], 'required', 'on' => self::SCENARIO_CREATE],

            ['operation', 'in', 'range' => $range, 'message' => $messageRange],

            ['operation', 'in', 'not' => true,
                'range' => [self::OP_INSERT_BEFORE, self::OP_INSERT_AFTER],
                'when' => function($model) {
                    return $model->newParent < 0;
                },
                'whenClient' => "function (attribute, value) {
                    return $('#" . $newParentElementId . "').val() < 0;
                }",
                'message' => Yii::t('yii2category', 'You cannot insert before or after root node')
            ]
        ];
    }

    /**
     * @inheritdoc
     */    
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['newParent', 'operation'],
            self::SCENARIO_UPDATE => ['newParent', 'operation'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $operationMessage = $this->model->isNewRecord ? 
                'Specify how to add a new item to the position choosen in the tree' 
                : 'Specify how to change position of the current category';
        return [
            'newParent' => Yii::t('yii2category', 'Position'),
            'operation' => Yii::t('yii2category', $operationMessage)
        ];
    }

    /**
     * Choose a root node as parent fot the category
     */
    public function chooseRoot()
    {
        $this->newParent = $this->getRootId();
    }

    /**
     * Get the id (negative) of the root node
     * 
     * @return integer
     */
    public function getRootId()
    {
        $service = $this->service;
        if (!($this->model instanceof BaseCategory)) {
            throw new \Exception('Form\'s model property must be set');
        }
        $root = $service->getRoot(get_class($this->model));
        return $root->getId();
    }

    /**
     * Get the list of items for [[operation]] field
     * 
     * @return array
     */
    public function getOperationItems()
    {
        $model = $this->model;
        $result = [];
        if ($this->scenario == CategoryForm::SCENARIO_UPDATE) {
            $result[self::OP_VIEW] = Yii::t('yii2category', 'Do not change position');
        }
        $result[ self::OP_APPEND_TO] =
            $model->isNewRecord ? Yii::t('yii2category', 'Append to') : Yii::t('yii2category', 'Move to');
        $result[self::OP_INSERT_BEFORE] = Yii::t('yii2category', 'Insert before');
        $result[self::OP_INSERT_AFTER] = Yii::t('yii2category', 'Insert after');
        return $result;
    }
    
    /**
     * Building items for listBox (<select> tag)
     * @return array
     */
    public function getPositionItems()
    {
        // Get the managing tree service
        $service = $this->service;
        // Get the root of the tree
        $root = $service->getRoot(get_class($this->model));
        // Building flat tree
        $tree = $service->buildFlatTree($root, true, true, false, $this->getExceptIds());
        // Building select items with appropriate indents for every node
        $items = $service->buildSelectItems($tree, function($node) {
            return ($node['id'] < 0) ? '- ' . Yii::t('yii2category', 'root') : '' . str_repeat('  ', $node['level']) . str_repeat('-', $node['level']) .
                ' ' . \yii\helpers\Html::encode($node['name']) . '';
        });        
        return $items;
    }

    /**
     * Get the attributes your form, inherited from this one, added
     * 
     * @return array
     */
    public function getAdditionalAttributes()
    {
        return array_diff($this->attributes(), ['newParent', 'operation']);
    }
    
    /**
     * Load additional form fields from model
     * 
     * @param \mgrechanik\yii2category\models\BaseCategory $model
     */
    public function loadAdditionalAttributesFromModel($model)
    {
        foreach ($this->getAdditionalAttributes() as $name) {
            $this->{$name} = $model->{$name};
        }
    }   
    
    /**
     * Load additional form fields to model
     * 
     * @param \mgrechanik\yii2category\models\BaseCategory $model
     */
    public function loadAdditionalAttributesToModel($model)
    {
        foreach ($this->getAdditionalAttributes() as $name) {
            $model->{$name} = $this->{$name};
        }
    }     

    /**
     * We need to exclude the current category model and all it's subtree 
     * from select choice list
     *
     * @return array
     */
    public function getExceptIds()
    {
        return $this->model->isNewRecord ? [] : [$this->model->id];
    }

    /**
     * Valid Ids of models to who this model could be added/moved to
     *
     * @return array
     */
    protected function getValidParentIds()
    {
        $modelClass = get_class($this->model);
        $service = $this->service;
        $root = $service->getRoot($modelClass);
        $exceptIds = $this->getExceptIds();
        return $service->buildSubtreeIdRange($root, true, $exceptIds);
    }
}