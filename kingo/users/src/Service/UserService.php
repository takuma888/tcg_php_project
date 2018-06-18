<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午4:44
 */

namespace Users\Service;


use Users\MySQL\Table\UserAuthTable;
use Users\MySQL\Table\UserProfileTable;
use Users\MySQL\Table\UserRoleTable;

class UserService
{

    /**
     * @param $id
     * @param array $authFields
     * @param array $profileFields
     * @return array
     * @throws \Exception
     */
    public function updateUser($id, array $authFields = [], array $profileFields = [])
    {
        $tables = [];
        if ($authFields) {
            $tables[] = $this->getAuthTable();
        }
        if ($profileFields) {
            $tables[] = $this->getProfileTable();
        }
        if (!$tables) {
            throw new \Exception("到底要更新什么表的什么字段");
        }
        $supportTransaction = query()::supportTransaction($tables);
        $connection = query()::connectionForWrite($tables);
        if ($supportTransaction) {
            $connection->beginTransaction();
        }
        try {
            if ($authFields) {
                $sql = "UPDATE {@table.user_auth} SET ";
                $sets = [];
                $params = [];
                foreach ($authFields as $field => $value) {
                    $field = trim($field, '`');
                    $sets[] = "`{$field}` = :{$field}";
                    $params[":{$field}"] = $value;
                }
                $sql .= implode(', ', $sets) . ' WHERE `id` = :id';
                $params[':id'] = $id;
                $query = query($sql)->table('{@table.user_auth}', $this->getAuthTable())
                    ->setParameters($params);
                $authFields['id'] = $id;
                $sql = $query->getSQLForWrite([
                    '{@table.user_auth}' => $authFields,
                ]);
                $stmt = $connection->prepare($sql);
                $stmt->execute($query->getParameters());
            }

            if ($profileFields) {
                $sql = "UPDATE {@table.user_profile} SET ";
                $sets = [];
                $params = [];
                foreach ($profileFields as $field => $value) {
                    $field = trim($field, '`');
                    $sets[] = "`{$field}` = :{$field}";
                    $params[":{$field}"] = $value;
                }
                $sql .= implode(', ', $sets) . ' WHERE `id` = :id';
                $params[':id'] = $id;
                $query = query($sql)->table('{@table.user_profile}', $this->getProfileTable())
                    ->setParameters($params);
                $authFields['id'] = $id;
                $sql = $query->getSQLForWrite([
                    '{@table.user_profile}' => $authFields,
                ]);
                $stmt = $connection->prepare($sql);
                $stmt->execute($query->getParameters());
            }

            if ($supportTransaction) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            if ($supportTransaction) {
                $connection->rollBack();
            }
            throw $e;
        }
        if ($profileFields) {
            return $this->getUserInfoById($id, true);
        } else {
            return $this->getUserInfoById($id, false);
        }
    }

    /**
     * @param array $authFields
     * @param array $profileFields
     * @return array
     * @throws \Exception
     */
    public function createUser(array $authFields, array $profileFields = [])
    {
        $tables = [
            $this->getAuthTable(),
        ];
        if ($profileFields) {
            $tables[] = $this->getProfileTable();
        }
        $authSQL = "INSERT INTO {@table.user_auth} ";
        $parameters = [];
        $fields = [];
        foreach ($authFields as $field => $value) {
            $field = trim($field, '`');
            $parameters[":{$field}"] = $value;
            $fields[] = "`{$field}`";
        }
        $authSQL .= '(' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($parameters)) . ')';
        $query = query($authSQL)
            ->table('{@table.user_auth}', $this->getAuthTable())
            ->setParameters($parameters);

