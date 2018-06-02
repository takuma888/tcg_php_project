<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/5/31
 * Time: 下午5:15
 */

use Pimple\Container;

$container = new Container();
env(ENV_DEMO_P3, $container);