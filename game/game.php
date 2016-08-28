<?php
/**
 * game.php
 * Created by Babenoff at 27.08.16 - 0:00
 */

if(isset($_COOKIE[$config["cookie_name"]])){
    if($app->connect()){

    } else {
        $page = <<<ERR_CONNECT
<div class="d">
    Ошибка подключения
</div>
<div>
    Не удается установить соединение с базой данных
</div>
ERR_CONNECT;

    }
} else {
    header("Location: login.php");
    die();
}