<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 上午9:50
 */

/**
 * 定义顶级环境ID，默认是0
 */
define('ENV_DEFAULT', 0);
/**
 * 定义各个子项目的环境ID 值需要为2的n次幂 n最好从0开始
 */
define('ENV_USERS', pow(2, 0)); // users
define('ENV_OFFERS', pow(2, 1)); // offers

$apps = [
    'users', // 必须优先加载 users app
    'offers',
];

/**
 * 定义工程的根路径
 */
!defined('ROOT') && define('ROOT', __DIR__);

/**
 * Composer 自动加载
 */
$autoloader = require ROOT . '/../vendor/autoload.php';
loader($autoloader);


date_default_timezone_set('Asia/Shanghai');
/**
 * 启动环境
 */
require ROOT . '/public.php';
foreach ($apps as $app) {
    require ROOT . "/{$app}/service.php";
}
/**
 * 加载钩子
 */
foreach ($apps as $app) {
    $hookFile = ROOT . "/{$app}/hook.php";
    if (file_exists($hookFile)) {
        include $hookFile;
    }
}