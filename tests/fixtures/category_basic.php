<?php
/**
 * This file is part of the mgrechanik/yii2-materialized-path library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/yii2-materialized-path/blob/master/LICENCE.md
 * @link https://github.com/mgrechanik/yii2-materialized-path
 */

/**
 * This is the basic fixture for all integration tests.
 * It represents the next tree:
 * 
 * ROOT
 *   --- 1 
 *       --- 3
 *       --- 4
 *   --- 2
 *       --- 5
 *           --- 7
 *       --- 6
 */
return [
    [
        'id' => 1,
        'path' => '',
        'level' => 1,
        'weight' => 1,
        'name' => 'Laptops & PC',
    ],
    [
        'id' => 2,
        'path' => '',
        'level' => 1,
        'weight' => 2,
        'name' => 'Phones & Accessories',
    ],
    [
        'id' => 3,
        'path' => '1/',
        'level' => 2,
        'weight' => 1,
        'name' => 'Laptops',
    ],
    [
        'id' => 4,
        'path' => '1/',
        'level' => 2,
        'weight' => 2,
        'name' => 'PC',
    ],
    [
        'id' => 5,
        'path' => '2/',
        'level' => 2,
        'weight' => 1,
        'name' => 'Smartphones',
    ],
    [
        'id' => 6,
        'path' => '2/',
        'level' => 2,
        'weight' => 2,
        'name' => 'Batteries',
    ],    
    [
        'id' => 7,
        'path' => '2/5/',
        'level' => 3,
        'weight' => 1,
        'name' => 'Android',
    ],    	
];

