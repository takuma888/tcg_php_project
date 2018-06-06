<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 上午9:50
 */

use Pimple\Container;
use TCG\Http\Environment;
use TCG\Http\Request;
use TCG\Http\Response;
use TCG\Http\ResponseSender;
use TCG\Middleware\Dispatcher;

/**
 * 注册容器到环境中
 */
$container = container(ENV_USERS);
if (!$container) {
    $container = new Container();
    env(ENV_USERS, $container);
}

// http
// tcg http environment
$container['tcg.http.environment'] = function () {
    return Environment::mock($_SERVER);
};
// tcg http request
$container['http.request'] = function (Container $c) {
    return Request::createFromEnvironment($c['tcg.http.environment']);
};

// tcg http response
$container['http.response'] = $container->factory(function () {
    return new Response();
});

$container['http.response_sender'] = function () {
    return new ResponseSender();
};

// 中间件
// dispatcher
$container['middleware.dispatcher'] = $container->factory(function () {
    return new Dispatcher();
});

/**
 * 加载配置
 */
require __DIR__ . '/config.php';
/**
 * mysql数据库连接dsn
 */
$container['users.mysql.servers.master_dsn'] = $container->factory(function () {
    return USERS_MYSQL_DSN_MASTER;
});
$container['users.mysql.servers.slave_dsn'] = $container->factory(function(Container $c) {
    return $c['users.mysql.servers.master_dsn'];
});
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
    return new Users\MySQL\Table\UserAuthTable($c['users.mysql.servers.master'], $c['users.mysql.servers.slave'], $c['users.mysql.database.users']);
};
/**
 * 用户session表
 * @param Container $c
 * @return \Users\MySQL\Table\SessionTable
 */
$container['users.mysql.tables.session'] = function (Container $c) {
    return new \Users\MySQL\Table\SessionTable($c['users.mysql.servers.master'], $c['users.mysql.servers.slave'], $c['users.mysql.database.users']);
};
/**
 * @param Container $c
 * @return \TCG\Auth\Session\Session
 */
$container['auth.session'] = function (Container $c) {
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
        $gc = function ($maxlifetime) use ($sessionTable) {
            $sessionTable->gcSession();
            return true;
        }
    );
    return $session;
};
/**
 * @return \TCG\Auth\Session\Segment
 */
$container['auth.session_segment.main'] = function () {
    return new \TCG\Auth\Session\Segment('main');
};
/**
 * @return \TCG\Auth\Session\Segment
 */
$container['auth.session_segment.flash'] = function () {
    return new \TCG\Auth\Session\Segment('flash');
};

