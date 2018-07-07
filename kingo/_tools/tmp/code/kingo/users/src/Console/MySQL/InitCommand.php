<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/9
 * Time: 上午10:31
 */

namespace Users\Console\MySQL;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('users:mysql.init')
            ->setDescription('创建使用到的MySQL库和表')
            ->addOption('show-sql', null, InputOption::VALUE_OPTIONAL, '是否显示SQL', 0)
            ->addOption('drop', null, InputOption::VALUE_OPTIONAL, '是否drop已存在的表', 0)
            ->addOption('table', null, InputOption::VALUE_OPTIONAL, '指定表');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $showSQL = $input->getOption('show-sql');
        $drop = $input->getOption('drop');
        $tableName = $input->getOption('table');

        $tableNames = [
            'user_auth',
            'user_profile',
            'session',
            'user_role',
            'role_permission'
        ];

        foreach ($tableNames as $tableBaseName) {
            if (!$tableName || ($tableName && $tableName == $tableBaseName)) {
                $output->writeln("<question>创建 {$tableBaseName} 表</question>");
                $table = \table($tableBaseName);
                $sql = $table->create($drop);
                if ($showSQL) {
                    $output->writeln("<comment>{$sql}</comment>");
                }
            }
        }
    }
}