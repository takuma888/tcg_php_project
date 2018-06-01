<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/5/31
 * Time: 上午11:16
 */

/**
 * 定义各个子项目的环境ID 值需要为2的n次幂
 */
define('ENV_DEFAULT', 0); // 0000 0000

define('ENV_DEMO_P1', pow(2, 0)); // 0000 0001
define('ENV_DEMO_P2', pow(2, 1)); // 0000 0010
define('ENV_DEMO_P3', pow(2, 2)); // 0000 0100

/*
 * ENV_DEMO_P1 | ENV_DEMO_P2
 * = ENV_DEFAULT | ENV_DEMO_P1 | ENV_DEMO_P2
 * = 0000 0000 | 0000 0001 | 0000 0010
 * = 0000 0011
 */

/**
 * 定义工程的根路径
 */
!defined('ROOT') && define('ROOT', __DIR__);

/**
 * Composer 自动加载
 */
$autoloader = require ROOT . '/../vendor/autoload.php';

/**
 * 启动大环境
 */
require ROOT . '/env.php';

/**
 * 启动子环境
 */
require ROOT . '/p1/env.php';
require ROOT . '/p2/env.php';
require ROOT . '/p3/env.php';