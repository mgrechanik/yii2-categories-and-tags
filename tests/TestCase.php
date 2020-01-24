<?php
/**
 * This file is part of the mgrechanik/yii2-categories-and-tags library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-categories-and-tags/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-categories-and-tags
 */

namespace mgrechanik\yii2category\tests;

/**
 * Basic Test Case
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    protected function tearDown()
    {
        $this->destroyApplication();
        parent::tearDown();
    }

    protected function mockApplication()
    {
        AppFactory::getApplication();
        AppFactory::setSingletons();
    }

    protected function destroyApplication()
    {
        \Yii::$app = null;
    }
}