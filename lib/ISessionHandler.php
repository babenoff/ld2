<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2;


interface ISessionHandler
{
    public function open();
    public function read($sid);
    public function write($sid, $data);
    public function destroy($sid);
    public function clean();
}