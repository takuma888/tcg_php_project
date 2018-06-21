<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午3:56
 */


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /user
 * 用户数据
 */
route()->get('/user/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    $userInfo = $userService->getUserInfoById($id, true);
    if (!$userInfo['data']) {
        throw new \Exception("用户不存在");
    }
    unset($userInfo['data']['password']);
    return json($response, [
        'user' => $userInfo['data'],
    ]);
});

/**
 * post /user/add/username
 * 通过用户名添加用户
 */
route()->post('/user/add/username', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $username = $posts['username'] ?? '';
    $password = $posts['password'] ?? '';
    $confirmPassword = $posts['confirm_password'] ?? '';

    // 检查参数
    $username = trim($username);
    $password = trim($password);
    $confirmPassword = trim($confirmPassword);
    if (!$username) {
        throw new \Exception("用户名不能为空");
    }
    if (!$password) {
        throw new \Exception("密码不能为空");
    }
    if (!$confirmPassword) {
        throw new \Exception("确认密码不能为空");
    }
    if ($password != $confirmPassword) {
        throw new \Exception("密码与确认密码不一致");
    }
    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    $userInfo = $userService->getUserInfoByUsername($username);
    if ($userInfo['data']) {
        throw new \Exception("用户名已存在");
    }
    // 创建用户

    $userInfo = $userService->createUser([
        'username' => $username,
        'password' => md5($password),
    ]);
    if (!$userInfo['data']) {
        throw new \Exception("创建用户失败");
    }
    flash()->success("创建用户成功");
    return json($response, [
        'user' => $userInfo['data'],
    ]);

});
/**
 * post /user/add/email
 * 通过邮箱添加用户
 */
route()->post('/user/add/email', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $email = $posts['email'] ?? '';
    $password = $posts['password'] ?? '';
    $confirmPassword = $posts['confirm_password'] ?? '';

    // 检查参数
    $email = trim($email);
    $password = trim($password);
    $confirmPassword = trim($confirmPassword);
    if (!$email) {
        throw new \Exception("邮箱地址不能为空");
    }
    if (!$password) {
        throw new \Exception("密码不能为空");
    }
    if (!$confirmPassword) {
        throw new \Exception("确认密码不能为空");
    }
    if ($password != $confirmPassword) {
        throw new \Exception("密码与确认密码不一致");
    }

    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    $userInfo = $userService->getUserInfoByEmail($email);
    if ($userInfo['data']) {
        throw new \Exception("邮箱已存在");
    }
    // 创建用户

    $userInfo = $userService->createUser([
        'email' => $email,
        'password' => md5($password),
    ]);
    if (!$userInfo['data']) {
        throw new \Exception("创建用户失败");
    }
    flash()->success("创建用户成功");

    return json($response, [
        'user' => $userInfo['data'],
    ]);
});
/**
 * post /user/add/mobile
 * 通过手机号添加用户
 */
route()->post('/user/add/mobile', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $mobile = $posts['mobile'] ?? '';
    $password = $posts['password'] ?? '';
    $confirmPassword = $posts['confirm_password'] ?? '';

    // 检查参数
    $mobile = trim($mobile);
    $password = trim($password);
    $confirmPassword = trim($confirmPassword);
    if (!$mobile) {
        throw new \Exception("手机号不能为空");
    }
    if (!$password) {
        throw new \Exception("密码不能为空");
    }
    if (!$confirmPassword) {
        throw new \Exception("确认密码不能为空");
    }
    if ($password != $confirmPassword) {
        throw new \Exception("密码与确认密码不一致");
    }

    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    $userInfo = $userService->getUserInfoByMobile($mobile);
    if ($userInfo['data']) {
        throw new \Exception("邮箱已存在");
    }
    // 创建用户

    $userInfo = $userService->createUser([
        'mobile' => $mobile,
        'password' => md5($password),
    ]);
    if (!$userInfo['data']) {
        throw new \Exception("创建用户失败");
    }
    flash()->success("创建用户成功");

    return json($response, [
        'user' => $userInfo['data'],
    ]);
});

/**
 * post /user/edit
 * 修改用户
 */
