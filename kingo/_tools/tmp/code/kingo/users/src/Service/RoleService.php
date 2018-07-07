<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/15
 * Time: 下午2:08
 */


namespace Users\Service;


use Users\MySQL\Table\RolePermissionTable;
use Users\MySQL\Table\UserRoleTable;

class RoleService
{

    /**
     * @param $id
     * @param array $roleFields
     * @return array
     * @throws \Exception
     */
    public function updateRole($id, array $roleFields)
    {
        $tables = [
            $this->getRolePermissionTable(),
        ];
        $connection = query()::connectionForWrite($tables);
        $sql = "UPDATE {@table.role_permission} SET ";
        $sets = [];
        $params = [];
        foreach ($roleFields as $field => $value) {
            $field = trim($field, '`');
            $sets[] = "`{$field}` = :{$field}";
            $params[":{$field}"] = $value;
        }
        $sql .= implode(', ', $sets) . ' WHERE `id` = :old_id';
        $params[':old_id'] = $id;
        $query = query($sql)->table('{@table.role_permission}', $this->getRolePermissionTable())
            ->setParameters($params);
        $authFields['id'] = $id;
        $sql = $query->getSQLForWrite([
            '{@table.role_permission}' => $authFields,
        ]);
        $stmt = $connection->prepare($sql);
        $stmt->execute($query->getParameters());
        return $this->getRoleInfoById($id);
    }


    /**
     * @param array $roleFields
     * @return array
     * @throws \Exception
     */
    public function createRole(array $roleFields)
    {
        $tables = [
            $this->getRolePermissionTable(),
        ];
        $sql = "INSERT INTO {@table} ";
        $parameters = [];
        $fields = [];
        foreach ($roleFields as $field => $value) {
            $field = trim($field, '`');
            $parameters[":{$field}"] = $value;
            $fields[] = "`{$field}`";
        }
        $sql .= '(' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($parameters)) . ')';
        $query = query($sql)
            ->table('{@table}', $this->getRolePermissionTable())
            ->setParameters($parameters);

        $sql = $query->getSQLForWrite([
            '{@table}' => $roleFields
        ]);
        $connection = query()::connectionForWrite($tables);
        $stmt = $connection->prepare($sql);
        $stmt->execute($parameters);
        $id = $connection->lastInsertId();
        return $this->getRoleInfoById($id);
    }

    /**
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getRoleInfoById($id)
    {
        $tables = [
            $this->getRolePermissionTable(),
        ];

        $sqlTpl = "SELECT * FROM {@table.role_permission} as `auth`";
        $sqlTpl .= " WHERE `auth`.`id` = :id";
        $query = query($sqlTpl);
        $query->table('{@table.role_permission}', $this->getRolePermissionTable())
            ->setParameter(':id', $id);
        $connection = query()::connectionForRead($tables);
        $sql = $query->getSQLForRead([
            '{@table.role_permission}' => ['id' => $id],
        ]);

        $stmt = $connection->prepare($sql);
        $stmt->execute($query->getParameters());
        $data = $stmt->fetch();
        return [
            'data' => $data,
        ];
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function deleteOneById($id)
    {
        $tables = [
            $this->getRolePermissionTable(),
            $this->getUserRoleTable(),
        ];
        $supportTransaction = query()::supportTransaction($tables);
        $connection = query()::connectionForWrite($tables);
        if ($supportTransaction) {
            $connection->beginTransaction();
        }
        try {
            // 删除 role_permission
            $query = query('DELETE FROM {@table} WHERE `id` = :id');
            $query->table('{@table}', $this->getRolePermissionTable())
                ->setParameter(':id', $id);
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute($query->getParameters());
            // 删除 user_role
            $query = query('DELETE FROM {@table} WHERE `rid` = :id');
            $query->table('{@table}', $this->getUserRoleTable())
                ->setParameter(':id', $id);
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute($query->getParameters());
            if ($supportTransaction) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            if ($supportTransaction) {
                $connection->rollBack();
            }
            throw $e;
        }

        return true;
    }

    /**
     * @param array $ids
     * @return bool
     * @throws \Exception
     */
    public function deleteManyByIds(array $ids)
    {
        if (!$ids) {
            return false;
        }
        $tables = [
            $this->getRolePermissionTable(),
        ];
        $supportTransaction = query()::supportTransaction($tables);
        $connection = query()::connectionForWrite($tables);
        if ($supportTransaction) {
            $connection->beginTransaction();
        }
        $quoteIds = [];
        $params = [];
        foreach ($ids as $i => $id) {
            $quoteIds[] = ':id_' . $i;
            $params[':id_' . $i] = $id;
        }

        try {
            // 删除 role_permission
            $query = query('DELETE FROM {@table} WHERE `id` in (' . implode(', ', $quoteIds) . ')');
            $query->table('{@table}', $this->getRolePermissionTable())
                ->setParameters($params);
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute($query->getParameters());
            // 删除 user_role
            $query = query('DELETE FROM {@table} WHERE `rid` in (' . implode(', ', $quoteIds) . ')');
            $query->table('{@table}', $this->getUserRoleTable())
                ->setParameters($params);
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute($query->getParameters());
            if ($supportTransaction) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            if ($supportTransaction) {
                $connection->rollBack();
            }
            throw $e;
        }

        return true;
    }

    /**
     * @param $clauseExpr
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function selectMany($clauseExpr, array $params = [])
    {
        $clauseExpr = trim($clauseExpr);
        if (!$clauseExpr) {
            $clauseExpr = 'WHERE 1';
        }
        if (strtoupper(substr($clauseExpr, 0, 6)) !== 'WHERE ') {
            $clauseExpr = 'WHERE ' . $clauseExpr;
        }
        $query = query("SELECT SQL_CALC_FOUND_ROWS * FROM {@table} " . $clauseExpr);
        $query->table('{@table}', $this->getRolePermissionTable())
            ->setParameters($params);
        $connection = $query->getConnectionForRead();
        $sql = $query->getSQLForRead();
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        $stmt = $connection->prepare("SELECT FOUND_ROWS()");
        $stmt->execute();
        $total = $stmt->fetch()['FOUND_ROWS()'];

        return [
            'data' => $data,
            'total' => intval($total),
        ];
    }

    /**
     * @param $clauseExpr
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function selectOne($clauseExpr, array $params = [])
    {
        $clauseExpr = trim($clauseExpr);
        if (!$clauseExpr) {
            $clauseExpr = 'WHERE 1';
        }
        if (strtoupper(substr($clauseExpr, 0, 6)) !== 'WHERE ') {
            $clauseExpr = 'WHERE ' . $clauseExpr;
        }
        $query = query("SELECT * FROM {@table} " . $clauseExpr);
        $query->table('{@table}', $this->getRolePermissionTable())
            ->setParameters($params);
        $connection = $query->getConnectionForRead();
        $sql = $query->getSQLForRead();

        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetch();
        return [
            'data' => $data,
        ];
    }


    /**
     * @return RolePermissionTable
     */
    private function getRolePermissionTable()
    {
        return table('role_permission');
    }

    /**
     * @return UserRoleTable
     */
    private function getUserRoleTable()
    {
        return table('user_role');
    }
}