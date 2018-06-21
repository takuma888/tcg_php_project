<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/21
 * Time: 下午5:07
 */

/**
 * 权限
 */

for ($i = 1; $i <= 10; $i ++) {
    permission(
        '1' . str_repeat('0', $i - 1),
        '测试权限' . $i,
        str_repeat("测试权限{$i}的描述", mt_rand(1, 100)) . '<strong>aaaa</strong>');
}