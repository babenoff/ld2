<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


interface IUserRepository extends IRepository
{
    //пользователь
    const BASE = 0 << 1;
    //модераторы
    const M_CHAT = 1 << 1;
    const M_FORUM = 1 << 2;
    //администраторы по функциям
    const C_LOCATIONS = 1 << 3; //локации
    const C_ITEMS = 1 << 4; //предметы
    const C_NPC = 1 << 5; //npc
    const C_GAME = 1 << 6; //игра
    const C_EVENTS = 1 << 7; //квесты
    //редактирование
    const R_PROFESSIONS = 1 << 8; //профессии

    const ADMIN =   self::BASE          |
                    self::C_EVENTS      |
                    self::C_GAME        |
                    self::C_ITEMS       |
                    self::C_LOCATIONS   |
                    self::C_NPC         |
                    self::R_PROFESSIONS |
                    self::M_CHAT        |
                    self::M_FORUM;

    public function findByUsername(string $username):array;

    public function findByEmail(string $email):array;

}