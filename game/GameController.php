<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2Controller;

use LD2\Exception\RepositoryException;
use LD2\Repository\IHeroRepository;
use LD2\Repository\IUserRepository;

/**
 * Class GameController
 * @package LD2Controller
 *
 */
class GameController extends IsAuthController
{
    /**
     * @var IUserRepository $userRepo ,
     */
    protected $userRepo;
    /**
     * @var IHeroRepository $heroRepo
     */
    protected $heroRepo;
    /**
     * @var array
     */
    protected $user, $hero;

    protected function before()
    {
        parent::before();
        $this->userRepo = $this->getContainer()->get("user_repository");
        $this->heroRepo = $this->getContainer()->get("hero_repository");
        try {
            $user = $this->userRepo->findByUsername($this->username);
            $hero = $this->heroRepo->getHeroesByUsername($this->username);
            if (count($hero) > 0) {
                $hero = $hero[0];
            }
            $this->user = $user;
            $this->hero = $hero;
        } catch (RepositoryException $e) {
            $errs = [
                "errs" => [
                    "Не удалось загрузить героя",

                ]
            ];
            if (getenv("ENVIROMENT") == "development") {
                array_push($errs["errs"], $e->getMessage());
            }
            $this->render('error.twig', $errs);
            exit;
        }
    }

    public function mainAction()
    {
        $this->render("game/main.twig", [
            "user" => $this->user,
            "hero" => $this->hero
        ]);
    }

    public function logoutAction(){
        $this->sessData = [];
        session_destroy();
        header("Location: ".$this->generate('login'));
    }
}