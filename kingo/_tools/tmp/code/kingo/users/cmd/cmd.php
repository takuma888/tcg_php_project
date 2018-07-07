<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/9
 * Time: 上午10:08
 */

require __DIR__ . '/../../bootstrap.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Debug\Debug;

/**
 * 注册私有服务
 */
require __DIR__ . '/../private.php';


/**
 * 设置当前app
 */
app(ENV_USERS);

Debug::enable();

$application = new Application('Users Command Line', 'PHP v' . PHP_VERSION);

$application->add(new \Users\Console\MySQL\InitCommand());
$application->add(new \Users\Console\Account\InitCommand());
$application->add(new \Users\Console\Role\InitCommand());

$application->run();
