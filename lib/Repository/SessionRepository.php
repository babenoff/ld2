<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


use LD2\QueryBuilder\Additional\AndOp;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use LD2\QueryBuilder\Additional\SqlFunction;

class SessionRepository extends BaseRepository implements \SessionHandlerInterface, ISessionRepository
{

    protected function getConstraintColumns():array
    {
        return ["sid", "username"];
    }

    protected function getUpdateColumnsClackList():array
    {
        return [];
    }

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data):bool
    {
        return false;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data):bool
    {
        return $this->_update($data, new Condition(Condition::EQ, new Field("sid"), $data["sid"]));
    }

    /**
     * @param array $data
     * @return bool
     */
    public function remove(array $data):bool
    {
        $sql = $this->getPdo()->queryBuilder()->delete($this->getTables());
        $sql->setWhere(new Condition(Condition::EQ, new Field("sid"), $data["sid"]));
        $q = $this->getPdo()->prepare($sql->sql());
        $res = $q->execute($sql->parameters());
        return ($res) ? true : false;
    }

    /**
     * @param string $sid
     * @return array
     */
    public function findBySid(string $sid):array
    {
        $sql = $this->getPdo()->queryBuilder()->select($this->getTables());
        $sql->setSelect([new Field("session_data", 0, "sd")]);
        $sql->setWhere(new AndOp([
            new Condition(Condition::EQ, new Field("sid"), $sid),
            new Condition(Condition::RT,  new Field("touched"), date('Y-m-d H:i:s'))
        ]));
        $q = $this->getPdo()->prepare($sql->sql());
        $q->execute($sql->parameters());
        $session = $q->fetchAll(\PDO::FETCH_ASSOC);
        return (count($session) > 0) ? $session[0] : [];
    }

    /**
     * @return int
     */
    public function getCountSessions():int
    {
        $sql = "SELECT count(*) FROM sessions";
        $q = $this->getPdo()->prepare($sql);
        $q->execute();
        $res = $q->fetchAll(\PDO::FETCH_BOTH);

        return (is_array($res)) ? $res[0] : 0;
    }

    public function read($sid)
    {
        $session = $this->findBySid($sid);

        return (isset($session["sd"])) ? $session["sd"] : "";
    }


    public function close()
    {
        return true;
    }

    public function destroy($sid)
    {
        $data = [
            "sid" => $sid
        ];
        return $this->remove($data);
    }

    public function write($sid, $sessionData)
    {
        $date = date('Y-m-d H:i:s');
        $sql = "REPLACE INTO sessions SET sid=:sid, touched=:touched,session_data=:sd";
        $q = $this->getPdo()->prepare($sql);
        $q->bindValue(":sid", $sid);
        $q->bindValue(":touched", date('Y-m-d H:i:s',strtotime($date.' + 5 minutes')));
        $q->bindValue(":sd", $sessionData);
        $res = $q->execute();
        return ($res) ? true : false;
    }

    /**
     * Cleanup old sessions
     * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
     * @param int $maxlifetime <p>
     * Sessions that have not updated for
     * the last maxlifetime seconds will be removed.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function gc($maxlifetime)
    {
        $sql = "DELETE FROM sessions WHERE ((UNIX_TIMESTAMP(touched) + ".$maxlifetime.") < UNIX_TIMESTAMP())";
        $q = $this->getPdo()->prepare($sql);
        $res = $q->execute();
        return ($res) ? true : false;
    }

    /**
     * Initialize session
     * @link http://php.net/manual/en/sessionhandlerinterface.open.php
     * @param string $save_path The path where to store/retrieve the session.
     * @param string $name The session name.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function open($save_path, $name)
    {
        return true;
    }
}