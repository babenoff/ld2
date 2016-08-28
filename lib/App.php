<?php
/**
 * App.php
 * Created by Babenoff at 26.08.16 - 19:21
 */

namespace LD2;


use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use PDO;

class App
{
    /**
     * @var Database
     */
    protected $_pdo;
    /**
     * @var View
     */
    protected $_view;
    /**
     * @var array
     */
    protected $user;
    /**
     * @var array
     */
    protected $player;
    /**
     * @var array
     */
    protected $game;
    /**
     * @var Container
     */
    protected $_container;
    /**
     * @var EventDispatcher
     */
    protected $_eventDispatcher;

    public function __construct()
    {

    }

    public function run(){
        //echo "RUNNED!";
    }

    /**
     * @return Database
     */
    public function getPdo(): Database
    {
        return $this->_pdo;
    }

    /**
     * @param \PDO $pdo
     */
    public function setPdo(\PDO $pdo)
    {
        $this->_pdo = $pdo;
    }

    /**
     * @return View
     */
    public function getView(): View
    {
        return $this->_view;
    }

    /**
     * @param View $view
     */
    public function setView(View $view)
    {
        $this->_view = $view;
    }

    public function connect(){
        global $config;
        $qb = $this->getPdo()->queryBuilder();
        $auserSQL = $qb->select(["sessions"]);
        $sid = new Field("sid");
        $auserSQL->setSelect([new Field("username"), new Field("location_id")]);
        $auserSQL->setWhere(
            new Condition(
                Condition::EQ,
                $sid,
                $_COOKIE[$config["cookie_name"]]
            ));
        $sql = $auserSQL->sql();
        $stmt = $this->getPdo()->prepare($sql);

        return true;
    }

    public function removeLoadedLoc($locId){
        if(isset($this->game[$locId])){
            unset($this->game[$locId]);
        }
    }




    /**
     * @return array
     */
    public function getUser(): array
    {
        return $this->user;
    }

    /**
     * @param array $user
     */
    public function setUser(array $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getPlayer(): array
    {
        return $this->player;
    }

    /**
     * @param array $player
     */
    public function setPlayer(array $player)
    {
        $this->player = $player;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->_container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->_container = $container;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher(): EventDispatcher
    {
        return $this->_eventDispatcher;
    }

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }
}