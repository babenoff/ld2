<?php

/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * IGameManager.php at ld2.my
 * Created by Babenoff at 29.08.16 - 8:27
 */
namespace LD2\Manager;

use LD2\Exception\GameException;
use LD2\Exception\LoadLocException;
use LD2\Exportable;

interface GameManager extends Exportable
{
    /**
     * @param string $startLoc
     * @return void
     */
    public function loadGame(string $startLoc);

    public function loadLoc(string $locId): GameManager;

    /**
     * @param string|null $locId
     * @return array
     */
    public function toArray($locId = null);

    /**
     * @param string $locId
     * @return array
     * @throws LoadLocException
     */
    public function getObjects(string $locId):array;

    /**
     * @param string $locId
     * @return array
     * @throws LoadLocException
     */
    public function getRespawns(string $locId):array;

    /**
     * @param string $locId
     * @return array
     * @throws LoadLocException
     */
    public function getTimers(string $locId):array;

    /**
     * @param string $locId
     * @return boolean
     * @throws LoadLocException
     */
    public function hasLoaded(string $locId):bool;

    /**
     * Добавление объекта в локацию
     * @param string $lId
     * @param string $objId
     * @param array $obj
     * @throws LoadLocException
     * @return GameManager
     */
    public function addObject(string $lId, string $objId, array $obj):GameManager;

    /**
     * Добавление таймера в локацию
     * @param string $lId
     * @param string $tId
     * @param array $timer
     * @throws LoadLocException
     * @return GameManager
     */
    public function addTimer(string $lId, string $tId, array $timer):GameManager;

    /**
     * Добавление респавна
     * @param string $lId
     * @param string $rId
     * @param array $respawn
     * @throws LoadLocException
     * @return GameManager
     */
    public function addRespawn(string $lId, string $rId, array $respawn):GameManager;

    /**
     * @param string $lId
     * @param string $objId
     * @throws LoadLocException
     * @return GameManager
     */
    public function removeObject(string $lId, string $objId):GameManager;

    /**
     * @param string $lId
     * @param string $tId
     * @throws LoadLocException
     * @return GameManager
     */
    public function removeTimer(string $lId, string $tId):GameManager;

    /**
     * @param string $lId
     * @param string $rId
     * @throws LoadLocException
     * @return GameManager
     */
    public function removeRespawn(string $lId, string $rId):GameManager;

    /**
     * @param string $lId
     * @param string $oId
     * @throws GameException
     * @throws LoadLocException
     * @return array
     */
    public function getObject(string $lId, string $oId):array;

    /**
     * @param string $lId
     * @param string $tId
     * @throws GameException
     * @throws LoadLocException
     * @return array
     */
    public function getTimer(string $lId, string $tId):array;

    /**
     * @param string $lId
     * @param string $rId
     * @throws GameException
     * @throws LoadLocException
     * @return array
     */
    public function getRespawn(string $lId, string $rId):array;

    /**
     * @param string $lId
     * @throws LoadLocException
     * @return GameManager
     */
    public function clearObjects(string $lId):GameManager;

    /**
     * @param string $lId
     * @throws LoadLocException
     * @return GameManager
     */
    public function clearTimers(string $lId):GameManager;

    /**
     * @param string $lId
     * @throws LoadLocException
     * @return GameManager
     */
    public function clearRespawns(string $lId):GameManager;

    /**
     * @param string $lId
     * @param string $oId
     * @throws LoadLocException
     * @return bool
     */
    public function objectExists(string $lId, string $oId):bool;

    /**
     * @param string $lId
     * @param string $tId
     * @return bool
     * @internal param string $oId
     * @throws LoadLocException
     */
    public function timerExists(string $lId, string $tId):bool;

    /**
     * @param string $lId
     * @param string $rId
     * @return bool
     * @internal param string $oId
     * @throws LoadLocException
     */
    public function respawnExists(string $lId, string $rId):bool;

    public function saveGame():void;
}