        $sql = $query->getSQLForWrite([
            '{@table.user_auth}' => $authFields
        ]);
        $id = null;
        $connection = query()::connectionForWrite($tables);
        $supportTransaction = query()::supportTransaction($tables);
        if ($profileFields) {
            if ($supportTransaction) {
                $connection->beginTransaction();
            }
            try {
                $stmt = $connection->prepare($sql);
                $stmt->execute($parameters);
                $id = $connection->lastInsertId();

                // 创建profile
                $profileFields['id'] = $id;
                $profileSQL = "INSERT INTO {@table.user_profile} ";
                $parameters = [];
                $fields = [];
                foreach ($profileFields as $field => $value) {
                    $field = trim($field, '`');
                    $parameters[":{$field}"] = $value;
                    $fields[] = "`{$field}`";
                }
                $profileSQL .= '(' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($parameters)) . ')';
                $query = query($profileSQL)
                    ->table('{@table.user_profile}', $this->getProfileTable())
                    ->setParameters($parameters);

                $sql = $query->getSQLForWrite([
                    '{@table.user_profile}' => $profileFields
                ]);

                $stmt = $connection->prepare($sql);
                $stmt->execute($parameters);
                if ($supportTransaction) {
                    $connection->commit();
                }
            } catch (\Exception $e) {
                if ($supportTransaction) {
                    $connection->rollBack();
                }
                throw $e;
            }
        } else {
            $stmt = $connection->prepare($sql);
            $stmt->execute($parameters);
            $id = $connection->lastInsertId();
        }
        if ($profileFields) {
            return $this->getUserInfoById($id, true);
        } else {
            return $this->getUserInfoById($id, false);
        }
    }

    /**
     * @param $id
     * @param bool $withProfile
     * @return array
     * @throws \Exception
     */
    public function getUserInfoById($id, $withProfile = false)
    {
        $tables = [
            $this->getAuthTable(),
        ];
        if ($withProfile) {
            $tables[] = $this->getProfileTable();
        }

        $sqlTpl = "SELECT * FROM {@table.user_auth} as `auth`";
        if ($withProfile) {
            $sqlTpl .= " LEFT JOIN {@table.user_profile} as `profile` ON `auth`.`id` = `profile`.`id`";
        }
        $sqlTpl .= " WHERE `auth`.`id` = :id";
        $query = query($sqlTpl);
        $query->table('{@table.user_auth}', $this->getAuthTable())
            ->table('{@table.user_profile}', $this->getProfileTable())
            ->setParameter(':id', $id);
        $connection = query()::connectionForRead($tables);
        $sql = $query->getSQLForRead([
            '{@table.user_auth}' => ['id' => $id],
            '{@table.user_profile}' => ['id' => $id],
        ]);

        $stmt = $connection->prepare($sql);
        $stmt->execute($query->getParameters());
        $data = $stmt->fetch();
        return [
            'data' => $data,
        ];
    }

    /**
     * @param $username
     * @param bool $withProfile
     * @return array
     * @throws \Exception
     */
    public function getUserInfoByUsername($username, $withProfile = false)
    {
        $tables = [
            $this->getAuthTable(),
        ];
        if ($withProfile) {
            $tables[] = $this->getProfileTable();
        }

        $sqlTpl = "SELECT * FROM {@table.user_auth} as `auth`";
        if ($withProfile) {
            $sqlTpl .= " LEFT JOIN {@table.user_profile} as `profile` ON `auth`.`id` = `profile`.`id`";
        }
        $sqlTpl .= " WHERE `auth`.`username` = :username";
        $query = query($sqlTpl);
        $query->table('{@table.user_auth}', $this->getAuthTable())
            ->table('{@table.user_profile}', $this->getProfileTable())
            ->setParameter(':username', $username);
        $connection = query()::connectionForRead($tables);
        $sql = $query->getSQLForRead([
            '{@table.user_auth}' => ['username' => $username],
            '{@table.user_profile}' => ['username' => $username],
        ]);

        $stmt = $connection->prepare($sql);
        $stmt->execute($query->getParameters());
        $data = $stmt->fetch();
        return [
            'data' => $data,
        ];
    }

    /**
     * @param $email
     * @param bool $withProfile
     * @return array
     * @throws \Exception
     */
    public function getUserInfoByEmail($email, $withProfile = false)
    {
        $tables = [
            $this->getAuthTable(),
        ];
        if ($withProfile) {
            $tables[] = $this->getProfileTable();
        }

        $sqlTpl = "SELECT * FROM {@table.user_auth} as `auth`";
        if ($withProfile) {
            $sqlTpl .= " LEFT JOIN {@table.user_profile} as `profile` ON `auth`.`id` = `profile`.`id`";
        }
        $sqlTpl .= " WHERE `auth`.`email` = :email";
        $query = query($sqlTpl);
        $query->table('{@table.user_auth}', $this->getAuthTable())
            ->table('{@table.user_profile}', $this->getProfileTable())
            ->setParameter(':email', $email);
        $connection = query()::connectionForRead($tables);
        $sql = $query->getSQLForRead([
            '{@table.user_auth}' => ['email' => $email],
            '{@table.user_profile}' => ['email' => $email],
        ]);

        $stmt = $connection->prepare($sql);
        $stmt->execute($query->getParameters());
        $data = $stmt->fetch();
        return [
            'data' => $data,
        ];
    }

    /**
     * @param $mobile
     * @param bool $withProfile
     * @return array
     * @throws \Exception
     */
    public function getUserInfoByMobile($mobile, $withProfile = false)
    {
        $tables = [
            $this->getAuthTable(),
        ];
        if ($withProfile) {
            $tables[] = $this->getProfileTable();
        }

        $sqlTpl = "SELECT * FROM {@table.user_auth} as `auth`";
        if ($withProfile) {
            $sqlTpl .= " LEFT JOIN {@table.user_profile} as `profile` ON `auth`.`id` = `profile`.`id`";
        }
        $sqlTpl .= " WHERE `auth`.`mobile` = :mobile";
        $query = query($sqlTpl);
        $query->table('{@table.user_auth}', $this->getAuthTable())
            ->table('{@table.user_profile}', $this->getProfileTable())
            ->setParameter(':mobile', $mobile);
        $connection = query()::connectionForRead($tables);
        $sql = $query->getSQLForRead([
            '{@table.user_auth}' => ['mobile' => $mobile],
            '{@table.user_profile}' => ['mobile' => $mobile],
        ]);

        $stmt = $connection->prepare($sql);
        $stmt->execute($query->getParameters());
        $data = $stmt->fetch();
        return [
            'data' => $data,
        ];
    }


    /**
     * @param $clauseExpr
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function authSelectMany($clauseExpr, array $params = [])
    {
        $clauseExpr = trim($clauseExpr);
        if (!$clauseExpr) {
            $clauseExpr = 'WHERE 1';
        }
        if (strtoupper(substr($clauseExpr, 0, 6)) !== 'WHERE ') {
            $clauseExpr = 'WHERE ' . $clauseExpr;
        }
        $query = query("SELECT SQL_CALC_FOUND_ROWS * FROM {@table} " . $clauseExpr);
        $query->table('{@table}', $this->getAuthTable())
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
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function deleteOneById($id)
    {
        $tables = [
            $this->getAuthTable(),
            $this->getProfileTable(),
        ];
        $supportTransaction = query()::supportTransaction($tables);
        $connection = query()::connectionForWrite($tables);
        if ($supportTransaction) {
            $connection->beginTransaction();
        }
        try {
            // 删除 user_auth
            $query = query('DELETE FROM {@table} WHERE `id` = :id');
            $query->table('{@table}', $this->getAuthTable())
                ->setParameter(':id', $id);
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            // 删除 user_profile
            $query = query('DELETE FROM {@table} WHERE `id` = :id');
            $query->table('{@table}', $this->getProfileTable())
                ->setParameter(':id', $id);
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            // 删除 user_role
            $query = query('DELETE FROM {@table} WHERE `uid` = :id');
            $query->table('{@table}', $this->getUserRoleTable())
                ->setParameter(':id', $id);
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute();
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
        $clauseExpr = 'WHERE `id` in (' . implode(', ', $ids) . ')';
        $tables = [
            $this->getAuthTable(),
            $this->getProfileTable(),
        ];
        $supportTransaction = query()::supportTransaction($tables);
        $connection = query()::connectionForWrite($tables);
        if ($supportTransaction) {
            $connection->beginTransaction();
        }
        try {
            // 删除 user_auth
            $query = query('DELETE FROM {@table}' . $clauseExpr);
            $query->table('{@table}', $this->getAuthTable());
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            // 删除 user_profile
            $query = query('DELETE FROM {@table}' . $clauseExpr);
            $query->table('{@table}', $this->getProfileTable());
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            // 删除 user_role
            $query = query('DELETE FROM {@table} WHERE `uid` IN (' . implode(', ', $ids) . ')');
            $query->table('{@table}', $this->getUserRoleTable());
            $sql = $query->getSQLForWrite();
            $stmt = $connection->prepare($sql);
            $stmt->execute();
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
    public function authSelectOne($clauseExpr, array $params = [])
    {
        $clauseExpr = trim($clauseExpr);
        if (!$clauseExpr) {
            $clauseExpr = 'WHERE 1';
        }
        if (strtoupper(substr($clauseExpr, 0, 6)) !== 'WHERE ') {
            $clauseExpr = 'WHERE ' . $clauseExpr;
        }
        $query = query("SELECT * FROM {@table} " . $clauseExpr);
        $query->table('{@table}', $this->getAuthTable())
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
     * @return UserAuthTable
     */
    private function getAuthTable()
    {
        return table('user_auth');
    }

    /**
     * @return UserProfileTable
     */
    private function getProfileTable()
    {
        return table('user_profile');
    }

    /**
     * @return UserRoleTable
     */
    private function getUserRoleTable()
    {
        return table('user_role');
    }
}