route()->post('/user/edit/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    $posts = $request->getParsedBody();
    $authFields = $posts['auth'] ?? [];
    $profileFields = $posts['profile'] ?? [];

    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    $userInfo = $userService->getUserInfoById($id);
    if (!$userInfo['data']) {
        throw new \Exception("用户不存在");
    }
    if ($authFields) {
        // 验证
        if (isset($authFields['username']) && !$authFields['username']
            && isset($authFields['email']) && !$authFields['email']
            && isset($authFields['mobile']) && !$authFields['mobile']) {
            throw new \Exception("用户名，邮箱，手机号不能同时为空");
        }
        /** @var \Users\Service\AuthService $authService */
        $authService = service(\Users\Service\AuthService::class);
        if (isset($authFields['username']) && $authFields['username']) {
            $authService->validateUsernameUnique($authFields['username'], $id);
        }
        if (isset($authFields['email']) && $authFields['email']) {
            $authService->validateEmailUnique($authFields['email'], $id);
        }
        if (isset($authFields['mobile']) && $authFields['mobile']) {
            $authService->validateMobileUnique($authFields['mobile'], $id);
        }
        // 整理
        $keys = ['username', 'email', 'mobile'];
        foreach ($keys as $key) {
            if (isset($authFields[$key])) {
                if (!$authFields[$key]) {
                    $authFields[$key] = null;
                }
            }
        }
    }
    $userInfo = $userService->updateUser($id, $authFields, $profileFields);
    flash()->success('更新用户成功');
    return json($response, [
        'user' => $userInfo['data'],
    ]);
});

/**
 * post /user/delete
 * 删除用户
 */
route()->post('/user/delete/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    try {
        $userService->deleteOneById($id);
        flash()->success('删除成功');
    } catch (\Exception $e) {
        flash()->error($e->getMessage());
    }
    return json($response, []);
});

/**
 * post /user/password
 * 修改密码
 */
route()->post('/user/password[/{id:\d+}]', function (ServerRequestInterface $request, ResponseInterface $response, $id = null) {
    if (!$id) {
        $id = session()->get('uid');
    }
    $id = intval($id);

    $posts = $request->getParsedBody();
    $password = $posts['password'] ?? '';
    $confirmPassword = $posts['confirm_password'] ?? '';

    // 检查参数
    $password = trim($password);
    $confirmPassword = trim($confirmPassword);
    if (!$password) {
        throw new \Exception("密码不能为空");
    }
    if (!$confirmPassword) {
        throw new \Exception("确认密码不能为空");
    }
    if ($password != $confirmPassword) {
        throw new \Exception("密码与确认密码不一致");
    }

    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);

    $userInfo = $userService->updateUser($id, [
        'password' => md5($password),
    ]);
    return json($response, [
        'user' => $userInfo['data'],
    ]);
});

/**
 * post /user/validate-username
 * 验证用户名合法性
 */
route()->post('/user/validate-username/unique', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $username = $posts['username'] ?? '';
    $id = $posts['id'] ?? null;
    try {
        /** @var \Users\Service\AuthService $authService */
        $authService = service(\Users\Service\AuthService::class);
        if ($authService->validateUsernameUnique($username, $id)) {
            return json($response, []);
        } else {
            return json($response, [
                'invalid' => '用户名不合法',
            ]);
        }
    } catch (\Exception $e) {
        return json($response, [
            'invalid' => $e->getMessage()
        ]);
    }
});
/**
 * post /user/validate-email
 * 验证邮箱合法性
 */
route()->post('/user/validate-email/unique', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $email = $posts['email'] ?? '';
    $id = $posts['id'] ?? null;
    try {
        /** @var \Users\Service\AuthService $authService */
        $authService = service(\Users\Service\AuthService::class);
        if ($authService->validateEmailUnique($email, $id)) {
            return json($response, []);
        } else {
            return json($response, [
                'invalid' => '邮箱不合法',
            ]);
        }
    } catch (\Exception $e) {
        return json($response, [
            'invalid' => $e->getMessage()
        ]);
    }
});
/**
 * post /user/validate-mobile
 * 验证手机号码合法性
 */
route()->post('/user/validate-mobile/unique', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $mobile = $posts['mobile'] ?? '';
    $id = $posts['id'] ?? null;
    try {
        /** @var \Users\Service\AuthService $authService */
        $authService = service(\Users\Service\AuthService::class);
        if ($authService->validateMobileUnique($mobile, $id)) {
            return json($response, []);
        } else {
            return json($response, [
                'invalid' => '手机号不合法',
            ]);
        }
    } catch (\Exception $e) {
        return json($response, [
            'invalid' => $e->getMessage()
        ]);
    }
});


