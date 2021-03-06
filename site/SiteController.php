<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2Controller;


use Gregwar\Captcha\CaptchaBuilder;
use LD2\BaseController;
use LD2\Exception\HeroRepositoryException;
use LD2\Exception\UserRepositoryException;
use LD2\Repository\HeroRepository;
use LD2\Repository\IHeroRepository;
use LD2\Repository\IUserRepository;
use LD2\Repository\UserRepository;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class SiteController extends BaseController
{
    /**
     * @var IUserRepository
     */
    protected $userRepo;

    protected function before()
    {
        parent::before();
        $this->userRepo = $this->getContainer()->get("user_repository");
    }


    public function loginAction()
    {
        $uname = $this->request->get("username", false);
        $pwd = $this->request->get("username", false);
        if (false === $uname) {
            $this->render('login.twig');
        } else {
                $this->forward('connect', [
                    "username" => $uname,
                    "password" => $this->request->get("password")
                ]);
        }
    }

    public function connectAction($username, $password)
    {
            try{
                if(preg_match(UserRepository::EMAIL_REGEX, $username)){
                    $user = $this->userRepo->findByEmail($username);
                } else {
                    $user = $this->userRepo->findByUsername(htmlspecialchars($username));
                }
                if(password_verify($password, $user["password"])){
                    $_SESSION["username"] = $user["username"];
                    header("Location: ".$this->generate("game_main"));
                } else {
                    $this->render('login.twig', [
                        "errs" => [
                            "Неверная комбинация логин/пароль"
                        ]
                    ]);
                }
            }catch (UserRepositoryException $e){
                $this->render('login.twig', [
                    "errs" => [
                        "Неверная комбинация логин/пароль"
                    ]
                ]);
            }
    }

    public function registrationAction()
    {
        $params = [
            "errs" => []
        ];
        if (false !== $this->getRequest()->get("email", false)) {
            if ($res = $this->regProcess($params)) {
                $params["regOk"] = 1;
                $params["username"] = $res[0];
                $params["password"] = $res[1];
            }
        }
        $this->render("registration.twig", $params);
    }

    protected function regProcess(array &$params)
    {
        //$username = $this->request->get("username", null);
        $email = $this->request->get("email", null);
        $password = $this->request->get("password", null);
        $confirmPassword = $this->request->get("password_confirm", null);
        $nickname = $this->request->get("nickname", null);
        $code = $this->request->get("captcha", null);
        $cCode = $_SESSION["captcha"];
        $emailMatches = [];
        if ($password and $confirmPassword) {
            if (preg_match("/(?=.{4,20}$)(?![_.-])(?!.*[_.-]{2})[a-zа-яё0-9_\-\s]+([^._-])/i", $nickname)) {
                if (preg_match(UserRepository::EMAIL_REGEX, $email, $emailMatches)) {
                    if ($password == $confirmPassword) {
                        if ($code == $_SESSION["captcha"]) {
                            /** @var IUserRepository $userRepo */
                            $userRepo = $this->getContainer()->get("user_repository");
                            /** @var IHeroRepository $heroRepo */
                            $heroRepo = $this->getContainer()->get("hero_repository");
                            try {
                                $user = $userRepo->findByUsername($emailMatches["Username"]);
                                $params["errs"][] = "E-Mail " . $email . " уже зарегистрирован";
                            } catch (UserRepositoryException $e) {
                                $user = [
                                    "username" => $emailMatches["Username"],
                                    "email" => $email,
                                    "password" => password_hash($password, PASSWORD_DEFAULT)
                                ];
                                $authors = $this->getContainer()->get("composer.json")->authors;
                                $isAdmin = false;
                                foreach ($authors as $author) {
                                    if ($author->email == $email) {
                                        $isAdmin = true;
                                    }
                                }
                                if ($isAdmin) {
                                    $user["role"] = UserRepository::ADMIN;
                                } else {
                                    $user["role"] = UserRepository::BASE;
                                }
                                try {
                                    $userRepo->create($user);
                                    try {
                                        $hero = $heroRepo->getHeroByTitle($nickname);
                                    } catch (HeroRepositoryException $e) {
                                        $hero = [
                                            "username" => $user["username"],
                                            "title" => $nickname,
                                            "exp_points" => 5,
                                            "hero_char" => [20, 20, 20, 20],
                                            "hero_stats" => [10, 10, 10],
                                            "hero_params" => [],
                                            "hero_status" => [
                                                "ghost" => false,
                                                "crim" => false,
                                                "rider" => false
                                            ],
                                            "hero_timers" => [],
                                            "hero_war" => [],
                                            "hero_statistic" => [],
                                            "hero_magic" => [],
                                            "hero_inventory" => [],
                                            "hero_bank" => [],
                                            "hero_equip" => []
                                        ];
                                        try {
                                            $heroRepo->create($hero);
                                            return [$user["username"], $password];
                                        } catch (HeroRepositoryException $e) {
                                            $params["errs"][] = $e->getMessage();
                                        }
                                    }
                                } catch (UserRepositoryException $e) {
                                    $params["errs"][] = $e->getMessage();
                                }
                            }
                        } else {
                            $params["errs"][] = "Вы ввели неверный код с картинки.";
                        }
                    } else {
                        $params["errs"][] = "Пароли не совпадают";
                    }
                }
            } else {
                $params["errs"][] = "Неверный синтаксис в имени персонажа. Разрешены русские и латинские символы, знак подчеркивания _ и тире -, но не более одного подряд. Минимальная дляни имени 4 символа. Первым сиволом разрешена только буква или цифра.";
            }
        } else {
            $params["errs"][] = "Заполните все поля";
        }
        return false;
    }
}