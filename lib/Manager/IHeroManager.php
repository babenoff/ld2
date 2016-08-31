<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Manager;


/**
 * Interface IHeroManager
 * @package LD2\Manager
 */
interface IHeroManager
{
    /**
     * @param string $title
     * @return void
     */
    public function loadHero($title);
}