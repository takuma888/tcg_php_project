<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/20
 * Time: 上午9:08
 */

namespace Users\Service;


class AuthService
{
    /**
     * @param $username
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function validateUsernameUnique($username, $id = null)
    {
        $username = trim($username);
        if (!$username) {
            throw new \Exception("用户名不能为空");
        }
        /** @var UserService $userService */
        $userService = service(UserService::class);
        $userInfo = $userService->getUserInfoByUsername($username);
        if ($userInfo['data'] && $userInfo['data']['id'] != $id) {
            throw new \Exception("用户名已被使用");
        }
        return true;
    }

    /**
     * @param $email
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function validateEmailUnique($email, $id = null)
    {
        $email = trim($email);
        if (!$email) {
            throw new \Exception("邮箱地址不能为空");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("邮箱格式不错误");
        }
        /** @var UserService $userService */
        $userService = service(UserService::class);
        $userInfo = $userService->getUserInfoByEmail($email);
        if ($userInfo['data'] && $userInfo['data']['id'] != $id) {
            throw new \Exception("邮箱已被使用");
        }
        return true;
    }

    /**
     * @param $mobile
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function validateMobileUnique($mobile, $id = null)
    {
        $mobile = trim($mobile);
        if (!$mobile) {
            throw new \Exception("手机号码不能为空");
        }
        /** @var UserService $userService */
        $userService = service(UserService::class);
        $userInfo = $userService->getUserInfoByMobile($mobile);
        if ($userInfo['data'] && $userInfo['data']['id'] != $id) {
            throw new \Exception("手机号已被使用");
        }
        return true;
    }


    /**
     * @param $username
     * @param $password
     * @return array
     * @throws \Exception
     */
    public function loginByUsername($username, $password)
    {
        /** @var UserService $userService */
        $userService = service(UserService::class);
        $userInfo = $userService->getUserInfoByUsername($username, true);
        if (!$userInfo['data']) {
            throw new \Exception("用户名不存在");
        }
        if ($userInfo['data']['password'] != md5($password)) {
            throw new \Exception("密码错误");
        }
        // 记录登录时间
        $userService->updateUser($userInfo['data']['id'], [
            'login_at' => date('Y-m-d H:i:s'),
            'session_id' => session_id(),
        ]);
        session()->set('uid', $userInfo['data']['id']);
        return $userInfo;
    }

    /**
     * @param $email
     * @param $password
     * @return array
     * @throws \Exception
     */
    public function loginByEmail($email, $password)
    {
        /** @var UserService $userService */
        $userService = service(UserService::class);
        $userInfo = $userService->getUserInfoByEmail($email, true);
        if (!$userInfo['data']) {
            throw new \Exception("邮箱不存在");
        }
        if ($userInfo['data']['password'] != md5($password)) {
            throw new \Exception("密码错误");
        }
        // 记录登录时间
        $userService->updateUser($userInfo['data']['id'], [
            'login_at' => date('Y-m-d H:i:s'),
            'session_id' => session_id(),
        ]);
        session()->set('uid', $userInfo['data']['id']);
        return $userInfo;
    }

    /**
     * @param $mobile
     * @param $password
     * @return array
     * @throws \Exception
     */
    public function loginByMobile($mobile, $password)
    {
        /** @var UserService $userService */
        $userService = service(UserService::class);
        $userInfo = $userService->getUserInfoByMobile($mobile, true);
        if (!$userInfo['data']) {
            throw new \Exception("手机号不存在");
        }
        if ($userInfo['data']['password'] != md5($password)) {
            throw new \Exception("密码错误");
        }
        // 记录登录时间
        $userService->updateUser($userInfo['data']['id'], [
            'login_at' => date('Y-m-d H:i:s'),
            'session_id' => session_id(),
        ]);
        session()->set('uid', $userInfo['data']['id']);
        return $userInfo;
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session()->set('uid', null);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function session()
    {
        $uid = session()->get('uid', null);
        if ($uid) {
            /** @var UserService $userService */
            $userService = service(UserService::class);
            $userInfo = $userService->getUserInfoById($uid, true);
            if (!$userInfo['data']) {
                throw new \Exception("用户不存在");
            }
            session()->set('uid', $userInfo['data']['id']);
            return $userInfo;
        }
        return [];
    }
}