<?php
/**
 * login.php
 * Created by Babenoff at 26.08.16 - 19:31
 */
require "setup.php";

setcookie($config["cookie_name"], "", time() - 3600);
$page = "";
if (isset($_POST["username"])) {

} else {
    $page .= <<<LOGIN
<form class="login_form" action="login.php" method="post">
    <div>Логин:</div>
    <div>
        <input class="ram form-control" name="username" placeholder="Логин" />
    </div>
    <div>Пароль:</div>
    <div>
        <input class="ram form-control" name="password" placeholder="Пароль" />
    </div>
    <div>
        <input class="button btn-primary" type="submit" value="Вход" /> <a class="strong" href="reg.php?">Регистрация</a>
    </div>
</form>
<hr/>
<a class="strong" href="/?"></a>
LOGIN;
}

$app->getView()->display($page);