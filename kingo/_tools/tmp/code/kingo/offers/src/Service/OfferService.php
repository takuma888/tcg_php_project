<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/30
 * Time: 上午10:54
 */

namespace Offers\Service;


class OfferService
{
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
            $this->getOfferBaseTable(),
        ];

        $fields = query()::duplicateFields($tables, [], '`');
        $fields = implode(', ', $fields);
        $sqlTpl = "SELECT SQL_CALC_FOUND_ROWS {$fields} FROM {@table.offer_base} ";
        $sqlTpl .= $clauseExpr;
        $query = query($sqlTpl);
        $query->table('{@table.offer_base}', $this->getOfferBaseTable());
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
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getExtInfoById($id)
    {
        $tables = [
            $this->getOfferExtTable(),
        ];

        $fields = query()::duplicateFields($tables, [], '`');
        $fields = implode(', ', $fields);
        $sqlTpl = "SELECT {$fields} FROM {@table.ext} WHERE `id` = :id";
        $query = query($sqlTpl);
        $query->table('{@table.ext}', $this->getOfferExtTable());
        $connection = query()::connectionForRead($tables);
        $sql = $query->getSQLForRead();

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id' => $id,
        ]);
        $data = $stmt->fetch();

        return [
            'data' => $data,
        ];
    }


    /**
     * @return \TCG\MySQL\Table
     */
    private function getOfferBaseTable()
    {
        return table('offer_base');
    }

    /**
     * @return \TCG\MySQL\Table
     */
    private function getOfferExtTable()
    {
        return table('offer_ext');
    }
}