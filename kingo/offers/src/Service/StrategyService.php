<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/30
 * Time: 下午1:37
 */


namespace Offers\Service;


class StrategyService
{

    /**
     * @param $id
     * @param bool $withExt
     * @return array
     * @throws \Exception
     */
    public function getInfoById($id, $withExt = false)
    {
        $baseData = $this->getBaseInfoById($id);
        $extData = [];
        if ($withExt) {
            $extData = $this->extSelectMany("`strategy_id` = :strategy_id", [
                ':strategy_id' => $id,
            ]);
        }
        return [
            'base' => $baseData['data'],
            'ext' => $extData['data'],
        ];
    }


    /**
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getBaseInfoById($id)
    {
        $tables = [
            $this->getStrategyBaseTable(),
        ];

        $fields = query()::duplicateFields($tables, [], '`');
        $fields = implode(', ', $fields);

        $sqlTpl = "SELECT {$fields} FROM {@table.strategy_base} as `base`";
        $sqlTpl .= " WHERE `base`.`id` = :id";
        $query = query($sqlTpl);
        $query->table('{@table.strategy_base}', $this->getStrategyBaseTable())
            ->setParameter(':id', $id);
        $connection = query()::connectionForRead($tables);
        $sql = $query->getSQLForRead([
            '{@table.strategy_base}' => ['id' => $id],
        ]);
        $stmt = $connection->prepare($sql);
        $stmt->execute($query->getParameters());
        $data = $stmt->fetch();
        return [
            'data' => $data
        ];
    }

    /**
     * @param $baseFields
     * @throws \Exception
     */
    public function baseCreate($baseFields)
    {
        $sql = "INSERT INTO {@table.strategy_base} ";
        $parameters = [];
        $fields = [];
        foreach ($baseFields as $field => $value) {
            $field = trim($field, '`');
            $parameters[":{$field}"] = $value;
            $fields[] = "`{$field}`";
        }
        $sql .= '(' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($parameters)) . ')';
        $query = query($sql)
            ->table('{@table.strategy_base}', $this->getStrategyBaseTable())
            ->setParameters($parameters);

