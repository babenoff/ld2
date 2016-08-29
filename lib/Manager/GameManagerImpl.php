<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * GameManagerImpl.php at ld2.my
 * Created by Babenoff at 29.08.16 - 8:31
 */

namespace LD2\Manager;


use LD2\Exception\GameException;
use LD2\Exception\LoadLocException;
use LD2\Service\IGameService;
use Symfony\Component\DependencyInjection\Exception\LogicException;

class GameManagerImpl implements GameManager
{
    /**
     * @var IGameService
     */
    protected $gameService;
    /**
     * @var array
     */
    protected $_loadedLocations = [];

    private $_validKeys = [
        "objects",
        "respawns",
        "timers",
        "location_id"
    ];

    /**
     * @param string $locId
     * @return GameManager
     * @internal param array $locData
     */
    public function loadLoc(string $locId):GameManager
    {
        $locData = $this->getGameService()->getLocByLocationId($locId);
        foreach ($locData as $key => $value) {
            if (!is_array($value)) {
                $locData[$key] = unserialize($value);
            }
        }
        $this->_loadedLocations[$locId] = $locData;
        return $this;
    }

    /**
     * @param string|null $locId
     * @return array
     */
    public function toArray($locId = null):array
    {
        if (!is_null($locId)) {
            if ($this->hasLoaded($locId)) {
                return $this->_loadedLocations[$locId];
            } else {
                return [];
            }
        } else {
            return $this->_loadedLocations;
        }
    }

    /**
     * @param string $locId
     * @return array
     * @throws LoadLocException
     */
    public function getObjects(string $locId):array
    {
        $this->_checkLoc($locId);
        return $this->_loadedLocations[$locId]["objects"];
    }

    /**
     * @param string $locId
     * @return array
     * @throws LoadLocException
     */
    public function getRespawns(string $locId):array
    {
        $this->_checkLoc($locId);
        return $this->_loadedLocations[$locId]["respawns"];
    }

    /**
     * @param string $locId
     * @return array
     */
    public function getTimers(string $locId):array
    {
        $this->_checkLoc($locId);
        return $this->_loadedLocations[$locId]["timers"];
    }

    /**
     * @param string $locId
     * @return boolean
     */
    public function hasLoaded(string $locId):bool
    {
        return isset($this->_loadedLocations[$locId]);
    }

    /**
     * @param $locId
     * @param null $key
     * @throws LoadLocException
     * @throws LogicException
     */
    private function _checkLoc($locId, $key = null):void
    {
        if (false === $this->hasLoaded($locId)) {
            throw new LoadLocException($locId);
        }
        if (!is_null($key) and !in_array($key, $this->_validKeys)) {
            throw new \LogicException(sprintf("You used invalid key %s"));
        }
    }

    /**
     * @param string $locId
     * @param string $key
     * @param array|string $params
     * @throws \LogicException
     * @throws LoadLocException
     * @return GameManagerImpl
     */
    private function _setParams(string $locId, string $key, $params):GameManagerImpl
    {
        $this->_checkLoc($locId, $key);
        if (!is_array($params)) {
            $params = unserialize($params);
        }
        $this->_loadedLocations[$locId][$key] = $params;
        return $this;
    }

    /**
     * @param string $lId
     * @param string $key
     * @param string $pId
     * @param array $p
     * @throws LoadLocException
     * @throws LogicException
     * @return $this|GameManagerImpl
     */
    private function _addParam(string $lId, string $key, string $pId, array $p):GameManagerImpl
    {
        $this->_checkLoc($lId, $key);
        $this->_loadedLocations[$lId][$key][$pId] = $p;
        return $this;
    }

    /**
     * @param string $lId
     * @param string $key
     * @param string $pId
     * @throws LoadLocException
     * @throws LogicException
     * @return $this|GameManagerImpl
     */
    private function _removeParam(string $lId, string $key, string $pId):GameManagerImpl
    {
        $this->_checkLoc($lId, $key);
        unset($this->_loadedLocations[$lId][$key][$pId]);
        return $this;
    }

    /**
     * @param string $lId
     * @param string $key
     * @throws LoadLocException
     * @throws LogicException
     * @return GameManagerImpl
     */
    private function _clear(string $lId, string $key):GameManagerImpl
    {
        $this->_checkLoc($lId, $key);
        $this->_loadedLocations[$lId][$key] = [];
        return $this;
    }

    /**
     * @param string $locId
     * @param array|string $objects
     * @return GameManagerImpl
     */
    public function setObjects(string $locId, $objects):GameManagerImpl
    {
        $this->_setParams($locId, "objects", $objects);
        return $this;
    }

    /**
     * @param string $locId
     * @param array|string $respawns
     * @return GameManagerImpl
     */
    public function setRespawns(string $locId, $respawns):GameManagerImpl
    {
        $this->_setParams($locId, "respawns", $respawns);
        return $this;
    }

    /**
     * @param string $locId
     * @param array|string $timers
     * @return GameManagerImpl
     */
    public function setTimers(string $locId, $timers):GameManagerImpl
    {
        $this->_setParams($locId, "timers", $timers);
        return $this;
    }

    /**
     * @return array
     */
    public function export():array
    {
        $export = [];
        foreach ($this->_loadedLocations as $locId => $loc) {
            $export[$locId] = [];
            foreach ($loc as $key => $val) {
                $export[$locId][$key] = serialize($val);
            }
        }
        return $export;
    }

    /**
     * Добавление объекта в локацию
     * @param string $lId
     * @param string $objId
     * @param array $obj
     * @throws LoadLocException
     * @return GameManager
     */
    public function addObject(string $lId, string $objId, array $obj):GameManager
    {
        $this->_addParam($lId, "objects", $objId, $obj);
        return $this;
    }

