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
            throw new \Exception("邮箱已被使用");
        }
        return true;
    }
}