<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/5/31
 * Time: 下午1:52
 */

require __DIR__ . '/../bootstrap.php';

// 设置当前环境
env(ENV_DEMO_P1);


c()->get('twig')->display('@demo_p1/test.html.twig', [
    'a' => 'b',
]);
