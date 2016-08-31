<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

$page = "";
$errors = [];
$tmp = "";
if(isset($_POST["username"])){
    /** @var \LD2\Repository\IHeroRepository $heroRepo */
    $heroRepo = $app->getContainer()->get("hero_repository");
    $tmp .= "";
}  else {
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
        <input class="button btn-primary" type="submit" value="Регистрация" />
    </div>
</form>
<hr />

<a class="strong" href="./login.php">на главную</a>
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
$app->getView()->display($page, [], "Регистрация");