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
        '用户管理系统',
        '0b1' . str_repeat('0', $i - 1),
        '<strong>用户管理系统测试权限</strong>' . $i,
        str_repeat("用户管理系统测试权限{$i}的描述", mt_rand(1, 100)) . '<strong>aaaa</strong>');
}


for ($i = 11; $i <= 20; $i ++) {
    permission(
        '其他管理系统',
        '0b1' . str_repeat('0', $i - 1),
        '其他管理系统测试权限' . $i,
        str_repeat("其他管理系统测试权限{$i}的描述", mt_rand(1, 100)) . '<strong>aaaa</strong>');
}