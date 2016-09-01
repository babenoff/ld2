<?php
/**
 * App.php
 * Created by Babenoff at 26.08.16 - 19:21
 */

namespace LD2;


use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use PDO;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class App
{
    const ROOT = ROOT;
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
     * @var ContainerBuilder
     */
    protected $_container;

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
        return $this->_container->get("pdo");
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
        return false;
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
     * @return ContainerBuilder
     */
    public function getContainer(): ContainerBuilder
    {
        return $this->_container;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function setContainer(ContainerBuilder $container)
    {
        $this->_container = $container;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher(): EventDispatcher
    {
        return $this->getContainer()->get("evd");
    }

}