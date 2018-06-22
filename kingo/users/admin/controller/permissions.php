<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/22
 * Time: 上午9:30
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * post /permissions
 * 获取角色权限
 */
route()->post('/permissions', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $ids = $posts['ids'] ?? [];
    $roles = [];
    $scopes = [];

    if ($ids) {
        /** @var \Users\Service\RoleService $roleService */
        $roleService = service(\Users\Service\RoleService::class);
        $quoteIds = [];
        $params = [];
        foreach ($ids as $i => $id) {
            $quoteIds[] = ':id_' . $i;
            $params[':id_' . $i] = $id;
        }
        $clauseExpr = "`id` IN (" . implode(', ', $quoteIds) . ') ORDER BY `priority` DESC';
        $rolesResult = $roleService->selectMany($clauseExpr, $params);
        foreach ($rolesResult['data'] as $role) {
            $roles[$role['id']] = $role;
        }
    }
    /** @var \Users\Service\PermissionService $permissionService */
    $permissionService = service(\Users\Service\PermissionService::class);
    $permissions = $permissionService->getPermissions();

    foreach ($roles as $roleId => $role) {
        $rolePermissionBitExpr = gmp_init($role['permissions'], 2);
        $rolePermissions = [];
        foreach ($permissions as $permissionId => $permission) {
            $permissionIdBitExpr = gmp_init($permissionId, 2);
            $calc = gmp_and($rolePermissionBitExpr, $permissionIdBitExpr);
            $rolePermissions[$permissionId] = gmp_strval($calc, 2) == gmp_strval($permissionIdBitExpr, 2);
            $scopes[$permission['scope']] = $permission['scope'];
        }
        $roles[$roleId]['role_permissions'] = $rolePermissions;
    }

    return json($response, [
        'roles' => $roles,
        'permissions' => $permissions,
        'scopes' => array_values($scopes),
    ]);
});

/**
 * post /permissions/edit
 * 保存角色权限
 */
route()->post('/permissions/edit', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $rolesPermissions = $posts['roles'] ?? [];

    /** @var \Users\Service\RoleService $roleService */
    $roleService = service(\Users\Service\RoleService::class);

    $table = table('role_permission');
    $supportTransaction = query()::supportTransaction([$table]);
    $conn = query()::connectionForWrite([$table]);
    if ($supportTransaction) {
        $conn->beginTransaction();
    }
    try {
        foreach ($rolesPermissions as $roleId => $permissions) {
            $roleInfo = $roleService->getRoleInfoById($roleId);
            if (!$roleInfo['data']) {
                continue;
            }
            $role = $roleInfo['data'];
            $rolePermissions = $role['permissions'];
            $rolePermissions = gmp_init($rolePermissions, 2);
            foreach ($permissions as $permissionId => $status) {
                $permissionId = gmp_init($permissionId, 2);
                if ($status) {
                    $rolePermissions = gmp_or($rolePermissions, $permissionId);
                } else {
                    $rolePermissions = gmp_and($rolePermissions, gmp_com($permissionId));
                }
            }
            // 更新
            $roleService->updateRole($roleId, [
                'permissions' => '0b' . gmp_strval($rolePermissions, 2),
            ]);
        }
        if ($supportTransaction) {
            $conn->commit();
        }
        flash()->success('更新权限成功');

    } catch (\Exception $e) {
        if ($supportTransaction) {
            $conn->rollBack();
        }
        flash()->error('更新权限失败');
        throw $e;
    }
    return json($response, []);
});