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
!defined('BOOT_ROOT') && define('BOOT_ROOT', __DIR__);

/**
 * Composer 自动加载
 */
$oAutoloader = require __DIR__ . '/../vendor/autoload.php';

bootEnv(BOOT_ROOT);

foreach (scandir(BOOT_ROOT) as $sAppDir) {
    if ($sAppDir != '.' && $sAppDir != '..') {
        $sAppDir = BOOT_ROOT . '/' . $sAppDir;
        if (is_dir($sAppDir)) {
            bootEnv($sAppDir);
            bootRoute($sAppDir);
        }
    }
}

