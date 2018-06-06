<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午6:38
 */

namespace Users\MySQL\Table;


use TCG\MySQL\Query;
use TCG\MySQL\Statement;
use TCG\MySQL\Table;

class SessionTable extends Table
{
    protected $tableBaseName = 'auth_session';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `session_id` VARBINARY(128) NOT NULL PRIMARY KEY, 
  `session_data` BLOB NOT NULL, 
  `session_lifetime` MEDIUMINT NOT NULL, 
  `session_time` INTEGER UNSIGNED NOT NULL
) ENGINE = InnoDB COLLATE utf8_bin;
SQL;

    public function getTableFields()
    {
        return [

        ];
    }


    /**
     * No locking is done. This means sessions are prone to loss of data due to
     * race conditions of concurrent requests to the same session. The last session
     * write will win in this case. It might be useful when you implement your own
     * logic to deal with this like an optimistic approach.
     */
    const LOCK_NONE = 0;
    /**
     * Creates an application-level lock on a session. The disadvantage is that the
     * lock is not enforced by the database and thus other, unaware parts of the
     * application could still concurrently modify the session. The advantage is it
     * does not require a transaction.
     * This mode is not available for SQLite and not yet implemented for oci and sqlsrv.
     */
    const LOCK_ADVISORY = 1;
    /**
     * Issues a real row lock. Since it uses a transaction between opening and
     * closing a session, you have to be careful when you use same database connection
     * that you also use for your application logic. This mode is the default because
     * it's the only reliable solution across DBMSs.
     */
    const LOCK_TRANSACTIONAL = 2;
    /**
     * @var int The strategy for locking, see constants
     */
    private $lockMode = self::LOCK_TRANSACTIONAL;
    /**
     * It's an array to support multiple reads before closing which is manual, non-standard usage.
     *
     * @var Statement[] An array of statements to release advisory locks
     */
    private $unlockStatements = [];

    /**
     * @var bool Whether a transaction is active
     */
    private $inTransaction = false;
    /**
     * @var bool Whether gc() has been called
     */
    private $gcCalled = false;
    /**
     * @var bool True when the current session exists but expired according to session.gc_maxlifetime
     */
    private $sessionExpired = false;


    /**
     * @param $sessionId
     * @return bool|string
     * @throws \Exception
     */
    public function readSession($sessionId)
    {
        try {
            $this->sessionExpired = false;
            if (self::LOCK_ADVISORY === $this->lockMode) {
                $this->unlockStatements[] = $this->doAdvisoryLock($sessionId);
            }
            $pdo = $this->getWriteServer()->connect();
            if (self::LOCK_TRANSACTIONAL === $this->lockMode) {
                $this->beginTransaction();
                $selectSql = 'SELECT `session_data`, `session_lifetime`, `session_time` FROM {@table} WHERE `session_id` = :id FOR UPDATE';
            } else {
                $selectSql = 'SELECT `session_data`, `session_lifetime`, `session_time` FROM {@table} WHERE `session_id` = :id';
            }
            $query = new Query();
            $query->sql($selectSql)
                ->table('{@table}', $this)
                ->setParameter(':id', $sessionId);
            $selectStmt = $pdo->prepare($query->getSQLForWrite([
                'session_id' => $sessionId,
            ]));
            $selectStmt->execute($query->getParameters());
            $sessionRows = $selectStmt->fetchAll(\PDO::FETCH_NUM);
            if ($sessionRows) {
                if ($sessionRows[0][1] + $sessionRows[0][2] < time()) {
                    $this->sessionExpired = true;
                    return '';
                }
                return is_resource($sessionRows[0][0]) ? stream_get_contents($sessionRows[0][0]) : $sessionRows[0][0];
            }

            if (self::LOCK_TRANSACTIONAL === $this->lockMode) {
                // Exclusive-reading of non-existent rows does not block, so we need to do an insert to block
                // until other connections to the session are committed.
                try {
                    $query = new Query();
                    $query->sql('INSERT INTO {@table} (`session_id`, `session_data`, `session_lifetime`, `session_time`) VALUES (:id, :data, :lifetime, :time)')
                        ->table('{@table}', $this)
                        ->setParameters([
                            ':id' => $sessionId,
                            ':data' => '',
                            ':lifetime' => 0,
                            ':time' => time(),
                        ]);
                    $sql = $query->getSQLForWrite([
                        'session_id' => $sessionId,
                    ]);
                    $parameters = $query->getParameters();
                    $pdo = $this->getWriteServer()->connect();
                    $insertStmt = $pdo->prepare($sql);
                    $insertStmt->execute($parameters);
                } catch (\PDOException $e) {
                    // Catch duplicate key error because other connection created the session already.
                    // It would only not be the case when the other connection destroyed the session.
                    if (0 === strpos($e->getCode(), '23')) {
                        // Retrieve finished session data written by concurrent connection. SELECT
                        // FOR UPDATE is necessary to avoid deadlock of connection that starts reading
                        // before we write (transform intention to real lock).
                        $selectStmt->execute();
                        $sessionRows = $selectStmt->fetchAll(\PDO::FETCH_NUM);
                        if ($sessionRows) {
                            return is_resource($sessionRows[0][0]) ? stream_get_contents($sessionRows[0][0]) : $sessionRows[0][0];
                        }
                        return '';
                    }
                    throw $e;
                }
            }
            return '';

        } catch (\PDOException $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * @param $sessionId
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function writeSession($sessionId, $data)
    {
        $maxlifetime = (int) ini_get('session.gc_maxlifetime');
        $pdo = $this->getWriteServer()->connect();
        try {
            // We use a single MERGE SQL query when supported by the database.
            $mergeSql =<<<SQL
INSERT INTO {@table} (`session_id`, `session_data`, `session_lifetime`, `session_time`) VALUES (:id, :data, :lifetime, :time)
ON DUPLICATE KEY UPDATE 
`session_data` = VALUES(`session_data`), 
`session_lifetime` = VALUES(`session_lifetime`), 
`session_time` = VALUES(`session_time`)
SQL;
            $query = new Query($mergeSql);
            $query->table('{@table}', $this);
            $query->setParameters([
                ':id' => $sessionId,
                ':data' => $data,
                ':lifetime' => $maxlifetime,
                ':time' => time(),
            ]);
            $stmt = $pdo->prepare($query->getSQLForWrite([
                'session_id' => $sessionId,
            ]));
            $stmt->execute($query->getParameters());
        } catch (\PDOException $e) {
            $this->rollback();
            throw $e;
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    public function closeSession()
    {
        $this->commit();
        while ($unlockStmt = array_shift($this->unlockStatements)) {
            $unlockStmt->execute();
        }
        if ($this->gcCalled) {
            $this->gcCalled = false;
            // delete the session records that have expired
            $query = new Query();
            $query->sql("DELETE FROM {@table} WHERE `session_lifetime` + `session_time` < :time")
                ->table('{@table}', $this)
                ->setParameter(':time', time());
            $sql = $query->getSQLForWrite();
            $parameters = $query->getParameters();
            $pdo = $this->getWriteServer()->connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($parameters);
        }
    }

    /**
     * @param $sessionId
     * @throws \Exception
     */
    public function destroySession($sessionId)
    {
        // delete the record associated with this id
        $query = new Query();
        $query->sql("DELETE FROM {@table} WHERE `session_id` = :id")
            ->table('{@table}', $this)
            ->setParameter(':id', $sessionId);
        $sql = $query->getSQLForWrite();
        $parameters = $query->getParameters();
        try {
            $pdo = $this->getWriteServer()->connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($parameters);
        } catch (\PDOException $e) {
            $this->rollback();
            throw $e;
        }

    }

    /**
     *
     */
    public function gcSession()
    {
        $this->gcCalled = true;
    }


    /**
     * Executes an application-level lock on the database.
     * @param $sessionId
     * @return bool|\PDOStatement
     * @throws \Exception
     */
    private function doAdvisoryLock($sessionId)
    {
        $pdo = $this->getReadServer()->connect();
        // should we handle the return value? 0 on timeout, null on error
        // we use a timeout of 50 seconds which is also the default for innodb_lock_wait_timeout
        $query = new Query();
        $query->sql('SELECT GET_LOCK(:key, 50)')
            ->setParameter(':key', $sessionId);
        $sql = $query->getSQLForWrite([
            'session_id' => $sessionId,
        ]);
        $parameters = $query->getParameters();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($parameters);

        $query = new Query();
        $query->sql('DO RELEASE_LOCK(:key)');
        $sql = $query->getSQLForWrite([
            'session_id' => $sessionId,
        ]);
        $releaseStmt = $pdo->prepare($sql);
        $releaseStmt->bindValue(':key', $sessionId, \PDO::PARAM_STR);
        return $releaseStmt;
    }


    /**
     * Returns true when the current session exists but expired according to session.gc_maxlifetime.
     *
     * Can be used to distinguish between a new session and one that expired due to inactivity.
     *
     * @return bool Whether current session expired
     */
    protected function isSessionExpired()
    {
        return $this->sessionExpired;
    }


    /**
     * Helper method to begin a transaction.
     *
     * Since SQLite does not support row level locks, we have to acquire a reserved lock
     * on the database immediately. Because of https://bugs.php.net/42766 we have to create
     * such a transaction manually which also means we cannot use PDO::commit or
     * PDO::rollback or PDO::inTransaction for SQLite.
     *
     * Also MySQLs default isolation, REPEATABLE READ, causes deadlock for different sessions
     * due to http://www.mysqlperformanceblog.com/2013/12/12/one-more-innodb-gap-lock-to-avoid/ .
     * So we change it to READ COMMITTED.
     */
    private function beginTransaction()
    {
        if (!$this->inTransaction) {
            $pdo = $this->getWriteServer()->connect();
            $pdo->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $pdo->beginTransaction();
            $this->inTransaction = true;
        }
    }


    /**
     * Helper method to commit a transaction.
     */
    private function commit()
    {
        if ($this->inTransaction) {
            try {
                // commit read-write transaction which also releases the lock
                $pdo = $this->getWriteServer()->connect();
                $pdo->commit();
                $this->inTransaction = false;
            } catch (\PDOException $e) {
                $this->rollback();
                throw $e;
            }
        }
    }

    /**
     * Helper method to rollback a transaction.
     */
    public function rollBack()
    {
        // We only need to rollback if we are in a transaction. Otherwise the resulting
        // error would hide the real problem why rollback was called. We might not be
        // in a transaction when not using the transactional locking behavior or when
        // two callbacks (e.g. destroy and write) are invoked that both fail.
        if ($this->inTransaction) {
            $pdo = $this->getWriteServer()->connect();
            $pdo->rollBack();
            $this->inTransaction = false;
        }
    }
}