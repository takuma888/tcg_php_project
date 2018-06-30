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
        $id = null;
        $connection = query()::connectionForWrite([$this->getStrategyBaseTable()]);
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