<?php
/**
 * CalssNotExistsException.php at ld2.my
 * Created by Babenoff at 28.08.16 - 0:04
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Exception;


use Exception;

class ClassNotExistsException extends RuntimeExcetion
{
    public function __construct($className)
    {
        $mess = "Class %s not exists";
        parent::__construct(sprintf($mess, $className), 404);
    }
}