<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/15
 * Time: 下午4:12
 */

namespace Users\Service;


use Users\MySQL\Table\RolePermissionTable;
use Users\MySQL\Table\UserRoleTable;

class PermissionService
{
    /**
     * @var array
     */
    protected static $permissions = [];

    /**
     * @var array
     */
    protected static $userPermissionCache = [];

    /**
     * 注册权限
     * @param string $permissionValue
     * @param string $permissionName
     * @param string $permissionDesc
     * @return $this
     */
    public function registerPermission($permissionValue, $permissionName, $permissionDesc)
    {
        self::$permissions[$permissionValue] = [
            'name' => $permissionName,
            'desc' => $permissionDesc,
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return self::$permissions;
    }

    /**
     * @param $uid
     * @param $permissionExpr
     * @return int
     * @throws \Exception
     */
    public function hasPermission($uid, $permissionExpr)
    {
        $userPermission = $this->getPermissionsByUserId($uid);
        return $userPermission & $permissionExpr;
    }

    /**
     * @param $uid
     * @return string
     * @throws \Exception
     */
    public function getPermissionsByUserId($uid)
    {
        if (!isset(self::$userPermissionCache[$uid])) {
            $rolePermissions = $this->getRolePermissionsByUserId($uid);
            $permissionExpr = decbin(0);
            if ($rolePermissions['data']) {
                foreach ($rolePermissions['data'] as $rolePermission) {
                    $permissionExpr |= $rolePermission['permission'];
                }
            }
            self::$userPermissionCache[$uid] = $permissionExpr;
        }
        return self::$userPermissionCache[$uid];
    }


    /**
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function getRolePermissionsByUserId($userId)
    {
        $sql = <<<SQL
SELECT * FROM {@table.user_role} as `ur` LEFT JOIN  {@table.role_permission} as `rp` 
ON `ur`.rid = `rp`.`id` 
WHERE `ur`.`uid` = :user_id
SQL;
        $tables = [
            $this->getUserRoleTable(),
            $this->getRolePermissionTable(),
        ];
        $query = query($sql)
            ->table('{@table.user_role}', $this->getUserRoleTable())
            ->table('{@table.role_permission}', $this->getRolePermissionTable())
            ->setParameter(':user_id', $userId);
        $connection = query()::connectionForRead($tables);

        $stmt = $connection->prepare($query->getSQLForRead());
        $stmt->execute($query->getParameters());
        return [
            'data' => $stmt->fetchAll(),
        ];
    }

    /**
     * @return UserRoleTable
     */
    private function getUserRoleTable()
    {
        return table('user_role');
    }

    /**
     * @return RolePermissionTable
     */
    private function getRolePermissionTable()
    {
        return table('role_permission');
    }
}