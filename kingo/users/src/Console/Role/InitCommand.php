<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/21
 * Time: 下午3:24
 */

namespace Users\Console\Role;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Users\Service\RoleService;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('users:role.init')
            ->setDescription('初始化角色');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $permissions ='0b' . str_repeat('1', 9999);
        $roles = [
            [
                'id' => 'root',
                'name' => '根角色',
                'description' => '创始人角色，应该拥有所有权限',
                'priority' => 9999,
                'permissions' => $permissions,
            ],
            [
                'id' => 'superadmin',
                'name' => '超级管理员',
                'description' => '超级管理员角色，拥有几乎所有权限',
                'priority' => 999,
                'permissions' => $permissions,
            ],
            [
                'id' => 'developer',
                'name' => '开发者',
                'description' => '开发者角色，基本上拥有所有权限',
                'priority' => 99,
                'permissions' => $permissions,
            ],
        ];

        foreach ($roles as $role) {
            /** @var RoleService $roleService */
            $roleService = service(RoleService::class);
            $roleService->createRole($role);
        }
    }
}