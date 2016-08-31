<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */
$page =<<<ERROR
    Страница не найдена
ERROR;

$app->getView()->display($page, [], "Ошибка");
