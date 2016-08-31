<?php
/**
 * reg.php
 * Created by Babenoff at 27.08.16 - 0:03
 */
require "setup.php";
$page = "<h1>Регистрация</h1>";
$errors = [];
if(isset($_POST["username"])){
    /** @var \LD2\Repository\IHeroRepository $heroRepo */
    $heroRepo = $app->getContainer()->get("hero_repository");
    $tmp = "";
}  else {
    $tmp .= <<<REG_
<form class="login_form" action="login.php" method="post">
    <div>Логин:</div>
    <div>
        <input class="ram" name="username" placeholder="Логин" />
    </div>
    <div>Пароль:</div>
    <div>
        <input class="ram" name="password" placeholder="Пароль" />
    </div>
    <div>Повторите пароль:</div>
    <div>
        <input class="ram" name="password_c" placeholder="Пароль" />
    </div>
    <div>Имя персонажа (будет видно в игре):</div>
    <div>
        <input class="ram" name="title" placeholder="Имя персонажа" />
    </div>
    <div>
        <input class="wh1" type="submit" value="Регистрация" />
    </div>
</form>
<hr />

<a class="strong" href="./?">на главную</a>
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
$app->getView()->display($page);