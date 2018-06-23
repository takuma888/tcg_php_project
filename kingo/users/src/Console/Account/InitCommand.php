<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/19
 * Time: 上午9:36
 */

namespace Users\Console\Account;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('users:account.init')
            ->setDescription('创建根用户')
            ->addOption('username', null, InputOption::VALUE_OPTIONAL, '用户名', '')
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, '密码', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        $username = trim($username);
        $password = trim($password);
        if (!$username) {
            throw new \Exception("用户名不能为空");
        }
        if (!$password) {
            throw new \Exception("密码不能为空");
        }
        /** @var \Users\Service\UserService $userService */
        $userService = service(\Users\Service\UserService::class);
        $tmpUser = $userService->getUserInfoByUsername($username);
        if ($tmpUser['data']) {
            throw new \Exception("用户名已存在");
        }
        $userInfo = $userService->createUser([
            'username' => $username,
            'password' => md5($password),
        ]);
        if (!$userInfo['data']) {
            throw new \Exception("创建用户失败");
        }
        // 关联账号角色
        $userId = $userInfo['data']['id'];
        $userService->editRole($userId, [
            ROLE_ROOT, ROLE_SUPERADMIN, ROLE_DEVELOPER
        ]);
        $output->writeln("创建用户成功! ID:{$userInfo['data']['id']}");
    }
}