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
     * @param string $scope
     * @param string $permissionValue
     * @param string $permissionName
     * @param string $permissionDesc
     * @return $this
     */
    public function registerPermission($scope, $permissionValue, $permissionName, $permissionDesc)
    {
        if (substr($permissionValue, 0, 2) != '0b') {
            $permissionValue = '0b' . $permissionValue;
        }
        self::$permissions[$permissionValue] = [
            'scope' => $scope,
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
        $userPermission = gmp_init($userPermission, 2);
        $permissionExpr = gmp_init($permissionExpr, 2);
        $calc = gmp_and($userPermission, $permissionExpr);
        $calc = '0b' . gmp_strval($calc, 2);
        $permissionExpr = '0b' . gmp_strval($permissionExpr, 2);
        return $calc == $permissionExpr;
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
            // 先优先判断角色
            $roles = [];
            foreach ($rolePermissions['data'] as $rolePermission) {
                $roles[] = $rolePermission['id'];
            }
            if (array_intersect($roles, [ROLE_ROOT, ROLE_SUPERADMIN, ROLE_DEVELOPER])) {
                self::$userPermissionCache[$uid] = PERMISSION_ALL;
            } else {
                $permissionExpr = gmp_init('0b0', 2);
                if ($rolePermissions['data']) {
                    foreach ($rolePermissions['data'] as $rolePermission) {
                        $permissionExpr = gmp_or($rolePermission, gmp_init($rolePermission['permission'], 2));
                    }
                }
                self::$userPermissionCache[$uid] = '0b' . gmp_strval($permissionExpr, 2);
            }
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