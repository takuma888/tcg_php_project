<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/9
 * Time: 下午5:51
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /role
 * 获取角色数据
 */
route()->get('/role/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    /** @var \Users\Service\RoleService $roleService */
    $roleService = service(\Users\Service\RoleService::class);
    $roleInfo = $roleService->getRoleInfoById($id);
    if (!$roleInfo['data']) {
        throw new \Exception("角色不存在");
    }
    return json($response, [
        'role' => $roleInfo['data'],
    ]);
});


/**
 * post /role/add
 * 添加角色
 */
route()->post('/role/add', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $id = $posts['id'] ?? '';
    $name = $posts['name'] ?? '';
    $priority = $posts['priority'] ?? 0;
    $desc = $posts['desc'] ?? '';

    // 检查参数
    $id = trim($id);
    if (!$id) {
        throw new \Exception("角色ID不能为空");
    }
    $name = trim($name);
    if (!$name) {
        throw new \Exception("角色名称不能为空");
    }

    // 验证重复性
    /** @var \Users\Service\RoleService $roleService */
    $roleService = service(\Users\Service\RoleService::class);
    $tmpRoleInfo = $roleService->getRoleInfoById($id);
    if ($tmpRoleInfo['data']) {
        throw new \Exception("角色ID已存在");
    }
    // 创建
    $roleInfo = $roleService->createRole([
        'id' => $id,
        'name' => $name,
        'priority' => $priority,
        'description' => $desc,
    ]);
    return json($response, [
        'role' => $roleInfo['data'],
    ]);
});

/**
 * post /role/edit
 * 修改角色数据
 */
route()->post('/role/edit/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    /** @var \Users\Service\RoleService $roleService */
    $roleService = service(\Users\Service\RoleService::class);
    $roleInfo = $roleService->getRoleInfoById($id);
    if (!$roleInfo['data']) {
        throw new \Exception("角色不存在");
    }
    $posts = $request->getParsedBody();
    $newId = $posts['id'] ?? '';
    $name = $posts['name'] ?? '';
    $priority = $posts['priority'] ?? 0;
    $desc = $posts['desc'] ?? '';
    // 检查参数
    $newId = trim($newId);
    if (!$newId) {
        throw new \Exception("角色ID不能为空");
    }
    $roleInfo = $roleService->getRoleInfoById($newId);
    if ($roleInfo['data'] && $roleInfo['data']['id'] != $id) {
        throw new \Exception("角色ID已存在");
    }
    $name = trim($name);
    if (!$name) {
        throw new \Exception("角色名称不能为空");
    }
    $roleInfo = $roleService->updateRole($id, [
        'id' => $newId,
        'name' => $name,
        'priority' => $priority,
        'description' => $desc,
    ]);
    flash()->success('更新角色成功');
    return json($response, [
        'role' => $roleInfo['data'],
    ]);
});

/**
 * post /role/delete
 * 删除角色数据
 */
route()->post('/role/delete/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    /** @var \Users\Service\RoleService $roleService */
    $roleService = service(\Users\Service\RoleService::class);
    try {
        $roleService->deleteOneById($id);
        flash()->success('删除成功');
    } catch (\Exception $e) {
        flash()->error($e->getMessage());
    }
    return json($response, []);
});


route()->post('/role/validate-id/unique', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $newId = $posts['new_id'] ?? null;
    $oldId = $posts['old_id'] ?? null;
    $newId = trim($newId);
    $oldId = trim($oldId);

    try {
        /** @var \Users\Service\RoleService $roleService */
        $roleService = service(\Users\Service\RoleService::class);
        if (!$newId) {
            throw new \Exception("角色ID不能为空");
        }
        $roleInfo = $roleService->getRoleInfoById($newId);
        if ($roleInfo['data'] && $roleInfo['data']['id'] != $oldId) {
            throw new \Exception("角色ID已被占用");
        }
        return json($response, []);

    } catch (\Exception $e) {
        return json($response, [
            'invalid' => $e->getMessage()
        ]);
    }
});


