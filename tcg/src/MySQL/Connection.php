<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午12:33
 */

namespace TCG\MySQL;

use PDO;

class Connection extends PDO
{

    protected $transactionCount = 0;

    public function beginTransaction()
    {
        if (!$this->inTransaction()) {
            parent::beginTransaction();
        }
        $this->transactionCount += 1;
    }


    public function rollBack()
    {
        if ($this->inTransaction()) {
            parent::rollBack();
        }
        $this->transactionCount = 0;
    }

    public function commit()
    {
        if ($this->inTransaction()) {
            $this->transactionCount -= 1;
        }
        if ($this->transactionCount <= 0) {
            parent::commit();
        }
    }
}