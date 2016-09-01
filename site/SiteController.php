<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2Controller;


use Gregwar\Captcha\CaptchaBuilder;
use LD2\BaseController;

class SiteController extends BaseController
{
    public function loginAction(){
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

    public function registrationAction(){

        $page = "";
        $errors = [];
        $tmp = "";
        if(isset($_POST["username"])){
            /** @var \LD2\Repository\IHeroRepository $heroRepo */
            $heroRepo = $this->getApp()->getContainer()->get("hero_repository");
            $tmp .= "";
        }  else {
            /** @var CaptchaBuilder $captcha */
            $captcha = $this->getApp()->getContainer()->get("captcha");
            $code = random_int(0,9999);
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
        if(count($errors)){
            $page .="<div class='alert alert-danger'>";
            foreach ($errors as $error){
                $page .= "<div>".$error."</div>";
            }
            $page .="</div>";
        }
        $page .=$tmp;
        $this->getApp()->getView()->display($page, [], "Регистрация", false);
    }
}