    /**
     * Добавление таймера в локацию
     * @param string $lId
     * @param string $tId
     * @param array $timer
     * @return GameManager
     */
    public function addTimer(string $lId, string $tId, array $timer):GameManager
    {
        $this->_addParam($lId, "timers", $tId, $timer);
        return $this;
    }

    /**
     * Добавление респавна
     * @param string $lId
     * @param string $rId
     * @param array $respawn
     * @return GameManager
     */
    public function addRespawn(string $lId, string $rId, array $respawn):GameManager
    {
        $this->_addParam($lId, "respawns", $rId, $respawn);
        return $this;
    }

    /**
     * @param string $lId
     * @param string $objId
     * @throws LoadLocException
     * @throws LogicException
     * @return GameManager
     */
    public function removeObject(string $lId, string $objId):GameManager
    {
        $this->_removeParam($lId, "objects", $objId);
        return $this;
    }

    /**
     * @param string $lId
     * @param string $tId
     * @throws LoadLocException
     * @throws \LogicException
     * @return GameManager
     */
    public function removeTimer(string $lId, string $tId):GameManager
    {
        $this->_removeParam($lId, "timers", $tId);
        return $this;
    }

    /**
     * @param string $lId
     * @param string $rId
     * @throws LoadLocException
     * @throws \LogicException
     * @return GameManager
     */
    public function removeRespawn(string $lId, string $rId):GameManager
    {
        $this->_removeParam($lId, "respawns", $rId);
        return $this;
    }

    /**
     * @param string $lId
     * @param string $oId
     * @throws GameException
     * @throws LoadLocException
     * @return array
     */
    public function getObject(string $lId, string $oId):array
    {
        $this->_checkLoc($lId);
        if (false === $this->objectExists($lId, $oId)) {
            throw new GameException(sprintf("Object %s not exists into loc %s", $oId, $lId));
        }
        return $this->getObjects($lId)[$oId];
    }

    /**
     * @param string $lId
     * @param string $tId
     * @throws GameException
     * @throws LoadLocException
     * @return array
     */
    public function getTimer(string $lId, string $tId):array
    {
        $this->_checkLoc($lId);
        if (false === $this->timerExists($lId, $tId)) {
            throw new GameException(sprintf("Timer %s not exists into loc %s", $tId, $lId));
        }
        return $this->getTimers($lId)[$tId];
    }

    /**
     * @param string $lId
     * @param string $rId
     * @throws GameException
     * @throws LoadLocException
     * @return array
     */
    public function getRespawn(string $lId, string $rId):array
    {
        $this->_checkLoc($lId);
        if (false === $this->respawnExists($lId, $rId)) {
            throw new GameException(sprintf("Respawn %s not exists into loc %s", $rId, $lId));
        }
        return $this->getRespawns($lId)[$rId];
    }

    /**
     * @param string $lId
     * @return GameManager
     * @throws LoadLocException
     * @throws \LogicException
     */
    public function clearObjects(string $lId):GameManager
    {
        $this->_clear($lId, "objects");
        return $this;
    }

    /**
     * @param string $lId
     * @return GameManager
     * @throws LoadLocException
     */
    public function clearTimers(string $lId):GameManager
    {
        $this->_clear($lId, "timers");
        return $this;
    }

    /**
     * @param string $lId
     * @return GameManager
     * @throws LoadLocException
     */
    public function clearRespawns(string $lId):GameManager
    {
        $this->_clear($lId, "respawns");
        return $this;
    }

    /**
     * @param string $lId
     * @param string $oId
     * @throws LoadLocException
     * @return bool
     */
    public function objectExists(string $lId, string $oId):bool
    {
        $this->_checkLoc($lId);
        return isset($this->getObjects($lId)[$oId]);
    }

    /**
     * @param string $lId
     * @param string $tId
     * @return bool
     * @internal param string $oId
     * @throws LoadLocException
     */
    public function timerExists(string $lId, string $tId):bool
    {
        $this->_checkLoc($lId);
        return isset($this->getTimers($lId)[$tId]);
    }

    /**
     * @param string $lId
     * @param string $rId
     * @return bool
     * @internal param string $oId
     * @throws LoadLocException
     */
    public function respawnExists(string $lId, string $rId):bool
    {
        $this->_checkLoc($lId);
        return isset($this->getRespawns($lId)[$rId]);
    }

    /**
     * @return IGameService
     */
    public function getGameService(): IGameService
    {
        return $this->gameService;
    }

    /**
     * @param IGameService $gameService
     */
    public function setGameService(IGameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function saveGame():void
    {
        $locs = $this->export();
        foreach ($locs as $locId => $loc) {
            $this->getGameService()->update($locId, $loc);
        }
    }

    /**
     * Загрузка игры в память. Загружаются текущая локация $startLoc и все локации вокруг нее
     * @param string $startLoc
     * @return void
     */
    public function loadGame(string $startLoc)
    {
        if($this->checkLocId($startLoc)) {
            $locIds = $this->getGameService()->getNeighboringLocsIds($startLoc);
            $i = 0;
            do {
                try {
                    //пытаемся загрузиться
                    $this->loadLoc($locIds[$i]);
                    $i++;
                } catch (LoadLocException $e) {
                    //если словили исключение - пропускаем шаг
                    $i++;
                }
            } while ($i < count($locIds));
        } else {
            throw  new LogicException(sprintf("Incorrect locId into startLoc"));
        }
    }

    /**
     * @param $locId
     * @return boolean
     */
    public function checkLocId($locId){
        return (bool)preg_match("/^([a-z0-9]+)_(([0-9]+)x([0-9]+))$/s", $locId);
    }
}