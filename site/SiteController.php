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

class SiteController extends BaseController
{
    public function _loginAction()
    {
        $page = <<<LOGIN
<form class="login_form" action="login.php?action=connect" method="post">
    <div>Логин:</div>
    <div>
        <input class="ram form-control" name="username" placeholder="Логин" />
    </div>
    <div>Пароль:</div>
    <div>
        <input class="ram form-control" name="password" placeholder="Пароль" />
    </div>
    <div>
        <input class="button btn-primary" type="submit" value="Вход" />
    </div>
</form>
<hr/>
<div>
<a class="strong" href="{$this->generate("registration")}">Регистрация</a>
</div>
<a class="strong" href="/login.php?action=about">Об игре</a>
LOGIN;
        $this->getApp()->getView()->display($page, [], "Лайкдимион");
    }

    public function loginAction()
    {
        $this->render('login.twig');
    }


    public function _registrationAction()
    {

        $page = "";
        $errors = [];
        $tmp = "";
        if (isset($_POST["username"])) {
            /** @var \LD2\Repository\IHeroRepository $heroRepo */
            $heroRepo = $this->getApp()->getContainer()->get("hero_repository");
            $tmp .= "";
        } else {
            /** @var CaptchaBuilder $captcha */
            $captcha = $this->getApp()->getContainer()->get("captcha");
            $code = random_int(0, 9999);
            $captcha->setPhrase($code);
            $captcha->build();
            $_SESSION["captcha"] = $captcha;
            $tmp .= <<<REG_
<form class="login_form" action="login.php" method="post">
    <div>Логин:</div>
    <div>
        <input class="ram form-control" name="username" placeholder="Логин" />
    </div>
    <div>Пароль:</div>
    <div>
        <input class="ram form-control" name="password" placeholder="Пароль" />
    </div>
    <div>Повторите пароль:</div>
    <div>
        <input class="ram form-control" name="password_c" placeholder="Пароль" />
    </div>
    <div>Имя персонажа (будет видно в игре):</div>
    <div>
        <input class="ram form-control" name="title" placeholder="Имя персонажа" />
    </div>
    <div>
        <img src="{$captcha->inline()}" />
        <input class="ram form-control" name="captcha" placeholder="Код с картинки" />
    </div>
    <div>
        <input class="button btn-primary" type="submit" value="Регистрация" />
    </div>
</form>
<hr />

<a class="strong" href="{$this->generate("login")}">на главную</a>
REG_;
        }
        if (count($errors)) {
            $page .= "<div class='alert alert-danger'>";
            foreach ($errors as $error) {
                $page .= "<div>" . $error . "</div>";
            }
            $page .= "</div>";
        }
        $page .= $tmp;
        $this->getApp()->getView()->display($page, [], "Регистрация", false);
    }

    public function registrationAction()
    {
        $params = [
            "errs" => []
        ];
        if (false !== $this->getRequest()->get("email", false)) {
            if($res = $this->regProcess($params)){
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
                if (preg_match("/^(?'Username'[-\w\d\.]+?)(?:\s+at\s+|\s*@\s*|\s*(?:[\[\]@]){3}\s*)(?'Domain'[-\w\d\.]*?)\s*(?:dot|\.|(?:[\[\]dot\.]){3,5})\s*(?'TLD'\w+)$/i", $email, $emailMatches)) {
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
                                foreach ($authors as $author){
                                    if($author->email == $email){
                                        $isAdmin = true;
                                    }
                                }
                                if($isAdmin){
                                    $user["role"] = UserRepository::ADMIN;
                                } else {
                                    $user["role"] = UserRepository::BASE;
                                }
                                try {
                                    $userRepo->create($user);
                                    try{
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
                                        try{
                                            $heroRepo->create($hero);
                                            return [$user["username"], $password];
                                        } catch (HeroRepositoryException $e){
                                            $params["errs"][] = $e->getMessage();
                                        }
                                    }
                                }catch (UserRepositoryException $e){
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