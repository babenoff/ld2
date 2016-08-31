<?php

try {
    require "setup.php";
} catch (PDOException $e) {
    $page = <<<ERR_CONNECT
<div class="d">
    Ошибка подключения
</div>
<div>
    Не удается установить соединение с базой данных<br/>
    {$e->getMessage()}
</div>
ERR_CONNECT;
    $app->getView()->display($page, "ERROR");
}

?>