        $sql = $query->getSQLForWrite([
            '{@table.strategy_base}' => $baseFields
        ]);
        $connection = query()::connectionForWrite([$this->getStrategyBaseTable()]);
        $stmt = $connection->prepare($sql);
        $stmt->execute($parameters);
    }

    /**
     * @param $id
     * @param $baseFields
     * @throws \Exception
     */
    public function baseUpdate($id, $baseFields)
    {
        $sql = "UPDATE {@table.strategy_base} SET ";
        $parameters = [
            ':id' => $id,
        ];
        $sets = [];
        foreach ($baseFields as $field => $value) {
            $field = trim($field, '`');
            $parameters[":{$field}"] = $value;
            $sets[] = "`{$field}` = :{$field}";
        }
        $sql .= implode(', ', $sets) . ' WHERE `id` = :id';
        $query = query($sql)
            ->table('{@table.strategy_base}', $this->getStrategyBaseTable())
            ->setParameters($parameters);

        $sql = $query->getSQLForWrite([
            '{@table.strategy_base}' => $baseFields
        ]);
        $connection = query()::connectionForWrite([$this->getStrategyBaseTable()]);
        $stmt = $connection->prepare($sql);
        $stmt->execute($parameters);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function removeStrategyById($id)
    {
        $tables = [
            $this->getStrategyBaseTable(),
            $this->getStrategyExtTable()
        ];
        $conn = query()::connectionForWrite($tables);
        $supportTransaction = query()::supportTransaction($tables);
        if ($supportTransaction) {
            $conn->beginTransaction();
        }
        try {
            // delete base
            $sql = "DELETE FROM {@table.base} WHERE `id` = :id";
            $query = query($sql)->table('{@table.base}', $this->getStrategyBaseTable())
                ->setParameter(':id', $id);
            $sql = $query->getSQLForWrite([
                '{@table.base}' => [
                    'id' => $id,
                ]
            ]);
            $stmt = $conn->prepare($sql);
            $stmt->execute($query->getParameters());
            // delete ext
            $sql = "DELETE FROM {@table.ext} WHERE `strategy_id` = :id";
            $query = query($sql)->table('{@table.ext}', $this->getStrategyExtTable())
                ->setParameter(':id', $id);
            $sql = $query->getSQLForWrite([
                '{@table.ext}' => [
                    'strategy_id' => $id,
                ]
            ]);
            $stmt = $conn->prepare($sql);
            $stmt->execute($query->getParameters());
            if ($supportTransaction) {
                $conn->commit();
            }
        } catch (\Exception $e) {
            if ($supportTransaction) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function removeStrategyExtById($id)
    {
        $tables = [
            $this->getStrategyExtTable(),
        ];
        $conn = query()::connectionForWrite($tables);
        // delete ext
        $sql = "DELETE FROM {@table.ext} WHERE `id` = :id";
        $query = query($sql)->table('{@table.ext}', $this->getStrategyExtTable())
            ->setParameter(':id', $id);
        $sql = $query->getSQLForWrite([
            '{@table.ext}' => [
                'id' => $id,
            ]
        ]);
        $stmt = $conn->prepare($sql);
        $stmt->execute($query->getParameters());
    }


    /**
     * @param $extFields
     * @throws \Exception
     */
    public function extCreate($extFields)
    {
        $sql = "INSERT INTO {@table.ext} ";
        $parameters = [];
        $fields = [];
        foreach ($extFields as $field => $value) {
            $field = trim($field, '`');
            $parameters[":{$field}"] = $value;
            $fields[] = "`{$field}`";
        }
        $sql .= '(' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($parameters)) . ')';
        $query = query($sql)
            ->table('{@table.ext}', $this->getStrategyExtTable())
            ->setParameters($parameters);

        $sql = $query->getSQLForWrite([
            '{@table.ext}' => $extFields
        ]);
        $connection = query()::connectionForWrite([$this->getStrategyExtTable()]);
        $stmt = $connection->prepare($sql);
        $stmt->execute($parameters);
    }

    /**
     * @param $clauseExpr
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function extSelectMany($clauseExpr, array $params = [])
    {
        $clauseExpr = trim($clauseExpr);
        if (!$clauseExpr) {
            $clauseExpr = 'WHERE 1';
        }
        if (strtoupper(substr($clauseExpr, 0, 6)) !== 'WHERE ') {
            $clauseExpr = 'WHERE ' . $clauseExpr;
        }
        $tables = [
            $this->getStrategyExtTable(),
        ];

        $fields = query()::duplicateFields($tables, [], '`');
        $fields = implode(', ', $fields);
        $sqlTpl = "SELECT SQL_CALC_FOUND_ROWS {$fields} FROM {@table.strategy_ext}";
        $sqlTpl .= $clauseExpr;
        $query = query($sqlTpl);
        $query->table('{@table.strategy_ext}', $this->getStrategyExtTable());
        $connection = query()::connectionForRead($tables);
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
    public function baseSelectMany($clauseExpr, array $params = [])
    {
        $clauseExpr = trim($clauseExpr);
        if (!$clauseExpr) {
            $clauseExpr = 'WHERE 1';
        }
        if (strtoupper(substr($clauseExpr, 0, 6)) !== 'WHERE ') {
            $clauseExpr = 'WHERE ' . $clauseExpr;
        }
        $tables = [
            $this->getStrategyBaseTable(),
        ];

        $fields = query()::duplicateFields($tables, [], '`');
        $fields = implode(', ', $fields);
        $sqlTpl = "SELECT SQL_CALC_FOUND_ROWS {$fields} FROM {@table.strategy_base}";
        $sqlTpl .= $clauseExpr;
        $query = query($sqlTpl);
        $query->table('{@table.strategy_base}', $this->getStrategyBaseTable());
        $connection = query()::connectionForRead($tables);
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
     * @return \TCG\MySQL\Table
     */
    private function getStrategyBaseTable()
    {
        return table('strategy_base');
    }

    /**
     * @return \TCG\MySQL\Table
     */
    private function getStrategyExtTable()
    {
        return table('strategy_ext');
    }
}