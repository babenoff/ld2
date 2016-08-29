<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2;


use LD2\Exception\LoadLocException;
use LD2\Manager\GameManager;

class Ai
{
    /**
     * @var GameManager
     */
    protected $gameManager;

    /**
     * @return GameManager
     */
    public function getGameManager(): GameManager
    {
        return $this->gameManager;
    }

    /**
     * @param GameManager $gameManager
     */
    public function setGameManager(GameManager $gameManager)
    {
        $this->gameManager = $gameManager;
    }

    public function doAi(string $startLoc)
    {

    }

    private function _load(string $startLoc)
    {
        try {
            $this->getGameManager()->loadLoc($startLoc);

        } catch (LoadLocException $e) {
            throw $e;
        }
    }


}