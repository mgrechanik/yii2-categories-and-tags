<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category\tools;

use yii\base\BootstrapInterface;

/**
 * Bootstrap class for yii2 category extension
 * 
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.0
 */
class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $i18n = $app->i18n;
        $i18n->translations['yii2category'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@mgrechanik/yii2category/resources/i18n',
        ];
        
        \Yii::$container->setSingleton(
            \mgrechanik\yii2category\services\CategoryManageServiceInterface::class, 
            \mgrechanik\yii2category\services\CategoryManageService::class
        );        
    }
}

