<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

$page .= <<<LOGIN
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
<a class="strong" href="login.php?action=reg">Регистрация</a>
</div>
<a class="strong" href="/login.php?action=about">Об игре</a>
LOGIN;

$app->getView()->display($page, [], $app->getContainer()->getParameter("game_title"));