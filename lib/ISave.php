<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * ISave.php at ld2.my
 * Created by Babenoff at 28.08.16 - 22:37
 */

namespace LD2;


interface ISave
{
    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data);

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data);

    /**
     * @param mixed $byField
     * @return boolean
     */
    public function delete($byField);
}