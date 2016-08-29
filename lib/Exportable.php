<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * Exportable.php at ld2.my
 * Created by Babenoff at 29.08.16 - 18:40
 */

namespace LD2;


interface Exportable
{
    /**
     * @return array
     */
    public function export():array;
}