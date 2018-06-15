<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 上午9:50
 */

use Pimple\Container;

/**
 * 注册容器到环境中
 */
$container = container(ENV_USERS);
if (!$container) {
    $container = new Container();
    env(ENV_USERS, $container);
}

/**
 * 加载配置
 */
require __DIR__ . '/config.php';
/**
 * mysql数据库连接dsn
 */
$container['users.mysql.servers.master_dsn'] = function () {
    return USERS_MYSQL_DSN_MASTER;
};
$container['users.mysql.servers.slave_dsn'] = function(Container $c) {
    return $c['users.mysql.servers.master_dsn'];
};
/**
 * 库名称
 */
$container['users.mysql.database.users'] = $container->factory(function () {
    return USERS_MYSQL_DB_USERS;
});
/**
 * mysql数据库服务器
 * @param Container $c
 * @return \TCG\MySQL\Server
 */
$container['users.mysql.servers.master'] = function (Container $c) {
    return new \TCG\MySQL\Server($c['users.mysql.servers.master_dsn']);
};
/**
 * mysql数据库服务器
 * @param Container $c
 * @return \TCG\MySQL\Server
 */
$container['users.mysql.servers.slave'] = function (Container $c) {
    return new \TCG\MySQL\Server($c['users.mysql.servers.slave_dsn']);
};
/**
 * 用户认证表
 * @param Container $c
 * @return \Users\MySQL\Table\UserAuthTable
 */
$container['users.mysql.tables.user_auth'] = function (Container $c) {
    return new Users\MySQL\Table\UserAuthTable($c['users.mysql.servers.master'], [], $c['users.mysql.database.users']);
};
/**
 * 用户资料表
 * @param Container $c
 * @return \Users\MySQL\Table\UserProfileTable
 */
$container['users.mysql.tables.user_profile'] = function (Container $c) {
    return new \Users\MySQL\Table\UserProfileTable($c['users.mysql.servers.master'], [], $c['users.mysql.database.users']);
};
/**
 * 角色权限表
 * @param Container $c
 * @return \Users\MySQL\Table\RolePermissionTable
 */
$container['users.mysql.tables.role_permission'] = function (Container $c) {
    return new \Users\MySQL\Table\RolePermissionTable($c['users.mysql.servers.master'], [], $c['users.mysql.database.users']);
};
/**
 * 用户角色关联表
 * @param Container $c
 * @return \Users\MySQL\Table\UserRoleTable
 */
$container['users.mysql.tables.user_role'] = function (Container $c) {
    return new \Users\MySQL\Table\UserRoleTable($c['users.mysql.servers.master'], [], $c['users.mysql.database.users']);
};
/**
 * 用户session表
 * @param Container $c
 * @return \Users\MySQL\Table\SessionTable
 */
$container['users.mysql.tables.session'] = function (Container $c) {
    return new \Users\MySQL\Table\SessionTable($c['users.mysql.servers.master'], [], $c['users.mysql.database.users']);
};
/**
 * @param Container $c
 * @return \TCG\Auth\Session\Session
 */
$container['session'] = function (Container $c) {
    ini_set('session.gc_maxlifetime', 24 * 60 * 60);
    $session = new TCG\Auth\Session\Session($_COOKIE);
    /** @var \Users\MySQL\Table\SessionTable $sessionTable */
    $sessionTable = $c['users.mysql.tables.session'];
    $session->bind(
        $open = function ($savePath, $sessionName) {
            return true;
        },
        $close = function () use ($sessionTable) {
            $sessionTable->closeSession();
            return true;
        },
        $read = function ($sessionId) use ($sessionTable) {
            return $sessionTable->readSession($sessionId);
        },
        $write = function ($sessionId, $data) use ($sessionTable) {
            $sessionTable->writeSession($sessionId, $data);
            return true;
        },
        $destroy = function ($sessionId) use ($sessionTable) {
            $sessionTable->destroySession($sessionId);
            return true;
        },
        $gc = function () use ($sessionTable) {
            $sessionTable->gcSession();
            return true;
        }
    );
    return $session;
};
/**
 * @return \TCG\Auth\Session\Segment
 */
$container['session.main'] = function () {
    return new \TCG\Auth\Session\Segment('main');
};
/**
 * @return \TCG\Auth\Session\FlashSegment
 */
$container['session.flash'] = function () {
    return new \TCG\Auth\Session\FlashSegment('flash');
};


/**
 * 各种服务
 */

/**
 * 用户服务
 * @return \Users\Service\UserService
 */
$container[\Users\Service\UserService::class] = function () {
    return new \Users\Service\UserService();
};
/**
 * 角色服务
 * @return \Users\Service\RoleService
 */
$container[\Users\Service\RoleService::class] = function () {
    return new \Users\Service\RoleService();
};
/**
 * 权限服务
 * @return \Users\Service\PermissionService
 */
$container[\Users\Service\PermissionService::class] = function () {
    return new \Users\Service\PermissionService();
};



/**
 * 辅助方法
 */

/**
 * 主SESSION
 * @return \TCG\Auth\Session\Segment
 */
function session()
{
    return env()->get('session.main');
}

/**
 * Flash型的SESSION
 * @return \TCG\Auth\Session\FlashSegment
 */
function flash()
{
    return env()->get('session.flash');
}

/**
 * 判断uid是否有哪些权限
 * @param $uid
 * @param $permissionExpr
 * @return int
 * @throws Exception
 */
function can($uid, $permissionExpr)
{
    /** @var \Users\Service\PermissionService $permissionService */
    $permissionService = env()->get(\Users\Service\PermissionService::class);
    return $permissionService->hasPermission($uid, $permissionExpr);
}

/**
 * 注册权限
 * @param string $permissionValue
 * @param string $permissionName
 * @param string $permissionDesc
 */
function permission($permissionValue, $permissionName, $permissionDesc)
{
    /** @var \Users\Service\PermissionService $permissionService */
    $permissionService = env()->get(\Users\Service\PermissionService::class);
    $permissionService->registerPermission($permissionValue, $permissionName, $permissionDesc);
}



